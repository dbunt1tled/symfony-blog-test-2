<?php

namespace App\Entity;

interface AggregateRoot
{
    public function __toArray(): array;
}
