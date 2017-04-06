<?php
/**
 * 基本redis实现的环形消息队列
 * 用法:
 * use Com\RingQueue;
 * $queue = RingQueue::getInstance('msg');
 *
 * 加入队列
 * $queue->push('aaaaaa');
 * $queue->push('bbbbb');
 * 读取队列
 * $value = $queue->pop()
 *
 * 删除队列
 * $queue->flushQueue();
 */
namespace Com;


class RingQueue extends \Think\Cache\Driver\Redis
{
    static public $timeout = 1;
    static public $queueName = 'ring_queue';

    /**
     * 取得缓存类实例
     * @static
     * @access public
     * @return mixed
     */
    public static function getInstance($queueName)
    {
        if (C('DATA_CACHE_TYPE') != 'Redis') exit('DATA_CACHE_TYPE DO NOT Support Redis');

        //当前队列名称
        self::$queueName = 'ring_' . $queueName;

        static $_instance = array();
        if (!isset($_instance[self::$queueName])) {
            $_instance[self::$queueName] = new RingQueue();
        }

        return $_instance[self::$queueName];
    }

    //设置队列名称
    public static function setQueueName($name)
    {
        self::$queueName = 'ring_' . $name;
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

    /**
     * 读取队列,将读取到的值放在队列最左侧
     * @return string|nil
     */
    public function pop()
    {
        $result = $this->brPop(self::$queueName, self::$timeout);

        if (empty($result)) {
            return $result;
        } else {
            //将取出来的值添加到最队列最左侧
            $this->lPush(self::$queueName, $result[1]);

             return $result[1];
        }
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
