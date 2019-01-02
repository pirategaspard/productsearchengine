<?php 
namespace App\Service\Scraper;

use App\Entity\Product;
use App\Service\Util\ScraperUtils;
use Sunra\PhpSimple\HtmlDomParser;

abstract class AbstractScraper
{
	protected $source; 	
	protected $url = '';
	protected $html = '';	
	protected $products = array();
	
	function __construct(ScraperUtils $scraperutils) 
	{
		$this->utils = $scraperutils;
	}
	
	public function setSource($source=null) 
	{ 
		$this->source = $source;
	}
	
	public function setUrl($url): self
	{
        $this->url = $url;
        return $this;
    }   
	
	public function fetchHtml(): self
	{
        $this->html = HtmlDomParser::file_get_html($this->url,false,null,0);
        return $this;
    }
    
    public function clearHtml(): self
	{
        $this->html = '';
        return $this;
    }
    
    public function getNewProduct(): Product
    {
		$p = new Product;
		$p->setSource($this->source);
		return $p;
	}
	
	public function getProducts()
	{
		return $this->products;
	}
	
	// create an id that doesn't change when the Product is rescraped.
	public function createKey($Product): string
	{
		return substr(hash('sha256',$this->source->getId().$Product->getUrl().$Product->getName()),0,20);
	}

	// This function must be extended in the child.
	abstract function parseHtml();

}
?>
