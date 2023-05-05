<?php

namespace App\DTO;


use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUpdateFaecher
{

    #[Groups(["create"])]
    #[Assert\NotBlank(message: "Fach darf nicht leer sein", groups: ["create"])]
    public ?string $fach = null;

}