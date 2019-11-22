<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ShortRover;

class GameController extends AbstractController
{


    private $energy=50;
    private $distance=1;
    
    const CONTENTS = array(
        '1' => [0,'glace'],
        '2' => [1.1,'roche'],
        '3' => [1.5,'sable'],
        '4' => [1.2,'minerai'],
        '5' => [1.3,'argile'],
        '6' => [1.2,'fer'],
        '7' => [1,'inconnue'],
    );
    /**
     * @Route("/game", name="game")
     */
    public function index()
    {

        $rover = new ShortRover();
        $rover->choiceStep();

        return $this->render('game/index.html.twig', [
            'controller_name' => 'GameController',
        ]);
    }

}

?>
