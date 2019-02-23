<?php


namespace App\Exception;


use Throwable;

class EmptyBodyException extends \Exception
{
    public function __construct(int $code = 0, Throwable $previous = null)
    {
        parent::__construct('The body of the POS/PUT method cannot be empty!', $code, $previous);
    }
}
