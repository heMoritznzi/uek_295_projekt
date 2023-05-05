<?php

namespace App\DTO;


use Symfony\Component\Validator\Constraints as Assert;

class CreateUpdateFaecher
{

    #[Assert\NotBlank(message: "Fach darf nicht leer sein", groups: ["create"])]
    public ?string $fach = null;

}