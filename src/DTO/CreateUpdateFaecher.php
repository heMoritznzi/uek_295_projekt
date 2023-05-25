<?php

namespace App\DTO;

use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUpdateFaecher
{
    #[Groups(['create', 'Update', 'Delete'])]
    #[Assert\NotBlank(message: 'Fach darf nicht leer sein', groups: ['create', 'Update', 'Delete'])]
    public ?string $fach = null;
}
