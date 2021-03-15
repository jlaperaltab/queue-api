# Queue API

### How to Install

Create .env file
```sh
cp .env.example .env 
```

Install dependences
```sh
composer install
```

Generate artisan key
```sh
php artisan key:generate
```

Run migrations
```sh
php artisan migrate
```

Run local server
```sh
php artisan serve
```

Run Horizon for see metrics
```sh
php artisan horizon
```

Run "Queue work" for kinds of queues
```sh
php artisan queue:work --queue=high,default,low
```

## Usage
### Insert job in a queue
```
POST /api/job
```

| Attribute | Type  | Required  | Description
| ------ | ------ | ------ | ------ |
| submitter_id | string | yes |ID of submmiter the job
| command | string | yes | Command you need to run when the job is processed 
| priority | integer | yes |Priority of your job 1, 2, 3 => low, default, high

##### Response example

```
{
    "task_id": 115,
    "response": "Task successfully added to the queue"
}
```


### Get the next task to process by the high priority
```
GET /api/job
```
##### Response example

```
{
    "task_id": 115,
    "submitter_id": 23,
    "command": "\"alta\"",
    "priority": 1,
    "state": "queued"
}
```

### Get status of a Job by ID
```
GET /api/job/{jobid}
```
| Attribute | Type  | Required  | Description
| ------ | ------ | ------ | ------ |
| jobid | integer | yes| ID of the job you want to consult 
##### Response example

```
{
    "task_id": 104,
    "submitter_id": 23,
    "command": "\"alta\"",
    "priority": 3,
    "state": "completed"
}
```

### Edit a job only if it has not been processed yet 
```
PUT /api/job/{jobid}
```
| Attribute | Type  | Required  | Description
| ------ | ------ | ------ | ------ |
| command | string | yes| Command you need to run when the job is processed 
##### Response example

```
{
    "task_id": 115,
    "submitter_id": 23,
    "command": "mi new command",
    "priority": 1,
    "response": "Task update successfully"
}
```
