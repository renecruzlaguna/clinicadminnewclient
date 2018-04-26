<?php

namespace AppBundle\Controller;

use AppBundle\Libs\Controller\PrototypeDecorator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Libs\Controller\BaseController;


class AdminController extends BaseController
{
    public function indexAction(Request $request)
    {
        if ($this->getUser() && $this->getUser()->getRol()->getId() ==4) {
            return $this->redirect($this->generateUrl('manage_reserved_admin_list', array()));
        }
        $message = $request->query->get('message');
        $error = $request->query->get('error');
        $serviceDates = $this->get('app.dates');
        $data = $serviceDates->getPresentWeek();
        $queryEnd = $this->getRepo('Consulta')->getQueriesForIndex(4, $data['Domingo'], $data['Sábado']);
        $queryStart = $this->getRepo('Consulta')->getQueriesForIndex(2, $data['Domingo'], $data['Sábado']);
        $queryToday = $this->getRepo('Consulta')->getQueriesForIndex(2, null, null, 1);
        $queryAsigned = null;
        $queryTodayDoctor = null;
        if ($this->getUser() != null && $this->getUser()->getRol()->getId() == 2) {
            $queryAsigned = $this->getRepo('Consulta')->getQueriesForIndex(2, null, null, null, $this->getUser()->getId());
            $queryTodayDoctor = $this->getRepo('Consulta')->getQueriesForIndex(2, null, null, 1, $this->getUser()->getId());
        }


        return $this->render('AppBundle:Manage:index.html.twig', array('message' => $message,
            'error' => $error, 'queryEnd' => $queryEnd, 'queryStart' => $queryStart, 'queryToday' => $queryToday,
            'queryAsigned' => $queryAsigned, 'queryTodayDoctor' => $queryTodayDoctor));


    }

    public function cotizationAction(Request $request)
    {
        $data = $request->request->all();
        if ($data) {
            $allService=$this->getRepo('Servicio')->findAll();
            $services = $data['servicio'];
            $countServices = $data['servicesvalues'];
            $description=$data['descripcion'];
            $total=$data['total'];
            $rest=$data['guia'];
            $date=$data['fecha'];
            $html =  $this->renderView(
                'AppBundle:Manage:export_pdf_cot.html.twig', array(
                    'services' => $services,
                    'countServices' => $countServices,
                    'description' => $description,
                    'total' => $total,
                    'date' => $date,
                    'rest' => $rest,
                    'allservices'=>$allService
                )
            );

            $html = preg_replace('/>\s+</', '><', $html);
            $dompdf = $this->get('slik_dompdf');

            // Generate the pdf
            $dompdf->getpdf($html, 'UTF-8');
            $file = uniqid() . '.pdf';
            $filename = $this->getParameter('kernel.root_dir') . '/../web/bundles/app/docs/generatepdf/' . $file;
            // Either stream the pdf to the browser
            $dompdf->stream($filename);

            // Or get the output to handle it yourself
            $pdfoutput = $dompdf->output();
            file_put_contents($filename, $pdfoutput);
            $response = new Response();

            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', mime_content_type($filename));
            $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '";');
            $response->headers->set('Content-length', filesize($filename));


            $response->sendHeaders();

            $response->setContent(file_get_contents($filename));
            $this->get('manager.email')->sendMessageCot($file,$data['correo']);
            return $response;
        }
        $services = $this->getRepo('Servicio')->findAll();
        return $this->render('AppBundle:Manage:add_cot.html.twig', array('services' => $services));
    }

    public function renderToPageAction()
    {
        $repo = $this->getRepo('Consulta');
        $queryEndMonth = $repo->getInfoForAdmin(4);
        $queryCancelMonth = $repo->getInfoForAdmin(3);
        $queryRecMonth = $repo->getInfoForAdmin(4, 2);
        $queryPlanifMonth = $repo->getInfoForAdmin(null, 3);
        return $this->render('AppBundle:Default:top.html.twig', array('queryEndMonth' => $queryEndMonth,
            'queryCancelMonth' => $queryCancelMonth, 'queryRecMonth' => $queryRecMonth, 'queryPlanifMonth' => $queryPlanifMonth));


    }

    public function sendPromotionAction(Request $request)
    {
        $clients=$this->getRepo('Usuario')->findBy(array('rol'=>1));
        $data = $request->request->all();
        if ($data) {

            $clientsSend = $data['clientes'];
            $topic = $data['topic'];
            $body = $data['body'];
            $countOk=0;
            $countError=0;
            $serviceSend=$this->get('manager.email');
            foreach ($clientsSend as $email){
                $serviceSend->sendEmailToClient($body,$topic,$email,'',true);
                if($serviceSend->isSendToClient())
                    $countOk++;
                else
                    $countError++;
            }
            $message='';
            if($countError>0 or $countOk>0)
                $message='Algunos correos no se enviaron correctamente.Verifique los logs en el servidor';
            if($countError==0&&$countOk>0)
                $message='Correo(s) enviado(s) satisfactoriamente';
            return $this->redirect($this->generateUrl('manage', array('message' => $message, 'error' => '')));

        }
        return $this->render('AppBundle:Manage:send_promotion.html.twig', array('clients'=>$clients));


    }


    public function generateDataForGraphicAction()
    {
        $serviceDates = $this->get('app.dates');
        $data = $serviceDates->getPresentWeek();
        $speciality = $this->getRepo('Especialidad')->findAll();
        $result = new \stdClass();
        $result->datasets = array();
        $repoQuery = $this->getRepo('Consulta');
        $labels = array_keys($data);

        foreach ($speciality as $esp) {
            $dataset = new \stdClass();
            $dataset->label = $esp->getNombre();
            $dataset->data = array();
            foreach ($data as $days) {

                $dataset->data[] = $repoQuery->getTotalMonyQueryToGraphicForDay($esp->getId(), $days);
            }
            $rand = rand(0, 255);
            $rand2 = rand(0, 255);
            $rand3 = rand(0, 255);
            $dataset->backgroundColor = "rgba($rand,$rand2,$rand3,0.5)";
            $dataset->borderColor = "rgba($rand,$rand2,$rand3,0.7)";
            $dataset->pointBackgroundColor = "rgba($rand,$rand2,$rand3,1)";
            $dataset->pointBorderColor = "#fff";
            $result->datasets[] = $dataset;

        }
        $result->labels = $labels;
        return new JsonResponse(array('success' => true, 'data' => $result));
    }

    public function listUserAction(Request $request)
    {
        $message = $request->query->get('message');
        $error = $request->query->get('error');
        $result = $this->getRepo('Usuario')->getUserAdminOrSecretary();
        return $this->render('AppBundle:Manage:list_user.html.twig', array('users' => $result, 'message' => $message, 'error' => $error));
    }

    public function activeUserAction($id, $estado)
    {
        if ($estado == 0) {

            $user = $this->getRepo('Usuario')->find($id);
            if ($user && $user->getRol()->getId() == 2 && $user->getConsulta()->count() > 0) {

                return new JsonResponse(array('success' => false, 'error' => 'El doctor no se puede desactivar porque posee consultas asignadas'));
            }
        }
        return new JsonResponse($this->saveModel('Usuario', array('id' => $id, 'activo' => ($estado == 1) ? 1 : 0)));
    }

    public function verifyExistUserAction(Request $request)
    {
        $id = $request->request->get('id');
        $nombreUsuario = $request->request->get('nombreUsuario');
        if (empty($id)) {
            $id = -1;
        }
        $result = $this->getRepo('Usuario')->verifyExistance($nombreUsuario, $id);

        return new JsonResponse(array('data' => array('exist' => $result)));

    }

    public function addUserAction(Request $request)
    {
        $data = $request->request->all();
        $roles = $this->getRepo('Rol')->getRoleAdminOrSecretary();

        if (count($data) > 0) {

            $data['especialidad'] = -1;
            $data['edad'] = 0;
            $data['salario'] = 0;
            $data['activo'] = 1;
            $entity = $this->populateModel('Usuario', $data);
            $errors = $this->validateModel($entity);

            if (count($errors) > 0) {

                return $this->render('AppBundle:Manage:add_user.html.twig', array('roles' => $roles, 'errors' => $errors, 'data' => $data, 'exception' => null));
            } else {
                $result = $this->saveEntity($entity);
                if ($result === true) {
                    return $this->redirect($this->generateUrl('manage_user_list', array('message' => 'Usuario adicionado satisfactoriamente', 'error' => '')));


                }
                return $this->render('AppBundle:Manage:add_user.html.twig',
                    array('roles' => $roles, 'errors' => null, 'data' => $data, 'exception' => $result['error']));
            }
        } else {
            return $this->render('AppBundle:Manage:add_user.html.twig',
                array('roles' => $roles, 'errors' => null, 'data' => null, 'exception' => null));
        }

    }

    public function editUserAction(Request $request, $id)
    {
        $data = $request->request->all();
        $roles = $this->getRepo('Rol')->getRoleAdminOrSecretary();
        if (count($data) > 0) {

            $data['id'] = $id;
            $entity = $this->populateModel('Usuario', $data);
            $errors = $this->validateModel($entity);

            if (count($errors) > 0) {

                return $this->render('AppBundle:Manage:edit_user.html.twig', array('roles' => $roles, 'id' => $id, 'errors' => $errors, 'data' => $entity, 'exception' => null));
            } else {
                $result = $this->saveEntity($entity);
                if ($result === true) {
                    return $this->redirect($this->generateUrl('manage_user_list', array('message' => 'Usuario actualizado satisfactoriamente', 'error' => '')));

                }
                return $this->render('AppBundle:Manage:edit_user.html.twig', array('roles' => $roles, 'id' => $id, 'errors' => null, 'data' => $entity, 'exception' => $result['error']));
            }
        } else {
            $data = $this->getRepo('Usuario')->find($id);
            return $this->render('AppBundle:Manage:edit_user.html.twig', array('roles' => $roles, 'id' => $id, 'errors' => null, 'data' => $data, 'exception' => null));
        }

    }

    public function removeUserAction($id)
    {
        try {
            $result = $this->getRepo('Usuario')->find($id);
            $resultOperation = array('success' => false, 'error' => 'No se encontró el usaurio a eliminar');
            if ($result != null) {
                if ($result->getId() == $this->getUser()->getId()) {
                    return $this->redirect($this->generateUrl('manage_user_list', array('message' => '', 'error' => 'Un usuario no puede eliminarse el mismo.')));
                }
                $resultOperation = $this->removeModel('Usuario', $id);
            }
            if ($resultOperation['success']) {
                return $this->redirect($this->generateUrl('manage_user_list', array('message' => 'Operación realizada satisfactoriamente', 'error' => '')));
            } else {
                return $this->redirect($this->generateUrl('manage_user_list', array('message' => '', 'error' => $resultOperation['error'])));
            }
        } catch (\Exception $e) {
            return $this->redirect($this->generateUrl('manage_user_list', array('message' => '', 'error' => 'Ocurrió un error en el servidor')));
        }
    }


    public function showTemplateAction()
    {

        return $this->render('AppBundle:Manage:template.html.twig', array());
    }

    public function viewResultQueryAction(Request $request, $id, $queryId)
    {
        $query = $this->getRepo('Consulta')->find($queryId);
        if (!$query) {
            return $this->redirect($this->generateUrl('manage_reserved_admin_list',
                array('message' => '', 'error' => 'No se encontró la consulta')));
        }

        $repoEv = $this->getRepo('Evolucion');
        $idEvol = null;
        if ($query->getEvolucion() != null) {
            $idEvol = $query->getEvolucion()->getId();
        }
        $evolutions = $repoEv->getAllEvolutionsByPattient($query, $idEvol);
        $data = $request->request->all();
        $result = $this->getRepo('ResultadoConsulta')->find($id);
        if (!$result) {
            return $this->redirect($this->generateUrl('manage_reserved_admin_list',
                array('message' => '', 'error' => 'No se encontró la historia clínica')));
        }

        return $this->render('AppBundle:Manage:view_result_query.html.twig',
            array('errors' => null, 'evolutions' => $evolutions, 'query' => $query, 'data' => null, 'result' => $result, 'exception' => null));


    }
}