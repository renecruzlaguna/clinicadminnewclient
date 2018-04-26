<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Tipo
 *
 * @ORM\Table(name="servicio")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServicioRepository")
 * @UniqueEntity(fields={"nombre"}, message="Ya existe un servicio con el nombre dado")
 */
class Servicio
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255,unique=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $precio;



    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FacturaServicio", mappedBy="servicio")
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
     * Set nombre
     *
     * @param string $nombre
     * @return Servicio
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Servicio
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
     * Set precio
     *
     * @param float $precio
     * @return Servicio
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return float 
     */
    public function getPrecio()
    {
        return $this->precio;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->facturaserv = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add facturaserv
     *
     * @param \AppBundle\Entity\FacturaServicio $facturaserv
     * @return Servicio
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
    public function findByIdFactura($id)
    {
        $serv=$this->facturaserv->toArray();
        foreach ($serv as $s){
            if($s->getFactura()->getId()==$id)
                return $s;

        }

    }
}
