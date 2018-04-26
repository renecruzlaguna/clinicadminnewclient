<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Console\Style\SymfonyStyle;

class sendEmailCommand extends ContainerAwareCommand
{

    private $io;

    /**
     * Basic configuration of command [name, parameters]
     */
    protected function configure()
    {

        $this->setName('citaemail')
            ->setDescription('Enviar correo a clientes con citas para mañana');
    }

    /**
     * Run the command functionality
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->io = new SymfonyStyle($input, $output);
            $container = $this->getContainer();
            $repoQuery = $container->get('doctrine')->getRepository('AppBundle:Consulta');

            $querys = $repoQuery->getQueryToTomorrow();

            $countEmails = count($querys);

            if ($countEmails == 0) {
                $this->io->block("INFO: No existen citas para mañana");
                //$output->writeln("INFO: No existen pagos pendientes para mañana");
            } else {
                $this->io->block("Enviando correos " . 'Total a enviar: ' . $countEmails);
                // $output->writeln("Enviando correos " . 'Total a enviar: ' . $countEmails);
                $countOK = 0;
                $sendEmailService=$container->get('manager.email');

                $nameClient = array();
                foreach ($querys as $query) {
                    $sendEmailService->setReserve($query);
                    $sendEmailService->sendMessage(3);
                    $send =  $sendEmailService->isSendToClient();
                    $nameClient[]=$query->getUsuarioRegistro()->getCompleteName();
                    if ($send) {
                        $this->io->success($query->getUsuarioRegistro()->getCorreo());

                        $countOK++;
                    } else {
                        $this->io->error($query->getUsuarioRegistro()->getCorreo());
                    }
                }
                $message = '';
                if (count($nameClient) == 1) {
                    $message = 'el cliente que tiene cita para mañana es: ' . $nameClient[0];
                } else {
                    $message = ' los clientes que tienen cita para mañana son: '
                        . implode(',', array_diff($nameClient, array($nameClient[count($nameClient) - 1]))) . ' y ' . $nameClient[count($nameClient) - 1] .
                        '.';
                }
                $message='Estimado administrador '.$message;
                $sendEmailService->sendEmailToAdmin($message,'Clientes con citas para mañana');
                if($sendEmailService->isSendToAdmin()){
                    $this->io->success("Correo enviado satisfactoriamente al administrador");
                }else{
                    $this->io->error("Fall&oacute; el env&iacute;o de correo al administrador.");
                }

                if ($countOK == 0) {
                    $this->io->error("Fall&oacute; el env&iacute;o de correos.");
                } else if ($countOK != $countEmails) {
                    $this->io->warning("Fall&oacute; el env&iacute;o de'.$countEmails-$countOK.' correos.");
                } else {
                    $this->io->success("Correos enviados satisfactoriamente");
                }
            }
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());
        }
    }


}
