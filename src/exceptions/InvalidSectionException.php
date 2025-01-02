<?php

namespace hakuryo\ConfigParser\exceptions;

class InvalidSectionException extends \Exception
{
    public function __construct(string $message = "", int $code = 2, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
