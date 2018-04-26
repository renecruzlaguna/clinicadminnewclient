<?php

namespace AppBundle\Libs\EntityNormalizer;

use AppBundle\Libs\Normalizer\AbstractNormalizer;
use AppBundle\Libs\Decorator\CustomDecorator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Description of VisitaNormalizer
 *
 * @author code VisitaNormalizer
 */
class VisitaNormalizer extends AbstractNormalizer implements ContainerAwareInterface
{

    private $container;

    public function normalizeObject($object, $prototype)
    {

        $obj = array();
        $normalice = $this->container->get('manager.json');

        switch ($prototype) {
            case CustomDecorator::DEFAULT_DECORATOR:
                $obj['id'] = $object->getId();
                $obj['resumen'] = $object->getResumen();
                $obj['urlInforme'] = $object->getUrlInforme();
                $obj['direccion'] = $object->getDireccion();
                $obj['involucrados'] = $normalice->normalize('normalizer.usuario', $object->getInvolucrados(), CustomDecorator::SIMPLE);
                $fecha = $object->getFechaCreada();

                $obj['fechaCreada'] = empty($fecha) ? '' : $fecha->format('d/m/Y H:i:s');
                $fecha = $object->getFechaEjecucion();

                $obj['fechaEjecucion'] = empty($fecha) ? '' : $fecha->format('Y/m/d');

                $obj['tipoVisita'] = $normalice->normalize('normalizer.tipovisita', $object->getTipoVisita(), CustomDecorator::DEFAULT_DECORATOR);
                $obj['usuario'] = $normalice->normalize('normalizer.usuario', $object->getUsuario(), CustomDecorator::USER_RESPONSIBLE_DECORATOR);
                $obj['tipoTransporte'] = $normalice->normalize('normalizer.tipotransporte', $object->getTipoTransporte(), CustomDecorator::DEFAULT_DECORATOR);
                $obj['subCategoria'] = $normalice->normalize('normalizer.subcategoria', $object->getSubCategoria(), CustomDecorator::DEFAULT_DECORATOR);
                $obj['categoria'] = $obj['subCategoria']['categoria'];
                $obj['estado'] = $normalice->normalize('normalizer.estado', $object->getEstado(), CustomDecorator::DEFAULT_DECORATOR);


                break;
        }

        return $obj;
    }

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}
