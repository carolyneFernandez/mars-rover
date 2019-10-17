<?php

namespace App\Controller;

use App\Entity\EcoRover;
use App\Entity\IntelligentRover;
use App\Entity\ShortRover;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    const CONTENTS = array('1' => [0, 'glace'], '2' => [1.1, 'roche'], '3' => [1.5, 'sable'], '4' => [1.2, 'minerai'], '5' => [1.3, 'argile'], '6' => [1.2, 'fer'], '7' => [1, 'inconnue']);

//    const COST_CONTENT = array('1' => 0, '2' => 1.1, '3' => 1.5, '4' => 1.2, '5' => 1.3, '6' => 1.2, '7' => 1);


    /**
     * @Route("/game", name="game")
     * @throws \Exception
     */
    public function index(Request $request)
    {

        $errors = array();
        /*
         * Requête API méthode GET :
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
            } else {
                $errors[] = "La position du rover n'est pas renseignée.";
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
            $iceCases = $rover->requestIceCases();
//            dump($iceCases);
            dump($rover);
            $road = $rover->choiceStep();
            dump($road);
            $strJsonFileContents = file_get_contents("../public/" . $map);
            $arrayMap = json_decode($strJsonFileContents, true);
            dump($arrayMap);
        }
        $typeRover = "intelligent";


        return $this->render('game/index.html.twig', ['map' => $arrayMap, 'road' => $road, 'ice' => $iceCases]);
    }
}
