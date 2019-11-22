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

      if ($z >= -100 && $z <= -85) {
        $material = 1;
      } else if ($z > -85 && $z <= -75) {
        $material = 6;
      } else if ($z > -75 && $z <= -50) {
        $material = 2;
      } else if ($z > -50 && $z <= -45) {
        $material = 4;
      } else if ($z > -45 && $z <= -25) {
        $material = 2;
      } else if ($z > -25 && $z <= -10) {
        $material = 3;
      } else if ($z > -10 && $z <= 10) {
        $material = 5;
      } else if ($z > 10 && $z <= 25) {
        $material = 3;
      } else if ($z > 25 && $z <= 45) {
        $material = 2;
      } else if ($z > 45 && $z <= 50) {
        $material = 4;
      } else if ($z > 50 && $z <= 75) {
        $material = 2;
      } else if ($z > 75 && $z <= 85) {
        $material = 6;
      } else if ($z > 85 && $z <= 100) {
        $material = 1;
      } else {
        $material = 7;
      }

      return $material;
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
        /** Initialisation de la profondeur à 50 */
        $profondeur = 50;
        /** En fonction de la difficulté, on augmente la profondeur de la carte */
        switch ($_GET['dif']) {

          case 1:
            $profondeur = 50;
            break;

          case 2:
            $profondeur = 75;
            break;

          case 3:
            $profondeur = 100;
            break;

          default:
            $profondeur = 50;
            break;
        }
        /** Initialisation des 4 coins de la grille en générant une profondeur aléatoire en fonction 
         * du niveau de la map.
         * $grille[y][x][z]
         */
        $grille[0][0][0] = (int) mt_rand(-($profondeur), $profondeur);
        $grille[0][$h - 1][0] = (int) mt_rand(-($profondeur), $profondeur);
        $grille[$h - 1][0][0] = (int) mt_rand(-($profondeur), $profondeur);
        $grille[$h - 1][$h - 1][0] = (int) mt_rand(-($profondeur), $profondeur);
        var_dump($profondeur);
        var_dump($grille[0][0][0]);
        var_dump($grille[0][$h - 1][0]);

        $i = $h - 1;

        while ($i > 1) {
          $id = $i / 2;
          //Début de la phase Diamant
          //   dump($grille);
          for ($x = $id; $x < $h - 1; $x += $i) {
            for ($y = $id; $y < $h - 1; $y = $y + $i) {
              $moyenne = ($grille[$x - $id][$y - $id][0] + $grille[$x - $id][$y + $id][0] + $grille[$x + $id][$y + $id][0] + $grille[$x + $id][$y - $id][0]) / 4;
              $grille[$x][$y][0] = (int) ($moyenne + mt_rand(-($id), $id));
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
                set_time_limit(10);
                $grille[$x][$y][0] = (int) ($somme / $n + mt_rand(-($id), $id));
                if ($grille[$x][$y][0] > $profondeur || $grille[$x][$y][0] < -$profondeur ) {
                  $grille[$x][$y][0] = (int) ($n + mt_rand(-$profondeur, $profondeur));
                }
              

              //if ($grille[$x][$y][0] > $profondeur || $grille[$x][$y][0] < -$profondeur) {
              //  $grille[$x][$y][0] = 99;
             // }

              $grille[$x][$y][1] = setMaterial($grille[$x][$y][0]);
              //var_dump($grille[$x][$y][0]);
            }
          }
          $i = $id;
        }
        return $grille;
      }
    }
    $grille = map_gen(400, 400);
    //echo "<pre>";
    //print_r(json_encode($grille, JSON_FORCE_OBJECT));
    //echo "</pre>";
    // // echo "<pre>";
    // // var_dump($grille);
    // // echo "</pre>";
    return $this->render('carte/index.html.twig', [
      'controller_name' => 'CarteController',
      'grille' => $grille
    ]);
  }
}
