<?php

namespace App\Console;

use App\Models\Food;
use App\Models\Foods;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $foods = Foods::has('foodTransactions')->get();
            if ($foods->isEmpty()) {
                return response()->json(['error' => 'Không có giao dịch nào.']);
            }
            foreach ($foods as $food) {
                foreach ($food->foodTransactions as $foodTransaction) {
                    if ($foodTransaction->donor_status == 1 && $foodTransaction->status == 0) {
                        $expectedConfirmationTime = Carbon::parse($foodTransaction->donor_confirm_time)->addMinutes($food->remaining_time_to_accept);
                        if (now() > $expectedConfirmationTime) {
                            $foodTransaction->update(['status' => 3]);
                            $food->update(['quantity' => $food->quantity + $foodTransaction->quantity_received]);
                        }
                    }
                }
            }
        })->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
