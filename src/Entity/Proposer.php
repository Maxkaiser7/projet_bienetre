<?php

namespace App\Entity;

use App\Repository\ProposerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProposerRepository::class)]
class Proposer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'Prestataire', inversedBy: 'proposer')]
    #[ORM\JoinColumn(name: 'prestataire_id', referencedColumnName: 'id', nullable: false)]
    private ?Prestataire $prestataire = null;

    #[ORM\ManyToOne(targetEntity: 'CategorieDeServices', inversedBy: 'proposer')]
    #[ORM\JoinColumn(name: 'categorie_de_services_id', referencedColumnName: 'id',  nullable: false)]
    private ?CategorieDeServices $categorieDeServices = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrestataire(): ?Prestataire
    {
        return $this->prestataire;
    }

    public function setPrestataire(?Prestataire $prestataire): self
    {
        $this->prestataire = $prestataire;

        return $this;
    }

    public function getCategorieDeServices(): ?CategorieDeServices
    {
        return $this->categorieDeServices;
    }

    public function setCategorieDeServices(?CategorieDeServices $categorieDeServices): self
    {
        $this->categorieDeServices = $categorieDeServices;

        return $this;
    }
}
