<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\EcoRover;

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
    public function index()
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
        
        // Définition de la position de départ et d'arrivé
        $posX = 1;$posY = 4;
        $destX = 9;$destY = 8;
        $map[$posY][$posX]['start'] = true;
        $map[$destY][$destX]['end'] = true;

        $rover->setPosX($posX)->setPosY($posY);

        //Initialisation des cases adjacentes
        $adjCases = array();
        // Haut gauche
        $adjCases[$posY+1][$posX-1] = $map[$posY+1][$posX-1];
        // array_push($adjCases, $map[$posY+1][$posX-1]);
        // Haut
        $adjCases[$posY+1][$posX] = $map[$posY+1][$posX];
        // array_push($adjCases, $map[$posY+1][$posX]);
        // Haut droite
        $adjCases[$posY+1][$posX+1] = $map[$posY+1][$posX+1];
        //array_push($adjCases, $map[$posY+1][$posX+1]);
        // Droite
        $adjCases[$posY][$posX+1] = $map[$posY][$posX+1];
        //array_push($adjCases, $map[$posY][$posX+1]);
        // Bas droite
        $adjCases[$posY-1][$posX+1] = $map[$posY-1][$posX+1];
        //array_push($adjCases, $map[$posY-1][$posX+1]);
        // Bas
        $adjCases[$posY-1][$posX] = $map[$posY-1][$posX];
        //array_push($adjCases, $map[$posY-1][$posX]);
        // Bas gauche
        $adjCases[$posY-1][$posX-1] = $map[$posY-1][$posX-1];
        //array_push($adjCases, $map[$posY-1][$posX-1]);
        // Gauche
        $adjCases[$posY][$posX-1] = $map[$posY][$posX-1];
        //array_push($adjCases, $map[$posY][$posX-1]);
        $rover->setAdjCases($adjCases);

        $direction = $rover->brensenham($posX, $posY, $destX, $destY, true);

        // trace le segment de bresenham sur la carte
        // foreach ($direction as $y => $value) {
        //     foreach ($value as $x => $v) {
        //         $map[$y][$x]['path'] = true;
        //     }
        // }
        
        // place les cases de glace sur la carte
        foreach ($iceCases as $y => $case) {
            foreach ($case as $x => $value) {
                $map[$y][$x]['content'] = 1; //1 = glace
            }
        }
        
        // recupere les blocs de glaces qui se trouvent dans la bonne direction
        // $i = 0;
        $caseFound = false;
        foreach ($direction as $y => $case) {
            foreach ($case as $x => $value) {
                if($caseFound == false && isset($iceCases[$y][$x])){

                    // $validIceCases[$i]['x'] = $x;
                    // $validIceCases[$i]['y'] = $y;
                    // $i++;
                    $firstIceCase['x'] = $x;
                    $firstIceCase['y'] = $y;
                    $caseFound = true;
                }
            }
        }

        // Calcul de la longeur du chemin jusqu'à chaque blocs de glace
        // foreach ($validIceCases as $key => $axe) {
        //     $pathLength = 0;
        //     $pathToIce = $rover->brensenham($posX, $posY, $axe['x'], $axe['y']);
        //     foreach ($pathToIce as $y => $case) {
        //         foreach ($case as $x => $value) {
        //             $pathLength++;
        //         }
        //     }
        //     $validIceCases[$key]['pathLength'] = $pathLength;
        // }

        
        // trace le segment de bresenham sur la carte jusqu'a la premiere case de glace
        $pathToIce = $rover->brensenham($posX, $posY, $firstIceCase['x'], $firstIceCase['y']);
        foreach ($pathToIce as $y => $value) {
            foreach ($value as $x => $v) {
                $map[$y][$x]['path'] = true;
            }
        }

        //determine la longueur du chemin jusqu'a la case de glace pour chaque case adjacente
        $orderedAdjCases = array();
        foreach ($rover->getAdjCases() as $y => $case) {
            foreach ($case as $x => $value) {
                $pathLength = 0;
                $pathToIce = $rover->brensenham($x, $y, $firstIceCase['x'], $firstIceCase['y']);
                foreach ($pathToIce as $case) {
                    foreach ($case as $value) {
                        $pathLength++;
                    }
                }
                //tableau de case adjacente de la plus proche a la plus éloigné, la premiere clé étant l'indice de distance
                $orderedAdjCases[$pathLength][$y][$x] = true;
            }
        }
        //trie le tableau : clés par ordre croissantes
        ksort($orderedAdjCases);


        //si une case n'est pas pratiquable alors on la place dans ignoredCases et on la retire de orderedCases. On essaye alors de se déplacer sur la premiere case de orderedCase, si ce n'est pas possible on réitère l'opération.

        $ignoredAdjCases = array();

        // [...] A CODER 
        
        return $this->render('eco_rover/index.html.twig', [
            'controller_name' => 'EcoRoverController',
            'map' => $map
        ]);
    }

}
