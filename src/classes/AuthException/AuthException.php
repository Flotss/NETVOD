<?php

namespace iutnc\NetVOD\AuthException;

use Exception;

/**
 * Class AuthException
 * Cette classe permet de gérer les exceptions liées à l'authentification
 */
class AuthException extends Exception
{
    /**
     * Constructeur de la classe AuthException
     * @param string $message message de l'exception
     * @param int $code code de l'exception
     * @param Throwable|null $previous exception précédente
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}


?>