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
		//$sources = $source_repository->findAll();
		$sources = $source_repository->getNextOldest(10); // get the sources that haven't been looked at for a while
		return $this->fetchAllSourcesData($sources);				
	}
	
	public function fetchAllSourcesData($sources)
	{
		$data_count = array();
		$data_count['products'] = 0;
		$data_count['urls'] = 0;
		$product_repository = $this->ManagerRegistry->getRepository(Product::class);
		$source_repository = $this->ManagerRegistry->getRepository(Source::class);
		// Step 2) Loop over sources and check the source urls for products & urls
		foreach ($sources as $source)
		{							
			// Get the data from this source!
			$scraper = $this->findData($source->getUrl(),$source);	
			// Step 3) Did we find any products at the source url?
			if (count($scraper->getProducts()) > 0)
			{				
				$data_count['products'] += count($scraper->getProducts()); // increment counter
				// 3a) loop over the products and save
				foreach($scraper->getProducts() as $p)
				{					
					// Step 3b) delete existing product (if it exists)
					$product_repository->deleteByIdCode($p->getIdCode());
					// Step 3c) Save product					
					$this->EntityManager->persist($p);
					$this->EntityManager->flush();	
				}				
			}
			// Step 4) if they don't exists already, save the urls that were found for future scraping
			if (count($scraper->getUrls()) > 0)
			{
				// increment counter 				
				$data_count['urls'] += count($scraper->getUrls());
				foreach($scraper->getUrls() as $u)
				{					
					// Step 4a) have we seen this url before?
					if(!$source_repository->findOneBy(array('id_code' => $u->getIdCode())))
					{
						// Step 4b) If the url doesn't exist, save it! 					
						$this->EntityManager->persist($u);
					}
					$this->EntityManager->flush();
				}
			}
			unset($scraper); // clean up 
						
			/* 
			 * Step 5) Set a new date so that this source will not get 
			 * retrieved again until the other sources also get reviewed
			 */
			$source->setDateLastUpdated(); 
			$this->EntityManager->merge($source); 	
			//var_dump($source->getUrl());
		}
		return $data_count;
	}
	
	
	
	public function findData($url,$source=null)
	{				
		$scraper = new ScraperBasic();
		$scraper->setScraperUrl($url);
		$scraper->setSource($source);
		
		// TODO: check robots.txt before scraping		
		
		$scraper->fetchHtml(); // load the HTML for the url
		if ( $scraper->hasHtml() )
		{
			$scraper->parseHtml();  // Examine the HTML for products and urls
		}
		else
		{
			//echo 'unable to load page for:'.$url; die;
		}
		return $scraper;
	}
	
	// for one data source
	public function fetchSourceProducts($source)
	{
		$sources = array();
		$sources[] = $source;			
		return $this->fetchAllSourcesData($sources);
	}
	
}
