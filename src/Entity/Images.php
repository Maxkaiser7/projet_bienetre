<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $ordre = 0;

    #[ORM\Column(length: 255)]
    private ?string $Image = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Prestataire $prestataire = null;

    #[ORM\Column(nullable: true)]
    private ?int $prestataireId = null;


    #[ORM\OneToOne(inversedBy: 'images', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?CategorieDeServices $categorieDeServices = null;

    #[ORM\Column(nullable: true)]
    private ?int $categorieDeServicesId = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Internaute $internaute = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getImage()
    {
        return $this->Image;
    }

    public function setImage($Image): self
    {
        $this->Image = $Image;

        return $this;
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

    public function getInternaute(): ?Internaute
    {
        return $this->internaute;
    }

    public function setInternaute(?Internaute $internaute): self
    {
        $this->internaute = $internaute;

        return $this;
    }

    public function getPrestataireId(): ?int
    {
        return $this->prestataireId;
    }

    public function setPrestataireId(?int $prestataireId): self
    {
        $this->prestataireId = $prestataireId;

        return $this;
    }

    public function getCategorieDeServicesId(): ?int
    {
        return $this->categorieDeServicesId;
    }

    public function setCategorieDeServicesId(?int $categorieDeServicesId): self
    {
        $this->categorieDeServicesId = $categorieDeServicesId;

        return $this;
    }


}
