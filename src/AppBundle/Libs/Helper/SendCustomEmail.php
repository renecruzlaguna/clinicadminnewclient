<?php

namespace AppBundle\Libs\Helper;


use Swift_Attachment;

class SendCustomEmail
{

    private $container;
    private $reserve;
    private $dir;
    private $sendToClient = false;
    private $sendToDoctor = false;
    private $sendToAdmin = false;

    public function __construct($container)
    {
        $this->container = $container;
        $separator = DIRECTORY_SEPARATOR;
        $this->dir = $this->container->getParameter('kernel.root_dir') . "$separator" . "logs" . $separator . "emails" . $separator;
    }

    /*type  1 inserte una nueva reserva*/
    /*type  2 acepta una reserva*/
    /*type  3 informar de cita al dia siguiente*/
    /*type 4 informar de cita actualizada*/
    /*type 5 informar de cita cancelada*/
    /*type 6 informar de nuevo usuario creado*/

    public function sendMessage($typeMail)
    {
        try {
            switch ($typeMail) {
                case 1:
                    $body = 'Estimado usuario el presente correo es para informarle que se ha realizado una reservacion ' . $this->reserve->getDataToEmailClient() . '. Cuando el doctor acepte su reservación le enviaremos un correo de confirmación.Por favor acceda al sistema para más información.Atentamente Consultas Online YCDENTAL.';
                    $this->sendEmailToClient($body, 'Reserva de consulta realizada');
                    $body = 'Estimado doctor el presente correo es para informarle que se ha realizado una reservacion ' . $this->reserve->getDataToEmail() . ' y usted ha sido seleccionado como responsable.Por favor acceda al sistema para más información.';
                    $this->sendEmailToDoctor($body, 'Reserva de consulta realizada');
                    break;
                case 2:
                    $body = 'Estimado usuario el presente correo es para informarle que se ha aceptado su reserva de consulta ' . $this->reserve->getDataToEmailClientAprobbed() . 'Por favor acceda al sistema para más información.Atentamente Consultas Online YCDENTAL.';
                    $this->sendEmailToClient($body, 'Reserva aceptada');
                    $body = 'Estimado doctor el presente correo es para informarle que usted ha aceptado la reservación creada  ' . $this->reserve->getDataToEmail();
                    $this->sendEmailToDoctor($body, 'Reserva de consulta aceptada');

                    break;
                case 3:

                    $body = 'Estimado usuario el presente correo es para informarle que usted tiene una reserva de consulta para mañana' . $this->reserve->getDateClientAprobbed() . 'Por favor acceda al sistema para más información.Atentamente Consultas Online YCDENTAL.';
                    $this->sendEmailToClient($body, 'Recordatorio de cita para mañana');

                    break;

                case 4:
                    $body = 'Estimado usuario el presente correo es para informarle que se ha actualizado su reserva de consulta ' . 'Por favor acceda al sistema para más información.Atentamente Consultas Online YCDENTAL.';
                    $this->sendEmailToClient($body, 'Reserva de consulta actualizada');
                    $body = 'Estimado doctor el presente correo es para informarle que actualizado la reservación creada  ' . $this->reserve->getDataToEmail();
                    $this->sendEmailToDoctor($body, 'Reserva de consulta actualizada');

                    break;
                case 5:
                    $body = 'Estimado usuario el presente correo es para informarle que se ha cancelado su reserva de consulta ' . $this->reserve->getDataToEmailClientAprobbed() . 'Por favor acceda al sistema para más información.Atentamente Soluciones para la medicina.';
                    $this->sendEmailToClient($body, 'Reserva de consulta cancelada');
                    $body = 'Estimado doctor el presente correo es para informarle que se ha  cancelado la reservación creada  ' . $this->reserve->getDataToEmail();
                    $this->sendEmailToDoctor($body, 'Reserva de consulta cancelada');

                    break;


            }
        } catch (\Exception $e) {


        }
    }

    public function sendMessageCreateUser($user, $password, $url)
    {
        $body = 'Estimado usuario el presente correo es para informarle que usted se ha registrado en
         el sitio de reservas de consultas online.Las credenciales para acceder son las siguientes Nombre de usuario: ' . $user->getNombreUsuario() . '.Contraseña: ' . $password . '.Para más información puede accedder al sistema a través de la dirección ' . $url . '.Atentamente Consultas Online YCDENTAL.';
        $this->sendEmailToClient($body, 'Registro realizado', $user->getCorreo());

    }

    /*envio de pago de la consulta*/
    public function sendMessageCheck($factura)
    {
        $urlFile = $this->container->get('kernel')->getRootDir() . DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'bundles'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.'generatepdf'.DIRECTORY_SEPARATOR . $factura->getFichero();
        if (!file_exists($urlFile)) {
            $this->registerTraceOfEmail('No se encontro el fichero en la ruta' . $urlFile);
            return false;
        }

        $body = 'Estimado usuario en el presente correo le enviamos la factura de la consulta realizada.' . 'Atentamente Consultas Online YCDENTAL.';
        $this->sendEmailToClient($body, 'Factura de consulta', $factura->getConsulta()->getUsuarioRegistro()->getCorreo(), $urlFile);
        return $this->isSendToClient();
    }

    public function sendMessageCot($pdf,$email)
    {
        $urlFile = $this->container->get('kernel')->getRootDir() . DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'bundles'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.'generatepdf'.DIRECTORY_SEPARATOR .$pdf;
        if (!file_exists($urlFile)) {
            $this->registerTraceOfEmail('No se encontro el fichero en la ruta' . $urlFile);
            return false;
        }

        $body = 'Estimado usuario en el presente correo le enviamos la cotización realizada.' . 'Atentamente Consultas Online YCDENTAL.';
        $this->sendEmailToClient($body, 'Factura de consulta', $email, $urlFile);
        return $this->isSendToClient();
    }

    private function registerTraceOfEmail($text)
    {
        try {
            $date = new \DateTime('now');
            $date = $date->format('YmdHis');
            $myfile = @fopen($this->dir . ($date . uniqid()) . ".log", "w");

            @fwrite($myfile, $text);
        } catch (\Exception $e) {

        }
    }

    public function sendEmailToClient($body, $subject, $emailClient = '', $atachment = '',$html=false)
    {

        $mailAcount = $this->container->getParameter('mailer_user');
        $email = '';
        if ($this->reserve) {
            $email = $this->reserve->getUsuarioRegistro()->getCorreo();
        } else {
            $email = $emailClient;
        }

        if ($mailAcount) {

            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($mailAcount)
                ->setTo($email)
                ->setBody($body,$html==true?'text/html':'');
            if ($atachment) {
                $message->attach(Swift_Attachment::fromPath($atachment));
            }

            try {
                $send = $this->container->get('swiftmailer.mailer.default')->send($message);

                if ($send) {
                    $this->registerTraceOfEmail('Enviado satisfactoriamente el correo para ' . $email . ' con cuerpo ' . $body);
                    $this->sendToClient = true;
                } else {

                    $this->registerTraceOfEmail('No se pudo enviar el correo para ' . $email . ' con asunto ' . $subject . ' y cuerpo ' . $body);
                    $this->sendToClient = false;
                }
            } catch (\Exception $e) {
                $this->sendToClient = false;
                $this->registerTraceOfEmail($e->getMessage() . 'No se pudo enviar el correo para ' . $email . ' con asunto ' . $subject . ' y cuerpo ' . $body);
            }
        } else {
            $this->sendToClient = false;
            $this->registerTraceOfEmail('No se pudo enviar el correo para ' . $email . ' con asunto ' . $subject . ' y cuerpo ' . $body);
        }

    }

    public function sendEmailToAdmin($body, $subject)
    {

        $mailAcount = $this->container->getParameter('mailer_user');
        $repoUser = $this->container->get('doctrine')->getRepository('AppBundle:Usuario');
        $userAdmin = $repoUser->findOneBy(array('rol' => 3));


        if ($mailAcount && $userAdmin) {
            $email = $userAdmin->getCorreo();
            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($mailAcount)
                ->setTo($email)
                ->setBody($body);

            try {
                $send = $this->container->get('swiftmailer.mailer.default')->send($message);

                if ($send) {
                    $this->registerTraceOfEmail('Enviado satisfactoriamente el correo para ' . $email . ' con cuerpo ' . $body);
                    $this->sendToAdmin = true;
                } else {
                    $this->registerTraceOfEmail('No se pudo enviar el correo para ' . $email . ' con asunto ' . $subject . ' y cuerpo ' . $body);
                    $this->sendToAdmin = false;
                }
            } catch (\Exception $e) {
                $this->sendToAdmin = false;
                $this->registerTraceOfEmail('No se pudo enviar el correo para ' . $email . ' con asunto ' . $subject . ' y cuerpo ' . $body);
            }
        } else {
            $this->sendToAdmin = false;
            $this->registerTraceOfEmail('No se pudo enviar el correo para el administrador' . ' con asunto ' . $subject . ' y cuerpo ' . $body);
        }

    }

    public function sendEmailToDoctor($body, $subject)
    {
        $userResponsable = $this->reserve->getUsuario();

        $mailAcount = $this->container->getParameter('mailer_user');

        if ($mailAcount && $userResponsable) {

            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($mailAcount)
                ->setTo($userResponsable->getCorreo())
                ->setBody($body);

            try {
                $send = $this->container->get('swiftmailer.mailer.default')->send($message);

                if ($send) {
                    $this->sendToDoctor = true;
                    $this->registerTraceOfEmail('Enviado satisfactoriamente el correo para ' . $userResponsable->getCorreo() . ' con cuerpo ' . $body);
                } else {
                    $this->sendToDoctor = false;
                    $this->registerTraceOfEmail('No se pudo enviar el correo para ' . $userResponsable->getCorreo() . ' con asunto ' . $subject . ' y cuerpo ' . $body);
                }
            } catch (\Exception $e) {
                $this->sendToDoctor = false;
                $this->registerTraceOfEmail('No se pudo enviar el correo para ' . $userResponsable->getCorreo() . ' con asunto ' . $subject . ' y cuerpo ' . $body);
            }
        } else {
            $this->sendToDoctor = false;
            $this->registerTraceOfEmail('No se pudo enviar el correo para ' . $userResponsable->getCorreo() . ' con asunto ' . $subject . ' y cuerpo ' . $body);
        }

    }


    public function setReserve($reserve)
    {
        $this->reserve = $reserve;
    }

    public function isSendToClient()
    {
        return $this->sendToClient;
    }

    public function isSendToDoctor()
    {
        return $this->sendToDoctor;
    }

    public function isSendToAdmin()
    {
        return $this->sendToAdmin;
    }

}
