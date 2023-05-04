<?php

namespace App\Entity;

use App\Repository\FaecherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FaecherRepository::class)]
class Faecher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $Fach = null;

    #[ORM\OneToMany(mappedBy: 'Note_Fach', targetEntity: Note::class, orphanRemoval: true)]
    private Collection $faecher_note;

    public function __construct()
    {
        $this->faecher_note = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFach(): ?string
    {
        return $this->Fach;
    }

    public function setFach(string $Fach): self
    {
        $this->Fach = $Fach;

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getFaecherNote(): Collection
    {
        return $this->faecher_note;
    }

    public function addFaecherNote(Note $faecherNote): self
    {
        if (!$this->faecher_note->contains($faecherNote)) {
            $this->faecher_note->add($faecherNote);
            $faecherNote->setNoteFach($this);
        }

        return $this;
    }

    public function removeFaecherNote(Note $faecherNote): self
    {
        if ($this->faecher_note->removeElement($faecherNote)) {
            // set the owning side to null (unless already changed)
            if ($faecherNote->getNoteFach() === $this) {
                $faecherNote->setNoteFach(null);
            }
        }

        return $this;
    }
}
