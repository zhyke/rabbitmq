# RabbitMQ
### 参考 <a href="https://www.rabbitmq.com/tutorials/tutorial-one-php.html">官方教程</a>
**下面的内容都是我在看上面文档所学到的，因为是官方是英文文档，不能保证自己的翻译一定准确，所以会把原文也带上方便之后水平提高有更好的翻译，只要是因为都是原文摘抄的。**

RabbitMQ is a message broker: it accepts and forwards messages(RabbitMQ就是个消息代理：接受和发送消息/数据)

### 术语
- producer（生产者/发送者）：就是发送消息/数据的一方
- queue（队列）：Many producers can send messages that go to one queue, and many consumers can try to receive data from one queue.（生产者producer发送消息到队列queue中，然后消费者consumer监听着，然后从对应的队列queue中接收数据）
- consumer（消费者/接收者）：A consumer is a program that mostly waits to receive messages（消费者其实等待消息的接收方）

**总结来说：**RabbitMQ提供一套工具给我们在不同的服务间进行通信/传输数据，它有自己的一套机制保证数据的传输，不需要我们理解底层的交互。

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