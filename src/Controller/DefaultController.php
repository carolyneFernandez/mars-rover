<?php

namespace App\Controller;

use App\Entity\Map;
use App\Entity\Cases;
use App\Entity\ParamMap;
use App\Entity\Materials;
use App\Form\ParametersMapType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
      $level = $paramMap->getDifficulty();
      $paramMap->setMap($map);
      /**
       * En fonction de la difficulté, on set la profondeur de la case
       */
      switch($level){
        case "Facile":
          $profondeur = 50;
        break;

        case "Moyen":
          $profondeur = 70;
        break;

        case "Difficile":
          $profondeur = 100;
        break;
      }
      /**
       * Taille de la map
       */
      $map->setSizeX(100);      
      $map->setSizeY(100);
      
      $arrayMap = $this->map_gen( $map->getSizeX(), $map->getSizeY(), $profondeur);
      
      $arrayMap = json_encode($arrayMap);
      return new JsonResponse($arrayMap, 200, [], true);
    }

    return $this->render('index.html.twig', [
        'controller_name' => 'DefaultController',
        'form'=>$form->createView()
    ]);

  }


  public function setCaseMaterial(Cases $case){

    $glace  = new Materials;
    $glace->setLabel("glace");

    $roche  = new Materials;
    $roche->setLabel("roche");

    $sable  = new Materials;
    $sable->setLabel("sable");

    $minerai  = new Materials;
    $minerai->setLabel("minerai");

    $fer  = new Materials;
    $fer->setLabel("fer");

    $inconnu  = new Materials;
    $inconnu->setLabel("inconnu");

    $argile  = new Materials;
    $argile->setLabel("argile");

    if ($case->getPosZ() >= -100 && $case->getPosZ() <= -85) {
      $case->setMaterials($glace);

    } else if ($case->getPosZ() > -85 && $case->getPosZ() <= -75) {
      $case->setMaterials($fer);

    } else if ($case->getPosZ() > -75 && $case->getPosZ() <= -50) {
      $case->setMaterials($roche);

    } else if ($case->getPosZ() > -50 && $case->getPosZ() <= -45) {
      $case->setMaterials($minerai);

    } else if ($case->getPosZ() > -45 && $case->getPosZ() <= -25) {
      $case->setMaterials($argile);

    } else if ($case->getPosZ() > -25 && $case->getPosZ() <= -10) {
      $case->setMaterials($sable);

    } else if ($case->getPosZ() > -10 && $case->getPosZ() <= 10) {
      $case->setMaterials($argile);

    } else if ($case->getPosZ() > 10 && $case->getPosZ() <= 25) {
      $case->setMaterials($sable);

    } else if ($case->getPosZ() > 25 && $case->getPosZ() <= 45) {
      $case->setMaterials($roche);

    } else if ($case->getPosZ() > 45 && $case->getPosZ() <= 50) {
      $case->setMaterials($minerai);

    } else if ($case->getPosZ() > 50 && $case->getPosZ() <= 75) {
      $case->setMaterials($roche);

    } else if ($case->getPosZ() > 75 && $case->getPosZ() <= 85) {
      $case->setMaterials($fer);

    } else if ($case->getPosZ() > 85 && $case->getPosZ() <= 100) {
      $case->setMaterials($glace);
      
    } else {
      $case->setMaterials($inconnu);
    }
  }


  public function map_gen($x, $y, $z)
  { 
    $h = $x;

    $map = new Map;
    $map->setSizeX($x);
    $map->setSizeY($y);
    $arrayMap = array();


    $firstCase = new Cases;
    $firstCase->setPosX(0);
    $firstCase->setPosY(0);
    $firstCase->setPosZ(mt_rand(-$z, $z));
    $this->setCaseMaterial($firstCase);
    $arrayMap[0][0] = $firstCase;

    $secondCase = new Cases;
    $secondCase->setPosX($x-1);
    $secondCase->setPosY(0);
    $secondCase->setPosZ(mt_rand(-$z, $z));
    $this->setCaseMaterial($secondCase);
    $arrayMap[$x-1][0] = $secondCase;

    $thirdCase = new Cases;
    $thirdCase->setPosX(0);
    $thirdCase->setPosY($y-1);
    $thirdCase->setPosZ(mt_rand(-$z, $z));
    $this->setCaseMaterial($thirdCase);
    $arrayMap[0][$y-1] = $thirdCase;

    $fourthCase = new Cases;
    $fourthCase->setPosX($x-1);
    $fourthCase->setPosY($y-1);
    $fourthCase->setPosZ(mt_rand(-$z, $z));
    $this->setCaseMaterial($fourthCase);
    $arrayMap[$x-1][$y-1] = $fourthCase;
    

    /**
     * En fonction de la taille de la map, 
     * - création des cases avec attribution de position et des profondeurs gérer aléatoirement en fonction de la difficuté
     * - attribution du material en fonction de la profondeur
     * - ajout des cases dans la map
     */
    for($i = 0; $i < $x; $i++){
      for($j = 0; $j < $y; $j++){
        $case = new Cases;
        $case->setPosX($i);
        $case->setPosY($j);
        $case->setPosZ( mt_rand(-$z, $z) );
        $this->setCaseMaterial($case);
        $arrayMap[$j][$i] = $case; 
        $map->addCase($case);
      }
    }

    // dump($arrayMap);
    // die;

  
    $i = $h - 1;

    while($i > 1){
      $id = $i / 2;
      
      for ($x = $id; $x < $h - 1; $x += $i) {
        for ($y = $id; $y < $h - 1; $y = $y + $i) {
          $moyenne = ($arrayMap[$x - $id][$y - $id]->getPosZ() + $arrayMap[$x - $id][$y + $id]->getPosZ() + $arrayMap[$x + $id][$y + $id]->getPosZ() + $arrayMap[$x + $id][$y - $id]->getPosZ()) / 4;
          $arrayMap[$x][$y]->setPosZ((int) ($moyenne + mt_rand(-($id), $id)));
          $this->setCaseMaterial($arrayMap[$x][$y]);
        }
      }

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
                $somme = $somme + $arrayMap[$x - $id][$y]->getPosZ();
                $n = $n + 1;
              }
              if ($x + $id < $h) {
                $somme = $somme + $arrayMap[$x + $id][$y]->getPosZ();
                $n = $n + 1;
              }
              if ($y >= $id) {
                $somme = $somme + $arrayMap[$x][$y - $id]->getPosZ();
                $n = $n + 1;
              }
              if ($y + $id < $h) {
                $somme = $somme + $arrayMap[$x][$y + $id]->getPosZ();
                $n = $n + 1;
              }
                $arrayMap[$x][$y]->setPosZ((int) ($somme / $n + mt_rand(-($id), $id)));

                if ($arrayMap[$x][$y]->getPosZ() > $z || $arrayMap[$x][$y]->getPosZ() < -$z ) {
                  $arrayMap[$x][$y]->setPosZ((int) ($n + mt_rand(-$z, $z)));
                }

                $this->setCaseMaterial($arrayMap[$x][$y]);
              //var_dump($arrayMap[$x][$y][0]);
            }
          }
          $i = $id;
    }

    return $arrayMap;
    
  }

}