<?php

namespace AppBundle\Controller;

use AppBundle\Libs\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class EmailController extends BaseController
{


    public function sendAction($secret)
    {
        if ($secret == $this->getParameter('secret')) {
            try {


                $kernel = $this->get('kernel');
                $application = new Application($kernel);
                $application->setAutoExit(false);

                $input = new ArrayInput(array(
                    'command' => 'citaemail'
                ));

                $output = new BufferedOutput();
                $application->run($input, $output);

                // return the output, don't use if you used NullOutput()
                $content = $output->fetch();

                return new JsonResponse(array('success' => true, 'data' => $content));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'error' => $e->getMessage()));
            }
        } else {
            return new JsonResponse(array('success' => false, 'data' => 'Código de envío incorrecto.'));
        }
    }

}
