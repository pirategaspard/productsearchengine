<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug;

use App\Entity\Product;
use App\Form\ProductType;

class ProductController extends AbstractController
{				
	/**
	 * @Route("/admin/product", name="Products")
	 */
    public function products(Request $request) {
		$offset = $request->get('offset',0);
		$route='products';
		$repository = $this->getDoctrine()->getRepository(product::class);
		$products = $repository->findNext($offset);
		return $this->render('admin/product/index.html.twig',['results'=>$products, 'route'=>$route, 'offset'=>$offset]);
	}
	
	/**
	 * @Route("/admin/product/{id}", name="Product_Form", methods={"GET"})
	 */
	public function product_form($id=0) {			
		$product = $this->get_product($id);
		$form = $this->createForm(ProductType::class, $product);
		return $this->render('admin/product/form.html.twig',['form' => $form->createView(),'product'=>$product]);
	}
	
	/**
	 * @Route("/admin/product/{id}", name="Product_Save", methods={"POST"})
	 */
    public function product_save(Request $request) {	//product $product=null, 	
		$product = $this->get_product($request->request->get('product')['id']);
		$form = $this->createForm(productType::class, $product)->handleRequest($request);
		if ($form->isSubmitted() ) {  //&& $form->isValid()
			$product = $form->getData();
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($product);
			$entityManager->flush();			
		}
		else
		{
			//die('oops');
		}
		return $this->redirectToRoute('products');
	}
	
	/**
	 * @Route("/admin/product/{id}/delete", name="Product_Delete", methods={"POST"})
	 */
    public function product_delete($id=0) {			
		$product = $this->get_product($id);
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($product);
		$entityManager->flush();
		return $this->redirectToRoute('products');
	}
	
	private function get_product($id=0)
	{
		$repository = $this->getDoctrine()->getRepository(product::class);				
		return $repository->find($id);
	}
	
	// 1) Scrape URLS for Products
	// 2) Save Products found
	
	
}
