<?php
/**
 * 基本redis手消息队列
 * 用法:
 * use Com\Queue;
 * $queue = Queue::getInstance('msg');
 * 加入队列
 * $queue->push('aaaaaa');
 * $queue->push('bbbbb');
 * 获取队列长度
 * $queue->len();
 * 读取队列
 * $value = $queue->pop()
 * 删除队列
 * $queue->flushQueue();
 */
namespace Com;


class Queue extends \Think\Cache\Driver\Redis
{
    static public $timeout = 1;

    static public $queueName = 'queue';


    /**
     * 操作句柄
     * @var string
     * @access protected
     */
    protected $handler;

    /**
     * 缓存连接参数
     * @var integer
     * @access protected
     */
    protected $options = array();

    /**
     * 取得缓存类实例
     * @static
     * @access public
     * @return mixed
     */
    public static function getInstance($queueName, $options = [])
    {
        if (C('DATA_CACHE_TYPE') != 'Redis') exit('DATA_CACHE_TYPE DO NOT Support Redis');

        //当前队列名称
        self::$queueName = $queueName;

        static $_instance = array();
        if (!isset($_instance[$queueName])) {
            $_instance[$queueName] = new Queue();
        }
        return $_instance[$queueName];

    }

    //设置队列名称
    public static function setQueueName($name)
    {
        self::$queueName = $name;
    }

    /**
     * 添加队列(lpush)
     * @param string $value
     * @return int 队列长度
     */
    public function push($value)
    {
        return $this->lPush(self::$queueName, $value);
    }

    //brpop
    /**
     * 读取队列
     * @return string|nil
     */
    public function pop()
    {
        $result = $this->brPop(self::$queueName, self::$timeout);

        return empty($result) ? $result : $result[1];
    }

    /**
     * 删除一个消息队列
     */
    public function flushQueue()
    {
        $this->delete(self::$queueName);
    }

    /**
     * 返回队列长茺
     * @return int
     */
    public function len()
    {
        return $this->LLEN(self::$queueName);
    }
}

?>
