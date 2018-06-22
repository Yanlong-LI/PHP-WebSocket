<?php
/**
 * Created by PhpStorm.
 * User: Yanlongli
 * Date: 2018/6/11/0011
 * Time: 下午 12:43
 * APPLICATION:
 */

namespace Non0\Socket\Support;

class redis
{
    protected $object;

    function __construct()
    {
        $this->object = new \Redis();
    }
}