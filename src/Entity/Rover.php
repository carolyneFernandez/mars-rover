<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoverRepository")
 */
class Rover
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $posX;

    /**
     * @ORM\Column(type="integer")
     */
    private $posY;

    /**
     * @ORM\Column(type="integer")
     */
    private $energy = 100;

    /**
     * @ORM\Column(type="boolean")
     */
    private $playNextRound = true;

    /**
     * @ORM\Column(type="array")
     */
    private $adjCases = [];

    /**
     * @ORM\Column(type="int")
     */
    private $nextX = null;

    /**
     * @ORM\Column(type="int")
     */
    private $nextY = null;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Rover
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPosX()
    {
        return $this->posX;
    }

    /**
     * @param mixed $posX
     * @return Rover
     */
    public function setPosX($posX)
    {
        $this->posX = $posX;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPosY()
    {
        return $this->posY;
    }

    /**
     * @param mixed $posY
     * @return Rover
     */
    public function setPosY($posY)
    {
        $this->posY = $posY;
        return $this;
    }

    /**
     * @return int
     */
    public function getEnergy(): int
    {
        return $this->energy;
    }

    /**
     * @param int $energy
     * @return Rover
     */
    public function setEnergy(int $energy): Rover
    {
        $this->energy = $energy;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPlayNextRound(): bool
    {
        return $this->playNextRound;
    }

    /**
     * @param bool $playNextRound
     * @return Rover
     */
    public function setPlayNextRound(bool $playNextRound): Rover
    {
        $this->playNextRound = $playNextRound;
        return $this;
    }

    /**
     * @return array
     */
    public function getAdjCases(): array
    {
        return $this->adjCases;
    }

    /**
     * @param array $adjCases
     * @return Rover
     */
    public function setAdjCases(array $adjCases): Rover
    {
        $this->adjCases = $adjCases;
        return $this;
    }

    /**
     * @return null
     */
    public function getNextX()
    {
        return $this->nextX;
    }

    /**
     * @param null $nextX
     * @return Rover
     */
    public function setNextX($nextX)
    {
        $this->nextX = $nextX;
        return $this;
    }

    /**
     * @return null
     */
    public function getNextY()
    {
        return $this->nextY;
    }

    /**
     * @param null $nextY
     * @return Rover
     */
    public function setNextY($nextY)
    {
        $this->nextY = $nextY;
        return $this;
    }


}
