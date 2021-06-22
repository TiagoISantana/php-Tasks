#### Requirements

- PHP > 8.0.0;

###### Disclaimer

This library is in its initial stage and is without stability, I do not guarantee that it will work correctly on all machines.

###### What can you do with this?

- Execute php script in a new process and have "full control" of the task

###### Installation

Download the files and run composer

`composer install`


###### Running example.php

```php
<?php

//Load required files
require_once __DIR__ . '/vendor/autoload.php';

try {

    $async = new \Tasks\Async();

    //Run task in a new process
    $taskId = $async->runTask(script_path: __DIR__ . '/path_to_my/script.php', output: true);

    //Check if task is still running
    var_dump($async->checkTaskStatus(taskId: $taskId));

    //Get task output
    var_dump($async->getTaskOutput(taskId: $taskId, last_line: true));
    
    //Stop task
    $async->stopTask(taskId: $taskId);

} catch (Throwable $exception) {

    var_dump($exception);
    
}