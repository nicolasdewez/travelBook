<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelDictionary;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\HttpKernel\KernelInterface;

class QueuingContext implements Context
{
    use KernelDictionary;

    /** @var AMQPMessage[] */
    private $queuedMessages;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->setKernel($kernel);
        $this->queuedMessages = [];
    }

    /**
     * @BeforeScenario @reset-queue
     */
    public function cleanQueues()
    {
        $queues = ['registration'];

        foreach ($queues as $queue) {
            shell_exec(sprintf('php bin/console rabbitmq:purge %s --no-interaction --env=test', $queue));
        }
    }

    /**
     * @param string $producerName
     *
     * @When /^the queue associated to "([^"]*)" producer is empty$/
     */
    public function theQueueAssociatedToProducerIsEmpty(string $producerName)
    {
        $queueName = $this->getQueueName($producerName);

        $channel = $this->getChannel($producerName);
        $channel->queue_declare($queueName, false, true, false, false);
        $channel->queue_purge($queueName);

        if ($channel->basic_get($queueName)) {
            throw new LogicException(sprintf('The queue %s does not seem to be empty.', $queueName));
        }
    }

    /**
     * @param string    $producerName
     * @param TableNode $tableNode
     *
     * @When /^the queue associated to "([^"]*)" producer has messages below:$/
     */
    public function theQueueAssociatedToProducerHasMessagesBelow(string $producerName, TableNode $tableNode)
    {
        $expectedMessages = $this->getExpectedMessages($tableNode);
        $queuedMessages = $this->getQueuedMessages($producerName);

        if ($expectedMessages != $queuedMessages) {
            throw new LogicException(sprintf(
                'Message mismatch. Queue contains:%s%s',
                PHP_EOL,
                json_encode($queuedMessages)
            ));
        }
    }

    /**
     * @param string    $producerName
     * @param TableNode $tableNode
     *
     * @When /^the queue associated to "([^"]*)" producer has messages to re-publish below:$/
     */
    public function theQueueAssociatedToProducerHasMessagesToRePublishBelow(string $producerName, TableNode $tableNode)
    {
        $this->theQueueAssociatedToProducerHasMessagesBelow($producerName, $tableNode);
        $this->rePublishMessagesInProducer($producerName);
    }

    /**
     * @param string $producerName
     */
    private function rePublishMessagesInProducer(string $producerName)
    {
        $channel = $this->getChannel($producerName);

        foreach ($this->queuedMessages as $message) {
            $channel->basic_publish($message, $this->getExchangeName($producerName));
        }
    }

    /**
     * @param TableNode $tableNode
     *
     * @return array
     */
    private function getExpectedMessages(TableNode $tableNode)
    {
        $expectedMessages = [];
        foreach ($tableNode->getRowsHash() as $message) {
            $expectedMessages[] = $this->replaceDynamicValues($message);
        }

        return $expectedMessages;
    }

    /**
     * @param string $producerName
     *
     * @return array
     */
    private function getQueuedMessages($producerName)
    {
        $channel = $this->getChannel($producerName);

        $messages = [];
        do {
            /** @var AMQPMessage $message */
            $message = $channel->basic_get($this->getQueueName($producerName));
            if (!$message instanceof AMQPMessage) {
                break;
            }

            $this->queuedMessages[] = $message;
            $messages[] = $this->replaceDynamicValues($message->getBody());

            if ($message->get('message_count') == 0) {
                break;
            }
        } while (true);

        return $messages;
    }

    /**
     * @param string $producerName
     *
     * @return AMQPChannel
     */
    private function getChannel($producerName)
    {
        $container = $this->getContainer();

        $producerService = sprintf('old_sound_rabbit_mq.%s_producer', $producerName);
        $producer = $container->get($producerService);

        return $producer->getChannel();
    }

    /**
     * @param string $producerName
     *
     * @return string
     */
    private function getQueueName($producerName)
    {
        return $producerName;
    }

    /**
     * @param string $producerName
     *
     * @return string
     */
    private function getExchangeName($producerName)
    {
        return $producerName;
    }

    /**
     * @param string $data
     *
     * @return string
     */
    private function replaceDynamicValues($data)
    {
        return preg_replace(
            [
                '/\b(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})\+(\d{2}):(\d{2})\b/',
                '#:\d{10}(,|})#',
            ],
            [
                'ISO8601_TIMESTAMP',
                ':"UNIX_TIMESTAMP"$1',
            ],
            $data
        );
    }
}
