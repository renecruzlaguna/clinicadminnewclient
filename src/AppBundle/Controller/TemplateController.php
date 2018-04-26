<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Libs\Controller\BaseController;


class TemplateController extends BaseController
{



    public function showTemplateAction(){

        return $this->render('AppBundle:Doctor:template.html.twig', array());
    }
}