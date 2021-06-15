<?php


namespace Tasks;

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
    public string $_taskId;

    /**
     * @var string
     */
    private string $_php_path;

    /**
     * Async constructor.
     * @throws \Tasks\TaskException
     */
    public function __construct()
    {

        parent::__construct();

        $this->checkPhp();

    }

    /**
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
    public function runTask(string $script_path, bool $output = false, string $mode = 'rb'): string
    {

        if (is_file($script_path)) {

            $php_code = file_get_contents($script_path);

            if (str_contains($php_code, '<?php') != FALSE) {

                $taskId = $this->generateTaskId();

                $cmd = PHP_BINARY;

                if ($this->_OS == self::OS_WINDOWS) {

                    pclose(
                        popen(
                            command: 'start /B "'.$taskId.'" "' . $cmd . '" -f "' . $script_path . '" {'.$taskId.'}', mode: $mode
                        )
                    );

                    return $taskId;

                } else if ($this->_OS == self::OS_LINUX) {

//                    pclose(
//                        popen(
//                            command: $cmd . ' -f "' . $script_path . '" >/dev/null &', mode: $mode
//                        )
//                    );

                    return $taskId;

                } else {

                    throw new TaskException("Unsupported OS " . PHP_OS, 0x010);

                }

            } else {

                throw new TaskException('The provided file is not a php file.', 0x105);

            }

        } else {

            throw new TaskException('Provided file in not a valid file', 0x102);

        }

    }

    /**
     * @param string $taskId
     */
    public function stopTask(string $taskId): void
    {

        exec('wmic process where caption="php.exe" get commandline, Processid  | findstr "' . $taskId . '"', $out, $rc);

        if(isset($out[0])){

            $pid = (int)explode('  ',$out[0])[2];

            shell_exec("taskkill /F /PID $pid");

        } else{

            throw new TaskException('Unable to find task, maybe its already done?');

         }
    }

    /**
     * @param $taskId
     * @return bool
     */
    public function checkTaskStatus($taskId): bool
    {

        exec('wmic process where caption="php.exe" get commandline, Processid  | findstr "' . $taskId . '"', $out, $rc);

        if(empty($out))
            return false;
        else
            return true;

    }

    /**
     * @return string
     */
    public function generateTaskId(): string
    {

        return implode('-', str_split(substr(strtoupper(md5(time() . rand(1000, 9999))), 0, 20), 4));

    }

}