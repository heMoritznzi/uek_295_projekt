<?php

namespace App\DTO\Mapper;



use App\DTO\ShowFaecher;

class ShowNoteMapper extends BaseMapper
{
    public function mapEntityToDTO(object $entity)
    {
        $dto = new ShowFaecher();
        $dto->fach = $entity->getNoteFach()->getFach();

        return $dto;
    }
}