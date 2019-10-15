<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private $energy;

    /**
     * @ORM\Column(type="boolean")
     */
    private $playNextRound;

    /**
     * @ORM\Column(type="array")
     */
    private $adjCases = [];

    /**
     * @ORM\Column(type="array")
     */
    private $road = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Flag", mappedBy="hasFlag")
     */
    private $flags;

    public function __construct()
    {
        $this->flags = new ArrayCollection();
    }

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

    public function getEnergy(): ?int
    {
        return $this->energy;
    }

    public function setEnergy(int $energy): self
    {
        $this->energy = $energy;

        return $this;
    }

    public function getPlayNextRound(): ?bool
    {
        return $this->playNextRound;
    }

    public function setPlayNextRound(bool $playNextRound): self
    {
        $this->playNextRound = $playNextRound;

        return $this;
    }

    public function getAdjCases(): ?array
    {
        return $this->adjCases;
    }

    public function setAdjCases(array $adjCases): self
    {
        $this->adjCases = $adjCases;

        return $this;
    }

    public function getRoad(): ?array
    {
        return $this->road;
    }

    public function setRoad(array $road): self
    {
        $this->road = $road;

        return $this;
    }

    /**
     * @return Collection|Flag[]
     */
    public function getFlags(): Collection
    {
        return $this->flags;
    }

    public function addFlag(Flag $flag): self
    {
        if (!$this->flags->contains($flag)) {
            $this->flags[] = $flag;
            $flag->setHasFlag($this);
        }

        return $this;
    }

    public function removeFlag(Flag $flag): self
    {
        if ($this->flags->contains($flag)) {
            $this->flags->removeElement($flag);
            // set the owning side to null (unless already changed)
            if ($flag->getHasFlag() === $this) {
                $flag->setHasFlag(null);
            }
        }

        return $this;
    }
}
