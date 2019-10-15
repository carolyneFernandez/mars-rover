<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FlagRepository")
 */
class Flag
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Rover", inversedBy="flags")
     */
    private $hasFlag;

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

    public function getHasFlag(): ?Rover
    {
        return $this->hasFlag;
    }

    public function setHasFlag(?Rover $hasFlag): self
    {
        $this->hasFlag = $hasFlag;

        return $this;
    }
}
