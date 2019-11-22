<?php

namespace App\Entity;

use App\Controller\GameController;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IntelligentRoverRepository")
 */
class IntelligentRover extends Rover
{

    /**
     * Algorithme qui choisira le prochain coup en fonction de son type de rover
     * @throws \Exception
     */
    public function choiceStep()
    {
        dump("je fais mon traitement intelligent");
        $this->requestAdjCases(999, 999, 2);

        dump($this->getAdjCases());

        $road = $this->brensenham($this->getPosX(), $this->getPosY(), $this->getDestX(), $this->getDestY());
//        dump($road);

        $i = 0;
        $gradients = [];
        $lastX = $this->getPosX();
        $lastY = $this->getPosY();
        $lastZ = $this->getPosZ();
        $costs = [];
        $gradientsPercent = [];

        foreach ($road as $y => $array) {
            foreach ($array as $x => $value) {

//            $x = array_keys($array)[0];
                $z = $this->requestGetZ(intval($x), intval($y));
                if ($i != 0) {
                    $distance = $this->calculateDistance($lastX, $lastY, $x, $y);
                    $gradientPercent = $this->calculateGradient($lastZ, $z, $distance, true);
                    $gradient = $this->calculateGradient($lastZ, $this->requestGetZ(intval($x), intval($y)), $distance, false);
                    $gradients[intval($y)][intval($x)] = $gradient;
                    $gradientsPercent[intval($y)][intval($x)] = $gradientPercent;
                    $costs[intval($y)][intval($x)] = $this->calculateCost($x, $y, $gradient, $distance);
                }

                $lastX = $x;
                $lastY = $y;
                $lastZ = $z;
                $i++;
            }
        }

        dump($gradients);
        dump($gradientsPercent);
        dump($costs);

        return [
            'road' => $road,
            'costs' => $costs,
            'gradients' => $gradientsPercent,
        ];

    }


    /**
     * Calcul de distance entre 2 points donnés
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
     */
    public function calculateCost(int $xDest, int $yDest, float $gradient, int $distance)
    {
        // E x (1+p) x costContent
//        dump($gradient);
        $content = $this->requestGetContent($xDest, $yDest);
        return round($distance / 100 * (1 + $gradient) * GameController::CONTENTS[$content][0], 2);
    }


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


    /**
     * Algo de bresenham qui trace une ligne entre 2 points
     * @param $posX
     * @param $posY
     * @param $destX
     * @param $destY
     * @param bool $direction
     * @param bool $turn
     * @return mixed
     */
    public function brensenham($posX, $posY, $destX, $destY, $direction = false, $turn = false)
    {
        $x = $posX;
        $y = $posY;
        $dx = $destX - $posX; //distance sur l'axe des abscisses
        $dy = $destY - $posY; //distance sur l'axe des ordonnees
        //direction du segment
        $xinc = $dx > 0 ? 1 : -1;
        $yinc = $dy > 0 ? 1 : -1;
        //              |
        //              |
        //   xinc: -1   |   xinc: 1
        //   yinc: 1    |   yinc: 1
        //              |
        // --------------------------------
        //              |
        //   xinc: -1   |   xinc: 1
        //   yinc: -1   |   yinc: -1
        //              |
        //              |
        $path[$y][$x] = true;
        //convertion en valeur absolue pour evaluer la pente du segment
        $dx = abs($dx);
        $dy = abs($dy);

        //selon la pente du segment
        if ($dx > $dy) { //Si est plutot horizontale
            $error = $dx / 2;
            for ($i = 1; $i <= $dx; $i++) { //pour chaque pixel sur la distance des absisses
                $x += $xinc;
                $error += $dy;
                if ($error >= $dx) {
                    $error -= $dx;
                    $y += $yinc;
                }

                if ($turn) {
                    $path[$i][$y][$x] = true;
                } else {
                    $path[$y][$x] = true;
                }
                //si on cherche une direction
                if ($direction == true && ($y - 1 >= 0 && $y + 1 <= 9)) {
                    if ($turn) {
                        $path[$i][$y - 1][$x] = true;
                        $path[$i][$y + 1][$x] = true;
                    }
                    $path[$y - 1][$x] = true;
                    $path[$y + 1][$x] = true;
                }
            }
        } else { //Si est plutot verticale
            $error = $dy / 2;
            for ($i = 1; $i <= $dy; $i++) {
                $y += $yinc;
                $error += $dx;
                if ($error >= $dy) {
                    $error -= $dy;
                    $x += $xinc;
                }
                if ($turn) {
                    $path[$i][$y][$x] = true;
                } else {
                    $path[$y][$x] = true;
                }
                //si on cherche une direction
                if ($direction == true && ($x - 1 >= 0 && $x + 1 <= 9)) {
                    if ($turn) {
                        $path[$i][$y][$x - 1] = true;
                        $path[$i][$y][$x + 1] = true;
                    }
                    $path[$y][$x - 1] = true;
                    $path[$y][$x + 1] = true;
                }
            }
        }

        return $path;
    }


}


