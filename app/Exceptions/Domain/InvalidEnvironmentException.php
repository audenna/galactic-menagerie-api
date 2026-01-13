<?php

namespace App\Exceptions\Domain;

use DomainException;

class InvalidEnvironmentException extends DomainException
{
    public function __construct(string $message = 'Animal cannot survive in this enclosure environment.') {
        parent::__construct($message);
    }
}
