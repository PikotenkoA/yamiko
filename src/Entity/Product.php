<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="products")
 */
class Product
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", options={"default":0} )
     */
    private $isTop;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductImage", mappedBy="product", orphanRemoval=true,
     *  cascade={"persist"})
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", mappedBy="product")
     */
    private $orderItems;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AttributeValue",
     *  mappedBy="product", orphanRemoval=true, cascade={"persist"})
     */
    private $attributeValues
    ;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comfyID;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="products")
     */
    private $categories;

    public function __construct()
    {
        $this->isTop=false;
        $this->images = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
        $this->product = new ArrayCollection();
        $this->value = new ArrayCollection();
        $this->attributeValues = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIsTop(): bool
    {
        return $this->isTop;
    }

    public function setIsTop(bool $isTop): self
    {
        $this->isTop = $isTop;

        return $this;
    }



    /**
     * @return Collection|ProductImage[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ProductImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(ProductImage $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setProduct($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->contains($orderItem)) {
            $this->orderItems->removeElement($orderItem);
            // set the owning side to null (unless already changed)
            if ($orderItem->getProduct() === $this) {
                $orderItem->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AttributeValue[]
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(AttributeValue $product): self
    {
        if (!$this->product->contains($product)) {
            $this->product[] = $product;
            $product->setAttribute($this);
        }

        return $this;
    }

    public function removeProduct(AttributeValue $product): self
    {
        if ($this->product->contains($product)) {
            $this->product->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getAttribute() === $this) {
                $product->setAttribute(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AttributeValue[]
     */
    public function getValue(): Collection
    {
        return $this->value;
    }

    public function addValue(AttributeValue $value): self
    {
        if (!$this->value->contains($value)) {
            $this->value[] = $value;
            $value->setProduct($this);
        }

        return $this;
    }

    public function removeValue(AttributeValue $value): self
    {
        if ($this->value->contains($value)) {
            $this->value->removeElement($value);
            // set the owning side to null (unless already changed)
            if ($value->getProduct() === $this) {
                $value->setProduct(null);
            }
        }

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
            $attributeValue->setProduct($this);
        }

        return $this;
    }

    public function removeAttributeValue(AttributeValue $attributeValue): self
    {
        if ($this->attributeValues->contains($attributeValue)) {
            $this->attributeValues->removeElement($attributeValue);
            // set the owning side to null (unless already changed)
            if ($attributeValue->getProduct() === $this) {
                $attributeValue->setProduct(null);
            }
        }

        return $this;
    }

    public function getComfyID(): ?int
    {
        return $this->comfyID;
    }

    public function setComfyID(?int $comfyID): self
    {
        $this->comfyID = $comfyID;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $catigory): self
    {
        if (!$this->categories->contains($catigory)) {
            $this->categories[] = $catigory;
        }

        return $this;
    }

    public function removeCategory(Category $catigory): self
    {
        if ($this->categories->contains($catigory)) {
            $this->categories->removeElement($catigory);
        }

        return $this;
    }
}
