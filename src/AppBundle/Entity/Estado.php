<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Estado
 *
 * @ORM\Table(name="estado")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EstadoRepository")
 * @UniqueEntity( fields={"nombre"}, message="Ya existe un estado con el nombre proporcionado." )
 * )
 */
class Estado
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
     * @ORM\Column(name="nombre", type="string", unique=true, length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Consulta", mappedBy="estado")
     */
    private $consulta;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->consulta = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return Estado
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
     * @return Estado
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
     * Add consulta
     *
     * @param \AppBundle\Entity\Consulta $consulta
     * @return Estado
     */
    public function addConsultum(\AppBundle\Entity\Consulta $consulta)
    {
        $this->consulta[] = $consulta;

        return $this;
    }

    /**
     * Remove consulta
     *
     * @param \AppBundle\Entity\Consulta $consulta
     */
    public function removeConsultum(\AppBundle\Entity\Consulta $consulta)
    {
        $this->consulta->removeElement($consulta);
    }

    /**
     * Get consulta
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConsulta()
    {
        return $this->consulta;
    }
}
