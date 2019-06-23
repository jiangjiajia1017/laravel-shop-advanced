<?php

namespace App\Http\Controllers;


use App\Events\OrderPaid;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Installment;
use Illuminate\Support\Facades\DB;

class InstallmentsController extends Controller
{
    public function index(Request $request)
    {
        $installment = Installment::query()
            ->where('user_id', $request->user()->id)
            ->paginate(10);

        return view('installments.index', ['installments' => $installment]);

    }

    public function show(Installment $installment)
    {
        $this->authorize('own', $installment);
        $items = $installment->items()->orderBy('sequence')->get();
        return view('installments.show',
            [
                'installment' => $installment,
                'items'       => $items,
                //下一个未付款的还款计划
                'nextItem' => $items->where('paid_at', null)->first(),
            ]
        );

    }

    public function payByAlipay(Installment $installment)
    {
        if ($installment->order->closed) {
            throw new InvalidRequestException('对应的商品订单已被关闭');
        }
        if ($installment->status === Installment::STATUS_FINISHED) {
            throw new InvalidRequestException('该分期订单已结清');
        }

        if($nextItem = $installment->items()->whereNull('paid_at')->orderBy('sequence')->first()){
            throw new InvalidRequestException('该分期订单已结清');
        }

        return app('alipay')->web([
            // 支付订单号使用分期流水号+还款计划编号
            'out_trade_no' => $installment->no.'_'.$nextItem->sequence,
            'total_amount' => $nextItem->total,
            'subject'      => '支付 Laravel Shop 的分期订单：'.$installment->no,
            // 这里的 notify_url 和 return_url 可以覆盖掉在 AppServiceProvider 设置的回调地址
            'notify_url'   => ngrok_url('installments.alipay.notify'),
            'return_url'   => route('installments.alipay.return'),
        ]);

    }

    public function alipayReturn()
    {
        try {
            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }

        return view('pages.success', ['msg' => '付款成功']);

    }

    /**
     * 支付宝支付回调
     * @return string
     */
    public function alipayNotify()
    {
        //校验支付宝回调参数是否正确
        $data = app('alipay')->verify();

        // 如果订单状态不是成功或者结束，则不走后续的逻辑
        if (!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // 拉起支付时使用的支付订单号是由分期流水号 + 还款计划编号组成的
        // 因此可以通过支付订单号来还原出这笔还款是哪个分期付款的哪个还款计划
        list($no, $sequence) = explode('_', $data->out_trade_no);

        if (!$installment = Installment::query()->where('no', $no)->first()){
            return 'fail';
        }

        // 根据还款计划编号查询对应的还款计划，原则上不会找不到，这里的判断只是增强代码健壮性
        if (!$items = $installment->items()->where('sequence', $sequence)->first()){
            return 'fail';
        }

        if ($items->paid_at){
            return app('alipay')->success();
        }

        DB::transaction(function () use ($data, $no,$installment, $items){
            //更新对应还款计划
            $items->update([
                'paid_at'        => Carbon::now(), // 支付时间
                'payment_method' => 'alipay', // 支付方式
                'payment_no'     => $data->trade_no, // 支付宝订单号
            ]);

            // 如果这是第一笔还款
            if ($items->sequence === 1) {
                // 将分期付款的状态改为还款中
                $installment->update(['status' => Installment::STATUS_REPAYING]);
                // 将分期付款对应的商品订单状态改为已支付
                $installment->order->update([
                    'paid_at'        => Carbon::now(),
                    'payment_method' => 'installment', // 支付方式为分期付款
                    'payment_no'     => $no, // 支付订单号为分期付款的流水号
                ]);
                // 触发商品订单已支付的事件
                event(new OrderPaid($installment->order));
            }

            // 如果这是最后一笔还款
            if ($items->sequence === $installment->count ) {
                // 将分期付款状态改为已结清
                $installment->update(['status' => Installment::STATUS_FINISHED]);
            }

        });
        return app('alipay')->success();
    }

}
