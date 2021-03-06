<?php

namespace Mozcu\MozcuBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends EntityRepository
{
    public function liveSearchByName($name, $startWith = false) {
        $like = $startWith ? "'$name%'" : "'%$name%'" ;
        $dql  = "SELECT t FROM MozcuMozcuBundle:Tag t WHERE  t.name LIKE $like AND LENGTH(t.name) < 20";
        $query = $this->getEntityManager()->createQuery($dql)->setFirstResult(0)->setMaxResults(10);
        return $query->getResult();
    }
    
    public function findMostPopular() {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("t, count(a.id) AS albumCant")
            ->from("MozcuMozcuBundle:Tag", "t")
            ->innerJoin("t.albums", "a")
            ->groupBy('t.id')
            ->orderBy('albumCant', 'DESC');
        
        $query = $qb->getQuery()
                    ->setFirstResult(0)
                    ->setMaxResults(10);
        
        $results = $query->getResult();
        
        return array_map(
            function ($result) { return $result[0]; },
            $results
        );
    }
    
}
