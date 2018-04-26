<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Tipo
 *
 * @ORM\Table(name="plantrabajo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlanTrabajoRepository")
 */
class PlanTrabajo {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="planTrabajo")
     * @ORM\JoinColumn(
     *     name="id_medico",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $usuario;

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
     * @return PlanTrabajo
     */
    public function setDia($dia) {
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

    /**
     * Set mes
     *
     * @param string $mes
     * @return PlanTrabajo
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
     * @return PlanTrabajo
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
     * Set usuario
     *
     * @param \AppBundle\Entity\Usuario $usuario
     * @return PlanTrabajo
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
     * Set horaInicial
     *
     * @param string $horaInicial
     * @return PlanTrabajo
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
     * @return PlanTrabajo
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
     * Set minutoFinal
     *
     * @param string $minutoFinal
     * @return PlanTrabajo
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
     * Set minutoInicial
     *
     * @param string $minutoInicial
     * @return PlanTrabajo
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

    private function completeZero($value) {
        if ($value < 10)
            return '0' . $value;
        return $value;
    }

    public function getDiaWithZero() {
        return $this->completeZero($this->getDia());
    }

}
