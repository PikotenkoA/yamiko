<?php
namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @ORM\Table(name="categories")
 */
class Category
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="subcategories")
     */
    private $parent;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="parent")
     */
    private $subcategories;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Attribute", mappedBy="category", orphanRemoval=true)
     */
    private $attributes;
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product", mappedBy="categories")
     */
    private $products;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comfyId;
    public function __construct()
    {
        $this->subcategories = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->products = new ArrayCollection();
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
    public function __toString()
    {
        return (string)$this->name;
    }
    public function getParent(): ?self
    {
        return $this->parent;
    }
    public function setParent(?self $parent): self
    {
        $this->parent = $parent;
        return $this;
    }
    /**
     * @return Collection|self[]
     */
    public function getSubcategories(): Collection
    {
        return $this->subcategories;
    }
    public function addSubcategory(self $subcategory): self
    {
        if (!$this->subcategories->contains($subcategory)) {
            $this->subcategories[] = $subcategory;
            $subcategory->setParent($this);
        }
        return $this;
    }
    public function removeSubcategory(self $subcategory): self
    {
        if ($this->subcategories->contains($subcategory)) {
            $this->subcategories->removeElement($subcategory);
            // set the owning side to null (unless already changed)
            if ($subcategory->getParent() === $this) {
                $subcategory->setParent(null);
            }
        }
        return $this;
    }
    /**
     * @return Collection|Attribute[]
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }
    public function addAttribute(Attribute $attribute): self
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes[] = $attribute;
            $attribute->setCategory($this);
        }
        return $this;
    }
    public function removeAttribute(Attribute $attribute): self
    {
        if ($this->attributes->contains($attribute)) {
            $this->attributes->removeElement($attribute);
            // set the owning side to null (unless already changed)
            if ($attribute->getCategory() === $this) {
                $attribute->setCategory(null);
            }
        }
        return $this;
    }
    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }
    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addCategory($this);
        }
        return $this;
    }
    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            $product->removeCategory($this);
        }
        return $this;
    }
    public function getComfyId(): ?int
    {
        return $this->comfyId;
    }
    public function setComfyId(?int $comfyId): self
    {
        $this->comfyId = $comfyId;
        return $this;
    }
}