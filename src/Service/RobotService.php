<?php
namespace App\Service;

use App\Entity\Source;
use App\Entity\Product;
use App\Service\Scraper\ScraperBasic;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class RobotService
{
	private $ManagerRegistry;
	private $EntityManager;
	private $ScraperBasic;	
	private $product_count = 0;
	
    public function __construct(ManagerRegistry $ManagerRegistry, EntityManagerInterface $EntityManager, ScraperBasic $ScraperBasic)
    {
        $this->ManagerRegistry = $ManagerRegistry;
        $this->EntityManager = $EntityManager;
        $this->ScraperBasic = $ScraperBasic;        
    }
    
    public function getProductCount()
    {
		return $this->product_count;
	}
        
    public function fetchSourceUpdates()
    {
		// Step 1) get list of urls from existing sources				
		$repository = $this->ManagerRegistry->getRepository(Source::class);
		$sources = $repository->findAll();
		$this->fetchSourceProducts($sources);
		return $this;				
	}
	
	public function fetchSourceProducts($sources)
	{
		// Step 2) Loop over sources and check the source urls for products
		foreach ($sources as $source)
		{
			$products = array();
			
			// Step 2a) delete all existing products?
			// Step 2b) look for products at the source url
			$products = $this->findProducts($source->getUrl(),$source);
			if (count($products) > 0)
			{
				// increment counter 
				$this->product_count += count($products);
				// save all the listings
				foreach($products as $p)
				{
					$this->EntityManager->persist($p);
					$this->EntityManager->flush();
				}
			}
		}		
	}
	
	public function findProducts($url,$source=null)
	{		
		$scraper = $this->ScraperBasic;
		$scraper->setUrl($url);
		$scraper->setSource($source);
		$scraper->fetchHtml();
		//if ( $scraper->html )
		$newProducts = $scraper->parseHtml()->getProducts();
		$scraper->clearHtml(); // free some memory
		return $newProducts;
	}
}
