<?php

namespace App\DTO;

use App\Validator\FachDoesExist;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUpdateNote
{

    #[Assert\LessThanOrEqual(value: 6, message: "Die note darf nicht grösser als 6 sein", groups: ["create"])]
    public ?float $note = null;


    #[FachDoesExist(groups: ["create"])]
    public ?int $fach = null;


}
