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
    private ?int $assignmentCategoryId = null;

    #[ORM\Column(nullable: true)]
    private ?string $assignmentCategoryName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startTimestamp = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endTimestamp = null;

    #[ORM\Column(options: ["default" => true])]
    private ?bool $active = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssignmentCategoryId(): ?int
    {
        return $this->assignmentCategoryId;
    }

    public function setAssignmentCategoryId(?int $assignmentCategoryId): static
    {
        $this->assignmentCategoryId = $assignmentCategoryId;

        return $this;
    }

    public function getAssignmentCategoryName(): ?string
    {
        return $this->assignmentCategoryName;
    }

    public function setAssignmentCategoryName(?string $assignmentCategoryName): static
    {
        $this->assignmentCategoryName = $assignmentCategoryName;

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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
}
