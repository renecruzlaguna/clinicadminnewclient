<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResultQuery
 *
 * @ORM\Table(name="historia_clinica")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResultQueryRepository")
 */
class ResultadoConsulta
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
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="historia")
     * @ORM\JoinColumn(
     *     name="id_cliente",
     *     referencedColumnName="id",
     *     nullable=false
     * )
     */
    private $cliente;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo", type="text")
     */
    private $motivo;

    /**
     * @var string
     *
     * @ORM\Column(name="areas", type="text",nullable=true)
     */
    private $areas;

    /**
     * @var bool
     *
     * @ORM\Column(name="tto", type="boolean")
     */
    private $tto;

    /**
     * @var int
     *
     * @ORM\Column(name="tiempo", type="integer")
     */
    private $tiempo;

    /**
     * @var string
     *
     * @ORM\Column(name="appp", type="text", nullable=true)
     */
    private $appp;

    /**
     * @var string
     *
     * @ORM\Column(name="apf", type="text", nullable=true)
     */
    private $apf;

    /**
     * @var string
     *
     * @ORM\Column(name="reaccionMedicamento", type="text", nullable=true)
     */
    private $reaccionMedicamento;

    /**
     * @var string
     *
     * @ORM\Column(name="experienciaAnestesia", type="text", nullable=true)
     */
    private $experienciaAnestesia;

    /**
     * @var string
     *
     * @ORM\Column(name="antecedentesHemorragicos", type="text", nullable=true)
     */
    private $antecedentesHemorragicos;

    /**
     * @var string
     *
     * @ORM\Column(name="cepilladoDentario", type="text", nullable=true)
     */
    private $cepilladoDentario;

    /**
     * @var string
     *
     * @ORM\Column(name="habitosNocivos", type="text", nullable=true)
     */
    private $habitosNocivos;

    /**
     * @var string
     *
     * @ORM\Column(name="diagnostico", type="text", nullable=true)
     */
    private $diagnostico;

    /**
     * @var string
     *
     * @ORM\Column(name="planificacionTratamiento", type="text", nullable=true)
     */
    private $planificacionTratamiento;

    /**
     * @var string
     *
     * @ORM\Column(name="tratamientoRealizado", type="text", nullable=true)
     */
    private $tratamientoRealizado;

    /**
     * @var string
     *
     * @ORM\Column(name="evolucion", type="text", nullable=true)
     */
    private $evolucion;

    /**
     * @var string
     *
     * @ORM\Column(name="examenFisico", type="text")
     */
    private $examenFisico;

    /**
     * @var string
     *
     * @ORM\Column(name="examenFacial", type="text", nullable=true)
     */
    private $examenFacial;

    /**
     * @var string
     *
     * @ORM\Column(name="ovaloFacial", type="text", nullable=true)
     */
    private $ovaloFacial;

    /**
     * @var string
     *
     * @ORM\Column(name="perfilFacial", type="text", nullable=true)
     */
    private $perfilFacial;

    /**
     * @var string
     *
     * @ORM\Column(name="examenBucal", type="text", nullable=true)
     */
    private $examenBucal;

    /**
     * @var string
     *
     * @ORM\Column(name="labios", type="text", nullable=true)
     */
    private $labios;

    /**
     * @var string
     *
     * @ORM\Column(name="mucosaCarillo", type="text", nullable=true)
     */
    private $mucosaCarillo;


    /**
     * @var string
     *
     * @ORM\Column(name="encias", type="text", nullable=true)
     */
    private $encias;


    /**
     * @var string
     *
     * @ORM\Column(name="paladarDuro", type="text", nullable=true)
     */
    private $paladarDuro;


    /**
     * @var string
     *
     * @ORM\Column(name="orofaringe", type="text", nullable=true)
     */
    private $orofaringe;


    /**
     * @var string
     *
     * @ORM\Column(name="lengua", type="text", nullable=true)
     */
    private $lengua;


    /**
     * @var string
     *
     * @ORM\Column(name="pisoLengua", type="text", nullable=true)
     */
    private $pisoLengua;


    /**
     * @var string
     *
     * @ORM\Column(name="glandulasSalibales", type="text", nullable=true)
     */
    private $glandulasSalibales;


    /**
     * @var string
     *
     * @ORM\Column(name="relacionOclusion", type="text", nullable=true)
     */
    private $relacionOclusion;


    /**
     * @var string
     *
     * @ORM\Column(name="relacionMolar", type="text", nullable=true)
     */
    private $relacionMolar;


    /**
     * @var string
     *
     * @ORM\Column(name="relacionCanino", type="text", nullable=true)
     */
    private $relacionCanino;


    /**
     * @var string
     *
     * @ORM\Column(name="resalte", type="text", nullable=true)
     */
    private $resalte;


    /**
     * @var string
     *
     * @ORM\Column(name="sobrepase", type="text", nullable=true)
     */
    private $sobrepase;


    public function __construct()
    {
        $this->tiempo = 0;
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
     * Set motivo
     *
     * @param string $motivo
     * @return ResultadoConsulta
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;

        return $this;
    }

    /**
     * Get motivo
     *
     * @return string 
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set tto
     *
     * @param boolean $tto
     * @return ResultadoConsulta
     */
    public function setTto($tto)
    {
        $this->tto = $tto;

        return $this;
    }

    /**
     * Get tto
     *
     * @return boolean 
     */
    public function getTto()
    {
        return $this->tto;
    }

    /**
     * Set tiempo
     *
     * @param integer $tiempo
     * @return ResultadoConsulta
     */
    public function setTiempo($tiempo)
    {
        $this->tiempo = $tiempo;

        return $this;
    }

    /**
     * Get tiempo
     *
     * @return integer 
     */
    public function getTiempo()
    {
        return $this->tiempo;
    }

    /**
     * Set appp
     *
     * @param string $appp
     * @return ResultadoConsulta
     */
    public function setAppp($appp)
    {
        $this->appp = $appp;

        return $this;
    }

    /**
     * Get appp
     *
     * @return string 
     */
    public function getAppp()
    {
        return $this->appp;
    }

    /**
     * Set apf
     *
     * @param string $apf
     * @return ResultadoConsulta
     */
    public function setApf($apf)
    {
        $this->apf = $apf;

        return $this;
    }

    /**
     * Get apf
     *
     * @return string 
     */
    public function getApf()
    {
        return $this->apf;
    }

    /**
     * Set reaccionMedicamento
     *
     * @param string $reaccionMedicamento
     * @return ResultadoConsulta
     */
    public function setReaccionMedicamento($reaccionMedicamento)
    {
        $this->reaccionMedicamento = $reaccionMedicamento;

        return $this;
    }

    /**
     * Get reaccionMedicamento
     *
     * @return string 
     */
    public function getReaccionMedicamento()
    {
        return $this->reaccionMedicamento;
    }

    /**
     * Set experienciaAnestesia
     *
     * @param string $experienciaAnestesia
     * @return ResultadoConsulta
     */
    public function setExperienciaAnestesia($experienciaAnestesia)
    {
        $this->experienciaAnestesia = $experienciaAnestesia;

        return $this;
    }

    /**
     * Get experienciaAnestesia
     *
     * @return string 
     */
    public function getExperienciaAnestesia()
    {
        return $this->experienciaAnestesia;
    }

    /**
     * Set antecedentesHemorragicos
     *
     * @param string $antecedentesHemorragicos
     * @return ResultadoConsulta
     */
    public function setAntecedentesHemorragicos($antecedentesHemorragicos)
    {
        $this->antecedentesHemorragicos = $antecedentesHemorragicos;

        return $this;
    }

    /**
     * Get antecedentesHemorragicos
     *
     * @return string 
     */
    public function getAntecedentesHemorragicos()
    {
        return $this->antecedentesHemorragicos;
    }

    /**
     * Set cepilladoDentario
     *
     * @param string $cepilladoDentario
     * @return ResultadoConsulta
     */
    public function setCepilladoDentario($cepilladoDentario)
    {
        $this->cepilladoDentario = $cepilladoDentario;

        return $this;
    }

    /**
     * Get cepilladoDentario
     *
     * @return string 
     */
    public function getCepilladoDentario()
    {
        return $this->cepilladoDentario;
    }

    /**
     * Set habitosNocivos
     *
     * @param string $habitosNocivos
     * @return ResultadoConsulta
     */
    public function setHabitosNocivos($habitosNocivos)
    {
        $this->habitosNocivos = $habitosNocivos;

        return $this;
    }

    /**
     * Get habitosNocivos
     *
     * @return string 
     */
    public function getHabitosNocivos()
    {
        return $this->habitosNocivos;
    }

    /**
     * Set diagnostico
     *
     * @param string $diagnostico
     * @return ResultadoConsulta
     */
    public function setDiagnostico($diagnostico)
    {
        $this->diagnostico = $diagnostico;

        return $this;
    }

    /**
     * Get diagnostico
     *
     * @return string 
     */
    public function getDiagnostico()
    {
        return $this->diagnostico;
    }

    /**
     * Set planificacionTratamiento
     *
     * @param string $planificacionTratamiento
     * @return ResultadoConsulta
     */
    public function setPlanificacionTratamiento($planificacionTratamiento)
    {
        $this->planificacionTratamiento = $planificacionTratamiento;

        return $this;
    }

    /**
     * Get planificacionTratamiento
     *
     * @return string 
     */
    public function getPlanificacionTratamiento()
    {
        return $this->planificacionTratamiento;
    }

    /**
     * Set tratamientoRealizado
     *
     * @param string $tratamientoRealizado
     * @return ResultadoConsulta
     */
    public function setTratamientoRealizado($tratamientoRealizado)
    {
        $this->tratamientoRealizado = $tratamientoRealizado;

        return $this;
    }

    /**
     * Get tratamientoRealizado
     *
     * @return string 
     */
    public function getTratamientoRealizado()
    {
        return $this->tratamientoRealizado;
    }

    /**
     * Set evolucion
     *
     * @param string $evolucion
     * @return ResultadoConsulta
     */
    public function setEvolucion($evolucion)
    {
        $this->evolucion = $evolucion;

        return $this;
    }

    /**
     * Get evolucion
     *
     * @return string 
     */
    public function getEvolucion()
    {
        return $this->evolucion;
    }

    /**
     * Set examenFisico
     *
     * @param string $examenFisico
     * @return ResultadoConsulta
     */
    public function setExamenFisico($examenFisico)
    {
        $this->examenFisico = $examenFisico;

        return $this;
    }

    /**
     * Get examenFisico
     *
     * @return string 
     */
    public function getExamenFisico()
    {
        return $this->examenFisico;
    }

    /**
     * Set examenFacial
     *
     * @param string $examenFacial
     * @return ResultadoConsulta
     */
    public function setExamenFacial($examenFacial)
    {
        $this->examenFacial = $examenFacial;

        return $this;
    }

    /**
     * Get examenFacial
     *
     * @return string 
     */
    public function getExamenFacial()
    {
        return $this->examenFacial;
    }

    /**
     * Set ovaloFacial
     *
     * @param string $ovaloFacial
     * @return ResultadoConsulta
     */
    public function setOvaloFacial($ovaloFacial)
    {
        $this->ovaloFacial = $ovaloFacial;

        return $this;
    }

    /**
     * Get ovaloFacial
     *
     * @return string 
     */
    public function getOvaloFacial()
    {
        return $this->ovaloFacial;
    }

    /**
     * Set perfilFacial
     *
     * @param string $perfilFacial
     * @return ResultadoConsulta
     */
    public function setPerfilFacial($perfilFacial)
    {
        $this->perfilFacial = $perfilFacial;

        return $this;
    }

    /**
     * Get perfilFacial
     *
     * @return string 
     */
    public function getPerfilFacial()
    {
        return $this->perfilFacial;
    }

    /**
     * Set examenBucal
     *
     * @param string $examenBucal
     * @return ResultadoConsulta
     */
    public function setExamenBucal($examenBucal)
    {
        $this->examenBucal = $examenBucal;

        return $this;
    }

    /**
     * Get examenBucal
     *
     * @return string 
     */
    public function getExamenBucal()
    {
        return $this->examenBucal;
    }

    /**
     * Set labios
     *
     * @param string $labios
     * @return ResultadoConsulta
     */
    public function setLabios($labios)
    {
        $this->labios = $labios;

        return $this;
    }

    /**
     * Get labios
     *
     * @return string 
     */
    public function getLabios()
    {
        return $this->labios;
    }

    /**
     * Set mucosaCarillo
     *
     * @param string $mucosaCarillo
     * @return ResultadoConsulta
     */
    public function setMucosaCarillo($mucosaCarillo)
    {
        $this->mucosaCarillo = $mucosaCarillo;

        return $this;
    }

    /**
     * Get mucosaCarillo
     *
     * @return string 
     */
    public function getMucosaCarillo()
    {
        return $this->mucosaCarillo;
    }

    /**
     * Set encias
     *
     * @param string $encias
     * @return ResultadoConsulta
     */
    public function setEncias($encias)
    {
        $this->encias = $encias;

        return $this;
    }

    /**
     * Get encias
     *
     * @return string 
     */
    public function getEncias()
    {
        return $this->encias;
    }

    /**
     * Set paladarDuro
     *
     * @param string $paladarDuro
     * @return ResultadoConsulta
     */
    public function setPaladarDuro($paladarDuro)
    {
        $this->paladarDuro = $paladarDuro;

        return $this;
    }

    /**
     * Get paladarDuro
     *
     * @return string 
     */
    public function getPaladarDuro()
    {
        return $this->paladarDuro;
    }

    /**
     * Set orofaringe
     *
     * @param string $orofaringe
     * @return ResultadoConsulta
     */
    public function setOrofaringe($orofaringe)
    {
        $this->orofaringe = $orofaringe;

        return $this;
    }

    /**
     * Get orofaringe
     *
     * @return string 
     */
    public function getOrofaringe()
    {
        return $this->orofaringe;
    }

    /**
     * Set lengua
     *
     * @param string $lengua
     * @return ResultadoConsulta
     */
    public function setLengua($lengua)
    {
        $this->lengua = $lengua;

        return $this;
    }

    /**
     * Get lengua
     *
     * @return string 
     */
    public function getLengua()
    {
        return $this->lengua;
    }

    /**
     * Set pisoLengua
     *
     * @param string $pisoLengua
     * @return ResultadoConsulta
     */
    public function setPisoLengua($pisoLengua)
    {
        $this->pisoLengua = $pisoLengua;

        return $this;
    }

    /**
     * Get pisoLengua
     *
     * @return string 
     */
    public function getPisoLengua()
    {
        return $this->pisoLengua;
    }

    /**
     * Set glandulasSalibales
     *
     * @param string $glandulasSalibales
     * @return ResultadoConsulta
     */
    public function setGlandulasSalibales($glandulasSalibales)
    {
        $this->glandulasSalibales = $glandulasSalibales;

        return $this;
    }

    /**
     * Get glandulasSalibales
     *
     * @return string 
     */
    public function getGlandulasSalibales()
    {
        return $this->glandulasSalibales;
    }

    /**
     * Set relacionOclusion
     *
     * @param string $relacionOclusion
     * @return ResultadoConsulta
     */
    public function setRelacionOclusion($relacionOclusion)
    {
        $this->relacionOclusion = $relacionOclusion;

        return $this;
    }

    /**
     * Get relacionOclusion
     *
     * @return string 
     */
    public function getRelacionOclusion()
    {
        return $this->relacionOclusion;
    }

    /**
     * Set relacionMolar
     *
     * @param string $relacionMolar
     * @return ResultadoConsulta
     */
    public function setRelacionMolar($relacionMolar)
    {
        $this->relacionMolar = $relacionMolar;

        return $this;
    }

    /**
     * Get relacionMolar
     *
     * @return string 
     */
    public function getRelacionMolar()
    {
        return $this->relacionMolar;
    }

    /**
     * Set relacionCanino
     *
     * @param string $relacionCanino
     * @return ResultadoConsulta
     */
    public function setRelacionCanino($relacionCanino)
    {
        $this->relacionCanino = $relacionCanino;

        return $this;
    }

    /**
     * Get relacionCanino
     *
     * @return string 
     */
    public function getRelacionCanino()
    {
        return $this->relacionCanino;
    }

    /**
     * Set resalte
     *
     * @param string $resalte
     * @return ResultadoConsulta
     */
    public function setResalte($resalte)
    {
        $this->resalte = $resalte;

        return $this;
    }

    /**
     * Get resalte
     *
     * @return string 
     */
    public function getResalte()
    {
        return $this->resalte;
    }

    /**
     * Set sobrepase
     *
     * @param string $sobrepase
     * @return ResultadoConsulta
     */
    public function setSobrepase($sobrepase)
    {
        $this->sobrepase = $sobrepase;

        return $this;
    }

    /**
     * Get sobrepase
     *
     * @return string 
     */
    public function getSobrepase()
    {
        return $this->sobrepase;
    }

    /**
     * Set cliente
     *
     * @param \AppBundle\Entity\Usuario $cliente
     * @return ResultadoConsulta
     */
    public function setCliente(\AppBundle\Entity\Usuario $cliente)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \AppBundle\Entity\Usuario 
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set areas
     *
     * @param string $areas
     * @return ResultadoConsulta
     */
    public function setAreas($areas)
    {
        $this->areas = $areas;

        return $this;
    }

    /**
     * Get areas
     *
     * @return string 
     */
    public function getAreas()
    {
        return $this->areas;
    }
}
