<?php

namespace AppBundle\Repository;



class JournalPostRepository extends \Doctrine\ORM\EntityRepository
{
    public function findNext($post){

        $qb = $this->createQueryBuilder('p');
        $qb->select('p');
        $qb->leftJoin('p.journal', 'j');
        $qb->where("p.id > :postId ")
            ->andWhere("p.enabled = true")
            ->andWhere("j.id = :journalId");

        $qb->orderBy('p.id', 'ASC');
        $qb->setMaxResults(1);
        $qb->setParameter('postId', $post->getId());
        $qb->setParameter('journalId', $post->getJournal()->getId());
        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result == null){
            $qb = $this->createQueryBuilder('p');
            $qb->select('p');
            $qb->leftJoin('p.journal', 'j');

            $qb->andWhere("p.enabled = true")
                ->andWhere("j.id = :journalId");
            $qb->setParameter('journalId', $post->getJournal()->getId());
            $qb->orderBy('p.id', 'ASC');
            $qb->setMaxResults(1);
            $result = $qb->getQuery()->getOneOrNullResult();
        }

        return $result;
    }

    public function findPrevious($post){

        $qb = $this->createQueryBuilder('p');
        $qb->select('p');
        $qb->leftJoin('p.journal', 'j');
        $qb->where("p.id < :postId ")
            ->andWhere("p.enabled = true")
            ->andWhere("j.id = :journalId");

        $qb->orderBy('p.created', 'ASC');
        $qb->setMaxResults(1);
        $qb->setParameter('postId', $post->getId());
        $qb->setParameter('journalId', $post->getJournal()->getId());
        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result == null){
            $qb = $this->createQueryBuilder('p');
            $qb->select('p');
            $qb->leftJoin('p.journal', 'j');

            $qb->andWhere("p.enabled = true")
                ->andWhere("j.id = :journalId");
            $qb->setParameter('journalId', $post->getJournal()->getId());
            $qb->orderBy('p.id', 'DESC');
            $qb->setMaxResults(1);
            $result = $qb->getQuery()->getOneOrNullResult();
        }

        return $result;
    }
}
