<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Product;

class SearchController extends AbstractController
{			
	/**
	 * @Route("/search", name="Search Results", methods={"POST"})
	 */
    public function search(Request $request) {
		$repository = $this->getDoctrine()->getRepository(product::class);
		$products = $repository->findAll();
		return $this->render('search/results.html.twig',['results'=>$products]);
	}
	
	/**
	 * @Route("/search", name="Search")
	 */
    public function form() {
		return $this->render('search/form.html.twig');
	}
	
	/**
	 * @Route("/", name="Home")
	 */
	public function index() {
		return $this->form();
	}
}
