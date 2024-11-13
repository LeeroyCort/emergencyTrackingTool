<?php

namespace App\Entity;

use App\Repository\AssignmentCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\AssignmentGroup;
use App\Entity\AssignmentRootCategory;

#[ORM\Entity(repositoryClass: AssignmentCategoryRepository::class)]
class AssignmentCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, AssignmentGroup>
     */
    #[ORM\ManyToMany(targetEntity: AssignmentGroup::class, inversedBy: 'assignmentCategories')]
    private Collection $containedAssignmentGroups;

    #[ORM\ManyToOne(inversedBy: 'assignmentCategories')]
    private ?AssignmentRootCategory $rootCategory = null;

    public function __construct()
    {
        $this->containedAssignmentGroups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, AssignmentGroup>
     */
    public function getContainedAssignmentGroups(): Collection
    {
        return $this->containedAssignmentGroups;
    }

    public function addContainedAssignmentGroup(AssignmentGroup $containedAssignmentGroup): static
    {
        if (!$this->containedAssignmentGroups->contains($containedAssignmentGroup)) {
            $this->containedAssignmentGroups->add($containedAssignmentGroup);
        }

        return $this;
    }

    public function removeContainedAssignmentGroup(AssignmentGroup $containedAssignmentGroup): static
    {
        $this->containedAssignmentGroups->removeElement($containedAssignmentGroup);

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
