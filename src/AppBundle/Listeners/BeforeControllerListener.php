<?php

namespace AppBundle\Listeners;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use AppBundle\Libs\Controller\BaseController;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


/**
 * Description of ControllerListener
 *
 * @author code
 */
class BeforeControllerListener {

    private $doctrineService;


    public function __construct($container) {
        $this->container = $container;

    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event) {


        $controller = $event->getController();
        $request = $event->getRequest();
        $uri = $request->getRequestUri();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }
        if ($uri == '/api/token-authentication') {
            return;
        }

        if ($controller[0] instanceof BaseController) {
            $key = $event->getRequest()->headers->get('apiKey');
            if (!$key) {
                $event->setController(function() {
                    return new JsonResponse(array('success' => false, 'error' => 'No se encontr&oacute; una llave v&aacute;lida en la cabecera de la petici&oacute;n'));
                });
            } else {
                $result = $this->container->get('doctrine')->getRepository('AppBundle:Usuario')->findOneBy(array('token' => $key));
                if (!$result) {
                    $event->setController(function() {
                        return new JsonResponse(array('success' => false, 'error' => 'Token inv&aacute;lido', 'code' => 3000));
                    });
                } else {
                    $this->container->get('security.context')->setToken( new UsernamePasswordToken($result,$result->getToken(),'app',$result->getRoles()));


                }
            }
        }
    }

}
