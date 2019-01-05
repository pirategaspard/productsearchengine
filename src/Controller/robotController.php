<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug;
use App\Service\RobotService;
use App\Entity\Source;

class robotController extends AbstractController
{			
	/**
	 * @Route("/admin/robot", name="robots")
	 */
    public function index() {		
		return $this->render('admin/index.html.twig');
	}
	
	/**
	 * @Route("/admin/robot/updateAll", name="robots updateAll")
	 */
	public function updateAll(RobotService $RobotService) {
		set_time_limit(100); // this process take a while
		$result_data = $RobotService->fetchAllSourcesUpdates();
		return $this->redirectToRoute('products');
	}
	
	/**
	 * @Route("/admin/robot/update/{id}", name="robots update source")
	 */
	public function updateSpecific(RobotService $RobotService, $id=0) {
		$source = $this->getDoctrine()->getRepository(source::class)->find($id);
		$result_data = $RobotService->fetchSourceProducts($source);
		return $this->redirectToRoute('products');
	}

}
