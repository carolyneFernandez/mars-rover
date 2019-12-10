<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParamMapRepository")
 */
class ParamMap
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
    private $difficulty;

    

    

    /**
     * @ORM\Column(type="boolean")
     */
    private $glace;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fer;

    /**
     * @ORM\Column(type="boolean")
     */
    private $argile;

    /**
     * @ORM\Column(type="boolean")
     */
    private $minerai;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sable;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inconnu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $roche;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Map", inversedBy="paramMap", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $map;

    public function __construct()
    {
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }


   

    public function isGlace(): ?bool
    {
        return $this->glace;
    }

    public function setGlace(bool $glace): self
    {
        $this->glace = $glace;

        return $this;
    }

    public function isFer(): ?bool
    {
        return $this->fer;
    }

    public function setFer(bool $fer): self
    {
        $this->fer = $fer;

        return $this;
    }

    public function isArgile(): ?bool
    {
        return $this->argile;
    }

    public function setArgile(bool $argile): self
    {
        $this->argile = $argile;

        return $this;
    }

    public function isMinerai(): ?bool
    {
        return $this->minerai;
    }

    public function setMinerai(bool $minerai): self
    {
        $this->minerai = $minerai;

        return $this;
    }

    public function isSable(): ?bool
    {
        return $this->sable;
    }

    public function setSable(bool $sable): self
    {
        $this->sable = $sable;

        return $this;
    }

    public function isInconnu(): ?bool
    {
        return $this->inconnu;
    }

    public function setInconnu(bool $inconnu): self
    {
        $this->inconnu = $inconnu;

        return $this;
    }

    public function isRoche(): ?bool
    {
        return $this->roche;
    }

    public function setRoche(bool $roche): self
    {
        $this->roche = $roche;

        return $this;
    }

    public function getMap(): ?Map
    {
        return $this->map;
    }

    public function setMap(Map $map): self
    {
        $this->map = $map;

        return $this;
    }

    public function getMaterials(): ?array
    {
        return [
            "Ice"       => $this->isGlace(),
            "Iron"      => $this->isFer(),
            "Clay"      => $this->isArgile(),
            "Minerals"  => $this->isMinerai(),
            "Sand"      => $this->isSable(),
            "Unknown"   => $this->isInconnu(),
            "Rock"      => $this->isRoche(),
        ];
    }
}
