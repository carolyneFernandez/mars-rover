<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\EcoRover;
use App\Service\EcoRoverService;

class EcoRoverController extends AbstractController
{
    const CONTENTS = array(
        '1' => 'glace',
        '2' => 'roche',
        '3' => 'sable',
        '4' => 'minerai',
        '5' => 'argile',
        '6' => 'fer',
        '7' => 'inconnue'
    );

    const COST_CONTENT = array(
        '1' => 0,
        '2' => 1.1,
        '3' => 1.5,
        '4' => 1.2,
        '5' => 1.3,
        '6' => 1.2,
        '7' => 1
    );

    const BONUS = array(
        '0' => 'coût x2, perd 1 tour',
        '1' => '-3 d\'énergie, perd 1 tour',
        '2' => 'recharge entre 20 et 60%',
        '3' => 'coût -50% pour les 4 prochains tours.'
    );

    /**
     * @Route("/ecoRover", name="eco_rover")
     */
    public function index(EcoRoverService $ecoRoverService)
    {
        $file = file_get_contents("../assets/json/map.json");
        $iceFile = file_get_contents("../assets/json/ice.json");
        $iceCasesJSON = json_decode($iceFile);
        //Initialise le tableau avec les cases de glace
        foreach ($iceCasesJSON as $key => $case) {
            $res = explode(",", $case[0]);
            $iceCases[$res[1]][$res[0]] = $case[1];
        }
        $map = json_decode($file, true);
        $rover = new EcoRover();

        // définition de la position de départ et d'arrivé
        $posX = 6;
        $posY = 6;
        $rover->setPosX($posX)->setPosY($posY);
        $destX = 0;
        $destY = 5;
        $map[$posY][$posX]['start'] = true;
        $map[$destY][$destX]['end'] = true;
        $destination['x'] = $destX;
        $destination['y'] = $destY;


        // place les cases de glace sur la carte
        foreach ($iceCases as $y => $case) {
            foreach ($case as $x => $value) {
                $map[$y][$x]['content'] = 1; //1 = glace
            }
        }

        //place des murs de glace sur la carte 
        $map[4][2]['z-index'] = 99;
        $map[5][4]['z-index'] = 99;


        // pour chaque renvoie de la fonction de déplacement voulu

        // utiliser cette boucle pour debug
        // for ($i=0; $i < 5; $i++) { 
        //     // dd($destination);
        //     $nextCase = $this->move($map, $rover, $destination);
        //     $rover->setPosX($nextCase['x']);
        //     $rover->setPosY($nextCase['y']);
        //     // dump($nextCase);
        //     $path[$nextCase['y']][$nextCase['x']] = true;
        // }

        // boucle pour la version de prod
        $arrived = false;
        while ($arrived === false) {
            $nextCase = $ecoRoverService->move($map, $rover, $destination);
            $rover->setPosX($nextCase['x']);
            $rover->setPosY($nextCase['y']);
            $path[$nextCase['y']][$nextCase['x']] = true;
            if (isset($nextCase['arrived']) && $nextCase['arrived'] === true) {
                $arrived = true;
            }
        }

        // trace la direction du scan sur la carte, décommenter quand effectué avec la boucle FOR
        // foreach ($nextCase['direction'] as $y => $value) {
        //     foreach ($value as $x => $v) {
        //         $map[$y][$x]['path'] = true;
        //     }
        // }


        // trace le chemin parcouru sur la carte
        foreach ($path as $y => $row) {
            foreach ($row as $x => $value) {
                $map[$y][$x]['isPath'] = true;
            }
        }


        return $this->render('eco_rover/index.html.twig', [
            'controller_name' => 'EcoRoverController',
            'map' => $map
        ]);
    }

    
}
