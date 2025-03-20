<?php

namespace App\Entity;

use App\Repository\SquadMemberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SquadMemberRepository::class)]
class SquadMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false, unique: true)]
    private ?string $scanCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rank = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastAssignmentTimestamp = null;

    /**
     * @var Collection<int, AssignmentPosition>
     */
    #[ORM\OneToMany(targetEntity: AssignmentPosition::class, mappedBy: 'squadMember')]
    private Collection $assignmentPositions;
    
    public function __construct()
    {
        $this->assignmentPositions = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScanCode(): ?string
    {
        return $this->scanCode;
    }

    public function setScanCode(?string $scanCode): static
    {
        $this->scanCode = $scanCode;

        return $this;
    }

    public function getName1(): ?string
    {
        return $this->name1;
    }

    public function setName1(?string $name1): static
    {
        $this->name1 = $name1;

        return $this;
    }

    public function getName2(): ?string
    {
        return $this->name2;
    }

    public function setName2(?string $name2): static
    {
        $this->name2 = $name2;

        return $this;
    }

    public function getName3(): ?string
    {
        return $this->name3;
    }

    public function setName3(?string $name3): static
    {
        $this->name3 = $name3;

        return $this;
    }

    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setRank(?string $rank): static
    {
        $this->rank = $rank;

        return $this;
    }

    public function getLastAssignmentTimestamp(): ?\DateTimeInterface
    {
        return $this->lastAssignmentTimestamp;
    }

    public function setLastAssignmentTimestamp(?\DateTimeInterface $lastAssignmentTimestamp): static
    {
        $this->lastAssignmentTimestamp = $lastAssignmentTimestamp;

        return $this;
    }
    
    
    /**
     * @return Collection<int, AssignmentPosition>
     */
    public function getAssignmentPositions(): Collection
    {
        return $this->assignmentPositions;
    }

    public function addAssignmentPosition(AssignmentPosition $assignmentPosition): static
    {
        if (!$this->assignmentPositions->contains($assignmentPosition)) {
            $this->assignmentPositions->add($assignmentPosition);
            $assignmentPosition->setSquadMember($this);
        }

        return $this;
    }

    public function removeAssignmentPosition(AssignmentPosition $assignmentPosition): static
    {
        if ($this->assignmentPositions->removeElement($assignmentPosition)) {
            if ($assignmentPosition->getSquadMember() === $this) {
                $assignmentPosition->setSquadMember(null);
            }
        }

        return $this;
    }
    
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'scanCode',
        ]));
    }
}
