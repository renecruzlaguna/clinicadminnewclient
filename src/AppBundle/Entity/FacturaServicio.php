<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Visita
 *
 * @ORM\Table(name="factura_serv")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ConsultaRepository")
 */
class FacturaServicio
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
     * @var int
     *
     * @ORM\Column(name="cantidad", type="integer")
     */
    private $cantidad;



    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FacturaConsulta", inversedBy="facturaserv")
     * @ORM\JoinColumn(
     *     name="id_factura",
     *     referencedColumnName="id",
     *     onDelete="CASCADE",
     *     nullable=false
     * )
     */
    private $factura;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Servicio", inversedBy="facturaserv")
     * @ORM\JoinColumn(
     *     name="id_servicio",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $servicio;



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
     * Set cantidad
     *
     * @param integer $cantidad
     * @return FacturaServicio
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer 
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set factura
     *
     * @param \AppBundle\Entity\FacturaConsulta $factura
     * @return FacturaServicio
     */
    public function setFactura(\AppBundle\Entity\FacturaConsulta $factura)
    {
        $this->factura = $factura;

        return $this;
    }

    /**
     * Get factura
     *
     * @return \AppBundle\Entity\FacturaConsulta 
     */
    public function getFactura()
    {
        return $this->factura;
    }

    /**
     * Set servicio
     *
     * @param \AppBundle\Entity\Servicio $servicio
     * @return FacturaServicio
     */
    public function setServicio(\AppBundle\Entity\Servicio $servicio)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return \AppBundle\Entity\Servicio 
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * @param int $id
     */

}
