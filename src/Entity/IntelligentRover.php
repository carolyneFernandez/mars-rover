<?php

namespace App\Entity;


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
        $destX = 8;
        $destY = 2;
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
        $tableau[$x1 . "," . $y1] = true;
        $tableau[$x2 . "," . $y2] = true;

// $u et $v forme le curseur
        $u = $x1;
        $v = $y1;


// Si c'est une droite verticale :
        if ($x1 == $x2) {
            if ($y2 > $y1) {
//                droite verticale vers le bas
                while ($v <= $y2) {
                    $tableau[$x1 . "," . $v] = true;
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
                    $tableau[$x1 . "," . $v] = true;
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
                    $tableau[round($u) . ',' . $v] = true;
                }

            } elseif ($coeff <= -2) {
//                entre pi/4 et pi/2
                $coeff = ($x2 - $x1) / ($y2 - $y1);
                while ($v > $y2) {
                    $v--;
                    $u = $u - $coeff; // -- = +
                    $tableau[round($u) . ',' . $v] = true;
                }
            } elseif ($coeff > -2 && $coeff < 0) {
//                entre 0 et pi/4
                while ($u < $x2) {
                    $u_preced = $u;
//                    $v_preced = round($v);
                    $u++;
                    $v_preced = $v;
                    $v = $v + $coeff;

                    $tableau[$u . ',' . round($v)] = true;

//                    En fonction du coeff, si la position du curseur ne touche pas le point précédent, on place un point avant
                    if ($coeff < -1 && $coeff > -2) {
                        if (round($v) - $v_preced != 1 && $u_preced + $u != 1) {
                            $tableau[$u . "," . (round($v_preced) - 1)] = true;
                        }
                    }
                }

            } elseif ($coeff > 0) {
//                Sinon, dernier cas : entre 7pi/4 et 0 (ou 2pi)
                while ($u < $x2) {
                    $u_preced = $u;
//                    $v_preced = round($v);
                    $u++;
                    $v_preced = $v;
                    $v = $v + $coeff;

                    $tableau[$u . ',' . round($v)] = true;

//                    En fonction du coeff, si la position du curseur ne touche pas le point précédent, on place un point avant
                    if ($coeff > 1 && $coeff < 2) {
                        if (round($v) - $v_preced != 1 && $u_preced - $u != 1) {
                            $tableau[$u . "," . (round($v_preced) + 1)] = true;
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
        return $tableau;


    }

}
