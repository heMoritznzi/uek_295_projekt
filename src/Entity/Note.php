<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $Note = null;

    #[ORM\ManyToOne(inversedBy: 'faecher_note')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Faecher $Note_Fach = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getNote(): ?float
    {
        return $this->Note;
    }

    public function setNote(float $Note): self
    {
        $this->Note = $Note;

        return $this;
    }

    public function getNoteFach(): ?Faecher
    {
        return $this->Note_Fach;
    }

    public function setNoteFach(?Faecher $Note_Fach): self
    {
        $this->Note_Fach = $Note_Fach;

        return $this;
    }







}
