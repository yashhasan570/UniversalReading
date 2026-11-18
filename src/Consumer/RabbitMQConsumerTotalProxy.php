<?php

namespace Universal Reading\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\Consumer;

/**
 * A proxy class only used to count messages in a queue while lazy loading RabbitMQ services.
 * Only used in ImportController.
 */
class RabbitMQConsumerTotalProxy
{
    public function __construct(
        private readonly Consumer $readabilityConsumer,
        private readonly Consumer $universalreadingV1Consumer,
        private readonly Consumer $universalreadingV2Consumer,
        private readonly Consumer $firefoxConsumer,
        private readonly Consumer $chromeConsumer,
        private readonly Consumer $instapaperConsumer,
        private readonly Consumer $pinboardConsumer,
        private readonly Consumer $deliciousConsumer,
        private readonly Consumer $elcuratorConsumer,
        private readonly Consumer $shaarliConsumer,
        private readonly Consumer $pocketHtmlConsumer,
        private readonly Consumer $pocketCsvConsumer,
        private readonly Consumer $omnivoreConsumer,
        private readonly Consumer $urlListConsumer,
    ) {
    }

    /**
     * Count message in RabbitMQ queue.
     *
     * It get one message without acking it (so it'll stay in the queue)
     * which will include the total of *other* messages in the queue.
     * Adding one to that messages will result in the full total message.
     *
     * @param string $importService The import service related: readability, universalreading_v1 or universalreading_v2
     */
    public function getTotalMessage(string $importService): int
    {
        switch ($importService) {
            case 'readability':
                $consumer = $this->readabilityConsumer;
                break;
            case 'universalreading_v1':
                $consumer = $this->universalreadingV1Consumer;
                break;
            case 'universalreading_v2':
                $consumer = $this->universalreadingV2Consumer;
                break;
            case 'firefox':
                $consumer = $this->firefoxConsumer;
                break;
            case 'chrome':
                $consumer = $this->chromeConsumer;
                break;
            case 'instapaper':
                $consumer = $this->instapaperConsumer;
                break;
            case 'pinboard':
                $consumer = $this->pinboardConsumer;
                break;
            case 'delicious':
                $consumer = $this->deliciousConsumer;
                break;
            case 'elcurator':
                $consumer = $this->elcuratorConsumer;
                break;
            case 'shaarli':
                $consumer = $this->shaarliConsumer;
                break;
            case 'pocket_html':
                $consumer = $this->pocketHtmlConsumer;
                break;
            case 'pocket_csv':
                $consumer = $this->pocketCsvConsumer;
                break;
            case 'omnivore':
                $consumer = $this->omnivoreConsumer;
                break;
            case 'url_list':
                $consumer = $this->urlListConsumer;
                break;
            default:
                return 0;
        }

        $message = $consumer->getChannel()->basic_get('universalreading.import.' . $importService);

        if (null === $message) {
            return 0;
        }

        return $message->getMessageCount() + 1;
    }
}
