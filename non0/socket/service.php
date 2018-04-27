<?php
/**
 * Created by PhpStorm.
 * User: Yanlongli
 * Date: 2018/4/17 0017
 * Time: 下午 12:47
 * APPLICATION:
 */
namespace non0\socket;
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
        //关闭阻塞模式
        socket_set_nonblock($this->socket->master);
        //输出监听成功提示
        echo 'server listen' . $this->domain . ':' . $this->port.PHP_EOL;
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
                usleep(100);
//                sleep(2);
            } elseif ($accept_resource > 0) {
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
                        $string = @socket_read($value, 8096);
                        if ($string && $this->socket->isConnect($string)) {
                            //执行握手动作
                            $this->socket->handShake($value, $string);
                            //设定user基本信息
                            $this->socket->users[$this->socket->getKey($string)]['time'] = time();//该用户加入时间
                            $this->socket->users[$this->socket->getKey($string)]['socket'] = $key;//在sockets中的key
                        } elseif ($string) {
                            //解析数据包
                            $data = $this->socket->unCode($string, $key);
                            if ($data) {
//                                echo 'rec:'.$data;
                                $data = json_decode($data,true);
                                if(!self::getDispatcher()->hasListeners($data['type'])){
                                    self::getDispatcher()->dispatch('default',new message($data));
                                }
//                                if(is_array($data) && isset(self::$function->$data['type'])){
                                    $event = self::getDispatcher()->dispatch($data['type'],new message($data,$value));
//                                }
                            }
                        }
                    }
                }
            }
        } while (true);
    }

    /**
     * 这里处理返回数据
     */
    public function collback($data, $socket)
    {
        $arr['rec'] = $data;
        $arr['send'] = 'OK';
        $arr['time'] = time();//当前时间戳
        //对发送信息进行编码处理
        $str = $this->socket->code(json_encode($arr));
        socket_write($this->socket->sockets[$socket], $str, strlen($str));
    }

    /**
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
