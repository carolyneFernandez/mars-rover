<?php

namespace App\Entity;

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

        $road = $this->brensenham($this->getPosX(),$this->getPosY(), 3,9  );
        dump($road);

        $i = 0;
        $pentes = [];
        $precedX = $this->getPosX();
        $precedY = $this->getPosY();

        foreach ($road as $y => $x){

            if($i!=0){
                $pentes[intval($y)][intval($x)] = $this->calculPente($this->getPosZ(), $this->requestGetZ(intval($x), intval($y)) , $this->calculDistance($precedX, $precedY, $x, $y)  );
            }

            $precedX = $x;
            $precedY = $y;
            $i++;
        }

        dump($pentes);

        return $road;

    }


    public function calculDistance($xOr, $yOr, $xDest, $yDest)
    {
        if($xOr == $xDest || $yOr == $yDest){
            return 1;
        }else{
            return 1.4;
        }

//        $pentes[$x2Dest . ',' . $y2Dest] = $this->calculPente($this->requestGetZ($x1Ori, $y1Ori), $this->requestGetZ($x2Dest, $y2Dest), $distance);

    }



    public function calculPente($z1, $z2, $distance)
    {
        return (($z2 - $z1) / $distance);
    }


    /**
     * @param int $destX
     * @param int $destY
     * @param string $typeAction Défini si la fonction retourne toute la route ou seulement la case suivante
     * @return mixed
     */
    public function calculRoad(int $destX, int $destY, string $typeAction="all")
    {

//        Initialisation des points :
        $x1 = $this->getPosX();
        $y1 = $this->getPosY();
        $x2 = $destX;
        $y2 = $destY;
//        Bresenan fonctionne avec 8 cas. tranches de pi/4
//        Si en absisse x2 est devant x1, on les inverse --> 4 cas en moins
        if ($x2 < $x1) {
            $c = $x1;
            $d = $y1;
            $x1 = $x2;
            $y1 = $y2;
            $x2 = $c;
            $y2 = $d;
        }


// On colore le point de départ et d'arriver
        $tableau[$x1 . "," . $y1] = [0];


// $u et $v forme le curseur
        $u = $x1;
        $v = $y1;


// Si c'est une droite verticale :
        if ($x1 == $x2) {
            if ($y2 > $y1) {
//                droite verticale vers le bas
                while ($v <= $y2) {
                    $tableau[$x1 . "," . $v] = [1];


                    $v++;
                }
            } else {
//                droite verticale vers le haut. (en inversant les points)
                $c = $x1;
                $d = $y1;
                $x1 = $x2;
                $y1 = $y2;
                $x2 = $c;
                $y2 = $d;
                $v = $y1;
                while ($v <= $y2) {
                    $tableau[$x1 . "," . $v] = [1];
                    $v++;
                }
            }
//sinon :
        } else {
            if ($y2 - $y1 != 0) {
                $coeff = ($y2 - $y1) / ($x2 - $x1);
            } else {
                $coeff = 0;
            }
//            dump($coeff);
            if ($coeff >= 2) {
//                entre 3pi/2 et 7pi/4
                $coeff = ($x2 - $x1) / ($y2 - $y1);
                while ($v < $y2) {
                    $v++;
                    $u = $u + $coeff;
                    $tableau[round($u) . ',' . $v] = [1.4];
                }

            } elseif ($coeff <= -2) {
//                entre pi/4 et pi/2
                $coeff = ($x2 - $x1) / ($y2 - $y1);
                while ($v > $y2) {
                    $v--;
                    $u = $u - $coeff; // -- = +
                    $tableau[round($u) . ',' . $v] = [1.4];
                }
            } elseif ($coeff > -2 && $coeff < 0) {
//                entre 0 et pi/4
                while ($u < $x2) {
                    $u_preced = $u;
                    dump("yoo");
//                    $v_preced = round($v);
                    $u++;
                    $v_preced = $v;
                    $v = $v + $coeff;


//                    En fonction du coeff, si la position du curseur ne touche pas le point précédent, on place un point avant
                    if ($coeff < -1 && $coeff > -2) {
                        if (round($v) - $v_preced != 1 && $u_preced + $u != 1) {
                            $tableau[$u . "," . (round($v_preced) - 1)] = [1];
                        }
                    }
                    $tableau[$u . ',' . round($v)] = [1.4];
                }

            } elseif ($coeff > 0) {
//                Sinon, dernier cas : entre 7pi/4 et 0 (ou 2pi)
                while ($u < $x2) {
                    $u_preced = $u;
//                    $v_preced = round($v);
                    $u++;
                    $v_preced = $v;
                    $v = $v + $coeff;

                    $tableau[$u . ',' . round($v)] = [1.4];

//                    En fonction du coeff, si la position du curseur ne touche pas le point précédent, on place un point avant
                    if ($coeff > 1 && $coeff < 2) {
                        if (round($v) - $v_preced != 1 && $u_preced - $u != 1) {
                            $tableau[$u . "," . (round($v_preced) + 1)] = [1];
                        }
                    }

                }
            }
//            switch ($coeff) {
//
//                case ($coeff>=2):
//                    $coeff=($x2-$x1)/($y2-$y1);
////            echo "coeff : ".$coeff."<br/>";
//                    while ($v<$y2){
//                        $v++;
//                        $u=$u+$coeff;
//                        $tableau[round($u).','.$v]=true;
////                echo $u."<br/>";
//                    }
//                    break;
//
//                default:
//                    while ($u<$x2){
//                        $u_preced=$u;
//                        $v_preced=round($v);
//                        $u++;
//                        $v_preced=$v;
//                        $v=$v+$coeff;
//
//                        $tableau[$u.','.round($v)]=true;
////                echo $v." (".$u.','.round($v).")<br/>";
//
//                        //En fonction du coeff, si la position du curseur ne touche pas le point précédent, on place un point avant
//                        switch($coeff){
//                            case ($coeff>1 && $coeff<2):
//                                if(round($v)-$v_preced!=1 && $u_preced-$u!=1){
//                                    $tableau[$u.",".(round($v_preced)+1)]=true;
//                                }
//                                break;
//                            case ($coeff>2):
//                                if(round($v)-$v_preced!=1 && $u_preced-$u!=1){
//                                    $tableau[$u_preced.",".(round($v_preced)+1)]=true;
//                                }
//                                break;
//                            case ($coeff>=1 && $coeff<2):
//                                if(round($v)-$v_preced!=-1 && $u_preced-$u!=1){
//                                    $tableau[$u_preced.",".(round($v_preced)-1)]=true;
//                                }
//                                break;
//
//                        }
//
//                    }
//                    break;
//            }

        }

        // On color la case d'arrivée
        $tableau[$x2 . "," . $y2] = true;

        if ($typeAction == "first"){
            $coor = array_keys($tableau);
            $tab[$coor[0]] = $tableau[$coor[0]];
            $tab[$coor[1]] = $tableau[$coor[1]];
            $tableau = $tab;
            dump($tab);
        }

        return $tableau;

    }


    /**
     * @param $posX
     * @param $posY
     * @param $destX
     * @param $destY
     * @param bool $direction
     * @param bool $turn
     * @return mixed
     */
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
        $path[$y][$x] = true;
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


