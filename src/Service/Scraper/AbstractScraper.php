<?php 
namespace App\Service\Scraper;

use App\Entity\Product;
use App\Entity\Source;
use App\Service\Util\ScraperUtils;

abstract class AbstractScraper
{
	protected $source; 	
	protected $scraper_url = '';
	protected $html = '';	
	protected $products = array();
	protected $urls = array();
	
	function __construct() 
	{
		$this->utils = new ScraperUtils();
	}
	
	public function setSource($source=null) 
	{ 
		$this->source = $source;
	}	
	
	public function setScraperUrl($url): self
	{
        $this->scraper_url = $url;
        return $this;
    }
    
    public function getScraperUrl(): string
	{
        return $this->scraper_url;
    } 
    
    public function getProducts()
	{
		return $this->products;
	}
	
	public function getUrls()
	{
		return $this->urls;
	}	  
    
    public function hasHtml(): bool
    {
		if (!$this->html)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
    
    public function reset(): self
	{
        $this->html = '';
        $this->url = '';
        $this->products = array();
        return $this;
    }
    
    protected function getNewProduct(): Product
    {
		$p = new Product;
		$p->setSource($this->source);
		$p->setDateLastUpdated();
		return $p;
	}
	
	protected function getNewSource($url): Source
    {
		$s = new Source;
		$s->setUrl($url);
		$s->setIdCode($this->createKey($s->getUrl()));
		$s->setDateLastUpdated();
		return $s;
	}
	
	// create an id that doesn't change when the Product is rescraped.
	protected function createKey($url): string
	{
		return substr(hash('sha256',$this->source->getUrl().$url),-50);
	}
	
	// These function must be extended in the child.
	abstract public function parseHtml();
	abstract public function fetchHtml();
}
?>
