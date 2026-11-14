<?php

namespace Universal Reading\Controller\Import;

use Craue\ConfigBundle\Util\Config;
use OldSound\RabbitMqBundle\RabbitMq\Producer as RabbitMqProducer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Universal Reading\Import\UniversalReadingV1Import;
use Universal Reading\Redis\Producer as RedisProducer;

class UniversalReadingV1Controller extends Universal ReadingController
{
    public function __construct(
        private readonly UniversalReadingV1Import $universalreadingImport,
        private readonly Config $craueConfig,
        private readonly RabbitMqProducer $rabbitMqProducer,
        private readonly RedisProducer $redisProducer,
        array $allowMimetypes,
        string $resourceDir,
    ) {
        parent::__construct($allowMimetypes, $resourceDir);
    }

    #[Route(path: '/import/universalreading-v1', name: 'import_universalreading_v1', methods: ['GET', 'POST'])]
    #[IsGranted('IMPORT_ENTRIES')]
    public function indexAction(Request $request, TranslatorInterface $translator)
    {
        return parent::indexAction($request, $translator);
    }

    protected function getImportService()
    {
        if ($this->craueConfig->get('import_with_rabbitmq')) {
            $this->universalreadingImport->setProducer($this->rabbitMqProducer);
        } elseif ($this->craueConfig->get('import_with_redis')) {
            $this->universalreadingImport->setProducer($this->redisProducer);
        }

        return $this->universalreadingImport;
    }

    protected function getImportTemplate()
    {
        return 'Import/UniversalReadingV1/index.html.twig';
    }
}
