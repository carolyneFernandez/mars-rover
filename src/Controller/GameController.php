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

        $x1=0;
        $y1=0;
        
        //flag point 
        $x2=4;
        $y2=4;
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
            $table = $this->ligne_v($x1,$y1,$y2,$table);
    
        }else if($y1==$y2){
            $table = $this->ligne_h($y1,$x1,$x2,$table);
    
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

    public function ligne_v($x, $y1, $y2, $tab) 
    {
        for($i = $y1 ; $i <= $y2; $i ++) {
            $tab[$i][$x]['path'] = "o";
        }
        return $tab;
    }
    /**
     * function that will find the way if the line is vertical
     */
    public function ligne_h($y, $x1, $x2, $tab) 
    {
        for($i = $x1 ; $i <= $x2; $i ++) {
            $tab[$y][$i]['path'] = "o";
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
                $z1=$tab[$py][round($i)][0];
                $z2=$tab[$py+1][round($i+1)][0];
              
                $mateialCost= $constEnergy[$tab[round($px)][$i][1]][0];
               
                $tab[round($py)][$i]['path'] = " ";
                if(round($py)==round($py + $diff_x)-1){
                    $this->distance=1.4;
                }

                if($i<$y2){
                    $pont=($z2-$z1)/$this->distance;//ponemos la pendiente con 2 decimales
                    $pont=abs(round($pont,2));
                   echo $z2.' - '.$z1.' = '.$pont ." ";
                    if($pont <100){
                     //  echo $pont." ";
                        $distanceCost=round(($this->distance*(1+$pont)*$mateialCost),2) ;
                       // echo $distanceCost." ";
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
                       echo $pont." ";
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
   /* background: black;*/
}
.blanc{
    background: white;

}
</style>