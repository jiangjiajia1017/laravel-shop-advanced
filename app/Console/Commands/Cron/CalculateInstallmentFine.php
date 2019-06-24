<?php

namespace App\Console\Commands\Cron;

use App\Models\Installment;
use App\Models\InstallmentItem;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateInstallmentFine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:calculate-installment-fine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计算分期付款逾期费';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        InstallmentItem::query()
            ->with(['installment'])
            ->whereHas('installment', function ($query){
                $query->where('status', Installment::STATUS_REPAYING);
            })
            ->where('due_date', '<=', Carbon::now())
            ->whereNull('paid_at')
            ->chunkById(1000, function ($items){
                // 遍历查询出来的还款计划
                foreach ($items as $item) {
                    // 通过 Carbon 对象的 diffInDays 直接得到逾期天数
                    $overdueDays = Carbon::now()->diffInDays($item->due_date);
                    // 本金与手续费之和
                    $base = bcadd($item->base, $item->fee, 2);
                    // 计算逾期费
                    $fine = bcdiv(bcmul(bcmul($base , $overdueDays,2) , $item->installment->fine_rate ,2),100, 2);
                    // 避免逾期费高于本金与手续费之和，使用 compareTo 方法来判断
                    // 如果 $fine 大于 $base，则 compareTo 会返回 1，相等返回 0，小于返回 -1
                    $fine = bccomp($fine, $base) ? $base : $fine;
                    $item->update([
                        'fine' => $fine,
                    ]);
                }

            });
    }
}
