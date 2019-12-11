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

        if ($form->isSubmitted() && $form->isValid()) {
            $map = new Map;
            $level = $paramMap->getDifficulty();
            $paramMap->setMap($map);
            /**
             * En fonction de la difficulté, on set la profondeur de la case
             */
            switch ($level) {
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

            $arrayMap = $this->map_gen($map->getSizeX(), $map->getSizeY(), $profondeur);

            $carteTemp = fopen('carte' . time() . '.txt', 'w+');  //generates a new map every time, so don't forget to delete them
            // dump(stream_get_meta_data($carteTemp)["uri"]);die;
            fputs($carteTemp, json_encode($arrayMap));
            $mapName = stream_get_meta_data($carteTemp)["uri"];
            fclose($carteTemp);
            $mapName = str_replace(".txt", '', $mapName);
            $arrayMap = [
                "mapName" => $mapName,
                "difficulty" => $level,
                "materials" => $paramMap->getMaterials(),
                "map" => $arrayMap
            ];
            $arrayMap = json_encode($arrayMap);

            return new JsonResponse($arrayMap, 200, [], true);
        }

        return $this->render('index.html.twig', [
            'controller_name' => 'DefaultController',
            'form' => $form->createView()
        ]);

    }


    public function setCaseMaterial(Cases $case)
    {

        $glace = new Materials;
        $glace->setLabel("glace");

        $roche = new Materials;
        $roche->setLabel("roche");

        $sable = new Materials;
        $sable->setLabel("sable");

        $minerai = new Materials;
        $minerai->setLabel("minerai");

        $fer = new Materials;
        $fer->setLabel("fer");

        $inconnu = new Materials;
        $inconnu->setLabel("inconnu");

        $argile = new Materials;
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

        } else if ($case->getPosZ() > -10 && $case->getPosZ() <= 9) {
            $case->setMaterials($argile);
      
        } else if ($case->getPosZ() > 9 && $case->getPosZ() <= 10) {
            $case->setMaterials($glace);
 
        } else if ($case->getPosZ() > 10 && $case->getPosZ() <= 25) {
            $case->setMaterials($sable);

        } else if ($case->getPosZ() > 25 && $case->getPosZ() <= 45) {
            $case->setMaterials($glace);

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
        $secondCase->setPosX($x - 1);
        $secondCase->setPosY(0);
        $secondCase->setPosZ(mt_rand(-$z, $z));
        $this->setCaseMaterial($secondCase);
        $arrayMap[$x - 1][0] = $secondCase;

        $thirdCase = new Cases;
        $thirdCase->setPosX(0);
        $thirdCase->setPosY($y - 1);
        $thirdCase->setPosZ(mt_rand(-$z, $z));
        $this->setCaseMaterial($thirdCase);
        $arrayMap[0][$y - 1] = $thirdCase;

        $fourthCase = new Cases;
        $fourthCase->setPosX($x - 1);
        $fourthCase->setPosY($y - 1);
        $fourthCase->setPosZ(mt_rand(-$z, $z));
        $this->setCaseMaterial($fourthCase);
        $arrayMap[$x - 1][$y - 1] = $fourthCase;


        /**
         * En fonction de la taille de la map,
         * - création des cases avec attribution de position et des profondeurs gérer aléatoirement en fonction de la difficuté
         * - attribution du material en fonction de la profondeur
         * - ajout des cases dans la map
         */
        for ($i = 0 ; $i < $x ; $i++) {
            for ($j = 0 ; $j < $y ; $j++) {
                $case = new Cases;
                $case->setPosX($i);
                $case->setPosY($j);
                $case->setPosZ(mt_rand(-$z, $z));
                $this->setCaseMaterial($case);
                $arrayMap[$j][$i] = $case;
                $map->addCase($case);
            }
        }

        // dump($arrayMap);
        // die;


        $i = $h - 1;

        while ($i > 1) {
            $id = $i / 2;

            for ($x = $id ; $x < $h - 1 ; $x += $i) {
                for ($y = $id ; $y < $h - 1 ; $y = $y + $i) {
                    $moyenne = ($arrayMap[$x - $id][$y - $id]->getPosZ() + $arrayMap[$x - $id][$y + $id]->getPosZ() + $arrayMap[$x + $id][$y + $id]->getPosZ() + $arrayMap[$x + $id][$y - $id]->getPosZ()) / 4;
                    $arrayMap[$x][$y]->setPosZ((int)($moyenne + mt_rand(-($id), $id)));
                    $this->setCaseMaterial($arrayMap[$x][$y]);
                }
            }

            $decalage = 0;
            for ($x = 0 ; $x < $h ; $x = $x + $id) {
                if ($decalage == 0) {
                    $decalage = $id;
                } else {
                    $decalage = 0;
                }
                for ($y = $decalage ; $y < $h ; $y = $y + $i) {
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
                    $arrayMap[$x][$y]->setPosZ((int)($somme / $n + mt_rand(-($id), $id)));

                    if ($arrayMap[$x][$y]->getPosZ() > $z || $arrayMap[$x][$y]->getPosZ() < -$z) {
                        $arrayMap[$x][$y]->setPosZ((int)($n + mt_rand(-$z, $z)));
                    }

                    $this->setCaseMaterial($arrayMap[$x][$y]);
                    //var_dump($arrayMap[$x][$y][0]);
                }
            }
            $i = $id;
        }


        return $arrayMap;

    }

    /**
     * @Route("/api/getIceCase", name="getIceCase")
     */
    public function getIceCase()
    {
        if (!isset($_GET['mapName']) || $_GET['mapName'] == null) {
            print("need more params, ex: /api/getIceCase?mapName=carte4206664269");
            die;
        }
        $carteTemp = file_get_contents($_GET['mapName'] . '.txt');
        $carteTemp = json_decode($carteTemp, true);
        $iceCases = [];

        foreach ($carteTemp as $lineKey => $line) {
            foreach ($line as $caseKey => $case) {
                if ($case["material"] == "glace")
                    $iceCases[$lineKey][$caseKey] = $case["z"];
            }
        }

//        $iceCases = json_encode($iceCases);
//
//        return new JsonResponse($iceCases, 200, [], true);
        $iceCases = json_encode($iceCases);
        $jsonResponse = new Response();
        $jsonResponse->setContent($iceCases);
        $jsonResponse->headers->set('Content-Type', 'application/json');

        return $jsonResponse;


    }

    /**
     * @Route("/api/getMaterial", name="getMaterial")
     */
    public function getMaterial()
    {
        if (!isset($_GET['mapName']) || !isset($_GET['x']) || !isset($_GET['y']) || $_GET['mapName'] == null || $_GET['x'] == null || $_GET['y'] == null) {
            print("need more params, ex: /api/getMaterial?mapName=carte4206664269&x=24&y=96.");
            die;
        }

        $contents = array(
            'glace' => 1,
            'roche' => 2,
            'sable' => 3,
            'minerai' => 4,
            'argile' => 5,
            'fer' => 6,
            'inconnue' => 7,
            '1' => 'glace',
            '2' => 'roche',
            '3' => 'sable',
            '4' => 'minerai',
            '5' => 'argile',
            '6' => 'fer',
            '7' => 'inconnue'
        );

        $carteTemp = file_get_contents($_GET['mapName'] . '.txt');
        $carteTemp = json_decode($carteTemp, true);
        $x = $_GET['x'];
        $y = $_GET['y'];
        $idMaterial = 0;
        $material = $carteTemp[$y][$x]["material"];
        $idMaterial = $contents[$material];

//        foreach ($carteTemp as $lineKey => $line) {
//            if ($lineKey == $y) {
//                 foreach ($line as $caseKey => $case) {
//                    if ($caseKey == $x)
//                        $material = $case['material'];
//                }
//            }
//        }

//        $material = json_encode(["material" => $material]);

//        return new JsonResponse($material, 200, [], true);

        $idMaterial = json_encode($idMaterial);
        $jsonResponse = new Response();
        $jsonResponse->setContent($idMaterial);
        $jsonResponse->headers->set('Content-Type', 'application/json');

        return $jsonResponse;
    }

    /**
     * @Route("/api/getZ", name="getZ")
     */
    public function getZ()
    {
        if (!isset($_GET['mapName']) || !isset($_GET['x']) || !isset($_GET['y']) || $_GET['mapName'] == null || $_GET['x'] == null || $_GET['y'] == null) {
            print("need more params, ex: /api/getZ?mapName=carte4206664269&x=24&y=96");
            die;
        }

        $carteTemp = file_get_contents($_GET['mapName'] . '.txt');
        $carteTemp = json_decode($carteTemp, true);
        $x = $_GET['x'];
        $y = $_GET['y'];
        $z = 0;

        $z = $carteTemp[$y][$x]["z"];

        $z = json_encode($z);
        $jsonResponse = new Response();
        $jsonResponse->setContent($z);
        $jsonResponse->headers->set('Content-Type', 'application/json');

        return $jsonResponse;

//      return new JsonResponse($z, 200, [], true);
    }

    /**
     * @Route("/api/getAdjCases", name="getAdjCases")
     */
    public function getAdjCases(){

        if (!isset($_GET['mapName']) || !isset($_GET['x']) || !isset($_GET['y']) || !isset($_GET['y']) || $_GET['radius'] == null || $_GET['x'] == null || $_GET['y'] == null || $_GET['radius'] == null) {
            print("need more params, ex: /api/getAdjCases?mapName=carte4206664269&x=24&y=96&radius=2");
            die;
        }

        $file = file_get_contents($_GET['mapName'].".txt");
        $map = json_decode($file, true);
        $adjCases = array();
        $radius = $_GET['radius'];
        $x = $_GET['x'];
        $y = $_GET['y'];
        for ($i=1; $i < $radius + 1; $i++) {
            // Haut gauche
            if (isset($map[$y + $i][$x - $i])) {
                $adjCases[$y + $i][$x - $i] = $map[$y + $i][$x - $i];
            }
            // Haut
            if (isset($map[$y + $i][$x])) {
                $adjCases[$y + $i][$x] = $map[$y + $i][$x];
            }
            // Haut droite
            if (isset($map[$y + $i][$x + $i])) {
                $adjCases[$y + $i][$x + $i] = $map[$y + $i][$x + $i];
            }
            // Droite
            if (isset($map[$y][$x + $i])) {
                $adjCases[$y][$x + $i] = $map[$y][$x + $i];
            }
            // Bas droite
            if (isset($map[$y - $i][$x + $i])) {
                $adjCases[$y - $i][$x + $i] = $map[$y - $i][$x + $i];
            }
            // Bas
            if (isset($map[$y - $i][$x])) {
                $adjCases[$y - $i][$x] = $map[$y - $i][$x];
            }
            // Bas gauche
            if (isset($map[$y - $i][$x - $i])) {
                $adjCases[$y - $i][$x - $i] = $map[$y - $i][$x - $i];
            }
            // Gauche
            if (isset($map[$y][$x - $i])) {
                $adjCases[$y][$x - $i] = $map[$y][$x - $i];
            }
        }


        $adjCases = json_encode($adjCases);
        $jsonResponse = new Response();
        $jsonResponse->setContent($adjCases);
        $jsonResponse->headers->set('Content-Type', 'application/json');

        return $jsonResponse;





    }

}