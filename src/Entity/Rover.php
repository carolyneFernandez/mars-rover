<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
    private $destX;

    /**
     * @ORM\Column(type="integer")
     */
    private $destY;

    /**
     * @ORM\Column(type="float")
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
     * @ORM\Column(type="integer")
    */
    private $posZ;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $memory = [];

    /**
     * @return mixed
     */
    public function getDestX()
    {
        return $this->destX;
    }

    /**
     * @return mixed
     */
    public function getPosZ()
    {
        return $this->posZ;
    }

    /**
     * @param mixed $posZ
     * @return Rover
     */
    public function setPosZ($posZ)
    {
        $this->posZ = $posZ;

        return $this;
    }

    /**
     * @param mixed $destX
     * @return Rover
     */
    public function setDestX($destX)
    {
        $this->destX = $destX;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDestY()
    {
        return $this->destY;
    }

    /**
     * @param mixed $destY
     * @return Rover
     */
    public function setDestY($destY)
    {
        $this->destY = $destY;

        return $this;
    }

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

    public function requestGetZ($x, $y)
    {
        $x = round($x);
        $y = round($y);
        $parsed_json = json_decode(file_get_contents("../assets/json/map.json"), true);
        //        dump($parsed_json);
        //dump("z de $x,$y : " . $parsed_json[$y][$x][0]);
        return $parsed_json[$y][$x][0];
    }

    public function requestGetContent($x, $y)
    {
        $x = round($x);
        $y = round($y);
        $parsed_json = json_decode(file_get_contents("../assets/json/map.json"), true);
        //        dump($parsed_json);
        //dump("z de $x,$y : " . $parsed_json[$y][$x][0]);
        return $parsed_json[$y][$x][1];
    }

    public function getMemory(): ?array
    {
        return $this->memory;
    }

    public function setMemory(?array $memory): self
    {
        $this->memory = $memory;

        return $this;
    }
}
