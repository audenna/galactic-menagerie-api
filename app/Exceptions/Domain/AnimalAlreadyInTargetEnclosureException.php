<?php

namespace App\Exceptions\Domain;

use DomainException;

class AnimalAlreadyInTargetEnclosureException extends DomainException
{
    public function __construct(string $message = 'Animal is already in the target enclosure.') {
        parent::__construct($message);
    }
}
