<?php


namespace App\Entity;


interface PublishedDateEntityInterface
{
    public function setPublished(\DateTimeImmutable $published): PublishedDateEntityInterface;
}