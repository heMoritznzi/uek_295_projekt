<?php

namespace App\Validator;

use App\Repository\FaecherRepository;
use App\Repository\NoteRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FachDoesExistValidator extends ConstraintValidator
{
    public function __construct(private FaecherRepository $faecherRepository)
    {

    }


    public function validate($fach, Constraint $constraint) : void
    {
        $faecher = $this->faecherRepository->find($fach);

        $message = $constraint->__get("message");

        if (!$faecher) {
           $this->context
               ->buildViolation($message)
               ->setParameter("{{ fach }}", $fach)
               ->addViolation();

        }
    }
}