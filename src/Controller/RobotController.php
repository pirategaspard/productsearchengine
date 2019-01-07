<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug;
use App\Service\RobotService;
use App\Entity\Source;
use App\Entity\Product;

class RobotController extends AbstractController
{			
	/**
	 * @Route("/admin/robot", name="Robots")
	 */
    public function index() {		
		return $this->render('admin/index.html.twig');
	}
	
	/**
	 * @Route("/admin/robot/updateAll", name="Robots_UpdateAll")
	 */
	public function updateAll(RobotService $RobotService) {
		set_time_limit(100); // this process take a while
		$result_data = $RobotService->fetchAllSourcesUpdates();
		return $this->redirectToRoute('Products');
	}
	
	/**
	 * @Route("/admin/robot/update/{id}", name="Robot_Update_Source")
	 */
	public function updateSpecific(RobotService $RobotService, $id=0) {
		$source = $this->getDoctrine()->getRepository(source::class)->find($id);
		$result_data = $RobotService->fetchSourceProducts($source);
		return $this->redirectToRoute('Products');
	}
	
	/**
	 * @Route("/admin/robot/resetAll", name="Robots_ResetAll")
	 */
	public function resetAll(RobotService $RobotService) {
		$this->getDoctrine()->getRepository(product::class)->removeAllProducts();
		$this->getDoctrine()->getRepository(source::class)->removeAllSources();
		return $this->redirectToRoute('Products');
	}

}
