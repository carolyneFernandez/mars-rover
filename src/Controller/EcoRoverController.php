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
        
        // définition de la position de départ et d'arrivé
        $posX = 1;$posY = 4;
        $rover->setPosX($posX)->setPosY($posY);
        $destX = 9;$destY = 8;
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
            $nextCase = $this->move($map, $rover, $destination);
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

    public function move($map, $rover, $destination) {

        //Initialisation des cases adjacentes
        $adjCases = array();
        // Haut gauche
        if (isset($map[$rover->getPosY()+1][$rover->getPosX()-1])) {
            $adjCases[$rover->getPosY()+1][$rover->getPosX()-1] = $map[$rover->getPosY()+1][$rover->getPosX()-1];
        }
        // Haut
        if (isset($map[$rover->getPosY()+1][$rover->getPosX()])) {
            $adjCases[$rover->getPosY()+1][$rover->getPosX()] = $map[$rover->getPosY()+1][$rover->getPosX()];
        }
        // Haut droite
        if (isset($map[$rover->getPosY()+1][$rover->getPosX()+1])) {
            $adjCases[$rover->getPosY()+1][$rover->getPosX()+1] = $map[$rover->getPosY()+1][$rover->getPosX()+1];
        }
        // Droite
        if (isset($map[$rover->getPosY()][$rover->getPosX()+1])) {
            $adjCases[$rover->getPosY()][$rover->getPosX()+1] = $map[$rover->getPosY()][$rover->getPosX()+1];
        }
        // Bas droite
        if (isset($map[$rover->getPosY()-1][$rover->getPosX()+1])) {
            $adjCases[$rover->getPosY()-1][$rover->getPosX()+1] = $map[$rover->getPosY()-1][$rover->getPosX()+1];
        }
        // Bas
        if (isset($map[$rover->getPosY()-1][$rover->getPosX()])) {
            $adjCases[$rover->getPosY()-1][$rover->getPosX()] = $map[$rover->getPosY()-1][$rover->getPosX()];
        }
        // Bas gauche
        if (isset($map[$rover->getPosY()-1][$rover->getPosX()-1])) {
            $adjCases[$rover->getPosY()-1][$rover->getPosX()-1] = $map[$rover->getPosY()-1][$rover->getPosX()-1];
        }
        // Gauche
        if (isset($map[$rover->getPosY()][$rover->getPosX()-1])) {
            $adjCases[$rover->getPosY()][$rover->getPosX()-1] = $map[$rover->getPosY()][$rover->getPosX()-1];
        }
        $rover->setAdjCases($adjCases);

        // recupere toutes les cases (rayon de 3) dans la direction de la destination
        $direction = $rover->brensenham($rover->getPosX(), $rover->getPosY(), $destination['x'], $destination['y'], true);

        // pour ne pas manquer les cases de glace adjacente
        foreach ($rover->getAdjCases() as $y => $row) {
            foreach ($row as $x => $value) {
               $direction[$y][$x] = true;
            }
        }
        
        // recupere les blocs de glace qui se trouvent dans la bonne direction
        $caseFound = false;
        foreach ($direction as $y => $case) {
            foreach ($case as $x => $value) {
                // si aucune case n'a été trouvé, que c'est une case de glace et qu'elle n'a pas été consummée
                if ($caseFound == false && (isset($map[$y][$x]['content']) && $map[$y][$x]['content'] == 1) && !isset($rover->getIceConsumed()[$y][$x])){
                    $nextIceCase['x'] = $x;
                    $nextIceCase['y'] = $y;
                    $caseFound = true;
                }
               
            }
        }
        
        // si aucune case de glace dans la direction, la prochaine direction est la destination
        if (!isset($nextIceCase)) {
            $nextIceCase['x'] = $destination['x'];
            $nextIceCase['y'] = $destination['y'];
        }

        // détermine la longueur du chemin jusqu'à la case de glace pour chaque case adjacente afin de savoir lesquels sont les plus courts
        $orderedAdjCases = array();
        foreach ($rover->getAdjCases() as $y => $case) {
            foreach ($case as $x => $value) {
                // si la case de glace n'a pas été consommée
                if (!isset($rover->getIceConsumed()[$y][$x])) {
                    // si la case est la destination, alors on est arrivé
                    if ($x === $destination['x'] && $y === $destination['y']) {
                        return ['x' => $x, 'y' => $y, 'arrived' => true];
                    }
                    // si un des blocs adjacents est le bloc de glace alors c'est la prochaine case
                    if ($x === $nextIceCase['x'] && $y === $nextIceCase['y']) {
                        $rover->addIceConsumed($x, $y);
                        return ['x' => $x, 'y' => $y, 'direction' => $direction];
                    }
                    // calcul longueur chemin
                    $pathLength = 0;
                    $pathToIce = $rover->brensenham($x, $y, $nextIceCase['x'], $nextIceCase['y']);
                    foreach ($pathToIce as $case) {
                        foreach ($case as $value) {
                            $pathLength++;
                        }
                    }
                    // tableau de case adjacente de la plus proche a la plus éloigné, la première clé étant l'indice de distance
                    $orderedAdjCases[$pathLength][$y][$x] = true;
                }
            }
        }
        // trie le tableau : clés par ordre croissantes
        ksort($orderedAdjCases);

        // choix de la case : si une case n'est pas praticable alors on la place dans ignoredCases et on la retire de orderedAdjCases. On essaye alors de se déplacer sur la premiere case de orderedAdjCases, si ce n'est pas possible on réitère l'opération.
        
        $nextCase = $rover->brensenham($rover->getPosX(), $rover->getPosY(), $nextIceCase['x'], $nextIceCase['y'], false, true)[1];
        $ignoredAdjCases = array();

        // chemin le plus direct
        foreach ($nextCase as $y => $row) {
            foreach ($row as $x => $value) {
                // si la case de glace n'a pas été consommée
                if (!isset($rover->getIceConsumed()[$y][$x])) {
                    // si la pente est trop abrupte : A DEFINIR
                    if(isset($map[$y][$x]['z-index'])) {
                        array_push($ignoredAdjCases, ['x' => $x, 'y' => $y]);
                    }else {
                        return ['x' => $x, 'y' => $y, 'direction' => $direction];
                    }
                }
            }
        }
        
        // sinon choisit une autre case adjacente parmis celles praticable
        foreach ($orderedAdjCases as $key => $index) {
            foreach ($index as $y => $row) {
                foreach ($row as $x => $value) {
                    // si la case de glace n'a pas été consommée
                    if (!isset($rover->getIceConsumed()[$y][$x])) {
                        // si la pente est trop abrupte : A DEFINIR
                        if(isset($map[$y][$x]['z-index'])) {
                            array_push($ignoredAdjCases, ['x' => $x, 'y' => $y]);
                        }else {
                            return ['x' => $x, 'y' => $y, 'direction' => $direction];
                        }
                    }
                }
            }
        }
    }

}


