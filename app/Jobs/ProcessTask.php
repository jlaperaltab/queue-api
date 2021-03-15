<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;


class ProcessTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //use redis for allow job only 1 time every 5 seconds
        Redis::throttle('key')->block(0)->allow(1)->every(5)->then(function () {
            $start = microtime(true);
            Log::info('Queued to ProcessTask',['task' => $this->task]);
            
            //sleep for simulate a work for 30 seconds
            sleep(30);
            
            //change state of task to complete
            $this->task->state = 'completed';
            $this->task->save();

            $end = microtime(true);
            $time = $end - $start;
            
            //save log with time
            Log::info('time to process',[
            'start' => $start,
            'end' => $end,
            'total' => $time
            ]);
        });

        
    }
}