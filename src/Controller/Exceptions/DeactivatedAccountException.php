<?php

namespace App\Controller\Exceptions;

use Throwable;

class DeactivatedAccountException extends \Exception
{
    public function __construct($additionalMessage = "", $code = 0, Throwable $previous = null){
        parent::__construct('Votre compte est désactivé, vous n\'avez pas accès à cette ressource. ' . $additionalMessage, $code, $previous);
    }
}