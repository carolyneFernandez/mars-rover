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
        function debug($var) {
            echo "<pre>";
            var_dump($var);
            echo "</pre>";
        }
        $easyMap = new Map(300,300,10,10);
        $grille = $easyMap->map_gen();
        
        $keysX = array_keys($grille[10]); // X
        $keysY = array_keys($grille); // Y
        // echo $keys[24];
        // var_dump(current($grille[10][24]));
        // echo "<pre>";
        // var_dump($grille);
        // echo "</pre>";

        $res = $easyMap->requestAdjCases($keysX[24], $keysY[10], $grille);

        // debug($res);
        

        return $this->render('map/index.html.twig', [
            'controller_name' => 'MapController',
            'grille' => $grille
        ]);
    }
}
