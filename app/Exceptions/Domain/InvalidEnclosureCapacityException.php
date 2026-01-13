<?php

namespace App\Exceptions\Domain;

use DomainException;

class InvalidEnclosureCapacityException extends DomainException
{
    public function __construct(string $message = 'Capacity must be greater than zero') {
        parent::__construct($message);
    }
}
