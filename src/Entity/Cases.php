<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CasesRepository")
 */
class Cases implements JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Materials", inversedBy="cases")
     */
    private $materials;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Map", inversedBy="cases")
     */
    private $map;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosX(): ?int
    {
        return $this->posX;
    }

    public function setPosX(int $posX): self
    {
        $this->posX = $posX;

        return $this;
    }

    public function getPosY(): ?int
    {
        return $this->posY;
    }

    public function setPosY(int $posY): self
    {
        $this->posY = $posY;

        return $this;
    }

    public function getPosZ(): ?int
    {
        return $this->posZ;
    }

    public function setPosZ(int $posZ): self
    {
        $this->posZ = $posZ;

        return $this;
    }

    public function getMaterials(): ?Materials
    {
        return $this->materials;
    }

    public function setMaterials(?Materials $materials): self
    {
        $this->materials = $materials;

        return $this;
    }

    public function getMap(): ?Map
    {
        return $this->map;
    }

    public function setMap(?Map $map): self
    {
        $this->map = $map;

        return $this;
    }

    // public function __toString()
    // {
    //     return "123";
    // }

    public function jsonSerialize()
    {
        return [
            "x" => $this->getPosX(),
            "y" => $this->getPosY(),
            "z" => $this->getPosZ(),
            "material" => $this->getMaterials()->getLabel()
        ];
    }

    public function hasIce()
    {
        if( $this.getMaterials()->getLabel() == "glace" ){
            return true;
        }
        return false;
    }
}
