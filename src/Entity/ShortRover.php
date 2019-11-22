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
                    $s .= '<td>'.$value['path'].$value[0].$value[1].'</td>';
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
        for($i = $y1 ; $i <= $y2; $i ++) {
        
            if($energy < 4.5){
                 $this->deplacement=false;
             }
             if($this->deplacement==true){
               // $tab[$i][$x]['path'] = "V";

            }
            
             if($i ==  $y2){
                 $this->deplacement=false;
             }
            
            if($this->deplacement==true){



                if($this->calculteMovimentV($tab,$i,$x,$energy)){
                    $tab[$i][$x]['path'] = "V";
                }else{
                   
                    $this->calculteMovimentV($tab,$i,$x+1,$energy);
                }
                                
            }
        }
        return $tab;
    }


    public function calculteMovimentV($tab,$i,$x,$energy){
        $z1=$tab[round($i)][$x][0];//hateur actualle
        $z2=$tab[round($i+1)][$x][0];//hateur suivant
        $mateialCost= $this->constEnergy[$tab[round($i)][$x][1]][0];
        if($mateialCost==0){
            $energy+=15;
        }
       $pont=abs($z2-$z1)/$this->distance;//ponemos la pendiente con 2 decimales
       echo $pont.' ';
       if($pont <3){
            $distanceCost=($this->distance*(1+$pont)*$mateialCost) ;  
            $energy=$energy-$distanceCost;
            return true;                    
        }else{
           // $tab[$i][$y1]['path'] = "o";
            return false;
        }
    }
    
    
}
?>

