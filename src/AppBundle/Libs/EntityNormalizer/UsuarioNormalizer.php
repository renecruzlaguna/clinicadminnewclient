<?php

namespace AppBundle\Libs\EntityNormalizer;

use AppBundle\Entity\Usuario;
use AppBundle\Libs\Normalizer\AbstractNormalizer;
use AppBundle\Libs\Decorator\CustomDecorator;

/**
 * Description of AGBancoNormalizer
 *
 * @author code
 */
class UsuarioNormalizer extends AbstractNormalizer implements \Symfony\Component\DependencyInjection\ContainerAwareInterface {

    private $container;

    public function normalizeObject( $object, $prototype) {

        $obj = array();

        $normalice = $this->container->get('manager.json');

        switch ($prototype) {
            case CustomDecorator::DEFAULT_DECORATOR:
                $repositoryUser=$this->container->get('doctrine')->getRepository('AppBundle:Usuario');
                $obj['id'] = $object->getId();
                $obj['nombreUsuario'] = $object->getNombreUsuario();
                $obj['urlFoto'] = $object->getUrlFoto();
                $obj['correo'] = $object->getCorreo();
                $obj['nombre'] = $object->getNombre();
                $obj['apellido'] = $object->getApellido();
                $obj['asignedTask'] = $object->getTotalVisit();
                $obj['unreadMessage'] = 0;
                if ( in_array($object->getRol()->getId(), array(1,4)) )
                {
                    $obj['unreadMessage'] = $this->container->get('doctrine')->getRepository('AppBundle:Mensaje')->getUnreadMessages();
                }
                $obj['todayTask'] = $object->getTotalToday();
                $obj['token'] = $object->getToken();
                $obj['emptyDocTask'] = $repositoryUser->findAllWithEmptyDoc( $obj['token']);
                $obj['emptySummary'] = $repositoryUser->findAllWithEmptySummary( $obj['token']);
                $result=array();
                $result[]= $obj['nombre'] ;
                $result[]= $obj['apellido'] ;
                $obj['nombreCompleto'] =implode(' ',$result);
                $obj['rol'] = $normalice->normalize('normalizer.rol', $object->getRol(), CustomDecorator::DEFAULT_DECORATOR);
                break;
            case CustomDecorator::USER_RESPONSIBLE_DECORATOR:
                $repositoryUser=$this->container->get('doctrine')->getRepository('AppBundle:Usuario');
                $obj['id'] = $object->getId();
                $obj['nombreUsuario'] = $object->getNombreUsuario();
                $obj['urlFoto'] = $object->getUrlFoto();
                $obj['correo'] = $object->getCorreo();
                $obj['nombre'] = $object->getNombre();
                $obj['apellido'] = $object->getApellido();
                $obj['asignedTask'] = $object->getTotalVisit();
                
                $obj['todayTask'] = $object->getTotalToday();
                $result=array();
                $result[]= $obj['nombre'] ;
                $result[]= $obj['apellido'] ;
                $obj['nombreCompleto'] =implode(' ',$result);
                $obj['emptyDocTask'] = $repositoryUser->findAllWithEmptyDoc( $object->getToken());
                $obj['emptySummary'] = $repositoryUser->findAllWithEmptySummary( $object->getToken());
                $obj['rol'] = $normalice->normalize('normalizer.rol', $object->getRol(), CustomDecorator::DEFAULT_DECORATOR);
                break;

            case CustomDecorator::SIMPLE:
                $obj['id'] = $object->getId();


                $obj['nombre'] = $object->getNombre();
                $obj['apellido'] = $object->getApellido();
                $result=array();
                $result[]= $obj['nombre'] ;
                $result[]= $obj['apellido'] ;
                $obj['nombreCompleto'] =implode(' ',$result);
                    break;
        }

        return $obj;
    }

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        $this->container = $container;
    }

}
