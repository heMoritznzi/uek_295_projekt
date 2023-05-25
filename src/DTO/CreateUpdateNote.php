<?php

namespace App\DTO;

use App\Validator\FachDoesExist;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUpdateNote
{
    #[Groups(['create', 'Update', 'Delete'])]
    #[Assert\LessThanOrEqual(value: 6, message: 'Die note darf nicht grösser als 6 sein', groups: ['create', 'Update', 'Delete'])]
    #[Assert\NotBlank(message: 'note darf nicht leer sein', groups: ['create', 'Update', 'Delete'])]
    #[Assert\GreaterThanOrEqual(value: 1, message: 'Die note muss mindestens eine 1 sein', groups: ['create', 'Update', 'Delete'])]
    public ?float $note = null;

    #[FachDoesExist(groups: ['create'])]
    #[Assert\NotBlank(message: 'note darf nicht leer sein', groups: ['create'])]
    public ?int $fach = null;
}
