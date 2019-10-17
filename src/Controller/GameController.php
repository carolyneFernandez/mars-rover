<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    const CONTENTS = array('1' => [0, 'glace'], '2' => [1.1, 'roche'], '3' => [1.5, 'sable'], '4' => [1.2, 'minerai'], '5' => [1.3, 'argile'], '6' => [1.2, 'fer'], '7' => [1, 'inconnue']
    );

//    const COST_CONTENT = array('1' => 0, '2' => 1.1, '3' => 1.5, '4' => 1.2, '5' => 1.3, '6' => 1.2, '7' => 1);


    /**
     * @Route("/game", name="game")
     */
    public function index(Request $request)
    {
        /*
         * Requête API méthode GET :
         * int posX,
         * int posY,
         * string typeRover (short, intelligent, economic)
         * int energy
         * string map (nom de la map à utiliser)
         */
        /*
         * Réponse de la requête API :
         * int nextX -> prochain X du rover,
         * int nextY -> prochain Y du rover,
         * restEnergy -> restant d'énergie après le déplacement,
         * array analyse ( ['x,y','x,y'] -> implicitera le fait qu'on procède à des analyses de sols ).
         * array action ( ['move','stay', ... ] -> la décision du rover )
         */

        $typeRover = "intelligent";


        return $this->render('game/index.html.twig', ['controller_name' => 'GameController',]);
    }
}
