<?php

/**
 * Created by PhpStorm.
 * User: Yanlongli
 * 2018年5月18日15:40:53
 */

return array(

    'domain' => '0.0.0.0',
    'port' => '3000',
    'debug' => 0,
    'redis'=>[
        'host'=>'',
        'port'=>'',
        'password'=>'',
    ]
,
    //语言包
    'language' => 'zh-cn',

    //时区  亚洲/上海
    'timezone'=>'Asia/Shanghai',

    //日志
    'log' => array(
        'class' => 'Non0\Socket\Support\Logger',
        'name' => 'Non0.Socket',
        'level' => Monolog\Logger::DEBUG,//日志显示级别
        'file' => './log.log',
    ),

);