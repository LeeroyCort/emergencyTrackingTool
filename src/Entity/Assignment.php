<?php

namespace App\Entity;

use App\Repository\AssignmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private ?string $name = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startTimestamp = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endTimestamp = null;

    #[ORM\Column(options: ["default" => true])]
    private ?bool $active = null;

    /**
     * @var Collection<int, AssignmentPosition>
     */
    #[ORM\OneToMany(targetEntity: AssignmentPosition::class, mappedBy: 'assignment')]
    private Collection $assignmentPositions;

    #[ORM\ManyToOne(inversedBy: 'assignments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AssignmentCategory $assignmentCategory = null;

    #[ORM\ManyToOne(inversedBy: 'assignments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AssignmentRootCategory $rootCategory = null;

    public function __construct()
    {
        $this->assignmentPositions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
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

    public function getAssignmentRootCategoryId(): ?int
    {
        return $this->assignmentRootCategoryId;
    }

    public function setAssignmentRootCategoryId(?int $assignmentRootCategoryId): static
    {
        $this->assignmentRootCategoryId = $assignmentRootCategoryId;

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
    
    public function getAssignmentRootCategoryName(): ?string
    {
        return $this->assignmentRootCategoryName;
    }

    public function setAssignmentRootCategoryName(?string $assignmentRootCategoryName): static
    {
        $this->assignmentRootCategoryName = $assignmentRootCategoryName;

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
            $assignmentPosition->setAssignment($this);
        }

        return $this;
    }

    public function removeAssignmentPosition(AssignmentPosition $assignmentPosition): static
    {
        if ($this->assignmentPositions->removeElement($assignmentPosition)) {
            if ($assignmentPosition->getAssignment() === $this) {
                $assignmentPosition->setAssignment(null);
            }
        }

        return $this;
    }

    public function getAssignmentCategory(): ?AssignmentCategory
    {
        return $this->assignmentCategory;
    }

    public function setAssignmentCategory(?AssignmentCategory $assignmentCategory): static
    {
        $this->assignmentCategory = $assignmentCategory;

        return $this;
    }

    public function getRootCategory(): ?AssignmentRootCategory
    {
        return $this->rootCategory;
    }

    public function setRootCategory(?AssignmentRootCategory $rootCategory): static
    {
        $this->rootCategory = $rootCategory;

        return $this;
    }
}
