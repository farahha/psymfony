<?php

namespace Tests\PlatformBundle\Repository;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends \Doctrine\ORM\EntityRepository
{
    public function getLikeQueryBuilder($pattern)
    {
        return $this->createQueryBuilder('category')->where('category.name like :pattern')->setParameter(':pattern', $pattern);
    }
}
