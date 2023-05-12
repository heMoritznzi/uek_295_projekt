<?php

namespace App\DTO;



class CreateUser
{
    public ?string $username = null;


    public ?string $passwort = null;


    public ?bool $is_admin = false;
}