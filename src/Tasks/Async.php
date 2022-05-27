<?php


namespace Tasks {


    use Tasks\TaskException;

    /**
     * Class Async
     * @package Tasks
     */
    class Async extends Os implements IAppTask
    {

        /**
         * @var string
         */
        private string $_taskId;

        /**
         * @var string
         */
        private string $_php_path;

        /**
         * @var bool
         */
        private bool $_output;

        /**
         *
         */
        public $_OUT_DIR = __DIR__ . '\..\task-output\\';

        /**
         * Async constructor.
         * @throws \Tasks\TaskException
         */
        public function __construct()
        {

            parent::__construct();

            $this->checkPhp();

            if(!is_dir($this->_OUT_DIR))
                mkdir($this->_OUT_DIR);

        }

        /**
         * Check what is the php binary path and if it's on OS path (Windows)
         * @param string $php_path
         * @throws \Tasks\TaskException
         */
        private function checkPhp(string $php_path = 'php'): void
        {

            exec(command: "$php_path -v", output: $out, result_code: $rc);

            if ($rc == 0) {

                if (str_contains(haystack: $out[0], needle: 'PHP')) {

                    $this->_php_path = 'php';

                } else {

                    $this->_php_path = PHP_BINARY;

                }

            } else {

                throw new TaskException(message: 'Unable to get php path', code: 0x101);

            }

        }

        /**
         * @param string $script_path
         * @param bool $output
         * @param string $mode "r", "rb", "w", or "wb"
         * @return string
         * @throws \Tasks\TaskException
         */
        public function runTask(string $script_path, bool $output = false, string $mode = 'rb', array $args = []): string
        {

            $this->_output = $output;

            if (is_file(filename: $script_path)) {

                $php_code = file_get_contents(filename: $script_path);

                if (str_contains(haystack: $php_code, needle: '<?php') != FALSE) {

                    $taskId = $this->generateTaskId();

                    $cmd_output = '';

                    if ($output)
                        $cmd_output = ' > ' . $this->_OUT_DIR . $taskId;

                    if ($this->_OS == self::OS_WINDOWS) {

                        pclose(handle: popen(
                                command: self::OS_WINDOWS_CMD . ' "' . $taskId . '" "' . PHP_BINARY . '" -f "' . $script_path . '" {' . $taskId . '}' . ' ' . implode(' ',$args) . $cmd_output, mode: $mode
                            )
                        );

                        return $taskId;

                    } else if ($this->_OS == self::OS_LINUX) {

                        pclose(handle: popen(
                                command: PHP_BINARY . ' "' . $script_path . '" --TID=' . $taskId . ' ' .implode(' ',$args) .' &> /dev/null &', mode: $mode
                            )
                        );

                        return $taskId;

                    } else {

                        throw new TaskException(message: "Unsupported OS " . PHP_OS, code: 0x010);

                    }

                } else {

                    throw new TaskException(message: 'The provided file is not a php file.', code: 0x105);

                }

            } else {

                throw new TaskException(message: 'Provided file in not a valid file', code: 0x102);

            }

        }

        /**
         * TODO: FIX WINDOWS KILL TREE
         * @param string $taskId
         * @throws \Tasks\TaskException
         */
        public function stopTask(string $taskId): void
        {

            if ($this->_OS == self::OS_WINDOWS) {

                exec(command: 'wmic process where caption="php.exe" get commandline, Processid  | findstr "' . $taskId . '"', output: $out, result_code: $rc);

                if (isset($out[0])) {

                    $pid = (int)explode(separator: '  ', string: $out[0])[2];

                    //CMD does not work because of privilege....WTF :(
                    shell_exec(command: "taskkill /F /PID $pid");

                } else {

                    throw new TaskException(message: 'Unable to find task, maybe its already done?', code: 0x127);

                }

            } else if ($this->_OS == self::OS_LINUX) {

                exec(command: 'ps aux | grep "' . $taskId . '"', output: $out, result_code: $rc);

                if (is_array(value: $out) && isset($out[1])) {

                    $pid = preg_split(pattern: '/\s+/', subject: $out[0])[1];
                    shell_exec(command: "kill -9 $pid");

                } else {

                    throw new TaskException(message: 'Unable to find task, maybe its already done?', code: 0x301);

                }

            } else {

                throw new TaskException(message: "Unsupported OS " . PHP_OS, code: 0x010);

            }

        }

        /**
         * Method helps user to check if task is still running
         * @param string $taskId
         * @return bool
         */
        public function checkTaskStatus(string $taskId): bool
        {

            if ($this->_OS == self::OS_WINDOWS) {

                exec(command: 'wmic process where caption="php.exe" get commandline, Processid  | findstr "' . $taskId . '"', output: $out, result_code: $rc);

                return !empty($out);

            } else if ($this->_OS == self::OS_LINUX) {

                exec(command: 'ps aux | grep "' . $taskId . '"', output: $out, result_code: $rc);

                return is_array(value: $out) && isset($out[1]);

            } else {

                return false;

            }

        }

        /**
         * @param string $taskId
         * @param bool $last_line
         * @return string|false
         * @throws \Tasks\TaskException
         */
        public function getTaskOutput(string $taskId, bool $last_line = true): string|false
        {

            if(!is_dir($this->_OUT_DIR))
                mkdir($this->_OUT_DIR);


            if (!$this->_output) {

                trigger_error(message: 'Last output status set to false, task output may not be available', error_level: E_USER_WARNING);

            }

            $task_output = file_get_contents(filename: $this->_OUT_DIR . $taskId);

            if ($task_output != false) {

                if ($last_line) {

                    $string_last_line = explode(separator: "\n", string: $task_output);

                    return $string_last_line[count(value: $string_last_line) - 1];

                } else {

                    return $task_output;

                }

            } else {

                throw new TaskException(message: 'Unable to retrieve log', code: 0x122);

            }

        }

        /**
         * @return string
         */
        private function generateTaskId(): string
        {

            return implode(separator: '-', array: str_split(string: substr(string: strtoupper(string: md5(string: time() . rand(1000, 9999))), offset: 0, length: 20), length: 4));

        }

    }

}