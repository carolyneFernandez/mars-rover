<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


class IntelligentRover extends Rover
{

    /**
     * Algorithme qui choisira le prochain coup en fonction de son type de rover
     */
    public function choiceStep()
    {
        return [
            'nextX' => 2,
            'nextY' => 5,
            'energyRest' => 33.21,
            'memory' => []
        ];

    }

}