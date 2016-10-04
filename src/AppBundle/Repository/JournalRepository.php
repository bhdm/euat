<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * JournalRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class JournalRepository extends EntityRepository
{
    public function findNext($id){

        $qb = $this->createQueryBuilder('p');
        $qb->select('p');
        $qb->where("p.id > :id ");
        $qb->orderBy('p.id', 'ASC');
        $qb->setMaxResults(1);
        $qb->setParameter('id', $id);
        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result == null){
            $qb = $this->createQueryBuilder('p');
            $qb->select('p');
            $qb->orderBy('p.id', 'ASC');
            $qb->setMaxResults(1);
            $result = $qb->getQuery()->getOneOrNullResult();
        }

        return $result;
    }

    public function findPrevious($id){

        $qb = $this->createQueryBuilder('p');
        $qb->select('p');
        $qb->where("p.id < :id ");
        $qb->orderBy('p.created', 'ASC');
        $qb->setMaxResults(1);
        $qb->setParameter('id', $id);
        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result == null){
            $qb = $this->createQueryBuilder('p');
            $qb->select('p');
            $qb->orderBy('p.id', 'DESC');
            $qb->setMaxResults(1);
            $result = $qb->getQuery()->getOneOrNullResult();
        }

        return $result;
    }
}
