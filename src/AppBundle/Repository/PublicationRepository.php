<?php

namespace AppBundle\Repository;

/**
 * PublicationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PublicationRepository extends \Doctrine\ORM\EntityRepository
{
//    public function findAll()
//    {
//        return parent::findBy(['enabled' => true]);
//    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        if (!isset($criteria['enabled'])){
            $criteria['enabled'] = true;
        }
        return parent::findBy($criteria, $orderBy, $limit, $offset); // TODO: Change the autogenerated stub
    }

    public function search($query, $st = true){
        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        $qb->where("s.title LIKE '%$query%'")
            ->orWhere("s.body  LIKE '%$query%' ");
        if ($st == true){
            $qb->andWhere("s.enabled = 1");
        }
        $qb->orderBy('s.id', 'DESC');
        $result = $qb->getQuery()->getResult();

        return $result;
    }

}
