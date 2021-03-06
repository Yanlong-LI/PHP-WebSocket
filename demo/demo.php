<?php

use Non0\Socket\message;
use Non0\Socket\service;


/**
 * Created by PhpStorm.
 * User: Yanlongli
 * Date: 2018/4/27/0027
 * Time: 上午 10:06
 * APPLICATION:
 */


include '../vendor/autoload.php';
$socket = new service();

//注册服务
//系统消息
$socket::Register('system', function (message $message) use ($socket) {
    $data = [];
    $data['time'] = time();
    $data['type'] = 'system';
    $data['message'] = htmlspecialchars($message->get("message"));
    $message->send($data, $socket);
});
//群发消息
$socket::Register('group', function (message $message) use ($socket) {
    $data = [];
    $data['time'] = time();
    $data['type'] = 'group';
    $data['message'] = htmlspecialchars($message->get("message"));
    $message->sendALL($data, $socket,false);
});
//用户注册
$socket::register('register', function (message $message) use ($socket) {

    //获取用户信息
    $userInfo = \Non0\Socket\Service\User::getUserInfoByToken($message->get('token'));
    var_dump($userInfo);
    //判断返回资料
    if(!$userInfo || (isset($userInfo['errcode']) && $userInfo['errcode']!=0)){
        //注册失败，不做任何操作
        $message->send($userInfo,$socket);
        \Non0\Socket\Support\Log::info('用户注册失败',['registerInfo'=>$message->getMessage(),['requset'=>$userInfo]]);
        return;
    }
    $user = $userInfo;
    $user['name'] = $userInfo['nickname'];
    $user['reg_time'] = time();
    $socket->socket->users[$message->getKey()] = array_merge($socket->socket->users[$message->getKey()], $user);
    $data['type'] = 'register';
    $data['message'] = '注册成功';
    $data['time'] = time();
    $message->send($data, $socket);
    unset($data);
    $data['type'] = 'group';
    $data['message'] = $user['name'] . '加入聊天室';
    $data['time'] = time();
    $message->sendALL($data, $socket,false);

});
//获取用户列表
$socket::register('getUsers', function (message $message) use ($socket) {
    $data['type'] = 'users';
    $data['data'] = $socket->socket->users;
    $data['time'] = time();
    $message->send($data, $socket);
});

$socket::register('default', function (message $message) use ($socket) {
    echo 'undefined packet:' . $message->get('type');
});
//$socket->socket->sockets
$socket->read();