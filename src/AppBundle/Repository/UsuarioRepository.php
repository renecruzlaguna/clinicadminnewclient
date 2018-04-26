<?php

namespace AppBundle\Repository;

/**
 * UsersRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UsuarioRepository extends \AppBundle\Libs\Repository\BaseRepository {

    public function getBaseQuery($baseEntity, $start = 0, $limit = 30, $filters = array(), $columnsAlias = array(), $decorator = ResultDecorator::DEFAULT_DECORATOR) {
        
    }

    public function getUserAdminOrSecretary() {
        $qb = $this->createQueryBuilder('user');
        $qb->join('user.rol', 'rol');
        $qb->where($qb->expr()->eq('rol', 3));
        $qb->orWhere($qb->expr()->eq('rol', 4));
        return $qb->getQuery()->getResult();
    }

    public function verifyExistance($userName = '', $id = -1) {
        $qb = $this->createQueryBuilder('user');

        $exp = $qb->expr()->eq($qb->expr()->lower('user.nombreUsuario'), '?1');
        $qb->setParameter(1, strtolower($userName));

        $qb->where($exp);

        if ($id != -1) {
            $qb->andWhere($qb->expr()->neq('user.id', '?2'));
            $qb->setParameter(2, $id);
        }
        $result = $qb->getQuery()->getResult();
        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function addValuesDates($endDate, &$qb, $initialDate = '01-01-2000') {

        if ($endDate == -1) {
            $endDate = date('m-d-Y', strtotime('+1 year'));
        }
        if ($initialDate == -1) {
            $initialDate = '01-01-2000';
        }

        $qb->andWhere('query.fechaCompleta BETWEEN :from AND :to')
                ->setParameter('from', $initialDate)
                ->setParameter('to', $endDate);
    }

    //Grafic Report
    public function getTotalNewPatientBySpecialityForDay($speciality = -1, $date) {

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('user.id');
        $qb->from('AppBundle:Usuario', 'user');
        $qb->join('user.consultaRegistro', 'query');


        if ($speciality != -1) {
            $qb->join('query.especialidad', 'especialidad');
            $qb->andWhere($qb->expr()->eq('especialidad', '?1'));
            $qb->setParameter(1, $speciality);
        }

        $this->addValuesDates($date, $qb, $date);
        $qb->distinct();
        $totalPatient = 0;
        $result = $qb->getQuery()->getResult();
        foreach ($result as $userId) {
            $total = $this->detectIfPatientHasOtherQuerys($userId['id'], $speciality, $date);
            if ($total == 0) {
                $totalPatient++;
            }
        }
        return $totalPatient;
    }

    private function detectIfPatientHasOtherQuerys($patientId, $speciality, $date) {

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('user.id');
        $qb->from('AppBundle:Usuario', 'user');
        $qb->join('user.consultaRegistro', 'query');
        $qb->andWhere($qb->expr()->eq('user.id', '?1'));
        $qb->setParameter(1, $patientId);

        $qb->join('query.especialidad', 'especialidad');
        $qb->andWhere($qb->expr()->eq('especialidad', '?2'));
         $qb->setParameter(2, $speciality);

        $begin = \DateTime::createFromFormat("m-d-Y", $date);
        $intervalDays = new \DateInterval('P1D');
        $end = $begin->sub($intervalDays)->format("m-d-Y");
        $this->addValuesDates($end, $qb);
        $qb->distinct();
        return count($qb->getQuery()->getResult());
    }

}