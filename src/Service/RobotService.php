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
        
    public function fetchAllSourcesUpdates()
    {
		// Step 1) get list of urls from existing sources				
		$source_repository = $this->ManagerRegistry->getRepository(Source::class);
		$sources = $source_repository->findAll();
		return $this->fetchAllSourcesData($sources);				
	}
	
	public function fetchAllSourcesData($sources)
	{
		$data_count = array();
		$data_count['products'] = 0;
		$data_count['urls'] = 0;
		$product_repository = $this->ManagerRegistry->getRepository(Product::class);
		$source_repository = $this->ManagerRegistry->getRepository(Source::class);
		// Step 2) Loop over sources and check the source urls for products
		foreach ($sources as $source)
		{				
			// Step 2a) look for products at the source url
			$data = $this->findData($source->getUrl(),$source);			
			if (count($data['products']) > 0)
			{
				// increment counter 				
				$data_count['products'] += count($data['products']);
				// save all the listings
				foreach($data['products'] as $p)
				{					
					// Step 2b) delete existing product (if it exists)
					$product_repository->deleteByIdCode($p->getIdCode());
					// Step 2c) Save product
					$this->EntityManager->persist($p);
					$this->EntityManager->flush();	
				}				
			}
			if (count($data['urls']) > 0)
			{
				// increment counter 				
				$data_count['urls'] += count($data['urls']);
				// save all the listings
				foreach($data['urls'] as $u)
				{					
					// Step 2b) delete existing product (if it exists)
					$source_repository->deleteByIdCode($u->getIdCode());
					// Step 2c) Save product
					$this->EntityManager->persist($u);
					$this->EntityManager->flush();
				}				
			}
		}
		return $data_count;
	}
	
	/*
	public function fetchSourceProducts($source)
	{
		$data_count = array();
		$data_count['product'] = 0;
		$data_count['url'] = 0;
		$product_repository = $this->ManagerRegistry->getRepository(Product::class);
		$products = array();					
		// Step 2a) look for products at the source url
		$products = $this->findProducts($source->getUrl(),$source);
		if (count($products) > 0)
		{
			// increment counter 				
			$data_count['product'] += count($products);
			// save all the listings
			foreach($products as $p)
			{					
				// Step 2b) delete existing product (if it exists)
				$product_repository->deleteByIdCode($p->getIdCode());
				// Step 2c) Save product
				$this->EntityManager->persist($p);
				$this->EntityManager->flush();										
			}				
		}
		return $data_count;
	}*/
	
	public function findData($url,$source=null)
	{		
		$data = array();
		$data['products'] = 0;
		$data['urls'] = 0;
		$scraper = $this->ScraperBasic;
		$scraper->setScraperUrl($url);
		$scraper->setSource($source);
		$scraper->fetchHtml();
		if ( $scraper->hasHtml() )
		{
			// Examine the HTML for products and urls
			$scraper->parseHtml();
			$data['products'] = $scraper->getProducts();
			$data['urls'] = $scraper->getUrls();
		}
		$scraper->reset(); // free some memory
		return $data;
	}
}
