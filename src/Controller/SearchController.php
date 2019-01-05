<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Product;

class SearchController extends AbstractController
{			
	/**
	 * @Route("/search", name="Search Results")
	 */
    public function search(Request $request) {
		$search_text = $request->get('search_text');
		$products = $this->getDoctrine()->getRepository(product::class)->findProductsSimpleSearch($search_text);
		return $this->render('search/results.html.twig',['results'=>$products]);
	}
	
	/**
	 * @Route("/", name="Search Form")
	 */
    public function form(Request $request) {		
		return $this->render('search/form.html.twig');
	}
}
