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

    #[ORM\Column]
    private ?int $assignmentId = null;

    #[ORM\Column]
    private ?int $posId = null;

    #[ORM\Column]
    private ?int $squadMemberId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $scanTimestamp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $scannedType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssignmentId(): ?int
    {
        return $this->assignmentId;
    }

    public function setAssignmentId(int $assignmentId): static
    {
        $this->assignmentId = $assignmentId;

        return $this;
    }

    public function getPosId(): ?int
    {
        return $this->posId;
    }

    public function setPosId(int $posId): static
    {
        $this->posId = $posId;

        return $this;
    }

    public function getSquadMemberId(): ?int
    {
        return $this->squadMemberId;
    }

    public function setSquadMemberId(int $squadMemberId): static
    {
        $this->squadMemberId = $squadMemberId;

        return $this;
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

    public function getScannedType(): ?string
    {
        return $this->scannedType;
    }

    public function setScannedType(?string $scannedType): static
    {
        $this->scannedType = $scannedType;

        return $this;
    }
}
