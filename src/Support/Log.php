<?php

namespace Jsdsx\Xdx\CreditReport\Support;

use Jsdsx\Xdx\CreditReport\Kernel;

class Log
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected static $logger = null;

    public static function __callStatic($name, $arguments)
    {
        self::init();
        return call_user_func_array(array(self::$logger, $name), $arguments);
    }

    private static function init()
    {
        if (static::$logger === null) {
            $config = Kernel::getConfig('log', array('class' => 'Jsdsx\Xdx\CreditReport\Support\Logger'));
            $class = $config['class'];
            unset($config['class']);
            static::$logger = new $class($config);
        }
    }
}
