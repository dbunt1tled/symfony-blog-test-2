<?php


namespace App\EventSubscriber;


use App\Entity\AggregateRoot;
use Psr\Log\LoggerInterface;

abstract class AbstractSubscriber
{
    public const UNKNOWN_ACTION = 'unknown_action';

    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    final public function logEntity(string $action = self::UNKNOWN_ACTION, AggregateRoot $entity): void
    {
        $this->logger->info($action, [
            'entity' => $entity->__toArray(),
        ]);
    }
}
