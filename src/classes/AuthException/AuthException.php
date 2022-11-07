<?php

namespace iutnc\NetVOD\AuthException;

use Exception;

class AuthException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}


?>