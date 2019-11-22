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
    

    public function getId(): ?int
    {
        return $this->id;
    }


    public function choiceStep()
    {
        $noimpo=0;

        $url = './../assets/json/carte/map.json'; // path to your JSON file
        $data = file_get_contents($url); // put the contents of the file into a variable
        $table = json_decode($data,true); //

        $x1=1;
        $y1=0;
        
        //flag point 
        $x2=1;
        $y2=9;
        // Si en absisse x2 est devant x1, on les inverse
      
       
        if($x2 < $x1){
            $c=$x1;
            $d=$y1;
            $x1=$x2;
            $y1=$y2;
            $x2=$c;
            $y2=$d;
        }else if($y2 < $y1){
            $c=$x1;
            $d=$y1;
            $x1=$x2;
            $y1=$y2;
            $x2=$c;
            $y2=$d;
            $v=$y1;
        }
        if($x2==$x1){
            $table = $this->ligne_v($x1,$y1,$y2,$this->energy,$table);
    
        }else if($y1==$y2){
            $table = $this->ligne_h($y1,$x1,$x2,$this->energy,$table);
    
        }else{
            $table=$this->ligne($x1,$y1,$x2,$y2,$this->energy,$table);
        }
    
        /**
         * creation de table
        **/
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

     /**
     * function that will find the way if the line is vertical
     */

    public function ligne_v($x, $y1, $y2,$energy ,$tab) 
    {
        $tempX=$x;
        for($i = $y1 ; $i <= $y2; $i ++) {

            if($energy < 4.5){
                 $this->deplacement=false;
             }

             if($i ==  $y2){
                 $this->deplacement=false;
             }
            
            if($this->deplacement==true){
                $z1=$tab[round($i)][$x][0];//hateur actualle
                $z2=$tab[round($i+1)][$x][0];//hateur suivant
                $mateialCost= $this->constEnergy[$tab[round($i)][$x][1]][0];

                $teppont=$this->calculateSlope($energy,$mateialCost,$z1,$z2);
                switch($teppont){
                    case 3:
                        $x=$x+1;
                        break;
                    case 2:
                        if($x!=$tempX)
                            $x=$x-1;
                    break;
                }
               
                if($this->calculateSlope($energy,$mateialCost,$z1,$z2)){
                    $tab[$i][$x]['path'] = "V";
                }
                
            }

        }
        return $tab;
    }

    
    public function calculateSlope($energy,$mateialCost,$z1,$z2){
       
        if($mateialCost==0){
            $energy+=15;
        }
       $pont=abs($z2-$z1)/$this->distance;//ponemos la pendiente con 2 decimales

       if($pont <3){
            $distanceCost=($this->distance*(1+$pont)*$mateialCost) ;  
            $energy=$energy-$distanceCost;
            return $pont;                    
        }else{
           // $tab[$i][$y1]['path'] = "o";
            return $pont;
        }

    }

 /**
     * function that will find the way if the line is vertical
     */
    public function ligne_h($y, $x1, $x2, $energy,$tab) 
    {
        $constEnergy=GameController::CONTENTS;
        for($i = $x1 ; $i <= $x2; $i ++) {
            $tab[$y][$i]['path'] = "o";

            if($i<$x2){
                $z1=$tab[$y][round($i)][0];//prender le valeur de hateur actuell
                $z2=$tab[$y][round($i+1)][0];//prendrer le valueru de hateur suivante
                $mateialCost= $constEnergy[$tab[round($y)][$i][1]][0]; //prender le valeur de materieux
                $pont=($z2-$z1)/$this->distance;//ponemos la pendiente con 2 decimales
                $pont=abs(round($pont,2)); 

                if($pont <100){
                    $distanceCost=round(($this->distance*(1+$pont)*$mateialCost),2) ;
                    $energy=$energy-$distanceCost;
                }
              
            }
        }
        return $tab;
    }



    /**
     * function that will find the path if the line is diagonal
     */
    public function ligne($x1,$y1,$x2,$y2,$energy,$tab)
    {
        $constEnergy=GameController::CONTENTS;
    
       
        $diff_x = ($y2 - $y1) / ($x2 - $x1);
        $diff_y = ($x2 - $x1) / ($y2 - $y1);

        $px = $x1;
        $py = $y1;

        if ($diff_x <= 1) {
            for($i = $x1 ; $i <= $x2; $i ++) {
                
                $tab[round($py)][$i]['path'] = "0";

                if($i<$y2){ //ponemos esto para que solo lo haga cuando se desplaze
                    $z1=$tab[$py][round($i)][0];
                    $z2=$tab[$py+1][round($i+1)][0];
                
                    $mateialCost= $constEnergy[$tab[round($px)][$i][1]][0];
                
                    if(round($py)==round($py + $diff_x)-1){
                        $this->distance=1.4;
                    }
                    $pont=($z2-$z1)/$this->distance;//ponemos la pendiente con 2 decimales
                    $pont=abs(round($pont,2));                  
                    if($pont <100){
                        $distanceCost=round(($this->distance*(1+$pont)*$mateialCost),2) ;
                        $energy=$energy-$distanceCost;
                    }
                  
                   
                }

                $py += $diff_x;
                
            }
        }
        else {

            for($i = $y1 ; $i <= $y2; $i ++) {
                $this->distance=1;
               
                $z1=$tab[round($i)][$px][0];
                $z2=$tab[round($i+1)][$px][0];
                
                $mateialCost= $constEnergy[$tab[round($py)][$i][1]][0];

                //if the distance is diagonal, we change the value of the distance
                $tab[$i][round($px)]['path'] = "";
                if(round($px)==round($px + $diff_y)-1){
                   $this->distance=1.4;
                }
                if($i<$y2){
                    $pont=($z2-$z1)/$this->distance;//ponemos la pendiente con 2 decimales
                    $pont=abs(round($pont,2));
                    if($pont <100){
                        $distanceCost=round(($this->distance*(1+$pont)*$mateialCost),2) ;
                        $energy=$energy-$distanceCost;
                    }
                  
                   
                }
                

               
                $px += $diff_y;

            }
        }

        return $tab;
    }






    public function brensenham($posX, $posY, $destX, $destY, $direction = false, $turn = false){
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