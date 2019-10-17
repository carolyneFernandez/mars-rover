<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IntelligentRoverRepository")
 */
class IntelligentRover extends Rover
{

    /**
     * Algorithme qui choisira le prochain coup en fonction de son type de rover
     * @throws \Exception
     */
    public function choiceStep()
    {
        dump("je fais mon traitement intelligent");
        $this->requestAdjCases(999, 999, 2);

        dump($this->getAdjCases());
    }

}
