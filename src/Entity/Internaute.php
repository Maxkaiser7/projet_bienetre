<?php

namespace App\Entity;

use App\Repository\InternauteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: InternauteRepository::class)]
class Internaute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = '';

    #[ORM\Column]
    private ?bool $newsletter = false;
/*
    /**
     * @ORM\ManyToMany(targetEntity=Prestataire::class, inversedBy="internautesFavoris")
     * @ORM\JoinTable(
     *     name="internautes_prestataires",
     *     joinColumns={@ORM\JoinColumn(name="internaute_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="prestataire_id", referencedColumnName="id")}
     * )

    private ?Collection $prestatairesFavoris = null;
*/
    #[ORM\ManyToMany(targetEntity: Prestataire::class, inversedBy: 'prestataire')]
    #[ORM\JoinColumn(name: 'favoris')]
    private $prestataireFavoris;


    #[ORM\OneToOne(mappedBy: 'internaute', cascade: ['persist', 'remove'])]
    private ?Position $position = null;

    #[ORM\OneToMany(mappedBy: 'internaute', targetEntity: Abus::class)]
    private Collection $abuses;

    #[ORM\OneToMany(mappedBy: 'internaute', targetEntity: Commentaire::class)]
    private Collection $commentaires;


    #[Pure] public function __construct()
    {
        $this->abuses = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->prestataireFavoris = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isNewsletter(): ?bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(bool $newsletter): self
    {
        $this->newsletter = $newsletter;

        return $this;
    }


    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): self
    {
        // unset the owning side of the relation if necessary
        if ($position === null && $this->position !== null) {
            $this->position->setInternaute(null);
        }

        // set the owning side of the relation if necessary
        if ($position !== null && $position->getInternaute() !== $this) {
            $position->setInternaute($this);
        }

        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection<int, Abus>
     */
    public function getAbuses(): Collection
    {
        return $this->abuses;
    }

    public function addAbuse(Abus $abuse): self
    {
        if (!$this->abuses->contains($abuse)) {
            $this->abuses->add($abuse);
            $abuse->setInternaute($this);
        }

        return $this;
    }

    public function removeAbuse(Abus $abuse): self
    {
        if ($this->abuses->removeElement($abuse)) {
            // set the owning side to null (unless already changed)
            if ($abuse->getInternaute() === $this) {
                $abuse->setInternaute(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setInternaute($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getInternaute() === $this) {
                $commentaire->setInternaute(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Prestataire>
     */
    public function getPrestatairesFavoris(): Collection
    {
        if (!$this->prestataireFavoris) {
            $this->prestataireFavoris = new ArrayCollection();
        }
        return $this->prestataireFavoris;
    }

    public function addPrestatairesFavori(Prestataire $prestatairesFavori): self
    {
        if (!$this->prestataireFavoris->contains($prestatairesFavori)) {
            $this->prestataireFavoris->add($prestatairesFavori);
        }

        return $this;
    }


    public function removePrestatairesFavori(Prestataire $prestatairesFavori): self
    {
        $this->prestataireFavoris->removeElement($prestatairesFavori);

        return $this;
    }


}
