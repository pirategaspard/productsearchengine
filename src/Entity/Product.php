<?php
/* 
 * A Product needs a price and a name. 
 * */


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="product",indexes={@ORM\Index(columns={"name","data"}, flags={"fulltext"})}, uniqueConstraints={@ORM\UniqueConstraint(columns={"id_code"})});
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
     * @ORM\Column(type="string", length=50)
     */
    private $id_code;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url_image;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_last_updated;
    
    /**
     * @ORM\Column(type="text")
     */
    private $data;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Source", inversedBy="products")
     * @ORM\JoinColumn(nullable=true)
     */
    private $source;    

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(?int $id=0): self
    {
		$this->id = $id;
        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;
        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
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

    public function getUrlImage(): ?string
    {
        return $this->url_image;
    }

    public function setUrlImage(?string $url_image): self
    {
        $this->url_image = $url_image;
        return $this;
    }
    
    public function getDateLastUpdated(): ?\DateTimeInterface
    {
        return $this->date_last_updated;
    }

    public function setDateLastUpdated(?\DateTimeInterface $date_last_updated): self
    {
        $this->date_last_updated = $date_last_updated;
        return $this;
    }
    
    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): self
    {
        $this->data = $data;
        return $this;
    }    

	public function getSource(): ?Source
             {
                 return $this->source;
             }

	public function setSource(?Source $source): self
             {
                 $this->source = $source;
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
}
