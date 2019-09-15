<?php

namespace App\Console\Commands\Cron;

use Illuminate\Console\Command;
use App\Models\Installment;
use App\Models\InstallmentItem;
use Carbon\Carbon;

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
    protected $description = '计算分期付款的逾期费';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        InstallmentItem::query()
            ->with(['installment'])
            ->where('paid_at',null)
            ->whereDate('due_date','<=',Carbon::today())
            ->whereHas('installment',function ($query) {
                $query->where('status',Installment::STATUS_REPAYING);
            })
            ->chunkById(1000,function ($items){
                foreach ($items as $item) {
                    //计算当期本金与手续费之和
                    $base = big_number($item->base)->add($item->fee)->getValue();
                    $fine = big_number($base)->multiply(Carbon::today()->diffInDays($item->due_date))->multiply($item->Installment->fine_rate)->divide(100)->getValue();
                    $fine = min($base,$fine);
                    $item->update([
                        'fine' => $fine,
                    ]);
                }
            });
    }
}
