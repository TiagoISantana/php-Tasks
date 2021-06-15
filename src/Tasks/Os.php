<?php


namespace Tasks;


/**
 * Class Os
 * @package Tasks
 */
abstract class Os
{

    /**
     * Constant for windows os version
     */
    CONST OS_WINDOWS = 'WIN';

    /**
     * Constant for linux os version
     */
    CONST OS_LINUX = 'LNX';

    /**
     * Command for linux background execution
     */
    CONST OS_LINUX_CMD = '>/dev/null &';

    /**
     * Command for windows background execution
     */
    CONST OS_WINDOWS_CMD = 'start /B';

    /**
     * @var string
     * OS version check
     */
    public string $_OS = 'LNX';

    /**
     * OS constructor.
     */
    public function __construct()
    {

        if (strtoupper(substr(PHP_OS, 0, 3)) === self::OS_WINDOWS) {

            $this->_OS = self::OS_WINDOWS;

        }

    }

}