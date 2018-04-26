<?php

namespace AppBundle\Libs\EntityNormalizer;

use AppBundle\Libs\Normalizer\AbstractNormalizer;
use AppBundle\Libs\Decorator\CustomDecorator;

/**
 * Description of VisitaNormalizer
 *
 * @author code VisitaNormalizer
 */
class MensajeNormalizer extends AbstractNormalizer{

    public function normalizeObject($object, $prototype) {

        $obj = array();
        switch ($prototype) {
            case CustomDecorator::DEFAULT_DECORATOR:
                $obj['id'] = $object->getId();
                $obj['titulo'] = $object->getTitulo();
                $obj['cuerpo'] = $object->getCuerpo();
                $obj['leido'] = $object->getLeido();
                $obj['fecha'] = $object->getFecha()->format('Y/m/d');
                $obj['nombreUsuario'] = $object->getUsuario()->getCompleteName();
                $obj['correo'] = $object->getUsuario()->getCorreo();
                break;
        }

        return $obj;
    }
}
