<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Libs\Controller\BaseController;


class DefaultController extends BaseController
{
    public function indexAction()
    {
        return $this->render('AppBundle:Default:register.html.twig', array('errors' => null, 'data' => null, 'exception' => null));
    }

    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();
        $response = $this->render('AppBundle:Default:login.html.twig', array(
                'last_username' => $lastUsername,
                'error' => $error

            )
        );

        return $response;
    }

    public function securityCheckAction()
    {

    }

    public function logoutAction()
    {

    }

    public function registerAction(Request $request)
    {
        $data = $request->request->all();
        $data['rol'] = 1;
        $data['especialidad'] = -1;
        $data['salario'] = 0;
        $data['activo'] = 1;
        $data['edad'] = 20;
        $verifyCaptcha = $this->get('my.security.login_handler')->verify($request);
        if (!$verifyCaptcha) {
            $errors = array('captcha' => 1);
        } else {

            $entity = $this->populateModel('Usuario', $data);
            $errors = $this->validateModel($entity);
        }


        if (count($errors) > 0) {

            return $this->render('AppBundle:Default:register.html.twig', array('errors' => $errors, 'data' => $data, 'exception' => null));
        } else {
            $result = $this->saveEntity($entity);
            if ($result === true) {
                $password = $data['password'];
                $managerEmail = $this->get('manager.email');
                $url = $request->getSchemeAndHttpHost();
                $url .= '/manage/login';
                $managerEmail->sendMessageCreateUser($entity, $password, $url);
                return $this->redirect($this->generateUrl('app_login'));
            }
            return $this->render('AppBundle:Default:register.html.twig', array('errors' => null, 'data' => $data, 'exception' => $result['error']));
        }
    }
}