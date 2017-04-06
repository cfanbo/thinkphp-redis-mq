# thinkphp-redis-mq
基于THINKPHP3.2.3，将Queue.class.php和RingQueue.class.php文件放在 \ThinkPHP\Library\Com\ 目录里即可。  
使用方法可参数类库文件上方的说明信息！

 Queue.class.php是普通的消息队列  
 RingQueue.class.php 是环形队列,从队列的右侧读取出一个值，再将此值添加到队列的左侧  
 
 目前暂不支持对于队列中元素的删除功能
