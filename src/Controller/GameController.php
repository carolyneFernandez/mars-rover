<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ShortRover;

class GameController extends AbstractController
{
    const CONTENTS = array(
        '1' => [0,'glace'],
        '2' => [1.1,'roche'],
        '3' => [1.5,'sable'],
        '4' => [1.2,'minerai'],
        '5' => [1.3,'argile'],
        '6' => [1.2,'fer'],
        '7' => [1,'inconnue'],
    );

    
    const BONUS = array(
        '0' => 'coût x2, perd 1 tour',
        '1' => '-3 d\'énergie, perd 1 tour',
        '2' => 'recharge entre 20 et 60%',
        '3' => 'coût -50% pour les 4 prochains tours.'
    );

    private $energy=50;
    private $distance=1;
    private $deplacement=true;
    /**
     * @Route("/game", name="game")
     */
    public function index()
    {
      
        $this->choiceStep();

        return $this->render('game/index.html.twig', [
            'controller_name' => 'GameController',
        ]);
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
        $constEnergy=GameController::CONTENTS;
        for($i = $y1 ; $i <= $y2; $i ++) {
        
            if($energy < 4.5){
                 $this->deplacement=false;
             }
             if($this->deplacement==true){
                $tab[$i][$x]['path'] = "V";

            }
             if($i ==  $y2){
                 $this->deplacement=false;
 
             }
            if($this->deplacement==true){
                if(!$this->calculteMovimentV($tab,$i,$x,$constEnergy,$energy)){
                    $tab[$i][$y1]['path'] = "o";

                }
                                
            }
 
           
        }
        return $tab;
    }

    public function calculteMovimentV($tab,$i,$x,$constEnergy,$energy){
        $z1=$tab[round($i)][$x][0];//hateur actualle
        $z2=$tab[round($i+1)][$x][0];//hateur suivant
        $mateialCost= $constEnergy[$tab[round($i)][$x][1]][0];
        if($mateialCost==0){
            $energy+=15;
        }
       $pont=abs($z2-$z1)/$this->distance;//ponemos la pendiente con 2 decimales
       if($pont <3){

            $distanceCost=($this->distance*(1+$pont)*$mateialCost) ;  
            $energy=$energy-$distanceCost;
            return true;                    
        }else{
           // $tab[$i][$y1]['path'] = "o";
            return false;
        }
    }

    public function changePositionV($y){
        $y=$y+1;
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