<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
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
    private $round;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mapFile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(int $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function getMapFile(): ?string
    {
        return $this->mapFile;
    }

    public function setMapFile(string $mapFile): self
    {
        $this->mapFile = $mapFile;

        return $this;
    }

    /**
     *
     */
    public function nextRound()
    {
        $this->round++;
    }
}
