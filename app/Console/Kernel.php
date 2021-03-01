<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Library\RedisFacade as Redis;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
//        Commands\MessageBillCommand::class,
//		Commands\MessagePushCommand::class,
		Commands\AccountStendExport::class,
//	    Commands\AutoRecycleGoldSend::class,
//	    Commands\AutoRecyclePersonGold::class,
//	    Commands\AutoRecycleMessageGold::class,
//	    Commands\AutoRecycleActivityGold::class,
//	    Commands\AutoRecycleQuestionGold::class,
//        Commands\PushMySqlMsg::class,
//	    Commands\AutoProduceMultipleQr::class,
//	    Commands\SyncCache::class,
//	    Commands\PushTaxMsg::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        /**! 每天早上8點執行 !**/
//        $schedule->command('command:push-mysql-msg-to-user')->cron('0 8 * * *');
//        $schedule->command('command:push-mssql-msg-to-user')->cron('*/10 * * * *');
//
//		//獲取最後一天
//		$beginDate	= date('Y-m-01', strtotime(date("Y-m-d")));
//		$end_day	= date('d', strtotime("$beginDate +1 month -1 day"));
//
//		$schedule->command('MessageBillCommand')->cron('0 5 5,10,15,20,25,'.$end_day.' * *')->withoutOverlapping();//5,10,15,20,25,30，每月固定日期凌晨1點0分執行一次
//		$schedule->command('MessagePushCommand')->cron('0 8 5,10,15,20,25,'.$end_day.' * *')->withoutOverlapping();//5,10,15,20,25,30,每隔5日早上8點執行一次
//	    $schedule->command('command:pushTaxMsg')->cron('0 9 5,10,15,20,25,'.$end_day.' * *')->withoutOverlapping();//5,10,15,20,25,30,每隔5日早上9點執行一次 繳稅通知
//		$schedule->command('command:goldSend')->daily();
//		$schedule->command('command:messageGold')->daily();
//		$schedule->command('command:activityGold')->daily();
//		$schedule->command('command:questionGold')->daily();
//		$schedule->command('command:personGold')->daily();
//	    $schedule->command('command:autoProduceMultipleQr')->daily();
//	    $schedule->command('command:syncIndexCache')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
		
//		Redis::rpush('park:log_crontab', '已執行定時任務：'.date('Y-m-d H:i:s',time()));
//		Redis::EXPIRE('park:log_crontab', 3600);
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
