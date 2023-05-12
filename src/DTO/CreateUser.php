<?php

namespace App\DTO;

use phpDocumentor\Reflection\Types\Boolean;

class CreateUser
{
    public ?string $username = null;


    public ?string $passwort = null;


    public ?Boolean $is_admin = false;
}