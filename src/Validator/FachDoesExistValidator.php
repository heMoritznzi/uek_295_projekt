<?php

namespace App\Validator;

use App\Repository\NoteRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FachDoesExistValidator extends ConstraintValidator
{
    public function __construct(private NoteRepository $noteRepository)
    {

    }

    public function validate($fach, Constraint $constraint) : void
    {
        $faecher = $this->noteRepository->find($fach);

        if (!$faecher) {
           $this->context
               ->buildViolation($constraint->message)
               ->setParameter("{{ fach }}", $fach)
               ->addViolation();

        }
    }
}