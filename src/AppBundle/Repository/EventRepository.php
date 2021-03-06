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
        $qb->leftJoin('e.items', 'i');

        $qb->where('( e.end >= :dateStart and e.start <= :dateEnd ) ');
        $qb->orWhere('( i.end >= :dateStart and i.start <= :dateEnd ) ');

        $qb->setParameters([
                'dateStart' => $dateStart,
                'dateEnd' => $dateEnd
            ]);


        if (isset($owner) && $owner === 'Partner'){
            $qb->andWhere("e.type = 'PARTNER'");
        }else{
            $qb->andWhere("e.type != 'PARTNER'");
        }
        $qb->orderBy('e.start', 'ASC')
            ->addOrderBy('i.start', 'ASC');

//        $result = $qb->getQuery()->getSQL();
//        echo  $result;
//        exit;
        $qb->getFirstResult(1);
        $qb->getMaxResults(5);
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function search($query, $st = true, $id = null, $type = null ){
        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        $qb->where("s.title LIKE '%$query%'")
            ->orWhere("s.body  LIKE '%$query%' ");

        if ($id != null){
            $qb->andWhere("s.id = $id");
        }
        if ($type != null){
            $qb->andWhere("s.type = '$type'");
        }

        if ($st == true){
            $qb->andWhere("s.enabled = 1");
        }
            $qb->orderBy('s.id', 'DESC');
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function filter($type,$start,$end,$text){
        $qb = $this->createQueryBuilder('s');
        $qb->select('s', 'i');
        $qb->leftJoin('s.items', 'i');
        $qb->where('s.enabled = 1');
        if ($type != null){
            $qb->andWhere('s.type = :type');
            $qb->setParameter('type', $type);
        }

        if ($start != null){
            $qb->andWhere('( s.end >= :dateStart OR i.end >= :dateStart )')
                ->setParameter('dateStart' , $start);
        }
        if ($end != null){
            $qb->andWhere('( s.start <= :dateEnd OR i.start <= :dateEnd )')
                ->setParameter('dateEnd' , $end);
        }

        $qb->andWhere("(s.title LIKE '%$text%' OR s.body LIKE '%$text%' OR s.adrs LIKE '%$text%')");


        $qb->orderBy('s.start', 'DESC');
        $result = $qb->getQuery()->getResult();

        return $result;
    }


    public function findNext($event){

        $qb = $this->createQueryBuilder('e');
        $qb->select('e');
        $qb->where("e.start >= :start ")
            ->andWhere("e.id != :id")
            ->andWhere("e.enabled = true")
            ->andWhere("e.type = :category");

        $qb->orderBy('e.created', 'ASC');
        $qb->setMaxResults(1);
        $qb->setParameter('start', $event->getStart()->format('Y-m-d H:i:s'));
        $qb->setParameter('id', $event->getId());
        $qb->setParameter('category', $event->getType());
        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result == null){
            $qb = $this->createQueryBuilder('e');
            $qb->select('e');
            $qb->where("e.type = :category")
                ->andWhere("e.enabled = true");
            $qb->setParameter('category', $event->getType());
            $qb->orderBy('e.start', 'ASC');
            $qb->setMaxResults(1);
            $result = $qb->getQuery()->getOneOrNullResult();
        }

        return $result;
    }

    public function findPrevious($event){

        $qb = $this->createQueryBuilder('e');
        $qb->select('e');
        $qb->where("e.start < :start ")
            ->andWhere("e.id != :id")
            ->andWhere("e.enabled = true")
            ->andWhere("e.type = :category");

        $qb->orderBy('e.start', 'DESC');
        $qb->setMaxResults(1);
        $qb->setParameter('start', $event->getStart()->format('Y-m-d H:i:s'));
        $qb->setParameter('id', $event->getId());
        $qb->setParameter('category', $event->getType());
        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result == null){
            $qb = $this->createQueryBuilder('e');
            $qb->select('e');
            $qb->where("e.type = :category")
                ->andWhere("e.enabled = true");
            $qb->setParameter('category', $event->getType());
            $qb->orderBy('e.start', 'DESC');
            $qb->setMaxResults(1);
            $result = $qb->getQuery()->getOneOrNullResult();
        }

        return $result;
    }
}
