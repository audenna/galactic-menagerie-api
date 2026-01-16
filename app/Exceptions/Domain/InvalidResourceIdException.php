<?php

namespace App\Exceptions\Domain;

use DomainException;

class InvalidResourceIdException extends DomainException
{
    public function __construct(string $message = 'The selected resource identifier is invalid.') {
        parent::__construct($message);
    }
}
