<?php

/**
 * Created by PhpStorm.
 * User: Yanlongli
 * Date: 2018/6/22/0022
 * Time: 下午 5:14
 * APPLICATION:
 */

namespace Non0\Socket\Service;


use Non0\Socket\Support\Curl;

class User extends BaseService
{
    public static $token;

    /**
     * @param $token
     * @return mixed
     * @throws \Non0\Socket\SocketException
     */
    static public function getUserInfoByToken($token)
    {
        $data = [];

        return self::request(['token'=>$token],'','/user/info');
    }
}