<?php

namespace App\Service;

class EcoRoverService {

    private function setUpAdjCases($map, $rover) {
        //Initialisation des cases adjacentes
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


    

    public function orderAdjCases($rover, $nextIceCase, $direction, $destination) {
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

        return $orderedAdjCases;
    }

    private function getNextIceCase($direction, $map, $destination, $rover) {
        // recupere les blocs de glace qui se trouvent dans la bonne direction
        $caseFound = false;
        foreach ($direction as $y => $case) {
            foreach ($case as $x => $value) {
                // ---> DETERMINATION COUT DE LA COMPOSITION DU SOL
                
                //scan sur adjacent !!!!!!!! finalement : pas compter comme un scan car les blocs de glaces seront transmis par l'api
                // if (isset($rover->getAdjCases()[$y][$x]))
                // {
                //     $cost += 0.2;
                // } else {
                //     $cost += 0.4;
                // }
                
                // si aucune case n'a été trouvé, que c'est une case de glace et qu'elle n'a pas été consummée
                if ($caseFound == false && (isset($map[$y][$x]['content']) && $map[$y][$x]['content'] == 1) && !isset($rover->getIceConsumed()[$y][$x])) {
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

        return $nextIceCase;
    }

    private function getNextCase($rover, $nextIceCase, $direction, $map, $orderedAdjCases) {
        
        // choix de la case : si une case n'est pas praticable alors on la place dans ignoredCases et on la retire de orderedAdjCases. On essaye alors de se déplacer sur la premiere case de orderedAdjCases, si ce n'est pas possible on réitère l'opération.

        $cost = 0; //a remove

        $nextCase = $rover->brensenham($rover->getPosX(), $rover->getPosY(), $nextIceCase['x'], $nextIceCase['y'], false, true)[1];
        $ignoredAdjCases = array();

        // chemin le plus direct
        foreach ($nextCase as $y => $row) {
            foreach ($row as $x => $value) {
                // si la case de glace n'a pas été consommée
                if (!isset($rover->getIceConsumed()[$y][$x])) {
                    // si la pente est trop abrupte : A DEFINIR
                    // ---> DETERMINATION DE LA PENTE
                    //definir si la case scanné est horizontale/verticale ou diagonale
                    $cost += 1;
                    if (isset($map[$y][$x]['z-index'])) {
                        array_push($ignoredAdjCases, ['x' => $x, 'y' => $y]);
                    } else {
                        //par defaut : 1  A DEFINIR !!
                        $cost += 1;
                        dump($cost);
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
                        // ---> DETERMINATION DE LA PENTE
                        //definir si la case scanné est horizontale/verticale ou diagonale
                        $cost += 1;
                        if (isset($map[$y][$x]['z-index'])) {
                            array_push($ignoredAdjCases, ['x' => $x, 'y' => $y]);
                        } else {
                            // ---> DETERMINATION DE LA DIRECTION
                            //par defaut : 1  A DEFINIR !!
                            $cost += 1;
                            dump($cost);
                            $rover->setEnergy($rover->getEnergy() - $cost);
                            return ['x' => $x, 'y' => $y, 'direction' => $direction];
                        }
                    }
                }
            }
        }
    }
    
    public function move($map, $rover, $destination)
    {
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
}