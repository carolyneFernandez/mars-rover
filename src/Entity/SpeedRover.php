<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SpeedRoverRepository")
 */
class SpeedRover extends Rover
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Algorithme qui choisira le prochain coup en fonction de son type de rover
     */
    public function choiceStep()
    {

    }

}
