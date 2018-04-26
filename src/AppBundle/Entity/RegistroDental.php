<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Visita
 *
 * @ORM\Table(name="registro_dental")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ConsultaRepository")
 * * @UniqueEntity( fields={"paciente"}, message="Ya existe un registro dental para el paciente seleccionado." )
 */
class RegistroDental
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
     * @ORM\Column(name="fecha_creacion", type="datetime")
     */
    private $fechaCreacion;
    public function __construct()
    {
        $this->fechaCreacion= new \DateTime();
    }


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="registroDentalPaciente")
     * @ORM\JoinColumn(
     *     name="id_paciente",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $paciente;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="registroDentalMedico")
     * @ORM\JoinColumn(
     *     name="id_medico_registro",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $medico;

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
     * Set paciente
     *
     * @param \AppBundle\Entity\Usuario $paciente
     * @return RegistroDental
     */
    public function setPaciente(\AppBundle\Entity\Usuario $paciente)
    {
        $this->paciente = $paciente;

        return $this;
    }

    /**
     * Get paciente
     *
     * @return \AppBundle\Entity\Usuario 
     */
    public function getPaciente()
    {
        return $this->paciente;
    }

    /**
     * Set medico
     *
     * @param \AppBundle\Entity\Usuario $medico
     * @return RegistroDental
     */
    public function setMedico(\AppBundle\Entity\Usuario $medico)
    {
        $this->medico = $medico;

        return $this;
    }

    /**
     * Get medico
     *
     * @return \AppBundle\Entity\Usuario 
     */
    public function getMedico()
    {
        return $this->medico;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     * @return RegistroDental
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion
     *
     * @return \DateTime 
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }
}
