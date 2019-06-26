# RabbitMQ
### 参考 <a href="https://www.rabbitmq.com/tutorials/tutorial-one-php.html">官方教程</a>
**下面的内容都是我在看上面文档所学到的，因为是官方是英文文档，不能保证自己的翻译一定准确，所以会把原文也带上方便之后水平提高有更好的翻译，只要是因为都是原文摘抄的。**

### RabbitMQ 简介
RabbitMQ is a message broker: it accepts and forwards messages(RabbitMQ就是个消息代理：接受和发送消息/数据)

### 术语
- producer（生产者/发送者）：就是发送消息/数据的一方
- queue（队列）：Many producers can send messages that go to one queue, and many consumers can try to receive data from one queue.（生产者producer发送消息到队列queue中，然后消费者consumer监听着，从对应的队列queue中接收数据）A queue is a buffer that stores messages.（队列是存储消息的缓存）
- consumer（消费者/接收者）：A consumer is a program that mostly waits to receive messages（消费者其实等待消息的接收方）
- exchange（交换机）：On one side it receives messages from producers and the other side it pushes them to queues.（它一方面接收消息，一方面把消息发送到队列里）There are a few exchange types available: direct, topic, headers and fanout.（它支持几种类型：direct直连模式, topic主题交模式, header头模式 and fanout扇形模式） 

**总结来说：** 
RabbitMQ提供一套工具给我们在不同的服务间进行通信/传输数据，它有自己的一套机制保证数据的传输，不需要我们理解底层的交互。

**官方推荐的RbbitMQ composer：**

```
Add a composer.json file to your project:

{
    "require": {
        "php-amqplib/php-amqplib": ">=2.6.1"
    }
}

composer.phar install
```

### 案例1

##### 结论先行

- 会自动找空闲进程
- 如果没有设置选项，worker进程处理请求过程中断，会丢失消息
- 如果接收worker没有启动（receive），发送信息也可以成功，且当worker启动之后会接收先前发送的信息并处理
- 生产者的basic_publish里的$routing_key要和queue一样，不然消费者获得不了消息

**代码** 

receive.php  
send.php 
receive2.php  
send2.php 

**情景1:** 
启动receive2.php，不停发送消息send2.php，在receive2没有处理完的时候中断退出，
那些没有处理完的请求都会丢失

**情景2:**
启动2个receive2.php，启动receive2.php，不停发送消息send2.php，rabbitmq会发信息到空闲的那个进程中

**情景3：**
不停发送消息send2.php，启动receive2.php，会接收先前发送的信息并处理


### 案例2

#### 结论先行
- 持久化步骤，消费者：1.queue_declare的durable = true 2.回调方法进行basic_ack 生产者：1.queue_declare的durable = true 2.AMQPMessage设置为 AMQPMessage::DELIVERY_MODE_PERSISTENT
- 如果一个queue之前设置成durable =false，不能把它改为true，会报错
- 下面代码还是会有可能丢失数据，如果想要更高要求的的参考 <a href="https://www.rabbitmq.com/confirms.html">publisher confirms</a>

**代码**

receive2_ack.php  
send2.php 
send2_ack.php

**情景1**
一开始将queue1的durable =false，并且发消息成功了，之后改成true，再发送消息会报错，
RabbitMQ doesn't allow you to redefine an existing queue with different 
parameters and will return an error to any program that tries to do that. 

### 案例3

### 结论

- 不设置exchange也能使用，因为RabbitMq会使用默认的exchange，通过在生产者的basic_publish里的$routing_key进行匹配
- 生产者在queue_declare中不设定队列名，它会自己生成随机名称
- 消费者，queue_declare里$exclusive=true，连接断开之后，会自动删除此队列，同时注意在send里不需要在定义queue_declare了
- fanout模式类似与推送和订阅，接收者订阅相同的exchange，发送者向这个exchange发送信息，那么所有订阅了这个exchange的都会收到消息

**代码**

send_fanout.php
receive_fanout.php

### 案例4

### 结论

- direct模式支持设定过滤条件,发送方basic_publish设定routing_key,接收方queue_bind绑定要接收的routing_key,两者一样就能接收到，不一样的会被抛弃
- fanout模式不支持设定过滤条件,设定了routing_key不会报错，但是不会生效

**代码**

send_direct.php
receive_direct.php

### 案例5

### 结论

- topic的routing_key更加灵活，类似与正则匹配单个和多个单词
- * (star) can substitute for exactly one word.（*表示匹配任意一个单词）
- # (hash) can substitute for zero or more words.（#表示匹配0到多个单词）


**代码**

send_topic.php
receive_topic.php

### 案例6

### 结论

- delivery_mode: Marks a message as persistent (with a value of 2) or transient (1). （表示传送方式是持久的还是非持久的）
- content_type: Used to describe the mime-type of the encoding. For example for the often used JSON encoding it is a good practice to set this property to: application/json.
- reply_to: Commonly used to name a callback queue.（一般用来保存回调的queue名）
- correlation_id: Useful to correlate RPC responses with requests.（通过这个来关联请求和结果）


### 案例7

### 结论

**代码**

send_topic.php
receive_topic.php