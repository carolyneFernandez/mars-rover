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
    private $posZ;

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

    public function requestIceCases()
    {
        return [
            "2,3" => 16,
            "1,9" => 3,
            "4,6" => 14,
            "3,0" => 21,
            "8,2" => 27,
            "9,9" => 24,
        ];
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $radius
     * @return Rover
     * @throws \Exception
     */
    public function requestAdjCases($x, $y, $radius = 1)
    {
        //requête adjCases à l'API carte : position rover simulé : (x,y => z)
        $width = 1000 - 1;
        $height = 1000 - 1;

        for ($i = 1 ; $i <= $radius ; $i++) {
            if ($x + $i <= $width) {
                $cases[$x + $i . ',' . $y] = random_int(-99, 99); // milieu droite
            }
            if ($x + $i < $width && $y + $i <= $height) {
                $cases[($x + $i) . ',' . ($y + $i)] = random_int(-99, 99); // bas droite
            }
            if ($y + $i <= $height) {
                $cases[$x . ',' . ($y + $i)] = random_int(-99, 99); // milieu bas
            }
            if ($x - $i >= 0 && $y + $i <= $height) {
                $cases[($x - $i) . ',' . ($y + $i)] = random_int(-99, 99); // bas gauche
            }
            if ($x - $i >= 0) {
                $cases[($x - $i) . ',' . $y] = random_int(-99, 99); // milieu gauche

            }
            if ($x - $i >= 0 && $y - $i >= 0) {
                $cases[($x - $i) . ',' . ($y - $i)] = random_int(-99, 99); // haut gauche
            }
            if ($y - $i >= 0) {
                $cases[$x . ',' . ($y - $i)] = random_int(-99, 99); // milieu haut
            }
            if ($x + $i <= $width && $y - $i >= 0) {
                $cases[($x + $i) . ',' . ($y - $i)] = random_int(-99, 99); // haut droite
            }

        }

        $this->setAdjCases($cases);

        return $this;
    }

    public function requestGetZ($x, $y)
    {
        $x = round($x);
        $y = round($y);
        $parsed_json = json_decode(file_get_contents("../public/jsonmap.json"), true);
//        dump($parsed_json);
//        dump("z de $x,$y : ". $parsed_json[$y][$x][0]);
        return $parsed_json[$y][$x][0];
    }

    public function requestGetContent($x, $y)
    {
        $x = round($x);
        $y = round($y);
        $parsed_json = json_decode(file_get_contents("../public/jsonmap.json"), true);
//        dump($parsed_json);
//        dump("z de $x,$y : ". $parsed_json[$y][$x][0]);
        return $parsed_json[$y][$x][1];
    }


}
