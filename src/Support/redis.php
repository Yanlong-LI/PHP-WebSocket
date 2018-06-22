<?php
/**
 * Created by PhpStorm.
 * User: Yanlongli
 * Date: 2018/6/11/0011
 * Time: 下午 12:43
 * APPLICATION:
 */

namespace non0\socket\support;

class redis
{
    protected $object;

    function __construct()
    {
        $this->object = new \Redis();
    }
}