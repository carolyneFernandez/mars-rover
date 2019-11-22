<?php

namespace App\Controller;

use App\Entity\Cases;
use App\Entity\Map;
use App\Entity\ParamMap;
use App\Form\ParametersMapType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
class DefaultController extends AbstractController

{
  /**
   * @Route("/", name="index")
   */
  public function index(Request $request)
  {
    $paramMap = new ParamMap();
    $form = $this->createForm(ParametersMapType::class, $paramMap);
    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
      $map = new Map;
      $case = new Cases;
      $paramMap->setMap($map);
      /**
       * En fonction de la difficulté, on set la profondeur de la case
       */
      switch($paramMap->getDifficulty()){
        case "Facile":
          $case->setPosZ(50);
        break;

        case "Moyen":
          $case->setPosZ(70);
        break;

        case "Difficile":
          $case->setPosZ(100);
        break;
      }
      /**
       * Taille de la map
       */
      $map->setSizeX(10);      
      $map->setSizeY(10);
      
      $this->map_gen( $map->getSizeX(), $map->getSizeY(), $case->getPosZ());
      
    }

    return $this->render('index.html.twig', [
        'controller_name' => 'DefaultController',
        'form'=>$form->createView()
    ]);
  }

  public function map_gen($x, $y, $z){
    $h = $x;
    $grille = array();

    /**
     * Initialisation de la grille
     */
    for ($i = 0; $i < $h; $i++) {
      $grille[$i] = array();
    }
    for ($i = 0; $i < $h; $i++) {
      for ($j = 0; $j < $h; $j++) {
        $grille[$i][$j] = [0, 'NULL'];
      }
    }

    /** Initialisation des 4 coins de la grille en générant une profondeur aléatoire en fonction 
    * du niveau de la map.
    * $grille[y][x][z]
    */

    $grille[0][0][0] = (int) mt_rand(-($z), $z);
    $grille[0][$h - 1][0] = (int) mt_rand(-($z), $z);
    $grille[$h - 1][0][0] = (int) mt_rand(-($z), $z);
    $grille[$h - 1][$h - 1][0] = (int) mt_rand(-($z), $z);
    
    echo "<pre>";
    var_dump($grille);
    echo "</pre>";

    return $grille;

  }

}