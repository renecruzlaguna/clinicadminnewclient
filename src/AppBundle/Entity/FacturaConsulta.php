<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResultQuery
 *
 * @ORM\Table(name="factura")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FacturaConsultaRepository")
 */
class FacturaConsulta
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
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Consulta", inversedBy="factura")
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
     * @ORM\Column(name="descripcion", type="text",nullable=true)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="fichero", type="string",nullable=true)
     */
    private $fichero;

    /**
     * @var bool
     *
     * @ORM\Column(name="fecha", type="string")
     */
    private $fecha;

    /**
     * @var bool
     *
     * @ORM\Column(type="string",nullable=true)
     */
    private $guia;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="float")
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="ciudad", type="string",nullable=true)
     */
    private $ciudad;
    /**
     * @var string
     *
     * @ORM\Column(type="integer")
     */
    private $numeroFactura;

    /**
     * @var string
     *
     * @ORM\Column(type="integer",nullable=true)
     */
    private $enviado;

    /**
     * @var string
     *
     * @ORM\Column(type="integer",nullable=true)
     */
    private $formaPago;



    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FacturaServicio", mappedBy="factura")
     */
    private $facturaserv;




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
     * Set descripcion
     *
     * @param string $descripcion
     * @return FacturaConsulta
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set fecha
     *
     * @param boolean $fecha
     * @return FacturaConsulta
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return boolean 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set total
     *
     * @param float $total
     * @return FacturaConsulta
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float 
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set ciudad
     *
     * @param string $ciudad
     * @return FacturaConsulta
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    /**
     * Get ciudad
     *
     * @return string 
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set numeroFactura
     *
     * @param integer $numeroFactura
     * @return FacturaConsulta
     */
    public function setNumeroFactura($numeroFactura)
    {
        $this->numeroFactura = $numeroFactura;

        return $this;
    }

    /**
     * Get numeroFactura
     *
     * @return integer 
     */
    public function getNumeroFactura()
    {
        if(strlen($this->numeroFactura)<7){
            return str_pad($this->numeroFactura, 7, "0", STR_PAD_LEFT);
        }
        return $this->numeroFactura;
    }

    /**
     * Set formaPago
     *
     * @param integer $formaPago
     * @return FacturaConsulta
     */
    public function setFormaPago($formaPago)
    {
        $this->formaPago = $formaPago;

        return $this;
    }

    /**
     * Get formaPago
     *
     * @return integer 
     */
    public function getFormaPago()
    {
        return $this->formaPago;
    }

    /**
     * Set consulta
     *
     * @param \AppBundle\Entity\Consulta $consulta
     * @return FacturaConsulta
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

    /**
     * Set guia
     *
     * @param string $guia
     * @return FacturaConsulta
     */
    public function setGuia($guia)
    {
        $this->guia = $guia;

        return $this;
    }

    /**
     * Get guia
     *
     * @return string 
     */
    public function getGuia()
    {
        return $this->guia;
    }
    public function adaptDescripcion(){
        $words=explode(' ',$this->descripcion);
        $returns=array();
    }

    /**
     * Set fichero
     *
     * @param string $fichero
     * @return FacturaConsulta
     */
    public function setFichero($fichero)
    {
        $this->fichero = $fichero;

        return $this;
    }

    /**
     * Get fichero
     *
     * @return string 
     */
    public function getFichero()
    {
        return $this->fichero;
    }

    /**
     * Set enviado
     *
     * @param integer $enviado
     * @return FacturaConsulta
     */
    public function setEnviado($enviado)
    {
        $this->enviado = $enviado;

        return $this;
    }

    /**
     * Get enviado
     *
     * @return integer 
     */
    public function getEnviado()
    {
        return $this->enviado;
    }



    public function getValueTotal(){
       return $this->getTotal();
    }
    public function getIVA(){

        return (( ($this->getValueTotal()*12)/100));
    }
    function redondear_dos_decimal($valor) {
        $float_redondeado=round($valor * 100) / 100;
        return $float_redondeado;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->facturaserv = new \Doctrine\Common\Collections\ArrayCollection();
        $this->enviado=0;
    }

    /**
     * Add facturaserv
     *
     * @param \AppBundle\Entity\FacturaServicio $facturaserv
     * @return FacturaConsulta
     */
    public function addFacturaserv(\AppBundle\Entity\FacturaServicio $facturaserv)
    {
        $this->facturaserv[] = $facturaserv;

        return $this;
    }

    /**
     * Remove facturaserv
     *
     * @param \AppBundle\Entity\FacturaServicio $facturaserv
     */
    public function removeFacturaserv(\AppBundle\Entity\FacturaServicio $facturaserv)
    {
        $this->facturaserv->removeElement($facturaserv);
    }

    /**
     * Get facturaserv
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFacturaserv()
    {
        return $this->facturaserv;
    }
}
