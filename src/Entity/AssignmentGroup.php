<?php

namespace App\Entity;

use App\Repository\AssignmentGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\AssignmentCategory;

#[ORM\Entity(repositoryClass: AssignmentGroupRepository::class)]
class AssignmentGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(options: ["default" => 0])]
    private ?int $memberCount = null;
    
    private ?int $activeMemberCount = null;

    /**
     * @var Collection<int, AssignmentCategory>
     */
    #[ORM\ManyToMany(targetEntity: AssignmentCategory::class, mappedBy: 'containedAssignmentGroups')]
    private Collection $assignmentCategories;

    /**
     * @var Collection<int, AssignmentPosition>
     */
    #[ORM\OneToMany(targetEntity: AssignmentPosition::class, mappedBy: 'assignmentGroup')]
    private Collection $assignmentPositions;

    public function __construct()
    {
        $this->assignmentCategories = new ArrayCollection();
        $this->assignmentPositions = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMemberCount(): ?int
    {
        return $this->memberCount;
    }

    public function setMemberCount(?int $memberCount): static
    {
        $this->memberCount = $memberCount;

        return $this;
    }

    public function getActiveMemberCount(): ?int
    {
        return $this->activeMemberCount;
    }

    public function setActiveMemberCount(?int $activeMemberCount): static
    {
        $this->activeMemberCount = $activeMemberCount;

        return $this;
    }

    /**
     * @return Collection<int, AssignmentCategory>
     */
    public function getAssignmentCategories(): Collection
    {
        return $this->assignmentCategories;
    }

    public function addAssignmentCategory(AssignmentCategory $assignmentCategory): static
    {
        if (!$this->assignmentCategories->contains($assignmentCategory)) {
            $this->assignmentCategories->add($assignmentCategory);
            $assignmentCategory->addContainedAssignmentGroup($this);
        }

        return $this;
    }

    public function removeAssignmentCategory(AssignmentCategory $assignmentCategory): static
    {
        if ($this->assignmentCategories->removeElement($assignmentCategory)) {
            $assignmentCategory->removeContainedAssignmentGroup($this);
        }

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
            $assignmentPosition->setAssignmentGroup($this);
        }

        return $this;
    }

    public function removeAssignmentPosition(AssignmentPosition $assignmentPosition): static
    {
        if ($this->assignmentPositions->removeElement($assignmentPosition)) {
            // set the owning side to null (unless already changed)
            if ($assignmentPosition->getAssignmentGroup() === $this) {
                $assignmentPosition->setAssignmentGroup(null);
            }
        }

        return $this;
    }
}
