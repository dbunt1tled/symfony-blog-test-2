<?php


namespace App\Events;


use App\Entity\AggregateRoot;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractEvent extends Event
{
    /** @var object */
    private $entity;

    public function __construct(object $entity = null)
    {
        $this->entity = $entity;
    }

    /**
     * @return null|object
     */
    final public function getEntity(): ?AggregateRoot
    {
        return $this->entity;
    }

}
