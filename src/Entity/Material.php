<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MaterialRepository")
 */
class Material
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $materialName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Map", inversedBy="material")
     */
    private $map;
    

    public function __construct($materialName)
    {
        $this->materialName = $materialName;
    }

    public function setMaterial($z)
    {
        /* Si niveau de difficulté = 1 alors plus de plat que de montagne
             Si niveau de difficulté = 2 alors autant de plat que de montagne
             Si niveau de difficulté = 3 alors plus de montagne que de plat */

        if ($z >= -100 && $z <= -85) {
            $material = 1;
        } else if ($z > -85 && $z <= -75) {
            $material = 6;
        } else if ($z > -75 && $z <= -50) {
            $material = 2;
        } else if ($z > -50 && $z <= -45) {
            $material = 4;
        } else if ($z > -45 && $z <= -25) {
            $material = 2;
        } else if ($z > -25 && $z <= -10) {
            $material = 3;
        } else if ($z > -10 && $z <= 10) {
            $material = 5;
        } else if ($z > 10 && $z <= 25) {
            $material = 3;
        } else if ($z > 25 && $z <= 45) {
            $material = 2;
        } else if ($z > 45 && $z <= 50) {
            $material = 4;
        } else if ($z > 50 && $z <= 75) {
            $material = 2;
        } else if ($z > 75 && $z <= 85) {
            $material = 6;
        } else if ($z > 85 && $z <= 100) {
            $material = 1;
        } else {
            $material = 7;
        }

        return $material;
    }

    public function __toString()
    {
        return "wesh ça marche hein";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaterialName(): ?string
    {
        return $this->materialName;
    }

    public function setMaterialName(string $materialName): self
    {
        $this->materialName = $materialName;

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
}
