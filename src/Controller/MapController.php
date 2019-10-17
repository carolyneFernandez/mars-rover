<?php

namespace App\Controller;

use App\Entity\Map;
use App\Repository\EasyMapRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class MapController extends AbstractController
{
    /**
     * @Route("/map", name="map")
     */
    public function index(EasyMapRepository $easyMap)
    {   
        $easyMap = new Map(100,10,10,10);
        echo $easyMap->__toString();
        die();
        
        return $this->render('map/index.html.twig', [
            'controller_name' => 'MapController',
        ]);
    }
}
