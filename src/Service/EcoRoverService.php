<?php

namespace App\Service;

use App\Controller\GameController;

class EcoRoverService {

    /**
     * Initialise les cases adjacentes.
     * @param array $map
     * @param Rover $rover
     * @return Rover
     */
    private function setUpAdjCases($map, $rover) {
        $adjCases = array();
        // Haut gauche
        if (isset($map[$rover->getPosY() + 1][$rover->getPosX() - 1])) {
            $adjCases[$rover->getPosY() + 1][$rover->getPosX() - 1] = $map[$rover->getPosY() + 1][$rover->getPosX() - 1];
        }
        // Haut
        if (isset($map[$rover->getPosY() + 1][$rover->getPosX()])) {
            $adjCases[$rover->getPosY() + 1][$rover->getPosX()] = $map[$rover->getPosY() + 1][$rover->getPosX()];
        }
        // Haut droite
        if (isset($map[$rover->getPosY() + 1][$rover->getPosX() + 1])) {
            $adjCases[$rover->getPosY() + 1][$rover->getPosX() + 1] = $map[$rover->getPosY() + 1][$rover->getPosX() + 1];
        }
        // Droite
        if (isset($map[$rover->getPosY()][$rover->getPosX() + 1])) {
            $adjCases[$rover->getPosY()][$rover->getPosX() + 1] = $map[$rover->getPosY()][$rover->getPosX() + 1];
        }
        // Bas droite
        if (isset($map[$rover->getPosY() - 1][$rover->getPosX() + 1])) {
            $adjCases[$rover->getPosY() - 1][$rover->getPosX() + 1] = $map[$rover->getPosY() - 1][$rover->getPosX() + 1];
        }
        // Bas
        if (isset($map[$rover->getPosY() - 1][$rover->getPosX()])) {
            $adjCases[$rover->getPosY() - 1][$rover->getPosX()] = $map[$rover->getPosY() - 1][$rover->getPosX()];
        }
        // Bas gauche
        if (isset($map[$rover->getPosY() - 1][$rover->getPosX() - 1])) {
            $adjCases[$rover->getPosY() - 1][$rover->getPosX() - 1] = $map[$rover->getPosY() - 1][$rover->getPosX() - 1];
        }
        // Gauche
        if (isset($map[$rover->getPosY()][$rover->getPosX() - 1])) {
            $adjCases[$rover->getPosY()][$rover->getPosX() - 1] = $map[$rover->getPosY()][$rover->getPosX() - 1];
        }
        $rover->setAdjCases($adjCases);

        return $rover;
    }


    
    /**
     * Détermine la longueur du chemin jusqu'à la case de glace pour chaque case adjacente, triant les chemins du plus court au plus long.
     * @param Rover $rover
     * @param array $nextIceCase : coordonnées X et Y
     * @param array $direction : cases adjacentes pratiquables ou non
     * @param array $destination : coordonnées X et Y
     * @return array
     */
    public function orderAdjCases($rover, $nextIceCase, $direction, $destination) {
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

        return $orderedAdjCases;
    }

    /**
     * Recupère les blocs de glace qui se trouvent dans la bonne direction. Si aucune glace n'est trouvé, continue sur le chemin initial.
     * @param array $direction : cases adjacentes pratiquables ou non
     * @param array $map
     * @param array $destination : coordonnées X et Y
     * @param Rover $rover
     * @return array
     */
    private function getNextIceCase($direction, $map, $destination, $rover) {
        foreach ($direction as $y => $case) {
            foreach ($case as $x => $value) {
                // si aucune case n'a été trouvé, que c'est une case de glace et qu'elle n'a pas été consummée
                if ((isset($map[$y][$x]['content']) && $map[$y][$x]['content'] == 1) && !isset($rover->getIceConsumed()[$y][$x])) {
                    $nextIceCase['x'] = $x;
                    $nextIceCase['y'] = $y;
                }
            }
        }
        // si aucune case de glace est dans la direction, la prochaine direction est la destination
        if (!isset($nextIceCase)) {
            $nextIceCase['x'] = $destination['x'];
            $nextIceCase['y'] = $destination['y'];
        }

        return $nextIceCase;
    }

    /** Choix définitif de la prochaine case du rover. Choisit en fonction de la composition (le sable est évité) et de la pente pratiquable.
     * @param Rover $rover
     * @param array $nextIceCase : coordonnées X et Y
     * @param array $direction : cases adjacentes pratiquables ou non
     * @param array $map
     * @param array $orderedAdjCases

     * @return array
     */
    private function getNextCase($rover, $nextIceCase, $direction, $map, $orderedAdjCases) {
        // Si une case n'est pas praticable alors on la place dans ignoredCases et on la retire de orderedAdjCases. 
        //On essaye alors de se déplacer sur la premiere case de orderedAdjCases, si ce n'est pas possible on réitère l'opération.
        $cost = 0; //a remove
        $nextCase = $rover->brensenham($rover->getPosX(), $rover->getPosY(), $nextIceCase['x'], $nextIceCase['y'], false, true)[1];
        $ignoredAdjCases = array();

        // chemin le plus direct
        foreach ($nextCase as $y => $row) {
            foreach ($row as $x => $value) {
                // si la case de glace n'a pas été consommée
                if (!isset($rover->getIceConsumed()[$y][$x])) {
                    // ---> VERIFICATION D'UNE PENTE CORRECTE
                    //TODO
                    $dist = $this->calculateDistance($rover->getPosX(), $rover->getPosY(), $x, $y);
                    $percentGradiant = $this->calculateGradient($rover->requestGetZ($rover->getPosX(), $rover->getPosY()), $rover->requestGetZ($x, $y), $dist, true);
                    if($percentGradiant >= 150 || $percentGradiant <= 150){
                        array_push($ignoredAdjCases, ['x' => $x, 'y' => $y]);
                    }
                    dump("PG $percentGradiant");

                    if (isset($map[$y][$x]['z-index'])) {
                        array_push($ignoredAdjCases, ['x' => $x, 'y' => $y]);
                    } else {
                        //par defaut : 1  A DEFINIR !!
                        //TODO
                        $rover->setEnergy($rover->getEnergy() - $cost);
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
                        // ---> VERIFICATION D'UNE PENTE CORRECTE
                        //TODO
                        $dist = $this->calculateDistance($rover->getPosX(), $rover->getPosY(), $x, $y);
                        $percentGradiant = $this->calculateGradient($rover->requestGetZ($rover->getPosX(), $rover->getPosY()), $rover->requestGetZ($x, $y), $dist, true);
                        if($percentGradiant >= 150 || $percentGradiant <= 150){
                            array_push($ignoredAdjCases, ['x' => $x, 'y' => $y]);
                        }
                        dump("PG $percentGradiant");
                        
                        $cost += 1;
                        if (isset($map[$y][$x]['z-index'])) {
                            array_push($ignoredAdjCases, ['x' => $x, 'y' => $y]);
                        } else {
                            // ---> DETERMINATION DE LA DIRECTION
                            //par defaut : 1  A DEFINIR !!
                            //TODO
                            $rover->setEnergy($rover->getEnergy() - $cost);
                            return ['x' => $x, 'y' => $y, 'direction' => $direction];
                        }
                    }
                }
            }
        }
    }

    
    /** Fonction principale. Retourne la prochaine case du Rover.
     * @param array $map
     * @param Rover $rover
     * @param array $destination : coordonnées X et Y
     * @return array
     */
    public function move($map, $rover, $destination) {
        $cost = 0;

        $rover = $this->setUpAdjCases($map, $rover);

        // recupere toutes les cases (rayon de 3) dans la direction de la destination
        $direction = $rover->brensenham($rover->getPosX(), $rover->getPosY(), $destination['x'], $destination['y'], true);

        // pour ne pas manquer les cases de glace adjacente, ajoute les cases adjacente a la direction
        foreach ($rover->getAdjCases() as $y => $row) {
            foreach ($row as $x => $value) {
                $direction[$y][$x] = true;
            }
        }

        $nextIceCase = $this->getNextIceCase($direction, $map, $destination, $rover);

        $res = $this->orderAdjCases($rover, $nextIceCase, $direction, $destination);

        // si la prochaine case a été trouvé pendant le classement des cases adjacentes
        if (isset($res['x']) && isset($res['y'])) {
            return $res;
        } else { //sinon c'est que les cases ont été correctent triées
            $orderedAdjCases = $res;
        }
    
        $res = $this->getNextCase($rover, $nextIceCase, $direction, $map, $orderedAdjCases);

        return $res;
    }

#################################################################################################################################################

    
    /**
     * Calcul de distance entre 2 points donnés.
     * @param int $xOr
     * @param int $yOr
     * @param int $xDest
     * @param int $yDest
     * @return float|int
     */
    public function calculateDistance(int $xOr, int $yOr, int $xDest, int $yDest)
    {
        if ($xOr == $xDest) {
            $distance = abs($yDest - $yOr) * GameController::lineDistance; // horizontale
        } elseif ($yOr == $yDest) {
            $distance = abs($xDest - $xOr) * GameController::lineDistance; // verticale
        } else {
            $distance = intval(round(sqrt(pow(abs($yDest - $yOr), 2) + pow(abs($xDest - $xOr), 2)))) * GameController::diagonaleDistance; // diagonale
        }
        $distance = intval(round($distance));

        return $distance;

    }

    /**
     * Prend le cout de déplacement pour une distance de 1 ou 1.4 avec une pente en poucentage (0,03 pour 3%)
     * @param int $xDest utilisé pour connaitre la matière (costContent)
     * @param int $yDest utilisé pour connaitre la matière (costContent)
     * @param int $gradient pas en pourcentage !!
     * @param int $distance 1 ou 1.4 (E)
     * @return float|int
     */ /*
    public function calculateCost(int $xDest, int $yDest, float $gradient, int $distance)
    {
        // E x (1+p) x costContent
        // dump($gradient);
        $content = $this->requestGetContent($xDest, $yDest);
        return round($distance / 100 * (1 + $gradient) * GameController::CONTENTS[$content][0], 2);
    } */

    /**
     * Calcul la pente entre 2 points sur une distance donnée. (attention, ne vérifie pas si variation de pente entre les points !!)
     * @param int $z1
     * @param int $z2
     * @param int $distance
     * @param bool $percent
     * @return float|int
     */
    public function calculateGradient(int $z1, int $z2, int $distance, bool $percent = false)
    {
        if ($percent == false) {
            $gradient = ($z2 - $z1) / $distance;
        } else {
            $gradient = ($z2 - $z1) / $distance * 100;
        }

        return round($gradient, 2);

    }
}