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
    public function index(Request $request) {
		$admin = $request->get('admin',false);
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
		return $this->render('admin/admindata.html.twig',['data'=>$data,'admin'=>$admin]);
	}
	
	/**
	 * @Route("/admin/reports/products/perdomain", name="Admin_Products_Per_Domain")
	 */
	public function report_products_perdomain(){
		$product_repo = $this->getDoctrine()->getRepository(Product::class);
		$ProductsFoundPerDay = $product_repo->getProductsFoundPerDomain();
		$data = array();
		$data['chart'] = array();
		$data['chart']['type'] = 'pie'; //'horizontalBar';
		$data['chart']['title'] = 'Products Found Per Domain';
		//$data['chart']['x_label'] = 'X Axis';
		$data['chart']['y_label'] = '# of Products Found';
		$data['chart']['x_data'] = $ProductsFoundPerDay['domain'];
		$data['chart']['y_data'] = $ProductsFoundPerDay['count'];
		$data['chart']['colors'] = '["rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)"]';
		return $this->render('admin/report/chart.html.twig',['data'=>$data]);
	}
	
	
	/**
	 * @Route("/admin/reports/products/perday", name="Admin_Products_Per_Day")
	 */
	public function report_products_perday(){
		$product_repo = $this->getDoctrine()->getRepository(Product::class);
		$ProductsFoundPerDay = $product_repo->getProductsFoundPerDay();
		$data = array();
		$data['chart'] = array();
		$data['chart']['type'] = 'line';
		$data['chart']['title'] = 'Products Found Per Day';
		//$data['chart']['x_label'] = 'X Axis';
		$data['chart']['y_label'] = '# of Products Found';
		$data['chart']['x_data'] = $ProductsFoundPerDay['found_day'];
		$data['chart']['y_data'] = $ProductsFoundPerDay['count'];
		$data['chart']['colors'] = '["rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)"]';
		return $this->render('admin/report/chart.html.twig',['data'=>$data]);
	}
	
	/**
	 * @Route("/admin/reports/sources/perday", name="Admin_Sources_Per_Day")
	 */
	public function report_sources_perday(){
		$source_repo = $this->getDoctrine()->getRepository(Source::class);
		$SourcesVisitedPerDay = $source_repo->getSourcesVisitedPerDay();
		$data = array();
		$data['chart'] = array();
		$data['chart']['type'] = 'line';
		$data['chart']['title'] = 'Sources Found Per Day';
		//$data['chart']['x_label'] = 'X Axis';
		$data['chart']['y_label'] = '# of Sources Found';
		$data['chart']['x_data'] = $SourcesVisitedPerDay['visit_day'];
		$data['chart']['y_data'] = $SourcesVisitedPerDay['count'];
		$data['chart']['colors'] = '["rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)"]';
		return $this->render('admin/report/chart.html.twig',['data'=>$data]);
	}
	

}
