<?php
/**
 * Created by PhpStorm.
 * User: Yanlongli
 * Date: 2018/4/17 0017
 * Time: 下午 12:47
 * APPLICATION:
 */

namespace non0\socket;

use non0\socket\support\read;
use Symfony\Component\EventDispatcher\EventDispatcher;

class service
{
    public $socket;
    private $domain;
    private $port;


    /**
     * @var EventDispatcher
     */
    protected static $dispatcher = null;

    /**
     * service constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->domain = $config['domain'];
        $this->port = $config['port'];
        $this->socket = new socket();
        $this->socket->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $this->open();
    }

    /**
     * @param $domain
     * @param $port
     */
    public function open()
    {

        //绑定ip端口号
        if (($ret = socket_bind($this->socket->master, $this->domain, $this->port)) < 0) {
            echo 'server bind fail:' . socket_strerror($ret);
            exit();
        }

        //开始监听
        if (($ret = socket_listen($this->socket->master, 4)) < 0) {
            echo 'server listen fail:' . socket_strerror($ret);
            exit();
        }

        //关闭监听新客户端阻塞模式
        socket_set_nonblock($this->socket->master);

        //输出监听成功提示
        echo 'server listen' . $this->domain . ':' . $this->port . PHP_EOL;
    }

    /**
     * 开始监听数据包的死循环
     */
    public function read()
    {
        do {
            //监听是否有新客户端连接
            $accept_resource = socket_accept($this->socket->master);
            if ($accept_resource === false) {
                usleep(100000);//100微秒
                echo 'sleep 1s'.PHP_EOL;
//                sleep(2);
            } elseif ($accept_resource > 0) {
                //关闭每个socket资源的阻塞模式
                socket_set_nonblock($accept_resource);
                $this->socket->sockets[] = $accept_resource;
            } else {
                echo "error: " . socket_strerror($accept_resource);
                die;
            }

            //判断数组是否为空
            if (!empty($this->socket->sockets)) {

                foreach ($this->socket->sockets as $key => $value) {
                    //判断该资源对象是否为Socket，close后为unknown
                    if (get_resource_type($value) == 'Socket') {
                        //读取流数据
                        $string = @socket_read($value, 65535);
                        if ($string && $this->socket->isConnect($string)) {
                            //执行握手动作
                            $this->socket->handShake($value, $string);
                            //设定user基本信息
                            $this->socket->users[$key]['time'] = time();//该用户加入时间
                            $this->socket->users[$key]['key'] = $this->socket->getKey($string);//在sockets中的key
                        } elseif ($string) {
                            self::handle($string,$key);
                        }
                    }
                }
            }
        } while (true);
    }

    public function handle($string,$key,$bin = true)
    {
        if(strlen($string)<1){
            return;
        }
        $read = new read($string);
        if(!$bin){
            $read->data = $string;
        }
        //解析第一bit
        $head = $read->readByte();
        //表示最后一帧
        if (hexdec($head) < 128) {
            echo 'undefined head ' . $head . PHP_EOL;
            echo 'undefined pack ' . $read->data . PHP_EOL;
            return;
        }
        if(hexdec($head)==136){
            socket_close($this->socket->sockets[$key]);
            echo 'user out：'.$this->socket->users[$key]['name'].PHP_EOL;
            unset($this->socket->sockets[$key]);
            unset($this->socket->users[$key]);
            return;
        }

        /**
         * 0x0 表示附加数据帧
         * 0x1 表示文本数据帧
         * 0x2 表示二进制数据帧
         * 0x3-7 暂时无定义，为以后的非控制帧保留
         * 0x8 表示连接关闭{"type":"register","time":1529562040214,"name":"王大爷"}
         * 0x9 表示ping
         * 0xA 表示pong
         * 0xB-F 暂时无定义，为以后的控制帧保留
         */
        //掩码开关和数据长度
        $mp = $read->readByte();
        if (hexdec($mp) > 128 && hexdec($mp)<254) {
            $len = hexdec($mp) - 128;
            $read->maskingKey = $read->readBytes(4);
        }elseif(hexdec($mp)==254){
            $len = hexdec($read->readShort());
            echo $len.PHP_EOL;
            if($len<=0)die();
            $read->maskingKey = $read->readBytes(4);
        }elseif(hexdec($mp)==255){
            $len = hexdec($read->readInt());
            if($len<=0){
                $read->index = $read->index-8;
                echo $read->readInt();
                echo $len.PHP_EOL;
                die();
            }
            $read->maskingKey = $read->readBytes(4);
        } else {
            $len = hexdec($mp);
        }
        $maskHexData = $read->readString($len * 2);
        $Hexdata = $read->masking($maskHexData);
        $jsonStringData =  $read->hexToUtf8($Hexdata);
        $data = json_decode($jsonStringData,true);
        if (is_array($data) && isset($data['type']) && !self::getDispatcher()->hasListeners($data['type'])) {
            self::getDispatcher()->dispatch('default', new message($data, $key));
        } elseif (is_array($data) && isset($data['type'])) {
            self::getDispatcher()->dispatch($data['type'], new message($data, $key));
        } else {
            echo 'unReadMSG'.$read->data.PHP_EOL;
        }
        //处理粘包
        $data = substr($read->data,$read->index);
        self::handle($data,$key,false);
    }

    /**
     * 注册方法
     * @param $type
     * @param $callback
     * @param int $priority
     */
    public static function register($type, $callback, $priority = 0)
    {
        self::getDispatcher()->addListener($type, $callback, $priority);
    }

    /**
     * @return EventDispatcher
     */
    public static function getDispatcher()
    {
        if (self::$dispatcher == null) {
            self::$dispatcher = new EventDispatcher();
        }
        return self::$dispatcher;
    }
}
