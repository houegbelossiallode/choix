<?php

namespace App\Entity;

use App\Repository\AttestationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttestationRepository::class)]
class Attestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $contrat_stage = null;

    #[ORM\Column(length: 255)]
    private ?string $carte_scolaire = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'attestations')]
    private ?User $entreprise = null;

    #[ORM\ManyToOne(inversedBy: 'attestations')]
    private ?User $etudiant = null;

    public function __construct()
    {
       
        $this->created_at = new \DateTimeImmutable();
       
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContratStage(): ?string
    {
        return $this->contrat_stage;
    }

    public function setContratStage(string $contrat_stage): static
    {
        $this->contrat_stage = $contrat_stage;

        return $this;
    }

    public function getCarteScolaire(): ?string
    {
        return $this->carte_scolaire;
    }

    public function setCarteScolaire(string $carte_scolaire): static
    {
        $this->carte_scolaire = $carte_scolaire;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getEntreprise(): ?User
    {
        return $this->entreprise;
    }

    public function setEntreprise(?User $entreprise): static
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getEtudiant(): ?User
    {
        return $this->etudiant;
    }

    public function setEtudiant(?User $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }
}