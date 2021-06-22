<?php


namespace Tasks;


interface IAppTask
{

    public function runTask(string $script_path, bool $output = FALSE, string $mode = 'rw');

    public function stopTask(string $taskId): void;

    public function checkTaskStatus(string $taskId): bool;

    public function getTaskOutput(string $taskId, bool $last_line = true):string|false;


}