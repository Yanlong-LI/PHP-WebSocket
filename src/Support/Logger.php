<?php
/**
 * Copyright (c) 2018. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace Non0\Socket\Support;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\RotatingFileHandler;


/**
 * Class Logger
 * @package Non0\Socket\Support
 */
class Logger
{

    private $logger = null;

    /**
     * @var string The logging channel
     */
    private $name = 'pfinal.logger';
    /**
     * @var string 日志文件
     */
    private $file;

    /**
     * @var int 日志等级 对应常量: \Monolog\Logger::WARNING 、\Monolog\Logger::ERROR 等
     */
    private $level = \Monolog\Logger::DEBUG;

    public function __construct(array $config = array())
    {
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }

        $this->init();
    }

    /**
     *
     */
    public function init()
    {
        if ($this->logger instanceof \Psr\Log\LoggerInterface) {
            return;
        }

        $logger = new \Monolog\Logger($this->name);

        if (defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } else if (empty($this->file)) {
            $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, $this->level));
        } else {
            //$logger->pushHandler(new StreamHandler($this->file, $this->level));
            $logger->pushHandler(new RotatingFileHandler($this->file, 7, $this->level));
        }
        $this->logger = $logger;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->logger, $name), $arguments);
    }
}