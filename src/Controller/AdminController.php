<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug;

use App\Entity\Source;
use App\Entity\Product;

class AdminController extends AbstractController
{			
	/**
	 * @Route("/admin", name="Admin")
	 */
    public function index() {
		$source_repo = $this->getDoctrine()->getRepository(Source::class);
		$product_repo = $this->getDoctrine()->getRepository(Product::class);
		$count = array();
		$count['ProductsTotal'] = $product_repo->getCountProductsTotal();
		$count['SourcesTotal'] = $source_repo->getCountSourcesTotal();
		$count['SourcesNamedTotal'] = $source_repo->getCountSourcesNamedTotal();
		$count['SourcesUnnamedTotal'] = $source_repo->getCountSourcesUnnamedTotal();
		$count['SourcesVisitedTotal'] = $source_repo->getCountSourcesVisitedTotal();
		$count['SourcesNotVisitedTotal'] = $source_repo->getCountSourcesNotVisitedTotal();
		return $this->render('admin/admindata.html.twig',['count'=>$count]);
	}
	

}
