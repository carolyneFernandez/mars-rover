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
    private $energy=100;
    private $distance=1;
    private $deplacement=true;

    private $constEnergy = GameController::CONTENTS;

    private $path;

    public function getId():?int
    {
        return $this->id;
    }



    public function choiceStep()
    {

        $url = './../assets/json/carte/map.json'; // path to your JSON file
        $data = file_get_contents($url); // put the contents of the file into a variable
        $table = json_decode($data,true); //

        $x1=1;//$this->getPosX();
        $y1=1;//$this->getPosY();
        
        //flag point 
        $x2=1;
        $y2=9;

        $this->run($table, $x1, $y1, $x2, $y2);
        foreach ($this->path as $case) {
            $table[$case[1]][$case[0]]['path'] = 'X';
        }

        $s = '<table border="1">';
        foreach ($table as $y => $x) {
            $s .= '<tr>';
            foreach ($x as $value ) {
              
                if (isset($value['path'])) {
                    $s .= '<td>'.$value['path'].$value[0].'</td>';
                }else{
                    $s .= "<td class='blanc'></td>";
                }
            }
            $s .= '</tr>';
        }
        $s .= '</table>';
        echo $s;
        
    
    }



    public function calculteMoviment ($table, $x1, $y1, $x2, $y2, $energy) {


        $z1=$table[$y1][$x1][0];//hateur actualle
        $z2=$table[$y2][$x2][0];//hateur suivant
        
        $mateialCost= $this->constEnergy[$table[$y1][$x1][1]][0];
        if($mateialCost==0){
            $energy+=15;
        }
        echo $this->distance.' ';
       $pont=abs($z2-$z1)/$this->distance;//ponemos la pendiente con 2 decimales
       if($pont <3){
            $distanceCost=($this->distance*(1+$pont)*$mateialCost) ;  
            $energy=$energy-$distanceCost;
            return true;                    
        }else{
            return false;
        }
    }


    public function run ($table, $x1, $y1, $x2, $y2) {

        $this->path = [
            [$x1, $y1]
        ];

        while ($x1 !== $x2 || $y1 !== $y2) {
            list($x1, $y1) = $this->nextCase($table, $x1, $y1, $x2, $y2);
            $this->path[] = [$x1, $y1];
        }

    }

    public function pathLength ($brensenham) {

        $length = 0;
        foreach ($brensenham as $y) {
            foreach ($y as $x) {
                $length++;
            }
        }

        return $length;
    }

    public function isVisited ($x, $y) {

        foreach ($this->path as $case)
            if ($case[0] === $x && $case[1] === $y)
                return true;

        return false;
    }

    public function nextCase ($table, $x1, $y1, $x2, $y2) {

        $allAdjs = $this->adjCases($table, $x1, $y1);

        $adjs = [];
        foreach ($allAdjs as $case) {
            if (!$this->isObstacle($table, $x1, $y1, $case[0], $case[1])) {
                $adjs[] = [$case[0], $case[1]];
            }
        }

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

        if (!$adjs) {
            throw new \Exception('Seems rover is stuck, try to change map elevation');
        }

        return $adjs[0];

    }

    public function adjCases ($table, $x, $y) {

        $allCases = [
            [$x+1, $y], //right
            [$x+1, $y-1],//right hauter
            [$x, $y-1],//top
            [$x-1, $y-1],//top left
            [$x-1, $y],//left
            [$x-1, $y+1],//down left
            [$x, $y+1],//down 
            [$x+1, $y+1]//down right
        ];

        $cases = [];
        foreach ($allCases as $case) {
            if (isset($table[$case[1]][$case[0]])) {
                $cases[] = $case;
            }
        }

        return $cases;
    }

    public function isObstacle ($table, $x1, $y1, $x2, $y2) {
        
        $isObstacle = !$this->calculteMoviment($table, $x1, $y1, $x2, $y2, $this->energy);

        // if ($isObstacle) {
        //     $this->badMoves[] = [
        //         [$x1, $y1],
        //         [$x2, $y2]
        //     ];
        // }

        return $isObstacle;
    }



    public function brensenham($posX, $posY, $destX, $destY, $direction = false, $turn = false){

        if ($posX === $destX && $posY === $destY) return [];

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
        if($dx > $dy){ //Si est plutot horizontale
            $error = $dx / 2;
            for ($i=1; $i <= $dx; $i++) { //pour chaque pixel sur la distance des absisses
                $x += $xinc;
                $error += $dy;
                if($error >= $dx){
                    $error -= $dx;
                    $y += $yinc;
                }
                
                if ($turn) {
                    $path[$i][$y][$x] = true;
                } else {
                    $path[$y][$x] = true;
                }
                //si on cherche une direction
                if($direction == true && ($y-1 >= 0 && $y+1 <= 9)){
                    if ($turn) {
                        $path[$i][$y-1][$x] = true;
                        $path[$i][$y+1][$x] = true;
                    }
                    $path[$y-1][$x] = true;
                    $path[$y+1][$x] = true;
                }
            }
        } else{ //Si est plutot verticale
            $error = $dy / 2;
            for ($i=1; $i <= $dy; $i++) { 
                $y += $yinc;
                $error += $dx;
                if($error >= $dy){
                    $error -= $dy;
                    $x += $xinc;
                }
                if ($turn) {
                    $path[$i][$y][$x] = true;
                } else {
                    $path[$y][$x] = true;
                }
                //si on cherche une direction
                if($direction == true && ($x-1 >= 0 && $x+1 <= 9)){
                    if ($turn) {
                        $path[$i][$y][$x-1] = true;
                        $path[$i][$y][$x+1] = true;
                    } 
                    $path[$y][$x-1] = true;
                    $path[$y][$x+1] = true;
                }
            }   
        }



        return $path;
    }


    
    
}
?>

<style>
td {
    width: 30px;
    height: 30px;
    text-align: center;
   /*$$ background: black;*/
}
.blanc{
    background: white;

}
</style>