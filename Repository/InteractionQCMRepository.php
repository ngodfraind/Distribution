<?php

namespace UJM\ExoBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * InteractionQCMRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InteractionQCMRepository extends EntityRepository
{

    /**
     * InteractionQCM by Interaction
     *
     */
    public function getInteractionQCM($interactionId)
    {
        $qb = $this->createQueryBuilder('iqcm');
        $qb->join('iqcm.interaction', 'i')
            ->where($qb->expr()->in('i.id', $interactionId));

        return $qb->getQuery()->getResult();
    }
}