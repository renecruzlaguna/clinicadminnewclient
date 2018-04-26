<?php

namespace AppBundle\Repository;

use AppBundle\Libs\Normalizer\ResultDecorator;
use AppBundle\Libs\TraitMyCase\ValidateEntity;

/**
 * RolRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RolRepository extends \AppBundle\Libs\Repository\BaseRepository {

    use ValidateEntity;

    public function getBaseQuery($baseEntity, $start = 0, $limit = 30, $filters = array(), $columnsAlias = array(), $decorator = ResultDecorator::DEFAULT_DECORATOR) {
        
    }
    public function getRoleAdminOrSecretary(){
        $qb = $this->createQueryBuilder('rol');

        $qb->where($qb->expr()->eq('rol.id', 3));
        $qb->orWhere($qb->expr()->eq('rol.id', 4));
        return $qb->getQuery()->getResult();
    }

}