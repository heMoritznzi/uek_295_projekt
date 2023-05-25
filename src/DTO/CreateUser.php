<?php

namespace App\DTO;

use App\Validator\FachDoesExist;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


class CreateUser
{
    #[Groups(["create"])]
    #[Assert\NotBlank(message: "der username darf nicht leer sein", groups: ["create"])]
    public ?string $username = null;


    #[Assert\NotBlank(message: "der username darf nicht leer sein", groups: ["create"])]
    public ?string $passwort = null;


    public ?bool $is_admin = false;
}