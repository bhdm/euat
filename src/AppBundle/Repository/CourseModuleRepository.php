<?php

namespace AppBundle\Repository;

/**
 * CourseModuleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CourseModuleRepository extends \Doctrine\ORM\EntityRepository
{
    public function nextModule($course, $activeModule){
        $qb = $this->createQueryBuilder('m')
            ->select('m')
            ->where('m.course = :course')
            ->andWhere('m.sort <= :sort')
            ->andWhere('m.id != :id')
            ->orderBy('m.sort','DESC')
            ->addOrderBy('m.id','ASC')

            ->setParameter('course', $course->getId())
            ->setParameter('sort', $activeModule->getSort())
            ->setParameter('id', $activeModule->getId())
            ->setMaxResults(1)
            ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function backModule($course, $activeModule){
        $qb = $this->createQueryBuilder('m')
            ->select('m')
            ->where('m.course = :course')
            ->andWhere('m.sort >= :sort')
            ->andWhere('m.id != :id')
            ->orderBy('m.sort','DESC')
            ->addOrderBy('m.id','ASC')

            ->setParameter('course', $course->getId())
            ->setParameter('sort', $activeModule->getSort())
            ->setParameter('id', $activeModule->getId())
            ->setMaxResults(1)
        ;

//        echo $qb->getQuery()->getSql();
//        exit;
        return $qb->getQuery()->getOneOrNullResult();
    }



    public function stepModule($course, $activeModule, $moduleId){
        $qb = $this->createQueryBuilder('m')
            ->select('m')
            ->where('m.course = :course')
            ->andWhere('m.id <= :activeModuleId')
            ->andWhere('m.id = :stepModuleId')
            ->orderBy('m.sort','ASC')
            ->addOrderBy('m.id','ASC')

            ->setParameter('course', $course->getId())
            ->setParameter('activeModuleId', $activeModule->getId())
            ->setParameter('stepModuleId', $moduleId)
            ->setMaxResults(1)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}
