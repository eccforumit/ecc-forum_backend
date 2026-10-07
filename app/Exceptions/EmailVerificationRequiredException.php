<?php

namespace App\Exceptions;

use Exception;

class EmailVerificationRequiredException extends Exception
{
    public function __construct(public readonly string $email, string $message = 'Vous devez vérifier votre adresse email avant de vous connecter.')
    {
        parent::__construct($message, 403);
    }
}
