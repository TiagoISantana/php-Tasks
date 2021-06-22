<?php

//Load required files
require_once __DIR__ . '/vendor/autoload.php';

try {

    $async = new \Tasks\Async();

    //Run task in a new process
    $taskId = $async->runTask(script_path: __DIR__ . '/randomTask.php', output: true);

    sleep(2);

    //Check if task is still running
    var_dump($async->checkTaskStatus(taskId: $taskId));

    sleep(2);

    //Get task output
    $out = $async->getTaskOutput(taskId: $taskId, last_line: true);

    var_dump($out);

    sleep(2);

    //Stop task
    $async->stopTask(taskId: $taskId);

} catch (Throwable $exception) {

    var_dump($exception);

}