<?php

namespace AppBundle\Libs\EntityNormalizer;

use AppBundle\Libs\Normalizer\AbstractNormalizer;
use AppBundle\Libs\Decorator\CustomDecorator;

/**
 * Description of SubCategoriaNormalizer
 *
 * @author code EstadoNormalizer
 */
class SubCategoriaNormalizer extends AbstractNormalizer implements \Symfony\Component\DependencyInjection\ContainerAwareInterface {

    private $container;

    public function normalizeObject($object, $prototype) {
        $normalice = $this->container->get('manager.json');
        $obj = array();
        switch ($prototype) {
            case CustomDecorator::DEFAULT_DECORATOR:
                $obj['id'] = $object->getId();
                $obj['nombre'] = $object->getNombre();
                $obj['descripcion'] = $object->getDescripcion();
                $obj['categoria'] = $normalice->normalize('normalizer.categoria', $object->getCategoria(), CustomDecorator::DEFAULT_DECORATOR);
                ;
                break;
        }

        return $obj;
    }

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        $this->container = $container;
    }

}
