<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Task;
use App\Jobs\ProcessTask;


class TaskController extends Controller
{
    public function storeTask(Request $request){
        //Validate request 
        $rules = [
            'command' => 'required',
            'priority' => 'required|numeric',
            'submitter_id' => 'required|numeric',
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        } else {
            //Store Task in BBDD
            $task = new Task;
            $task->submitter_id = request('submitter_id');
            $task->command = request('command');
            //1 = Low, 2 = Default, 3 = High
            $task->priority = request('priority');
            //queued, completed
            $task->state = 'queued';
            $task->save();
            
            switch(request('priority')){
                case 1:
                    $priority = 'low';
                    break;
                case 2:
                    $priority = 'default';
                    break;
                case 3:
                    $priority = 'high';
                    break;
            }
            
            //Dispatch Task to Queue According to priority 
            dispatch(new ProcessTask($task))->onQueue($priority);

            //Return the Task ID for User
            return response()->json(
            [
                'task_id' => $task->id,
                'response'=> "Task successfully added to the queue"
            ],201);
        }
    }

    public function nextTask(){
        //Get the next task by priority 
        $task = Task::orderBy('priority', 'desc')->orderBy('id', 'asc')->where('state', 'queued')->first();
        if($task){
            return response()->json(
            [
                'task_id' => $task->id,
                'submitter_id' => $task->submitter_id,
                'command' => $task->command,
                'priority' => $task->priority,
                'state' => $task->state,
            ],201);
        }else{
            return response()->json(
            [
                'response'=> "There are no pending tasks to process "
            ],201);
        }
    }

    public function getTask(Request $request){
        //Get task by ID
        $task = Task::find(request('taskId'));
        if($task){
            return response()->json(
            [
                'task_id' => $task->id,
                'submitter_id' => $task->submitter_id,
                'command' => $task->command,
                'priority' => $task->priority,
                'state' => $task->state,
            ],201);
        }else{
            return response()->json(
            [
                'response'=> "Task ID not found y database"
            ],201);
        }
    }

    public function updateTask(Request $request){
        //Validate the request
        $rules = [
            'command' => 'required',
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        } else {
            //Get task by id
            $task = Task::find(request('taskId'));
            if($task){
                //Verify if task is completed
                if($task->state == 'completed'){
                    return response()->json(
                        [
                            'response'=> "Unable to update a Task already processed"
                        ],201);
                }else{
                    //update the task with the new command
                    $task->command = request('command');
                    $task->save();
                    return response()->json(
                    [
                        'task_id' => $task->id,
                        'submitter_id' => $task->submitter_id,
                        'command' => $task->command,
                        'priority' => $task->priority,
                        'response'=> "Task update successfully"
                    ],201);
                }
            }else{
                return response()->json(
                [
                    'response'=> "Task ID not found y database"
                ],201);
            }
        }
    }
}
