<?php
namespace App\Service\Scraper;

use Sunra\PhpSimple\HtmlDomParser;

class ScraperBasic extends AbstractScraper
{
	public function fetchHtml(): self
	{
		//$this->html = HtmlDomParser::file_get_html($this->scraper_url,false,null,0);
		try {
			$this->html = HtmlDomParser::file_get_html($this->scraper_url,false,null,0);
		}
		catch (\Exception $e) 
		{
			$this->html = ''; // leave html blank. 
		}
        return $this;
    }
	
	public function parseHtml()
    {
		$this->findProducts();
		$this->findUrls();		
		return $this; 
	}
	
	private function findProducts()
	{
		$flags = [];
		$flags['found_match'] = 0;	
		$product = $this->getNewProduct();	
		// Page title is the most important grab that first.
		$page_title = $this->findPageTitle();			
		// product name is usually in an h1 tag. Look for h1 tags
		$html_results = $this->html->find('h1');	
		foreach($html_results as $html_result) 
		{
			// Assuming that Product name is in the title, see if we can find an h1 tag that matches the title
			// For each h1 tag see if the text of the h1 tag is also in the page title
			$h = trim($this->utils->getRegexUtils()->replaceHTMLEntities($html_result->plaintext)); 
			$has_match = preg_match('('.$h.')',$page_title);
			if($has_match)
			{
				// We founda  match! Use this h1 as the product name
				$flags['found_match'] = 1;
				$product->setName($h);
				$product = $this->populateProduct($product,$html_result);
			}
		}
		/* 
		 * if we didnt' find an h1 tag that was included in the title we 
		 * will just guess on what to use for the product name
		 */
		if (! $flags['found_match'])
		{
			if (count($html_results))
			{
				/*
				 *  if we have both h1 tags and page title, take
				 *  the page title if its longer
				 */
				if(strlen($page_title) > strlen(trim($html_results[0]->plaintext)))
				{
					$product->setName($page_title);
				}
				else
				{
					// otherwise just take the first h1 ...because that's gotta be it right?
					$product->setName(trim($html_results[0]->plaintext));
				}
				$product = $this->populateProduct($product,$html_results[0]);
			}
		}
		else
		{
			/*
			 *  We probably won't find anything useful, but lets look 
			 * at the entire page anyway and try to find a product
			 */
			//$product->setName($page_title);
			//$product = $this->populateProduct($product,$this->html);
		}
		// if we created a Product record with a price we can save it. 
		if ( strlen($product->getPrice()) > 0 )
		{
			// set public key. If we recrawl this page we can use this key to find it. 
			$product->setIdCode($this->createKey($product));
			$this->products[] = $product;
		}
	}
	
	private function findUrls()
	{
		$anchors = $this->html->find('a');		
		foreach($anchors as $a)
		{
			$url = $this->utils->searchUrl($a->href,$this->getScraperUrl());
			if(strlen($url) > 0)
			{
				$this->urls[] = $this->getNewSource($url);
			}
		}
	}
		
	private function populateProduct($product,$html_match)
	{		
		// Do we have a description meta tag?		
		$product->setDescription($this->findProductDescription());
		$product->setUrl($this->findProductUrl());
		$product->setUrlImage($this->findProductImage($html_match));
		$product->setPrice($this->findProductPrice($html_match));
		//$product->setData($this->utils->getRegexUtils()->cleanText($this->html->plaintext));
		$product->setData('');
		return $product;
	}
	
		
	private function findPageTitle(): string
	{
		$page_title = '';
		// Do we have a title meta tag?
		$meta_title = $this->html->find("meta[name='title']", 0);
		if ( $meta_title )
		{		
			$page_title = $meta_title->content; 	// if we have a title meta tag use it
		}
		else if($this->html->find("title", 0))
		{			
			// otherwise use the page title
			$page_title = $this->html->find("title", 0)->plaintext; 
		}		
		return $this->utils->getRegexUtils()->cleanText($page_title);
	}
	
	private function findProductDescription(): string
	{
		$product_description = '';
		// Do we have a description meta tag?		
		$meta_description = $this->html->find("meta[property='og:description']", 0);
		//var_dump($meta_description); die;
		if ( $meta_description )
		{		
			$product_description = $meta_description->content; 	// if we have a meta tag defined use it
		} 
		else
		{
			$meta_description = $this->html->find("meta[name='description']", 0);
			if ( $meta_description )
			{		
				$product_description = $meta_description->content; 	// if we have a meta tag defined use it
			}
		}
		return $this->utils->getRegexUtils()->cleanText($product_description);
	}
	
	private function findProductUrl(): string
	{
		$product_url = '';
		// canonical url is not a usable link?! Just use the scraper_url
		/* $meta_canonical_url = $this->html->find("head link[rel=canonical]",0);		
		if ( $meta_canonical_url )
		{		
			$product_url = $this->utils->searchUrl($meta_canonical_url->href,$this->getScraperUrl());
			//$product_url = $meta_canonical_url->href;
		}
		else
		{
			$product_url = $this->getScraperUrl();
		}*/
		$product_url = $this->getScraperUrl();
		return $this->utils->getRegexUtils()->cleanText($product_url);
	}
	
	private function findProductImage($html_match): string
	{
		$i = 0;
		$max_search_depth = 5;
		$product_image = '';
		$meta_image = $this->html->find("meta[property='og:image']", 0);
		if ( $meta_image )
		{		
			$product_image = $meta_image->content; 	// if we have a meta tag defined use it
		}
		// if we didn't find an image
		if( strlen($product_image) == 0 )
		{
			// find the image closest to the h1. Go up 5 levels. 
			$html_current_search_context = $html_match;	//reset search context
			while (( strlen($product_image) == 0 ) && ( $i < $max_search_depth ))
			{		
				if ( $html_current_search_context )
				{			
					$html_imgs = ($html_current_search_context->find('img'));
					if ( count($html_imgs) )
					{						
						// We found an image. This doesn't always find a *good* image
						$product_image = $html_imgs[0]->src; 
					}
					else
					{	if($html_current_search_context)
						{
							$html_current_search_context = $html_current_search_context->parent();				
						}
					}			
					/*foreach($html_imgs as $img) 
					{
						//$prop = 'data-original';
						echo '<br>'.$img->src;
					}*/
				}
				$i++;
			}
		}
		return $product_image;
	}
	
	private function findProductPrice($html_match): string
	{
		// go up a few levels and let's search for a price and an image
		// find nearest price.
		$i = 0;
		$max_search_depth = 10;
		$product_price = '';
		$html_current_search_context = $html_match;	
		while (( strlen($product_price) == 0 ) && ( $i < $max_search_depth))
		{					
			if ( $html_current_search_context )
			{	
				$price = $this->utils->getRegexUtils()->findPrice($html_current_search_context->plaintext);
				if ( $price > 0)
				{
					$product_price = $price;
				}
				else
				{				
					$html_current_search_context = $html_current_search_context->parent();			
				}				
			}
			$i++;
		}
		$product_price = preg_replace('/[\.,]/','',$product_price);
		return $product_price;
	}
	
}
