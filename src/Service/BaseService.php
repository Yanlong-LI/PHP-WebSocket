<?php
/**
 * Copyright (c) 2018. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

/**
 * Created by PhpStorm.
 * User: Yanlongli
 * Date: 2018/6/25/0025
 * Time: 上午 8:51
 * APPLICATION:
 */

namespace Non0\Socket\Service;


use Non0\Socket\service;
use Non0\Socket\SocketException;
use Non0\Socket\Support\Curl;
use Non0\Socket\Support\Json;
use Non0\Socket\Support\Log;

class BaseService
{
    /**
     * @param null $data
     * @param string $method
     * @param string $extendUrl
     * @return mixed
     * @throws SocketException
     */
    protected static function request($data = null, $method = '',$extendUrl='')
    {
        //如传递了请求方式，怎使用，否则用配置方式
        $method = $method ? $method : Service::getConfig('method');
        //如配置请求方式无效，则根据data是否有数据选定请求方式，无get，有post
        if (!$method) {
            $method = is_null($data) ? 'get' : 'post';
        }
        try {
            $result = Json::parseOrFail(Curl::execute(Service::getConfig('api').$extendUrl, $method, $data));

            Log::debug('url：' .Service::getConfig('api'), [$method => $data, 'result' => $result]);

            return $result;
        } catch (SocketException $ex) {

            throw $ex;
        }
    }

}