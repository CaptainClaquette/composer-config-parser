<?php

namespace hakuryo\ConfigParser\exceptions;

class FileNotFoundException extends \Exception
{
    public function __construct(string $message = "", int $code = 1, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
