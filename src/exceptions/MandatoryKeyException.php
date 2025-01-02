<?php

namespace hakuryo\ConfigParser\exceptions;

class MandatoryKeyException extends \Exception
{
    public function __construct(string $message = "", int $code = 5, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
