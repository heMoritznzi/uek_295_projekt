<?php

namespace App\DTO\Mapper;


use App\DTO\ShowFaecher;

class ShowFaecherMapper extends BaseMapper
{
    public function mapEntityToDTO(object $entity)
    {
        $dto = new ShowFaecher();

        $dto->fach = $entity->getFach();
        foreach($entity->getFaecherNote() as $note) {
            $dto->noten[] = $note->getNote();
        }

        return $dto;
    }
}