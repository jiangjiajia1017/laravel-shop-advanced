<?php

namespace App\Console\Commands\Cron;

use App\Jobs\RefundCrowdfundingOrders;
use App\Models\CrowdfundingProduct;
use App\Models\Order;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FinishCrowdfunding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:finish-crowdfunding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '结束众筹';

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
        CrowdfundingProduct::query()
            ->where('end_at', '<=', Carbon::now())
            ->where('status', CrowdfundingProduct::STATUS_FUNDING)
            ->get()
            ->each(function (CrowdfundingProduct $crowdfundingProduct){
                if($crowdfundingProduct->target_amount > $crowdfundingProduct->total_amount){
                    //调用众筹失败逻辑
                }else{

                }

            });
    }


    /**
     * 众筹成功只需要更改状态即可
     * @param CrowdfundingProduct $crowdfundingProduct
     */
    protected function crowdfundingSucceed(CrowdfundingProduct $crowdfundingProduct)
    {
        $crowdfundingProduct->update([
            'status' => CrowdfundingProduct::STATUS_SUCCESS,
        ]);
    }

    /**
     * 众筹失败 执行退款动作
     * @param CrowdfundingProduct $crowdfundingProduct
     */
    protected function crowdfundingFaield(CrowdfundingProduct $crowdfundingProduct)
    {
        $crowdfundingProduct->update([
            'status' => CrowdfundingProduct::STATUS_FAIL,
        ]);
        //执行异步退款动作
        dispatch(new RefundCrowdfundingOrders($crowdfundingProduct));
    }
}
