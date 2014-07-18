<?php

namespace UJM\ExoBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * SubscriptionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubscriptionRepository extends EntityRepository
{
    /**
     * Exercises by user
     *
     */
    public function getExercisesUser($user)
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.user = :user')
            ->setParameter('user', $user)
            ->join('s.exercise', 'e')
            ->addSelect('e');

        return $qb->getQuery()->getResult();
    }

    /**
     * Allow to know if the User is enrolled to this Exercise
     *
     */
    public function getControlExerciseEnroll($user, $exercise)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->join('s.exercise', 'e')
            ->join('s.user', 'u')
            ->where($qb->expr()->in('e.id', $exercise))
            ->andWhere($qb->expr()->in('u.id', $user));

        return $qb->getQuery()->getResult();
    }
}