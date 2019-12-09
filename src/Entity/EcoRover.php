<?php

namespace App\Entity;

use App\Controller\GameController;
use Doctrine\ORM\Mapping as ORM;
use App\Service\EcoRoverService;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EcoRoverRepository")
 */
class EcoRover extends Rover
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $iceConsumed = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Algorithme qui choisira le prochain coup en fonction de son type de rover
     */
    public function choiceStep()
    {
        //renseigne les cases de glaces consommees depuis la memoire du rover
        if (isset($this->getMemory()['iceConsumed'])) {
            $this->setIceConsumed($this->getMemory()['iceConsumed']);
        }

        //mouvement via le service
        $ecoRoverService = new EcoRoverService();
        $result = $ecoRoverService->move($this);

        //si null, alors le rover n'a trouvé aucune case et il est bloqué
        if ($result == null) {
            $result['x'] = $this->getPosX();
            $result['y'] = $this->getPosY();
            $result['arrived'] = true;
        }

        //definition de l'energie restante
        if (isset($result['cost'])) {
            //Si il ne reste pas assez d'énergie pour le déplacement, on reste sur place
            if(($this->getEnergy() - $result['cost']) <= 0) {
                $result['x'] = $this->getPosX();
                $result['y'] = $this->getPosY();
                $this->setEnergy($this->getEnergy()+GameController::energyReload);
            }
            else {
                $this->setEnergy($this->getEnergy() - $result['cost']);
            }
        } 

        //propriete arrived pour tester le rover
        if (isset($result['arrived'])) {
            $arrived = true;
        } else {
            $arrived = false;
        }

        //instanciation de la memoire
        if (isset($result['memory'])) {
            $memory = $result['memory'];
        } else {
            $memory = array();
        }

        return [
            'nextX' => $result['x'],
            'nextY' => $result['y'],
            'energyRest' => $this->getEnergy(),
            'memory' => $memory,
            'arrived' => $arrived //for testing
        ];
    }

    public function getIceConsumed(): ?array
    {
        return $this->iceConsumed;
    }

    public function setIceConsumed(?array $iceConsumed): self
    {
        $this->iceConsumed = $iceConsumed;

        return $this;
    }

    public function addIceConsumed($x, $y): self
    {
        $this->iceConsumed[$y][$x] = true;

        return $this;
    }

}
