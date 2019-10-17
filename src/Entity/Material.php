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
