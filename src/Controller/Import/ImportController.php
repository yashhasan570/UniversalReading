<?php

namespace Universal Reading\Controller\Import;

use Craue\ConfigBundle\Util\Config;
use Predis\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Universal Reading\Consumer\RabbitMQConsumerTotalProxy;
use Universal Reading\Controller\AbstractController;
use Universal Reading\Import\ImportChain;

class ImportController extends AbstractController
{
    public function __construct(
        private readonly RabbitMQConsumerTotalProxy $rabbitMQConsumerTotalProxy,
        private readonly Client $redisClient,
    ) {
    }

    #[Route(path: '/import/', name: 'import', methods: ['GET'])]
    #[IsGranted('IMPORT_ENTRIES')]
    public function importAction(ImportChain $importChain)
    {
        return $this->render('Import/index.html.twig', [
            'imports' => $importChain->getAll(),
        ]);
    }

    /**
     * Display how many messages are queue (both in Redis and RabbitMQ).
     * Only for admins.
     */
    public function checkQueueAction(AuthorizationCheckerInterface $authorizationChecker, Config $craueConfig)
    {
        $nbRedisMessages = null;
        $nbRabbitMessages = null;
        $redisNotInstalled = false;
        $rabbitNotInstalled = false;

        if (!$authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->render('Import/check_queue.html.twig');
        }

        if ($craueConfig->get('import_with_rabbitmq')) {
            // in case rabbit is activated but not installed
            try {
                $nbRabbitMessages = $this->rabbitMQConsumerTotalProxy->getTotalMessage('readability')
                    + $this->rabbitMQConsumerTotalProxy->getTotalMessage('universalreading_v1')
                    + $this->rabbitMQConsumerTotalProxy->getTotalMessage('universalreading_v2')
                    + $this->rabbitMQConsumerTotalProxy->getTotalMessage('firefox')
                    + $this->rabbitMQConsumerTotalProxy->getTotalMessage('chrome')
                    + $this->rabbitMQConsumerTotalProxy->getTotalMessage('instapaper')
                    + $this->rabbitMQConsumerTotalProxy->getTotalMessage('pinboard')
                    + $this->rabbitMQConsumerTotalProxy->getTotalMessage('delicious')
                    + $this->rabbitMQConsumerTotalProxy->getTotalMessage('elcurator')
                    + $this->rabbitMQConsumerTotalProxy->getTotalMessage('shaarli')
                    + $this->rabbitMQConsumerTotalProxy->getTotalMessage('pocket_html')
                    + $this->rabbitMQConsumerTotalProxy->getTotalMessage('pocket_csv')
                    + $this->rabbitMQConsumerTotalProxy->getTotalMessage('omnivore')
                    + $this->rabbitMQConsumerTotalProxy->getTotalMessage('url_list')
                ;
            } catch (\Exception) {
                $rabbitNotInstalled = true;
            }
        } elseif ($craueConfig->get('import_with_redis')) {
            try {
                $nbRedisMessages = $this->redisClient->llen('universalreading.import.readability')
                    + $this->redisClient->llen('universalreading.import.universalreading_v1')
                    + $this->redisClient->llen('universalreading.import.universalreading_v2')
                    + $this->redisClient->llen('universalreading.import.firefox')
                    + $this->redisClient->llen('universalreading.import.chrome')
                    + $this->redisClient->llen('universalreading.import.instapaper')
                    + $this->redisClient->llen('universalreading.import.pinboard')
                    + $this->redisClient->llen('universalreading.import.delicious')
                    + $this->redisClient->llen('universalreading.import.elcurator')
                    + $this->redisClient->llen('universalreading.import.shaarli')
                    + $this->redisClient->llen('universalreading.import.pocket_html')
                    + $this->redisClient->llen('universalreading.import.pocket_csv')
                    + $this->redisClient->llen('universalreading.import.omnivore')
                    + $this->redisClient->llen('universalreading.import.url_list')
                ;
            } catch (\Exception) {
                $redisNotInstalled = true;
            }
        }

        return $this->render('Import/check_queue.html.twig', [
            'nbRedisMessages' => $nbRedisMessages,
            'nbRabbitMessages' => $nbRabbitMessages,
            'redisNotInstalled' => $redisNotInstalled,
            'rabbitNotInstalled' => $rabbitNotInstalled,
        ]);
    }
}
