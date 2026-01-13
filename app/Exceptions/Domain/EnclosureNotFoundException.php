<?php

namespace App\Exceptions\Domain;

use DomainException;

class EnclosureNotFoundException extends DomainException
{
    public function __construct(string $message = 'Enclosure not found.') {
        parent::__construct($message);
    }
}
