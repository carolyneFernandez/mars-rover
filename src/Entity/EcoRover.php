<?php

namespace App\Entity;

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
        $ecoRoverService = new EcoRoverService();
        $result = $ecoRoverService->move($this);
        if (isset($result['cost'])) {
            $this->setEnergy($this->getEnergy() - $result['cost']);
        }
        //for testing
        if (isset($result['arrived'])) {
            $arrived = true;
        } else {
            $arrived = false;
        }
        return [
            'nextX' => $result['x'],
            'nextY' => $result['y'],
            'energyRest' => $this->getEnergy(),
            'memory' => [],
            'arrived' => $arrived //for testing
        ];
    }

    public function brensenham($posX, $posY, $destX, $destY, $direction = false, $turn = false){
        $x = $posX;
        $y = $posY;
        $dx = $destX - $posX; //distance sur l'axe des abscisses
        $dy = $destY - $posY; //distance sur l'axe des ordonnees
        //direction du segment
        $xinc = $dx > 0 ? 1 : -1; 
        $yinc = $dy > 0 ? 1 : -1; 
        //              |
        //              |
        //   xinc: -1   |   xinc: 1
        //   yinc: 1    |   yinc: 1
        //              |
        // --------------------------------             
        //              |
        //   xinc: -1   |   xinc: 1
        //   yinc: -1   |   yinc: -1
        //              |
        //              |
        
        //convertion en valeur absolue pour evaluer la pente du segment
        $dx = abs($dx);
        $dy = abs($dy);
        
        //selon la pente du segment
        if($dx > $dy){ //Si est plutot horizontale
            $error = $dx / 2;
            for ($i=1; $i <= $dx; $i++) { //pour chaque pixel sur la distance des absisses
                $x += $xinc;
                $error += $dy;
                if($error >= $dx){
                    $error -= $dx;
                    $y += $yinc;
                }
                
                if ($turn) {
                    $path[$i][$y][$x] = true;
                } else {
                    $path[$y][$x] = true;
                }
                //si on cherche une direction
                if($direction == true && ($y-1 >= 0 && $y+1 <= 9)){
                    if ($turn) {
                        $path[$i][$y-1][$x] = true;
                        $path[$i][$y+1][$x] = true;
                    }
                    $path[$y-1][$x] = true;
                    $path[$y+1][$x] = true;
                }
            }
        } else{ //Si est plutot verticale
            $error = $dy / 2;
            for ($i=1; $i <= $dy; $i++) { 
                $y += $yinc;
                $error += $dx;
                if($error >= $dy){
                    $error -= $dy;
                    $x += $xinc;
                }
                if ($turn) {
                    $path[$i][$y][$x] = true;
                } else {
                    $path[$y][$x] = true;
                }
                //si on cherche une direction
                if($direction == true && ($x-1 >= 0 && $x+1 <= 9)){
                    if ($turn) {
                        $path[$i][$y][$x-1] = true;
                        $path[$i][$y][$x+1] = true;
                    } 
                    $path[$y][$x-1] = true;
                    $path[$y][$x+1] = true;
                }
            }   
        }



        return $path;
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
