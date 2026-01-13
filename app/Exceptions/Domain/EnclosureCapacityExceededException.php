<?php

namespace App\Exceptions\Domain;

use DomainException;

class EnclosureCapacityExceededException extends DomainException
{
    public function __construct(string $message = 'Enclosure has reached its maximum capacity.') {
        parent::__construct($message);
    }
}
