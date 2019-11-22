<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MapRepository")
 */
class Map
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
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $sizeX;

    /**
     * @ORM\Column(type="integer")
     */
    private $sizeY;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Cases", mappedBy="map")
     */
    private $cases;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ParamMap", mappedBy="map", cascade={"persist", "remove"})
     */
    private $paramMap;


    public function __construct()
    {
        $this->cases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSizeX(): ?int
    {
        return $this->sizeX;
    }

    public function setSizeX(int $sizeX): self
    {
        $this->sizeX = $sizeX;

        return $this;
    }

    public function getSizeY(): ?int
    {
        return $this->sizeY;
    }

    public function setSizeY(int $sizeY): self
    {
        $this->sizeY = $sizeY;

        return $this;
    }

    /**
     * @return Collection|Cases[]
     */
    public function getCases(): Collection
    {
        return $this->cases;
    }

    public function addCase(Cases $case): self
    {
        if (!$this->cases->contains($case)) {
            $this->cases[] = $case;
            $case->setMap($this);
        }

        return $this;
    }

    public function removeCase(Cases $case): self
    {
        if ($this->cases->contains($case)) {
            $this->cases->removeElement($case);
            // set the owning side to null (unless already changed)
            if ($case->getMap() === $this) {
                $case->setMap(null);
            }
        }

        return $this;
    }

    public function getParamMap(): ?ParamMap
    {
        return $this->paramMap;
    }

    public function setParamMap(ParamMap $paramMap): self
    {
        $this->paramMap = $paramMap;

        // set the owning side of the relation if necessary
        if ($this !== $paramMap->getMap()) {
            $paramMap->setMap($this);
        }

        return $this;
    }

    
}
