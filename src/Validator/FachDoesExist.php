<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;


#[\Attribute]
class FachDoesExist extends Constraint
{
    public string $message = "Das Fach mit der ID {{ fach }} existiert nicht";
}