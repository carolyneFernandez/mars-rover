<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AverageMapRepository")
 */
class AverageMap extends Map
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
    private $depthMin;

    /**
     * @ORM\Column(type="integer")
     */
    private $depthMax;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepthMin(): ?int
    {
        return $this->depthMin;
    }

    public function setDepthMin(int $depthMin): self
    {
        $this->depthMin = $depthMin;

        return $this;
    }

    public function getDepthMax(): ?int
    {
        return $this->depthMax;
    }

    public function setDepthMax(int $depthMax): self
    {
        $this->depthMax = $depthMax;

        return $this;
    }
}
