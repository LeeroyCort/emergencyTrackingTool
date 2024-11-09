<?php

namespace App\Entity;

use App\Repository\AssignmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssignmentRepository::class)]
class Assignment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $assignmentTypeId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startTimestamp = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endTimestamp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssignmentTypeId(): ?int
    {
        return $this->assignmentTypeId;
    }

    public function setAssignmentTypeId(?int $assignmentTypeId): static
    {
        $this->assignmentTypeId = $assignmentTypeId;

        return $this;
    }

    public function getStartTimestamp(): ?\DateTimeInterface
    {
        return $this->startTimestamp;
    }

    public function setStartTimestamp(\DateTimeInterface $startTimestamp): static
    {
        $this->startTimestamp = $startTimestamp;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getEndTimestamp(): ?\DateTimeInterface
    {
        return $this->endTimestamp;
    }

    public function setEndTimestamp(?\DateTimeInterface $endTimestamp): static
    {
        $this->endTimestamp = $endTimestamp;

        return $this;
    }
}
