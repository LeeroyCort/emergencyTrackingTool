<?php

namespace App\Entity;

use App\Repository\AssignmentPositionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssignmentPositionRepository::class)]
class AssignmentPosition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'assignmentPositions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Assignment $assignment = null;

    #[ORM\ManyToOne(inversedBy: 'assignmentPositions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SquadMember $squadMember = null;

    #[ORM\ManyToOne(inversedBy: 'assignmentPositions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AssignmentGroup $assignmentGroup = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $scanTimestamp = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updateTimestamp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScanTimestamp(): ?\DateTimeInterface
    {
        return $this->scanTimestamp;
    }

    public function setScanTimestamp(\DateTimeInterface $scanTimestamp): static
    {
        $this->scanTimestamp = $scanTimestamp;

        return $this;
    }

    public function getAssignment(): ?Assignment
    {
        return $this->assignment;
    }

    public function setAssignment(?Assignment $assignment): static
    {
        $this->assignment = $assignment;

        return $this;
    }

    public function getSquadMember(): ?SquadMember
    {
        return $this->squadMember;
    }

    public function setSquadMember(?SquadMember $squadMember): static
    {
        $this->squadMember = $squadMember;

        return $this;
    }

    public function getAssignmentGroup(): ?AssignmentGroup
    {
        return $this->assignmentGroup;
    }

    public function setAssignmentGroup(?AssignmentGroup $assignmentGroup): static
    {
        $this->assignmentGroup = $assignmentGroup;

        return $this;
    }

    public function getUpdateTimestamp(): ?\DateTimeInterface
    {
        return $this->updateTimestamp;
    }

    public function setUpdateTimestamp(?\DateTimeInterface $updateTimestamp): static
    {
        $this->updateTimestamp = $updateTimestamp;

        return $this;
    }
}
