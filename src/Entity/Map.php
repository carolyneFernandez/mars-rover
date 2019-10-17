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
     * @ORM\Column(type="integer")
     */
    private $sizeX;

    /**
     * @ORM\Column(type="integer")
     */
    private $sizeY;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Material", mappedBy="map")
     */
    private $material;

    public function __construct($sizeX, $sizeY)
    {
        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->material = new ArrayCollection();
    }

    public function MapGeneration(){

    }


    
    /*
     *  Cette fonction doit renvoyer les cases adjacentes en fonction de la case à la position 
     *  x, y et du rayon
     */
    public function caseAdjacentes($x, $y, $rayon)
    {

    }

    /*
     *  Cette fonction doit renvoyer l'altitude en fonction de la case à la position 
     *  x et y 
     */

    public function getAltitude($x, $y)
    {

    }
    
    
    public function emplacementCaseGlace()
    {

    }

    public function __toString()
    {
        return "test";
    }

    
    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection|Material[]
     */
    public function getMaterial(): Collection
    {
        return $this->material;
    }

    public function addMaterial(Material $material): self
    {
        if (!$this->material->contains($material)) {
            $this->material[] = $material;
            $material->setMap($this);
        }

        return $this;
    }

    public function removeMaterial(Material $material): self
    {
        if ($this->material->contains($material)) {
            $this->material->removeElement($material);
            // set the owning side to null (unless already changed)
            if ($material->getMap() === $this) {
                $material->setMap(null);
            }
        }
    
        return $this;
    }
}
