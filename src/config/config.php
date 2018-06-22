<?php
/**
 * Created by PhpStorm.
 * User: Yanlongli
 * 2018年5月18日15:40:53
 */

return array(
    //语言包
    'language' => 'zh-cn',

    //时区  亚洲/上海
    'timezone'=>'Asia/Shanghai',

    //日志
    'log' => array(
        'class' => 'Jsdsx\Xdx\CreditReport\Support\Logger',
        'name' => 'Jsdsx.Xdx.CreditReport',
        'level' => Monolog\Logger::DEBUG,//日志显示级别
        'file' => './log.log',
    ),

);