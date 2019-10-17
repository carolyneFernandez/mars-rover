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
     * @ORM\OneToOne(targetEntity="App\Entity\Material", cascade={"persist", "remove"})
     */
    private $materials;

    public function __construct($sizeX, $sizeY)
    {
        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->material = new ArrayCollection();
    }

    


    public function map_gen()
    {
        $material = new Material("wesh");

        $x = $this->sizeX;
        $y = $this->sizeY;
        if (isset($_GET['dif'])) {
            $h = $x;
            $grille = array();
            //Initialisation de la grille
            for ($i = 0; $i < $h; $i++) {
                $grille[$i] = array();
            }
            for ($i = 0; $i < $h; $i++) {
                for ($j = 0; $j < $h; $j++) {
                    $grille[$i][$j] = [0, 'NULL'];
                }
            }
            /** Initialisation de la profondeur à 50 */
            $profondeur = 50;
            /** En fonction de la difficulté, on augmente la profondeur de la carte */
            switch ($_GET['dif']) {

                case 1:
                    $profondeur = 50;
                    break;

                case 2:
                    $profondeur = 75;
                    break;

                case 3:
                    $profondeur = 100;
                    break;

                default:
                    $profondeur = 50;
                    break;
            }
            /** Initialisation des 4 coins de la grille en générant une profondeur aléatoire en fonction 
             * du niveau de la map.
             * $grille[y][x][z]
             */
            $grille[0][0][0] = (int) mt_rand(-($profondeur), $profondeur);
            $grille[0][$h - 1][0] = (int) mt_rand(-($profondeur), $profondeur);
            $grille[$h - 1][0][0] = (int) mt_rand(-($profondeur), $profondeur);
            $grille[$h - 1][$h - 1][0] = (int) mt_rand(-($profondeur), $profondeur);

            $i = $h - 1;

            while ($i > 1) {
                $id = $i / 2;
                //Début de la phase Diamant
                //   dump($grille);
                for ($x = $id; $x < $h - 1; $x += $i) {
                    for ($y = $id; $y < $h - 1; $y = $y + $i) {
                        $moyenne = ($grille[$x - $id][$y - $id][0] + $grille[$x - $id][$y + $id][0] + $grille[$x + $id][$y + $id][0] + $grille[$x + $id][$y - $id][0]) / 4;
                        $grille[$x][$y][0] = (int) ($moyenne + mt_rand(-($id), $id));
                        $grille[$x][$y][1] = $material->setMaterial($grille[$x][$y][0]);
                    }
                }
                //Phase de carré
                $decalage = 0;
                for ($x = 0; $x < $h; $x = $x + $id) {
                    if ($decalage == 0) {
                        $decalage = $id;
                    } else {
                        $decalage = 0;
                    }
                    for ($y = $decalage; $y < $h; $y = $y + $i) {
                        $somme = 0;
                        $n = 0;
                        if ($x >= $id) {
                            $somme = $somme + $grille[$x - $id][$y][0];
                            $n = $n + 1;
                        }
                        if ($x + $id < $h) {
                            $somme = $somme + $grille[$x + $id][$y][0];
                            $n = $n + 1;
                        }
                        if ($y >= $id) {
                            $somme = $somme + $grille[$x][$y - $id][0];
                            $n = $n + 1;
                        }
                        if ($y + $id < $h) {
                            $somme = $somme + $grille[$x][$y + $id][0];
                            $n = $n + 1;
                        }
                        set_time_limit(10);
                        $grille[$x][$y][0] = (int) ($somme / $n + mt_rand(-($id), $id));
                        if ($grille[$x][$y][0] > $profondeur || $grille[$x][$y][0] < -$profondeur) {
                            $grille[$x][$y][0] = (int) ($n + mt_rand(-$profondeur, $profondeur));
                        }


                        //if ($grille[$x][$y][0] > $profondeur || $grille[$x][$y][0] < -$profondeur) {
                        //  $grille[$x][$y][0] = 99;
                        // }

                        $grille[$x][$y][1] = $material->setMaterial($grille[$x][$y][0]);
                        //var_dump($grille[$x][$y][0]);
                    }
                }
                $i = $id;
            }
            return $grille;
        }
    }

    /*
     *  Cette fonction doit renvoyer les cases adjacentes en fonction de la case à la position 
     *  x, y et du rayon
     */
    public function requestAdjCases($x, $y, $grille, $radius = 1)
    {
        //requête adjCases à l'API carte : position rover simulé : (2,2)
        $width = 1000-1;
        $height = 1000-1;

        for ($i = 1; $i <= $radius; $i++) {
            if ($x + $i <= $width) {
                $cases[$x + $i . ',' . $y] = $grille[$x][$y][0]; // milieu droite
            }
            if ($x + $i < $width && $y + $i <= $height) {
                $cases[($x + $i) . ',' . ($y + $i)] = $grille[$x][$y][0]; // bas droite
            }
            if ($y + $i <= $height) {
                $cases[$x . ',' . ($y + $i)] = $grille[$x][$y][0]; // milieu bas
            }
            if ($x - $i >= 0 && $y + $i <= $height) {
                $cases[($x - $i) . ',' . ($y + $i)] = $grille[$x][$y][0]; // bas gauche
            }
            if ($x - $i >= 0) {
                $cases[($x - $i) . ',' . $y] = $grille[$x][$y][0]; // milieu gauche
            }
            if ($x - $i >= 0 && $y - $i >= 0) {
                $cases[($x - $i) . ',' . ($y - $i)] = $grille[$x][$y][0]; // haut gauche
            }
            if ($y - $i >= 0) {
                $cases[$x . ',' . ($y - $i)] = $grille[$x][$y][0]; // milieu haut
            }
            if ($x + $i <= $width && $y - $i >= 0) {
                $cases[($x + $i) . ',' . ($y - $i)] = $grille[$x][$y][0]; // haut droite
            }

        }

        return $cases;
    }

    /*
     *  Cette fonction doit renvoyer l'altitude en fonction de la case à la position 
     *  x et y 
     */

    public function getAltitude($x, $y, $grille)
    { 
        return $grille[$x][$y][0];
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

    public function getMaterials(): ?Material
    {
        return $this->materials;
    }

    public function setMaterials(?Material $materials): self
    {
        $this->materials = $materials;

        return $this;
    }
}
