<?php

namespace AppBundle\Repository;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends \Doctrine\ORM\EntityRepository
{

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
//        if (!isset($criteria['enabled'])){
//            $criteria['enabled'] = true;
//        }
        return parent::findBy($criteria, $orderBy, $limit, $offset); // TODO: Change the autogenerated stub
    }

    public function findEvent($owner, $dateStart,$dateEnd, array $params = [], $returnArray = false){
//        dump($dateStart);
//        dump($dateEnd);
//        exit;
        if ($returnArray == false){
            $dateEnd->modify('+2 month');
        }
        $qb = $this->createQueryBuilder('e');
        if ($returnArray === true){
            $qb->select('e.id , e.type, e.title, e.start, e.end');
        }else{
            $qb->select('e');
        }
        $qb->where('e.end >= :dateStart')
            ->andWhere('e.start <= :dateEnd')
            ->setParameters([
                'dateStart' => $dateStart,
                'dateEnd' => $dateEnd
            ]);
        if (isset($owner) && $owner === 'Partner'){
            $qb->andWhere("e.type = 'PARTNER'");
        }else{
            $qb->andWhere("e.type != 'PARTNER'");
        }
        $qb->orderBy('e.start');

//        $result = $qb->getQuery()->getSQL();
//        echo  $result;
//        exit;
        $qb->getFirstResult(1);
        $qb->getMaxResults(5);
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function search($query){
        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        $qb->where("s.title LIKE '%$query%'")
            ->orWhere("s.body  LIKE '%$query%' ")
            ->andWhere("s.enabled = 1")
            ->orderBy('s.created', 'DESC');
        $result = $qb->getQuery()->getResult();

        return $result;
    }

}
