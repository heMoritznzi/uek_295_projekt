<?php

namespace App\DTO\Mapper;


use App\DTO\ShowNote;

class ShowFaecherMapper extends BaseMapper
{
    public function mapEntityToDTO(object $entity)
    {
        $dto = new ShowNote();
        $dto->note = $entity->getnote();

        return $dto;
    }
}