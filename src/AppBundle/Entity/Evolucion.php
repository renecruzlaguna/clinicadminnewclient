<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResultQuery
 *
 * @ORM\Table(name="evolucion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EvolucionRepository")
 */
class Evolucion
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Consulta", inversedBy="evolucion")
     * @ORM\JoinColumn(
     *     name="id_consulta",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $consulta;
    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="text")
     */
    private $observaciones;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return Evolucion
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set consulta
     *
     * @param \AppBundle\Entity\Consulta $consulta
     * @return Evolucion
     */
    public function setConsulta(\AppBundle\Entity\Consulta $consulta)
    {
        $this->consulta = $consulta;

        return $this;
    }

    /**
     * Get consulta
     *
     * @return \AppBundle\Entity\Consulta 
     */
    public function getConsulta()
    {
        return $this->consulta;
    }
}
