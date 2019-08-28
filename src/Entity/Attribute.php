<?php
namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="App\Repository\AttributeRepository")
 */
class Attribute
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="attributes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;
    /**
     * @ORM\Column(type="json_array")
     */
    private $valuesList;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AttributeValue", mappedBy="attribute", orphanRemoval=true)
     */
    private $attributeValues;
    public function __construct()
    {
        $this->attributeValues = new ArrayCollection();
    }
    public function __toString()
    {
        return (string)$this->name;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function getCategory(): ?Category
    {
        return $this->category;
    }
    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }
    public function getValuesList()
    {
        return $this->valuesList;
    }
    public function setValuesList($valuesList): self
    {
        $this->valuesList = $valuesList;
        return $this;
    }
    /**
     * @return Collection|AttributeValue[]
     */
    public function getAttributeValues(): Collection
    {
        return $this->attributeValues;
    }
    public function addAttributeValue(AttributeValue $attributeValue): self
    {
        if (!$this->attributeValues->contains($attributeValue)) {
            $this->attributeValues[] = $attributeValue;
            $attributeValue->setAttribute($this);
        }
        return $this;
    }
    public function removeAttributeValue(AttributeValue $attributeValue): self
    {
        if ($this->attributeValues->contains($attributeValue)) {
            $this->attributeValues->removeElement($attributeValue);
            // set the owning side to null (unless already changed)
            if ($attributeValue->getAttribute() === $this) {
                $attributeValue->setAttribute(null);
            }
        }
        return $this;
    }
}