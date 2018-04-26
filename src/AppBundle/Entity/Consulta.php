<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Visita
 *
 * @ORM\Table(name="consulta")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ConsultaRepository")
 * * @UniqueEntity( fields={"usuario","usuarioRegistro","dia","mes","anno","horaInicial","horaFinal","minutoInicial","minutoFinal"}, message="Ya existe una consulta con el mismo medico en la fecha seleccionada." )
 */
class Consulta {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Especialidad", inversedBy="consulta")
     * @ORM\JoinColumn(
     *     name="id_especialidad",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $especialidad;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Estado", inversedBy="consulta")
     * @ORM\JoinColumn(
     *     name="id_estado",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $estado;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Evolucion", mappedBy="consulta")
     */
    private $evolucion;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\FacturaConsulta", mappedBy="consulta")
     */
    private $factura;

    /**
     * @var float
     *
     * @ORM\Column(type="integer",options={"default":1})
     */
    private $tipoConsulta = 1;

    /**
     * @var \DateTime
     *
     * @ORM\Column type="datetime",nullable=true)
     *
     */
    private $fechaCompleta;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="consulta")
     * @ORM\JoinColumn(
     *     name="id_medico",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="consultaRegistro")
     * @ORM\JoinColumn(
     *     name="id_usuario_registro",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $usuarioRegistro;

    /**
     * @var \DateTime
     *
     * @ORM\Column type="integer")
     *
     */
    private $dia;

    /**
     *
     *
     * @ORM\Column type="integer")
     *
     */
    private $mes;

    /**
     *
     *
     * @ORM\Column type="integer")
     *
     */
    private $anno;

    /**
     *
     *
     * @ORM\Column type="integer")
     *
     */
    private $horaInicial;

    /**
     *
     *
     * @ORM\Column type="integer")
     *
     */
    private $horaFinal;

    /**
     *
     *
     * @ORM\Column type="integer")
     *
     */
    private $minutoInicial;

    /**
     *
     *
     * @ORM\Column type="integer")
     *
     */
    private $minutoFinal;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje", type="text", length=255,nullable=true)
     */
    private $mensaje;

    public function getDataToEmail() {
        return 'por el usuario con nombre ' . $this->getUsuarioRegistro()->getCompleteName() . ' para la fecha ' . $this->completeZero($this->getDia()) . '/' . ($this->getMes()) . '/' . $this->getAnno() . ' y la hora ' . $this->getHoraInicialC() . ':' . $this->getMinutoInicialC();
    }

    public function getDataDate() {
        return ($this->getMes()) . '/' . $this->completeZero($this->getDia()) . '/' . $this->getAnno() . ' ' . $this->getHoraInicialC() . ':' . $this->getMinutoInicialC();
    }

    public function getDataToEmailClient() {
        return ' para la fecha ' . $this->completeZero($this->getDia()) . '/' . $this->getMes() . '/' . $this->getAnno() . ' y la hora ' . $this->getHoraInicialC() . ':' . $this->getMinutoInicialC() . ' y ha seleccionado como doctor a:' . $this->getUsuario()->getCompleteName() . ' en la especialidad de ' . $this->getEspecialidad()->getNombre() . '.';
    }

    public function getDataToEmailClientAprobbed() {
        return ' para la fecha ' . $this->completeZero($this->getDia()) . '/' . $this->getMes() . '/' . $this->getAnno() . ' y la hora ' . $this->getHoraInicialC() . ':' . $this->getMinutoInicialC() . ' y  como doctor a:' . $this->getUsuario()->getCompleteName() . ' en la especialidad de ' . $this->getEspecialidad()->getNombre() . '.';
    }

    public function getDateClientAprobbed() {
        return ' ' . $this->completeZero($this->getDia()) . '/' . $this->getMes() . '/' . $this->getAnno() . ' a las ' . $this->getHoraInicialC() . ':' . $this->getMinutoInicialC() . '  con el doctor: ' . $this->getUsuario()->getCompleteName() . ' en la especialidad de ' . $this->getEspecialidad()->getNombre() . '.';
    }

    public function getHoraInicialC() {
        return $this->completeZero($this->horaInicial);
    }

    public function getHoraFinalC() {
        return $this->completeZero($this->horaFinal);
    }

    public function getMinutoInicialC() {
        return $this->completeZero($this->minutoInicial);
    }

    public function getMinutoFinalC() {
        return $this->completeZero($this->minutoFinal);
    }

    private function completeZero($value) {
        if ($value < 10)
            return '0' . $value;
        return $value;
    }

    /**
     * Set resultado
     *
     * @param \AppBundle\Entity\ResultadoConsulta $resultado
     * @return Consulta
     */
    public function setResultado(\AppBundle\Entity\ResultadoConsulta $resultado = null) {
        $this->resultado = $resultado;

        return $this;
    }

    /**
     * Get resultado
     *
     * @return \AppBundle\Entity\ResultadoConsulta
     */
    public function getResultado() {
        return $this->resultado;
    }

    /**
     * Set factura
     *
     * @param \AppBundle\Entity\FacturaConsulta $factura
     * @return Consulta
     */
    public function setFactura(\AppBundle\Entity\FacturaConsulta $factura = null) {
        $this->factura = $factura;

        return $this;
    }

    /**
     * Get factura
     *
     * @return \AppBundle\Entity\FacturaConsulta
     */
    public function getFactura() {
        return $this->factura;
    }

    public function getDataToTemplate() {
        return ' ' . $this->getDia() . '/' . $this->getMes() . '/' . $this->getAnno() . ' ' . $this->getHoraInicialC() . ':' . $this->getMinutoInicialC();
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->servicio = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add servicio
     *
     * @param \AppBundle\Entity\Servicio $servicio
     * @return Consulta
     */
    public function addServicio(\AppBundle\Entity\Servicio $servicio) {
        $this->servicio[] = $servicio;

        return $this;
    }

    /**
     * Remove servicio
     *
     * @param \AppBundle\Entity\Servicio $servicio
     */
    public function removeServicio(\AppBundle\Entity\Servicio $servicio) {
        $this->servicio->removeElement($servicio);
    }

    /**
     * Get servicio
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServicio() {
        return $this->servicio;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set dia
     *
     * @param string $dia
     * @return Consulta
     */
    public function setDia($dia) {
        if ($dia < 10) {
            $dia = '0' . $dia;
        }
        $this->dia = $dia;

        return $this;
    }

    /**
     * Get dia
     *
     * @return string
     */
    public function getDia() {
        return $this->dia;
    }

    public function getDiaWithZero() {
       return $this->completeZero($this->getDia());
    }

    /**
     * Set mes
     *
     * @param string $mes
     * @return Consulta
     */
    public function setMes($mes) {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return string
     */
    public function getMes() {
        return $this->mes;
    }

    /**
     * Set anno
     *
     * @param string $anno
     * @return Consulta
     */
    public function setAnno($anno) {
        $this->anno = $anno;

        return $this;
    }

    /**
     * Get anno
     *
     * @return string
     */
    public function getAnno() {
        return $this->anno;
    }

    /**
     * Set horaInicial
     *
     * @param string $horaInicial
     * @return Consulta
     */
    public function setHoraInicial($horaInicial) {
        $this->horaInicial = $horaInicial;

        return $this;
    }

    /**
     * Get horaInicial
     *
     * @return string
     */
    public function getHoraInicial() {
        return $this->horaInicial;
    }

    /**
     * Set horaFinal
     *
     * @param string $horaFinal
     * @return Consulta
     */
    public function setHoraFinal($horaFinal) {
        $this->horaFinal = $horaFinal;

        return $this;
    }

    /**
     * Get horaFinal
     *
     * @return string
     */
    public function getHoraFinal() {
        return $this->horaFinal;
    }

    /**
     * Set minutoInicial
     *
     * @param string $minutoInicial
     * @return Consulta
     */
    public function setMinutoInicial($minutoInicial) {
        $this->minutoInicial = $minutoInicial;

        return $this;
    }

    /**
     * Get minutoInicial
     *
     * @return string
     */
    public function getMinutoInicial() {
        return $this->minutoInicial;
    }

    /**
     * Set minutoFinal
     *
     * @param string $minutoFinal
     * @return Consulta
     */
    public function setMinutoFinal($minutoFinal) {
        $this->minutoFinal = $minutoFinal;

        return $this;
    }

    /**
     * Get minutoFinal
     *
     * @return string
     */
    public function getMinutoFinal() {
        return $this->minutoFinal;
    }

    /**
     * Set mensaje
     *
     * @param string $mensaje
     * @return Consulta
     */
    public function setMensaje($mensaje) {
        $this->mensaje = $mensaje;

        return $this;
    }

    /**
     * Get mensaje
     *
     * @return string
     */
    public function getMensaje() {
        return $this->mensaje;
    }

    /**
     * Set especialidad
     *
     * @param \AppBundle\Entity\Especialidad $especialidad
     * @return Consulta
     */
    public function setEspecialidad(\AppBundle\Entity\Especialidad $especialidad) {
        $this->especialidad = $especialidad;

        return $this;
    }

    /**
     * Get especialidad
     *
     * @return \AppBundle\Entity\Especialidad
     */
    public function getEspecialidad() {
        return $this->especialidad;
    }

    /**
     * Set estado
     *
     * @param \AppBundle\Entity\Estado $estado
     * @return Consulta
     */
    public function setEstado(\AppBundle\Entity\Estado $estado) {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return \AppBundle\Entity\Estado
     */
    public function getEstado() {
        return $this->estado;
    }

    /**
     * Set evolucion
     *
     * @param \AppBundle\Entity\Evolucion $evolucion
     * @return Consulta
     */
    public function setEvolucion(\AppBundle\Entity\Evolucion $evolucion = null) {
        $this->evolucion = $evolucion;

        return $this;
    }

    /**
     * Get evolucion
     *
     * @return \AppBundle\Entity\Evolucion
     */
    public function getEvolucion() {
        return $this->evolucion;
    }

    /**
     * Set usuario
     *
     * @param \AppBundle\Entity\Usuario $usuario
     * @return Consulta
     */
    public function setUsuario(\AppBundle\Entity\Usuario $usuario) {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \AppBundle\Entity\Usuario
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * Set usuarioRegistro
     *
     * @param \AppBundle\Entity\Usuario $usuarioRegistro
     * @return Consulta
     */
    public function setUsuarioRegistro(\AppBundle\Entity\Usuario $usuarioRegistro) {
        $this->usuarioRegistro = $usuarioRegistro;

        return $this;
    }

    /**
     * Get usuarioRegistro
     *
     * @return \AppBundle\Entity\Usuario
     */
    public function getUsuarioRegistro() {
        return $this->usuarioRegistro;
    }

    /**
     * Set tipoConsulta
     *
     * @param integer $tipoConsulta
     * @return Consulta
     */
    public function setTipoConsulta($tipoConsulta) {
        $this->tipoConsulta = $tipoConsulta;

        return $this;
    }

    /**
     * Get tipoConsulta
     *
     * @return integer
     */
    public function getTipoConsulta() {
        return $this->tipoConsulta;
    }

    public function updateCompleteDate() {
        $this->fechaCompleta = $this->mes . '-' . $this->dia . '-' . $this->anno;
    }

}
