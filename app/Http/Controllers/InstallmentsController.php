<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Installment;

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

}
