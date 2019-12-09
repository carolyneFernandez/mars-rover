<?php

namespace App\Entity;

use App\Controller\GameController;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShortRoverRepository")
 */
class ShortRover extends Rover
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    private $constEnergy = GameController::COST_CONTENT;
    private $path;
    private $visitited;
    private $countVisit;

    private $culDeSacs;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function choiceStep()
    {


        $url = './../assets/json/map.json'; // path to your JSON file
        $data = file_get_contents($url); // put the contents of the file into a variable
        $table = json_decode($data, true); //

        $memory = $this->getMemory();
        $this->countVisit = isset($memory['countVisit']) ? $memory['countVisit'] : [];
        $this->culDeSacs = isset($memory['culDeSac']) ? $memory['culDeSac'] : [];
        $this->path = isset($memory['path']) ? $memory['path'] : [];


        $result = $this->run($table, $this->getPosX(), $this->getPosY(), $this->getDestX(), $this->getDestY());
        if (!$result) {
            $result = [
                'nextX' => $this->getPosX(),
                'nextY' => $this->getPosY(),
                'energyRest' => $this->getEnergy(),
                'memory' => []
            ];
        }
//        $pathStr = '';
//        foreach ($this->path as $case) {
//            $pathStr .= '(' . implode(',', $case) . ') ';
//        }

//        $s = '<table border="1">';
//        foreach ($table as $y => $xs) {
//            $s .= '<tr>';
//            foreach ($xs as $x => $value ) {
//
//                $color = $this->isCulDeSac($x, $y) ? 'blue' : (
//                    $this->isVisited($x, $y) ? 'red' : 'white');
//
//                $s .= '<td style="background-color:' . $color . ';">'.$value[0].'</td>';
//                // if () {
//                //     $s .= '<td >'.$value['path'].$value[0].'</td>';
//                // }else{
//                //     $s .= "<td class='blanc'></td>";
//                // }
//            }
//            $s .= '</tr>';
//        }
//        $s .= '</table>';
//        echo $s;

        /*   echo 'Longueur du chemin = ' . $this->pathLength($this->path) . '<br/>';
           echo 'Chemin =' . $pathStr;
           echo '<pre>';
           var_dump ($this->countVisit);
            echo '</pre>';*/
        $arrived = false;
        if ($this->getPosX() == $this->getDestX() && $this->getPosY() == $this->getDestY()) {
            $arrived = true;
        }
        $result['arrived'] = $arrived;

        return $result;
    }


    public function calculEnergy($table, $x1, $y1, $x2, $y2)
    {

        $mateialCost = $this->constEnergy[$table[$y1][$x1][1]];
        $pendentPorcentaje = $this->porcentagePendent($table, $x1, $y1, $x2, $y2);
        $distanceMetre = $this->distanceBetweenCase($x1, $y1, $x2, $y2);

        $pendent = round($pendentPorcentaje / 100, 2);
        $distance = round($distanceMetre / 100, 2);

        if ($mateialCost == 0) {
            $this->setEnergy(GameController::energyTotal);
        }
        if ($pendent != 0) {
            $distanceCost = ($distance) * (1 + ($pendent)) * $mateialCost;

        } else {
            $distanceCost = $distance * $mateialCost;

        }
        $this->setEnergy($this->getEnergy() - $distanceCost);
        return $this->getEnergy();


    }


    public function calculateGradient(int $z1, int $z2, int $distance, bool $percent = false)
    {
        if ($percent == false) {
            $gradient = ($z2 - $z1) / $distance;
        } else {
            $gradient = ($z2 - $z1) / $distance * 100;
        }

        return round($gradient, 2);

    }


    public function porcentagePendent($table, $x1, $y1, $x2, $y2)
    {
        /** @todo 2 requêtes pour obtenir les deux z */
        $z1 = $table[$y1][$x1][0];//hateur actualle
        $z2 = $table[$y2][$x2][0];//hateur suivant
        $distance = $this->distanceBetweenCase($x1, $y1, $x2, $y2);
        $pendent = (abs($z2 - $z1) / $distance) * 100;
        return $pendent;
    }

    public function distanceBetweenCase($x1, $y1, $x2, $y2)
    {
        return $x1 === $x2 || $y1 === $y2 ? 100 : 140;
    }


    public function run($table, $x1, $y1, $x2, $y2)
    {
        $result = [];
//        $this->path = [
//            [$x1, $y1]
//        ];

        if (empty($this->countVisit)) {
            $this->countVisit = [
                $x1 . '-' . $y1 => 0
            ];
        }
        if (empty($this->path)) {
            $this->path = [
                [$x1, $y1]
            ];
        }

        $compteur = 0;


        while (($x1 !== $x2 || $y1 !== $y2) && $this->getEnergy() > 4.5) {


            $case = $this->nextCase($table, $x1, $y1, $x2, $y2);

            if (!$case) {
                $result = [
                    'nextX' => $this->getPosX(),
                    'nextY' => $this->getPosY(),
                    'energyRest' => $this->getEnergy(),
                    'memory' => [
                        'countVisit' => $this->countVisit,
                        'culDeSac' => $this->culDeSacs,
                        'path' => $this->path,
                    ],
                    'arrived' => true
                ];
                return $result;
            } // Rover is stuck

            $this->calculEnergy($table, $x1, $y1, $case[0], $case[1]);
            list($x1, $y1) = $case;
            $this->path[] = $case;

            $this->countVisit[$x1 . '-' . $y1] = isset($this->countVisit[$x1 . '-' . $y1]) ? $this->countVisit[$x1 . '-' . $y1] + 1 : 0;

            if ($compteur == 0) {
                $result = [
                    'nextX' => $case[0],
                    'nextY' => $case[1],
                    'energyRest' => $this->getEnergy(),
                    'memory' => [
                        'countVisit' => $this->countVisit,
                        'culDeSac' => $this->culDeSacs,
                        'path' => $this->path,
                    ]
                ];
                return $result;
            }
            /*   return [

             ];*/
            $compteur++;

        }

        $result = [
            'nextX' => $this->getPosX(),
            'nextY' => $this->getPosY(),
            'energyRest' => $this->getEnergy(),
            'memory' => [
                'countVisit' => $this->countVisit,
                'culDeSac' => $this->culDeSacs
            ],
            'arrived' => true
        ];

        return $result;


    }

    /** Compute length of path according to straight/diagonal moves */
    public function pathLength($path)
    {

        $length = 0;
        $prevCase = null;
        foreach ($path as $case) {

            if ($prevCase) {
                $length += $this->distanceBetweenCase($prevCase[0], $prevCase[1], $case[0], $case[1]);
            }

            $prevCase = $case;
        }

        return $length;
    }

    public function isInList(array $list, $x, $y)
    {

        foreach ($list as $case)
            if ($case[0] === $x && $case[1] === $y)
                return true;

        return false;
    }

    public function isVisited($x, $y)
    {

        return $this->isInList($this->path, $x, $y);
    }

    public function isCulDeSac($x, $y)
    {

        return $this->isInList($this->culDeSacs, $x, $y);
    }


    public function nextCase($table, $x1, $y1, $x2, $y2)
    {


        $allAdjs = $this->adjCases($table, $x1, $y1);

        $adjs = [];
        foreach ($allAdjs as $case) {

            if (!$this->isObstacle($table, $x1, $y1, $case[0], $case[1]) && !$this->isCulDeSac($case[0], $case[1])) {
                $adjs[] = [$case[0], $case[1]];
            }


        }
        if (!$adjs) {//Seems rover is stuck, try to change map elevation
            return false;
        }
        if ($this->countVisit[$x1 . '-' . $y1] == count($adjs)) {
            $this->culDeSacs[] = [$x1, $y1];

        }
        // $this->countVisit[$x1.'-'.$y1] = isset( $this->countVisit[$x1.'-'.$y1]) ?  $this->countVisit[$x1.'-'.$y1] + 1 : 0;

        //   echo count($adjs)."<br>";
        // Current case is a cul-de-sac
        if (count($adjs) === 1) {
            $this->culDeSacs[] = [$x1, $y1];
        } // If multiple opportunities, then sort by preference
        else {

            usort($adjs, function ($a, $b) use ($x2, $y2) {

                $pathA = $this->brensenham($a[0], $a[1], $x2, $y2);
                $pathB = $this->brensenham($b[0], $b[1], $x2, $y2);

                $lengthA = $this->pathLength($pathA);
                $lengthB = $this->pathLength($pathB);

                if ($lengthA === $lengthB) return 0;

                return $lengthA < $lengthB ? -1 : 1;
            });

            usort($adjs, function ($a, $b) {

                $visitedA = $this->isVisited($a[0], $a[1]) ? 1 : 0;
                $visitedB = $this->isVisited($b[0], $b[1]) ? 1 : 0;

                if ($visitedA === $visitedB) return 0;

                return $visitedA < $visitedB ? -1 : 1;
            });
        }

        return $adjs[0];
    }

    public function adjCases($table, $x, $y)
    {

        $allCases = [
            [$x + 1, $y], //right
            [$x + 1, $y - 1],//right hauter
            [$x, $y - 1],//top
            [$x - 1, $y - 1],//top left
            [$x - 1, $y],//left
            [$x - 1, $y + 1],//down left
            [$x, $y + 1],//down
            [$x + 1, $y + 1]//down right
        ];

        $cases = [];
        foreach ($allCases as $case) {
            if (isset($table[$case[1]][$case[0]])) {
                $cases[] = $case;
            }
        }

        return $cases;
    }

    public function isObstacle($table, $x1, $y1, $x2, $y2)
    {

        return $this->porcentagePendent($table, $x1, $y1, $x2, $y2) >= 150;
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

        if ($posX === $destX && $posY === $destY) return [];

        $path = [];

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
                    // $path[$y][$x] = true;
                    $path[] = [$x, $y];
                }
                //si on cherche une direction
                if ($direction == true && ($y - 1 >= 0 && $y + 1 <= 9)) {
                    if ($turn) {
                        $path[$i][$y - 1][$x] = true;
                        $path[$i][$y + 1][$x] = true;
                    }
                    // $path[$y-1][$x] = true;
                    // $path[$y+1][$x] = true;
                    $path[] = [$x, $y - 1];
                    $path[] = [$x, $y + 1];
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
                    // $path[$y][$x] = true;
                    $path[] = [$x, $y];
                }
                //si on cherche une direction
                if ($direction == true && ($x - 1 >= 0 && $x + 1 <= 9)) {
                    if ($turn) {
                        $path[$i][$y][$x - 1] = true;
                        $path[$i][$y][$x + 1] = true;
                    }
                    // $path[$y][$x-1] = true;
                    // $path[$y][$x+1] = true;
                    $path[] = [$x - 1, $y];
                    $path[] = [$x + 1, $y];
                }
            }
        }

        return $path;
    }


}


//<style>
//td {
//    width: 30px;
//    height: 30px;
//    text-align: center;
//   /*$$ background: black;*/
//}
//.blanc{
//    background: white;
//
//}
//.inicial{
//    background:red;
//}
//</style>