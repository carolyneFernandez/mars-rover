<?php

namespace App\Controller;

use App\Entity\EcoRover;
use App\Entity\IntelligentRover;
use App\Entity\ShortRover;
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
        '1' =>
            'glace'
        ,
        '2' =>
            'roche'
        ,
        '3' =>
            'sable'
        ,
        '4' =>
            'minerai'
        ,
        '5' =>
            'argile'
        ,
        '6' =>
            'fer'
        ,
        '7' =>
            'inconnue'

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
     * @Route("/game", name="game")
     */
    public function index(EcoRoverService $ecoRoverService)
    {
        //recuperation de la map et des cases de glace -- simulation api
        $file = file_get_contents("../assets/json/map.json");
        $iceFile = file_get_contents("../assets/json/ice.json");
        $iceCasesJSON = json_decode($iceFile);
        $map = json_decode($file, true);

        //Initialise un tableau avec les cases de glace
        foreach ($iceCasesJSON as $key => $case) {
            $res = explode(",", $case[0]);
            $iceCases[$res[1]][$res[0]] = $case[1];
        }
        // place les cases de glace sur la carte pour la vue
        foreach ($iceCases as $y => $case) {
            foreach ($case as $x => $value) {
                $map[$y][$x]['content'] = 1; //1 = glace
            }
        }

        // définition de la position de départ et d'arrivé
        $posX = rand(0,8);
        $posY = rand(0,8);
        $destX = rand(0,8);
        $destY = rand(0,8);
        // evite que la destination soit la meme case que le départ
        while($posX == $destX && $posY == $destY) {
            $posX = rand(0,8);
            $posY = rand(0,8);
            $destX = rand(0,8);
            $destY = rand(0,8);
        }
        // $posX = 0;
        // $posY = 5;
        // $destX = 9;
        // $destY = 5;

        $map[$posY][$posX]['start'] = true;
        $map[$destY][$destX]['end'] = true;
        $destination['x'] = $destX;
        $destination['y'] = $destY;




        //set up requete HTTP POST
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/post-response");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);



        //set up avant le traitement du chemin
        $rover = new EcoRover();
        $arrived = false;
        $rover->setEnergy(100);
        $rover->setMemory([]);
        $rover->setPosX($posX)->setPosY($posY);
        $rover->setDestX($destX)->setDestY($destY);

        // requete POST
        $fields = [
            'posX' => $rover->getPosX(),
            'posY' => $rover->getPosY(),
            'typeRover' => 'economic',
            'energy' => $rover->getEnergy(),
            'destX' => $rover->getDestX(),
            'destY' => $rover->getDestY(),
            'map' => 'map.json',
            'memory' => $rover->getMemory()
        ];
        $json = json_encode($fields);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $response = curl_exec($ch);
        $nextCase = json_decode($response, true);
        dd($nextCase);

        // boucle pour la version de prod
        while ($arrived === false) {

            // requete POST
            $fields = [
                'posX' => $rover->getPosX(),
                'posY' => $rover->getPosY(),
                'typeRover' => 'economic',
                'energy' => $rover->getEnergy(),
                'destX' => $rover->getDestX(),
                'destY' => $rover->getDestY(),
                'map' => 'map.json',
                'memory' => $rover->getMemory()
            ];
            $json = json_encode($fields);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

            //traitement reponse
            $response = curl_exec($ch);
            $nextCase = json_decode($response, true);
            // $nextCase = $rover->choiceStep(); //test sans passer par l'api

            $rover->setPosX($nextCase['nextX']);
            $rover->setPosY($nextCase['nextY']);
            $rover->setEnergy($nextCase['energyRest']);
            $rover->setMemory($nextCase['memory']);

            //ajout du chemin pour la vue
            $path[$nextCase['nextY']][$nextCase['nextX']] = true;

            //stop le rover s'il est arrive
            if (isset($nextCase['arrived']) && $nextCase['arrived'] === true) {
                $arrived = true;
            }
        }

        // trace le chemin parcouru sur la carte
        foreach ($path as $y => $row) {
            foreach ($row as $x => $value) {
                $map[$y][$x]['isPath'] = true;
            }
        }

        return $this->render('eco_rover/index.html.twig', [
            'map' => $map
        ]);
    }

    /**
     * @Route("/post-response", name="post_response")
     * @throws \Exception
     */
    public function postResponse(Request $request)
    {
        $json = $request->getContent();
        $parameters = json_decode($json, true);
        $errors = array();

        /*
        * Requête du front vers l'API avec la méthode POST :
        * int posX,
        * int posY,
        * int destX,
        * int destY,
        * string typeRover (short, intelligent, economic)
        * int energyRest
        * string map (nom de la map à utiliser)
        * array memory
        * http://localhost:8000/post_response
        */

        /*
        * Réponse de la requête API :
        * int nextX -> prochain X du rover,
        * int nextY -> prochain Y du rover,
        * restEnergy -> restant d'énergie après le déplacement,
        * array momery ( array -> un tableau renvoyer sans traitement par le front ).
        */
        if ($request->isMethod('POST')) {
            if (isset($parameters['typeRover'])) {
                switch ($parameters['typeRover']) {
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
            if (isset($parameters['posX']) && isset($parameters['posY'])) {
                $rover->setPosX($parameters['posX'])->setPosY($parameters['posY']);
                $rover->setPosZ($rover->requestGetZ($rover->getPosX(), $rover->getPosY()));
            } else {
                $errors[] = "La position du rover n'est pas renseignée.";
            }
            if (isset($parameters['destX']) && isset($parameters['destY'])) {
                $rover->setDestX($parameters['destX'])->setDestY($parameters['destY']);
            } else {
                $errors[] = "La destination du rover n'est pas renseignée.";
            }
            if (isset($parameters['energy'])) {
                $rover->setEnergy($parameters['energy']);
            } else {
                $errors[] = "L'énergie du rover n'est pas renseignée.";
            }
            if (isset($parameters['map'])) {
                $map = $parameters['map'];
            } else {
                $errors[] = "La carte n'a pas été passée.";
            }
            if ($parameters['memory']) {
                $rover->setMemory($parameters['memory']);
            }
            if (!empty($errors)) {
                dump($errors);
                die;
            }

            // result est un array avec comme paramètre nextX, nextY, energyRest et memory
            $result = $rover->choiceStep();
            $response = json_encode($result);

        }
        $jsonResponse = new Response();
        $jsonResponse->setContent($response);
        $jsonResponse->headers->set('Content-Type', 'application/json');

        return $jsonResponse;

    }

    /**
     * @Route("/get-response", name="get_response")
     * @throws \Exception
     */
    public function getResponse(Request $request)
    {

        $errors = array();

        //!!!!!! aucune gestion de la memoire du rover !!!!!!

        /*
        * Requête du front vers l'API avec la méthode GET :
        * int posX,
        * int posY,
        * int destX,
        * int destY,
        * string typeRover (short, intelligent, economic)
        * int energyRest
        * string map (nom de la map à utiliser)
        * http://localhost:8000/get-response?posX=2&posY=4&typeRover=economic&energy=100&destX=8&destY=5&map=map.json
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

            // result est un array avec comme paramètre nextX, nextY, energyRest et memory
            $result = $rover->choiceStep();
            $response = json_encode($result);

        }
        $jsonResponse = new Response();

        $jsonResponse->headers->set('Content-Type', 'application/json');
        $jsonResponse->setContent($response);

        return $jsonResponse;

    }


}
