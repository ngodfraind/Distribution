<?php

namespace Icap\BlogBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;
use Icap\BlogBundle\Entity\Blog;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @DI\Service("icap_blog.transformer.int_to_blog")
 */
class IntToBlogTransformer implements DataTransformerInterface
{
    /** @var EntityManager  */
    private $entityManager;

    /**
    * @DI\InjectParams({
    *    "entityManager" = @DI\Inject("doctrine.orm.entity_manager")
    * })
    */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Badge[]|Badge $value
     *
     * @return int|string
     */
    public function transform($value)
    {
        return ($value instanceof Blog) ? $value->getId() : null;
    }

    /**
     * @param integer $blogId
     *
     * @return Badge|null
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($blogId)
    {
        if (!$blogId) {
            return null;
        }

        $badge = $this->entityManager->getRepository('IcapBlogBundle:Blog')->find($blogId);

        if (null === $badge) {
            throw new TransformationFailedException(sprintf(
                'Blog number "%s" cannot be found!',
                $blogId
            ));
        }

        return $badge;
    }
}
