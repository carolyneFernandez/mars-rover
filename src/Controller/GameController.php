<?php

namespace App\Controller;

use App\Entity\EcoRover;
use App\Entity\ShortRover;
use App\Entity\IntelligentRover;
use App\Service\EcoRoverService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GameController extends AbstractController
{
    const CONTENTS = array(
        '1' => 'glace',
        '2' => 'roche',
        '3' => 'sable',
        '4' => 'minerai',
        '5' => 'argile',
        '6' => 'fer',
        '7' => 'inconnue'
    );

    const COST_CONTENT = array(
        '1' => 0,
        '2' => 1.1,
        '3' => 1.5,
        '4' => 1.2,
        '5' => 1.3,
        '6' => 1.2,
        '7' => 1
    );

    const BONUS = array(
        '0' => 'coût x2, perd 1 tour',
        '1' => '-3 d\'énergie, perd 1 tour',
        '2' => 'recharge entre 20 et 60%',
        '3' => 'coût -50% pour les 4 prochains tours.'
    );

    const energyReload = 5; // taux d'énergie rechargé par tour passé (panneau solaire)

    const lineDistance = 100; // distance horizontale et vertical pour parcourir une case en mètre

    const diagonaleDistance = 140; // distance diagonale pour parcourir une case en mètre

    /**
     * @Route("/ecoRover", name="eco_rover")
     */
    public function index(EcoRoverService $ecoRoverService)
    {
        $file = file_get_contents("../assets/json/map.json");
        $iceFile = file_get_contents("../assets/json/ice.json");
        $iceCasesJSON = json_decode($iceFile);
        //Initialise le tableau avec les cases de glace
        foreach ($iceCasesJSON as $key => $case) {
            $res = explode(",", $case[0]);
            $iceCases[$res[1]][$res[0]] = $case[1];
        }
        $map = json_decode($file, true);
        $rover = new EcoRover();

        // définition de la position de départ et d'arrivé
        $posX = rand(0,8);
        // $posX = 3;
        $posY = rand(0,8);
        // $posY = 3;
        $rover->setPosX($posX)->setPosY($posY);
        $destX = rand(0,8);
        // $destX = 5;
        $destY = rand(0,8);
        // $destY = 8;
        // dump($posX, $posY, $destX, $destY);
        $rover->setDestX($destX);
        $rover->setDestY($destY);
        $map[$posY][$posX]['start'] = true;
        $map[$destY][$destX]['end'] = true;
        $destination['x'] = $destX;
        $destination['y'] = $destY;
        

        // place les cases de glace sur la carte
        foreach ($iceCases as $y => $case) {
            foreach ($case as $x => $value) {
                $map[$y][$x]['content'] = 1; //1 = glace
            }
        }

        // pour chaque renvoie de la fonction de déplacement voulu

        // utiliser cette boucle pour debug
        // for ($i=0; $i < 5; $i++) { 
        //     // dd($destination);
        //     $nextCase = $this->move($map, $rover, $destination);
        //     $rover->setPosX($nextCase['x']);
        //     $rover->setPosY($nextCase['y']);
        //     // dump($nextCase);
        //     $path[$nextCase['y']][$nextCase['x']] = true;
        // }

        // boucle pour la version de prod
        $arrived = false;
        while ($arrived === false) {
            $nextCase = $rover->choiceStep();
            // dump($nextCase);
            $rover->setPosX($nextCase['nextX']);
            $rover->setPosY($nextCase['nextY']);
            if (isset($nextCase['cost'])) {
                $rover->setEnergy('energyRest');
            }
            
            $path[$nextCase['nextY']][$nextCase['nextX']] = true;
            if (isset($nextCase['arrived']) && $nextCase['arrived'] === true) {
                $arrived = true;
            }
        }

        // trace la direction du scan sur la carte, décommenter quand effectué avec la boucle FOR
        // foreach ($nextCase['direction'] as $y => $value) {
        //     foreach ($value as $x => $v) {
        //         $map[$y][$x]['path'] = true;
        //     }
        // }

        // trace le chemin parcouru sur la carte
        foreach ($path as $y => $row) {
            foreach ($row as $x => $value) {
                $map[$y][$x]['isPath'] = true;
            }
        }


        return $this->render('eco_rover/index.html.twig', [
            'controller_name' => 'GameController',
            'map' => $map
        ]);
    }

    /**
     * @Route("/game", name="game")
     * @throws \Exception
     */
    public function response(Request $request)
    {

        $errors = array();
        /*
         * Requête du front vers l'API avec la méthode GET :
         * int posX,
         * int posY,
         * string typeRover (short, intelligent, economic)
         * int energy
         * string map (nom de la map à utiliser)
         * http://localhost:8000/game?posX=2&posY=4&typeRover=economic&energy=100&destX=8&destY=5&map=map.json
         */
        /*
         * Réponse de la requête API :
         * int nextX -> prochain X du rover,
         * int nextY -> prochain Y du rover,
         * restEnergy -> restant d'énergie après le déplacement,
         * array analyse ( ['x,y','x,y'] -> implicitera le fait qu'on procède à des analyses de sols ).
         * array action ( ['move','stay', ... ] -> la décision du rover )
         */
        if ($request->isMethod('GET')) {
            if ($request->get('typeRover')) {
                switch ($request->get('typeRover')) {
                    case 'short':
                        $rover = new ShortRover();
                        break;
                    case 'intelligent':
                        $rover = new IntelligentRover();
                        break;
                    case 'economic':
                        $rover = new EcoRover();
                        break;
                    default:
                        $errors[] = "Le type de rover n'existe pas.";
                }
            } else {
                $errors[] = "Le type de rover n'est pas renseigné.";

            }
            if (($request->get('posX') != null && $request->get('posY') != null)) {
                $rover->setPosX($request->get('posX'))->setPosY($request->get('posY'));
                $rover->setPosZ($rover->requestGetZ($rover->getPosX(), $rover->getPosY()));
            } else {
                $errors[] = "La position du rover n'est pas renseignée.";
            }
            if (($request->get('destX') != null && $request->get('destY') != null)) {
                $rover->setDestX($request->get('destX'))->setDestY($request->get('destY'));
            } else {
                $errors[] = "La destination du rover n'est pas renseignée.";
            }
            if ($request->get('energy')) {
                $rover->setEnergy($request->get('energy'));
            } else {
                $errors[] = "L'énergie du rover n'est pas renseignée.";
            }
            if ($request->get('map')) {
                $map = $request->get('map');
            } else {
                $errors[] = "La carte n'a pas été passée.";
            }
            if (!empty($errors)) {
                dump($errors);
                die;
            }

            // $iceCases = $rover->requestIceCases();
//            dump($iceCases);
            // dump($rover);

            // result est un array avec comme paramètre nextX, nextY, energyRest et memory
            $result = $rover->choiceStep();
            $response = json_encode($result);
            // dump($response);


//            dump($road);
            // $strJsonFileContents = file_get_contents("../public/" . $map);
            // $arrayMap = json_decode($strJsonFileContents, true);
            // dump($arrayMap);





        //    return $response;
        }
        $jsonResponse = new Response();
        $jsonResponse->setContent($response);
        $jsonResponse->headers->set('Content-Type', 'application/json');

        return $jsonResponse;

        // return $this->render('game/index.html.twig', [
        //     'map' => $arrayMap,
        //     'road' => $result['road'],
        //     'ice' => $iceCases,
        //     'costs' => $result['costs'],
        //     'gradients' => $result['gradients']
        // ]);
    }
}
