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
		$data = array();
		$data['ProductsTotal'] = $product_repo->getCountProductsTotal();
		$data['ProductsFoundPerDay'] = $product_repo->getProductsFoundPerDay();
		$data['SourcesTotal'] = $source_repo->getCountSourcesTotal();
		$data['SourcesNamedTotal'] = $source_repo->getCountSourcesNamedTotal();
		$data['SourcesUnnamedTotal'] = $source_repo->getCountSourcesUnnamedTotal();
		$data['SourcesVisitedTotal'] = $source_repo->getCountSourcesVisitedTotal();
		$data['SourcesNotVisitedTotal'] = $source_repo->getCountSourcesNotVisitedTotal();
		$data['SourcesVisitedPerDay'] = $source_repo->getSourcesVisitedPerDay();
		return $this->render('admin/admindata.html.twig',['data'=>$data]);
	}
	

}
