<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EcoRoverRepository")
 */
class EcoRover extends Rover
{

    /**
     * Algorithme qui choisira le prochain coup en fonction de son type de rover
     */
    public function choiceStep()
    {
        dump("je fais mon traitement eco");
    }
}
