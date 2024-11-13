<?php

namespace App\Entity;

use App\Repository\AssignmentRootCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\AssignmentCategory;

#[ORM\Entity(repositoryClass: AssignmentRootCategoryRepository::class)]
class AssignmentRootCategory
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
     * @var Collection<int, AssignmentCategory>
     */
    #[ORM\OneToMany(targetEntity: AssignmentCategory::class, mappedBy: 'rootCategory')]
    private Collection $assignmentCategories;

    public function __construct()
    {
        $this->assignmentCategories = new ArrayCollection();
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
            $assignmentCategory->setRootCategory($this);
        }

        return $this;
    }

    public function removeAssignmentCategory(AssignmentCategory $assignmentCategory): static
    {
        if ($this->assignmentCategories->removeElement($assignmentCategory)) {
            // set the owning side to null (unless already changed)
            if ($assignmentCategory->getRootCategory() === $this) {
                $assignmentCategory->setRootCategory(null);
            }
        }

        return $this;
    }
}
