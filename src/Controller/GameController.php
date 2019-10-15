<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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

    /**
     * @Route("/game", name="game")
     */
    public function index()
    {
        return $this->render('game/index.html.twig', [
            'controller_name' => 'GameController',
        ]);
    }
}
