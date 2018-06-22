<?php
/**
 * Copyright (c) 2018. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace Non0\Socket\Support;



use Non0\Socket\Service;

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
            $config = Service::getConfig('log', array('class' => 'Non0\Socket\Support\Logger'));
            $class = $config['class'];
            unset($config['class']);
            static::$logger = new $class($config);
        }
    }
}
