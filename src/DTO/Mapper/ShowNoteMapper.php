<?php

namespace App\DTO\Mapper;



use App\DTO\ShowNote;

class ShowNoteMapper extends BaseMapper
{
    public function mapEntityToDTO(object $entity)
    {
        $dto = new ShowNote();

        $dto->note = $entity->getNote();

        return $dto;
    }
}