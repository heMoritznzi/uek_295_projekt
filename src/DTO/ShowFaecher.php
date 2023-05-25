<?php

namespace App\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;

class ShowFaecher
{
    public ?string $fach = null;

    #[Property(
        'noten',
        type: 'array',
        items: new Items(
            ref: new Model(
                type: ShowNote::class
            )
        )
    )]
    public $noten = [];
}
