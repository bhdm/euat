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

        $qb = $this->getEntityManager()->createQueryBuilder();
        $expr = $this->getEntityManager()->getExpressionBuilder();

        $qb->select( 'p' )
            ->from( 'AppBundle:Publication', 'p' );

        foreach ( $criteria as $field => $value ) {

            if (is_object($value)){
                $qb->andWhere('p.' . $field .' = '."'" . $value->getId() ."'" );
            }else{
                $qb->andWhere('p.' . $field .' = '."'" . $value ."'" );
            }
        }

        $d = new \DateTime();
        $qb->andWhere( 'p.created <='."'".$d->format('Y-m-d H:i:s')."'");

        if ( $orderBy ) {

            foreach ( $orderBy as $field => $order ) {

                $qb->addOrderBy( 'p.' . $field, $order );
            }
        }

        if ( $limit )
            $qb->setMaxResults( $limit );

        if ( $offset )
            $qb->setFirstResult( $offset );

//        echo $qb->getQuery()->getSQL();
        return $qb->getQuery()
            ->getResult();
    }

//        return parent::findBy($criteria, $orderBy, $limit, $offset); // TODO: Change the autogenerated stub


    public function search($query, $st = true, $category = null){
        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        $qb->leftJoin('s.category','c');
        $qb->where("s.title LIKE '%$query%'")
            ->orWhere("s.body  LIKE '%$query%' ");

        if ($category != null){
            $qb->andWhere("c.id = ".$category);
        }
        if ($st == true){
            $qb->andWhere("s.enabled = 1");
        }

        $qb->orderBy('s.id', 'DESC');
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function findNext($publication){

        $qb = $this->createQueryBuilder('p');
        $qb->select('p');
        $qb->where("p.created > :created ")
            ->andWhere("p.id != :id")
            ->andWhere("p.enabled = true")
            ->andWhere("p.category = :category");

        $qb->orderBy('p.created', 'ASC');
        $qb->setMaxResults(1);
        $qb->setParameter('created', $publication->getCreated()->format('Y-m-d H:i:s'));
        $qb->setParameter('id', $publication->getId());
        $qb->setParameter('category', $publication->getCategory());
        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result == null){
            $qb = $this->createQueryBuilder('p');
            $qb->select('p');
            $qb->where("p.category = :category")
                ->andWhere("p.enabled = true");
            $qb->setParameter('category', $publication->getCategory());
            $qb->orderBy('p.created', 'ASC');
            $qb->setMaxResults(1);
            $result = $qb->getQuery()->getOneOrNullResult();
        }

        return $result;
    }

    public function findPrevious($publication){

        $qb = $this->createQueryBuilder('p');
        $qb->select('p');
        $qb->where("p.created < :created ")
            ->andWhere("p.id != :id")
            ->andWhere("p.enabled = true")
            ->andWhere("p.category = :category");

        $qb->orderBy('p.created', 'DESC');
        $qb->setMaxResults(1);
        $qb->setParameter('created', $publication->getCreated()->format('Y-m-d H:i:s'));
        $qb->setParameter('id', $publication->getId());
        $qb->setParameter('category', $publication->getCategory());
        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result == null){
            $qb = $this->createQueryBuilder('p');
            $qb->select('p');
            $qb->where("p.category = :category")
                ->andWhere("p.enabled = true");
            $qb->setParameter('category', $publication->getCategory());
            $qb->orderBy('p.created', 'DESC');
            $qb->setMaxResults(1);
            $result = $qb->getQuery()->getOneOrNullResult();
        }

        return $result;
    }

    public function findForApi($category, $page){
        $qb = $this->createQueryBuilder('p');
        $qb->select("p.title, p.anons, p.body, p.created, p.preview, c.title, p.slug, p.source");
        $qb->leftJoin('p.category', 'c');
        $qb->where("p.created < :created ")
            ->andWhere("p.id != :id")
            ->andWhere("p.enabled = true")
            ->andWhere("c.slug = ':category'");
        $qb->setParameter('category', $category);
        $qb->orderBy('p.created', 'DESC');
        $qb->setFirstResult(($page-1)*15);
        $qb->setMaxResults(15);
        return $qb->getQuery()->getResult();


    }
}
