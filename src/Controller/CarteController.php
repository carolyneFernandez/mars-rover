<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CarteController extends AbstractController
{
  /**
   * @Route("/carte", name="carte")
   */
  public function index()
  {
    /* $material = x correspond à une matière. 
        Liste : 
        pour $material égale 1 : Glace
                             2 : Roche
                             3 : Sable
                             4 : Minerai
                             5 : Argile
                             6 : Fer
                             7 : Inconnu 
        */
    function setMaterial($z)
    {
      /* Si niveau de difficulté = 1 alors plus de plat que de montagne
             Si niveau de difficulté = 2 alors autant de plat que de montagne
             Si niveau de difficulté = 3 alors plus de montagne que de plat */

      if (isset($_GET['dif'])) {
        if ($_GET['dif'] == 1) {
          if ($z >= -50 && $z <= 0) {
            $material = 3;
          } else if($z > 0 && $z <= 50){
            $material = 5;
          } else {
            $material = 7;
          }
      }

        return $material;
      }

      // $material = 2;
      // if($z >= -128 && $z<= -107){
      //   $material = 5;
      // } else if($z > -107 && $z <= -86){
      //   $material = 7;
      // } else if($z > -86 && $z <= -65){
      //   $material = 2;
      // } else if($z > -65 && $z <= -44){
      //   $material = 3;
      // } else if($z > -44 && $z <= -23){
      //   $material = 3;
      // } else if($z > -23 && $z <= -2){
      //   $material = 5;
      // } else if($z > -2 && $z <= 19){
      //   $material = 3;
      // } else if($z > 19 && $z <= 40){
      //   $material = 3;
      // } else if($z > 40 && $z <= 61){
      //   $material = 6;
      // } else if($z > 61 && $z <= 83){
      //   $material = 6;
      // }else if($z > 83 && $z <= 104){
      //   $material = 2;
      // }else if($z > 104 && $z <= 127){
      //   $material = 1;
      // }
      // return $material;
    }

    function map_gen($x, $y)
    {
      if (isset($_GET['dif'])) {
        $h = $x;
        $grille = array();
        //Initialisation de la grille
        for ($i = 0; $i < $h; $i++) {
          $grille[$i] = array();
        }
        for ($i = 0; $i < $h; $i++) {
          for ($j = 0; $j < $h; $j++) {
            $grille[$i][$j] = [0, 'NULL'];
          }
        }

        switch ($_GET['dif']) {

          case 1:
            $pronfondeur = 50;
            break;

          case 2:
            $pronfondeur = 75;
            break;

          case 3:
            $pronfondeur = 100;
            break;

          default:
            $pronfondeur = 50;
            break;
        }

        $grille[0][0][0] = (int) mt_rand(-($pronfondeur), $pronfondeur);
        $grille[0][$h - 1][0] = (int) mt_rand(-($pronfondeur), $pronfondeur);
        $grille[$h - 1][0][0] = (int) mt_rand(-($pronfondeur), $pronfondeur);
        $grille[$h - 1][$h - 1][0] = (int) mt_rand(-($pronfondeur), $pronfondeur);

        $i = $h - 1;

        while ($i > 1) {
          $id = $i / 2;
          //Début de la phase Diamant
          //   dump($grille);
          for ($x = $id; $x < $h - 1; $x += $i) {
            for ($y = $id; $y < $h - 1; $y = $y + $i) {
              $moyenne = ($grille[$x - $id][$y - $id][0] + $grille[$x - $id][$y + $id][0] + $grille[$x + $id][$y + $id][0] + $grille[$x + $id][$y - $id][0]) / 4;
              $grille[$x][$y][0] = (int) ($moyenne + mt_rand(-$id, $id));
              $grille[$x][$y][1] = setMaterial($grille[$x][$y][0]);
            }
          }
          //Phase de carré
          $decalage = 0;
          for ($x = 0; $x < $h; $x = $x + $id) {
            if ($decalage == 0) {
              $decalage = $id;
            } else {
              $decalage = 0;
            }
            for ($y = $decalage; $y < $h; $y = $y + $i) {
              $somme = 0;
              $n = 0;
              if ($x >= $id) {
                $somme = $somme + $grille[$x - $id][$y][0];
                $n = $n + 1;
              }
              if ($x + $id < $h) {
                $somme = $somme + $grille[$x + $id][$y][0];
                $n = $n + 1;
              }
              if ($y >= $id) {
                $somme = $somme + $grille[$x][$y - $id][0];
                $n = $n + 1;
              }
              if ($y + $id < $h) {
                $somme = $somme + $grille[$x][$y + $id][0];
                $n = $n + 1;
              }
              $grille[$x][$y][0] = (int) ($somme / $n + mt_rand(-$id, $id));
              $grille[$x][$y][1] = setMaterial($grille[$x][$y][0]);
            }
          }
          $i = $id;
        }
        return $grille;
      }
      // $h = $x;
      // $grille = array();
      // //Initialisation de la grille
      // for ($i = 0; $i < $h; $i++) {
      //   $grille[$i] = array();
      // }
      // for ($i = 0; $i < $h; $i++) {
      //   for ($j = 0; $j < $h; $j++) {
      //     $grille[$i][$j] = [0, 'NULL'];
      //   }
      // }

      // //Affectation de nombres random aux coins de la grille
      // $grille[0][0][0] = (int) mt_rand(-128, 127);
      // $grille[0][$h - 1][0] = (int) mt_rand(-128, 127);
      // $grille[$h - 1][0][0] = (int) mt_rand(-128, 127);
      // $grille[$h - 1][$h - 1][0] = (int) mt_rand(-128, 127);

      //   $i = $h - 1;

      //   while ($i > 1) {
      //     $id = $i / 2;
      //     //Début de la phase Diamant
      //     //   dump($grille);
      //     for ($x = $id; $x < $h - 1; $x += $i) {
      //       for ($y = $id; $y < $h - 1; $y = $y + $i) {
      //         $moyenne = ($grille[$x - $id][$y - $id][0] + $grille[$x - $id][$y + $id][0] + $grille[$x + $id][$y + $id][0] + $grille[$x + $id][$y - $id][0]) / 4;
      //         $grille[$x][$y][0] = (int) ($moyenne + mt_rand(-$id, $id));
      //         $grille[$x][$y][1] = setMaterial($grille[$x][$y][0]);
      //       }
      //     }
      //     //Phase de carré
      //     $decalage = 0;
      //     for ($x = 0; $x < $h; $x = $x + $id) {
      //       if ($decalage == 0) {
      //         $decalage = $id;
      //       } else {
      //         $decalage = 0;
      //       }
      //       for ($y = $decalage; $y < $h; $y = $y + $i) {
      //         $somme = 0;
      //         $n = 0;
      //         if ($x >= $id) {
      //           $somme = $somme + $grille[$x - $id][$y][0];
      //           $n = $n + 1;
      //         }
      //         if ($x + $id < $h) {
      //           $somme = $somme + $grille[$x + $id][$y][0];
      //           $n = $n + 1;
      //         }
      //         if ($y >= $id) {
      //           $somme = $somme + $grille[$x][$y - $id][0];
      //           $n = $n + 1;
      //         }
      //         if ($y + $id < $h) {
      //           $somme = $somme + $grille[$x][$y + $id][0];
      //           $n = $n + 1;
      //         }
      //         $grille[$x][$y][0] = (int) ($somme / $n + mt_rand(-$id, $id));
      //         $grille[$x][$y][1] = setMaterial($grille[$x][$y][0]);
      //       }
      //     }
      //     $i = $id;
      //   }
      //   return $grille;
      // }
      // $difficulty = 10;
      /* $_GET['dif'] = le niveau de difficulté chosi
        case x: représente la valeur de $_GET['dif'] (1 = facile, 2 = moyen, 3 = difficile)
        */
      // if (isset($_GET['dif'])){
      //     switch ($_GET['dif']) {
      //         case 1:
      //             $difficulty = 10;
      //             break;
      //         case 2:
      //             $difficulty = 30;
      //             break;
      //         case 3:
      //             $difficulty = 50;
      //             break;

      //         default:
      //             # code...
      //             break;
      //     }
      // }
      // dump($grille);

      // return un ficher twig générant un html en traitant les données envoyé en paramètre
    }
    $grille = map_gen(500, 500);
    return $this->render('carte/index.html.twig', [
      'controller_name' => 'CarteController',
      'grille' => $grille
    ]);
  }
}
