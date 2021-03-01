<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Library\RedisFacade as Redis;
class AccountStendExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AccountStendExport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
		$department_id=Redis::get('export:department_id');
		$send_time_start=Redis::get('export:send_time_start');
		$send_time_end=Redis::get('export:send_time_end');
		$count_export=Redis::get('export:count_export');
		if(empty($department_id) || empty($send_time_start) || empty($send_time_end) || empty($count_export)){
			
			exit('缺少必要参数');
		}
       $message=new GoldAccountStendExportService;
	   
	   $message->export_redis($department_id,$send_time_start,$send_time_end,$count_export);
	   Redis::set('export:test','已执行');
    }
}
