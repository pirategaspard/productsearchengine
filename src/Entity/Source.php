<?php

/*
 * Sources are the starting points for the web robot to begin 
 * looking for products. Sources can be urls that the web robot 
 * has found and stored for future scraping or entered by humans 
 * through the admin area
 * */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SourceRepository")
 */
class Source
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**     
     * @ORM\Column(type="string", length=50,nullable=true)
     */
    private $id_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=2040)
     */
    private $url;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="source")
     */
    private $products;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_last_updated;
    
    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_added;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(?int $id=0): self
    {
		$this->id = $id;
        return $this;
    }
    
    public function getIdCode(): ?string
    {
        return $this->id_code;
    }

    public function setIdCode(string $id_code): self
    {
        $this->id_code = $id_code;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }
    
	public function getDateLastUpdated(): ?\DateTimeInterface
    {
        return $this->date_last_updated;
    }

    public function setDateLastUpdated(): self
    {
        $this->date_last_updated = new \DateTime('now');
        return $this;
    }
    
    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->date_added;
    }

    public function setDateAdded($date=''): self  // have to allow this function to accept a value for the formbuilder
    {
		if ( $this->date_added == null )
		{
			$this->date_added = new \DateTime('now');
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
            $product->setSource($this);
        }
        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getSource() === $this) {
                $product->setSource(null);
            }
        }
        return $this;
    }
    
    public function addProducts(Collection $products): self
    {
		// safe way
        foreach ( $products as $product ) 
        {
			$this->addProduct($product);
		}
		// unsafe way
		//$this->products = $products;
        return $this;
    }
    
    public function __toString() {
	  return $this->title;
	} 
}
