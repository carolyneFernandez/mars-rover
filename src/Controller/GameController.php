<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    const CONTENTS = array('1' => [0, 'glace'], '2' => [1.1, 'roche'], '3' => [1.5, 'sable'], '4' => [1.2, 'minerai'], '5' => [1.3, 'argile'], '6' => [1.2, 'fer'], '7' => [1, 'inconnue']
    );

//    const COST_CONTENT = array('1' => 0, '2' => 1.1, '3' => 1.5, '4' => 1.2, '5' => 1.3, '6' => 1.2, '7' => 1);


    /**
     * @Route("/game", name="game")
     */
    public function index()
    {
        $typeRover = "intelligent";


        return $this->render('game/index.html.twig', ['controller_name' => 'GameController',]);
    }
}
