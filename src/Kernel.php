<?php

namespace Jsdsx\Xdx\CreditReport;

/** Created by PhpStorm.
 * User: Yanlongli
 * Date: 2018年5月18日
 * Time: 14:59:56
 * APPLICATION:信达付信用报告
 */

use Jsdsx\Xdx\CreditReport\Support\Log;

/**
 * Class Kernel
 * @package Jsdsx\Xdx\CreditReport
 */
class Kernel
{
    public static $lang;
    /**
     * @var Api
     */
    protected static $api = null;
    /**
     * @var array
     */
    private static $config = array();
    protected $configPath = null;

    public function __construct($configPath = null)
    {
        if (!is_null($configPath)) {
            $this->configPath = $configPath;
        }
        //初始化
        $this->init();
    }

    /**
     * 加载配置文件
     */
    protected function loadConfig()
    {
        //加载指定配置文件
        if (!is_null($this->configPath)) {
            self::$config = $this->configPath;
            Log::debug("loadConfig：". $this->configPath . PHP_EOL);
            //加载本地化配置文件
        } else if (file_exists(realpath('.') . '/config-local.php')) {
            self::$config = require realpath('.') . '/config-local.php';
            Log::debug("loadConfig：" . realpath('.') . '/config-local.php' . PHP_EOL);
            //加载配置文件
        } else if (file_exists(realpath('.') . '/config.php')) {
            self::$config = require realpath('.') . '/config.php';
            Log::debug("loadConfig：" . realpath('.') . '/config.php' . PHP_EOL);
        } else {
            Log::error('无法加载配置文件，请放置config.php到特定目录' . PHP_EOL);
            exit(self::$lang['notFileConfig']);
        }
    }

    /**
     * 初始化
     */
    public function init()
    {
        //初始化语言包  默认为中文
        static::$lang = require __DIR__ . "/lang/" . self::getConfig('language', 'zh-cn') . '.php';
        //加载配置文件
        $this->loadConfig();
        //加载自定义语言包  默认为中文
        static::$lang = require __DIR__ . "/lang/" . self::getConfig('language', 'zh-cn') . '.php';
        //初始化时间为 特定区域
        date_default_timezone_set(static::$config['timezone']);
        Log::debug('kernel.init start');
    }

    /**
     * @param $name
     * @param null $defaultValue
     * @return mixed|null
     */
    public static function getConfig($name, $defaultValue = null)
    {
        if (array_key_exists($name, static::$config)) {
            return static::$config[$name];
        }
        return $defaultValue;
    }

    /**
     * @param $name
     * @param null $defaultValue
     * @return null
     */
    public static function setConfig($name, $defaultValue = null)
    {
        if (array_key_exists($name, static::$config)) {
            return static::$config[$name] = $defaultValue;
        }
        return $defaultValue;
    }

}