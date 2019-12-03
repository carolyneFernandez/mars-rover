<?php

namespace App\Controller;

use App\Entity\EcoRover;
use App\Entity\IntelligentRover;
use App\Entity\ShortRover;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GameController extends AbstractController
{
    const CONTENTS = array(
        '1' => [
            0,
            'glace'
        ],
        '2' => [
            1.1,
            'roche'
        ],
        '3' => [
            1.5,
            'sable'
        ],
        '4' => [
            1.2,
            'minerai'
        ],
        '5' => [
            1.3,
            'argile'
        ],
        '6' => [
            1.2,
            'fer'
        ],
        '7' => [
            1,
            'inconnue'
        ]
    );

//    const COST_CONTENT = array('1' => 0, '2' => 1.1, '3' => 1.5, '4' => 1.2, '5' => 1.3, '6' => 1.2, '7' => 1);


    /**
     * @Route("/game", name="game")
     */
    public function index(Request $request)
    {
//        $rover = new IntelligentRover();
        $errors = array();
        /*
         * Requête du front vers l'API avec la méthode GET :
         * int posX,
         * int posY,
         * string typeRover (short, intelligent, economic)
         * int energy
         * string map (nom de la map à utiliser)
         * http://localhost:8000/game?posX=2&posY=4&typeRover=intelligent&energy=321&map=23144141212.json
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
//                $rover->setPosZ($rover->requestGetZ($rover->getPosX(), $rover->getPosY()) );
            } else {
                $errors[] = "La position du rover n'est pas renseignée.";
            }
            if ($request->get('energy')) {
                $rover->setEnergy($request->get('energy'));
            } else {
                $errors[] = "L'énergie du rover n'est pas renseignée.";
            }
            if($request->get('map')){
                $map = $request->get('map');
            }else{
                $errors[] = "La carte n'a pas été passée.";
            }

            

            $result = $rover->choiceStep();
        }

        $result['errors'] = $errors;
        $response = json_encode($result);

        return new Response($response);
//        return $this->render('game/index.html.twig', ['controller_name' => 'GameController',]);
    }


    /**
     * Non fonctionnel
     * Utiliser plutôt : http://localhost:8002/game?posX=2&posY=4&typeRover=intelligent&energy=321&destX=8&destY=5&map=jsonmap.json&
     * @Route("/simulate/request/rover", name="simule_request_rover")
     * @return string
     * @throws TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function simuleRequete()
    {


        $data = [
            'posX' => 2,
            'posY' => 4,
            'typeRover' => 'intelligent',
            'energy' => 321,
            'destX' => 8,
            'destY' => 5,
            'map' => 'jsonmap.json'
        ];
        $dataChaine = "?";
        foreach ($data as $key=>$value){
            $dataChaine .= $key."=".$value."&";
        }

//
        $httpClient = HttpClient::create();

        $response = $httpClient->request('GET', $_ENV['URL_API_ROVER'].$dataChaine);

        echo $response->getContent();




        dump($response);

        return $this->renderView('game/index.html.twig');


    }


}
