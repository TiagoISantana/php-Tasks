<?php


namespace Tasks;


/**
 * Class Os
 * This abstract class helps define/help view OS version
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
     *
     */
    CONST MEM_BLOCK_START = 0x00;

    /**
     *
     */
    CONST MEM_BLOCK_END = 0xFF;

    /**
     * @var string
     * OS version check
     */
    protected string $_OS = 'LNX';

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