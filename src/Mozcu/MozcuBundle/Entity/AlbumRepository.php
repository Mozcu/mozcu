<?php

namespace Mozcu\MozcuBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AlbumRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AlbumRepository extends EntityRepository {
    
    public function findAllPaginated($page, $cant) {
        $dql  = "SELECT a FROM MozcuMozcuBundle:Album a WHERE a.isActive = 1 ORDER BY a.id DESC";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
    
    public function findByProfile(Profile $profile) {
        $dql  = "SELECT a FROM MozcuMozcuBundle:Album a WHERE a.profile = :profileId ORDER BY a.releaseDate DESC";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter('profileId', $profile->getId());
        return $query->getResult();
    }
    
    public function findByTags(array $tags) {
        $in = "(" . implode(',', $tags) . ")";
        $cant = count($tags);
        
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select("a");
        $queryBuilder->from('MozcuMozcuBundle:Album', 'a');
        $queryBuilder->innerJoin('a.tags', 't');
        $queryBuilder->where("t.id IN {$in} AND a.isActive = 1");
        $queryBuilder->groupBy("a.name");
        $queryBuilder->having("COUNT(t.id) = {$cant}");
        $queryBuilder->orderBy('a.id', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }
    
    public function liveSearchByName($name) {
        $dql = "SELECT a from MozcuMozcuBundle:Album a JOIN a.profile p
                WHERE (a.name LIKE '%$name%' OR p.name LIKE '%$name%') AND a.isActive = 1";
        $query = $this->getEntityManager()->createQuery($dql);
        return $query->getResult();
    }
    
    public function findRelated(Album $album) {
        $tags = array();
        foreach($album->getTags() as $tag) {
            $tags[] = $tag->getId();
        }
        $in = "(" . implode(',', $tags) . ")";
        
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select("a");
        $queryBuilder->from('MozcuMozcuBundle:Album', 'a');
        $queryBuilder->innerJoin('a.tags', 't');
        $queryBuilder->where("t.id IN {$in} AND a.id <> {$album->getId()} AND a.isActive = 1");
        $queryBuilder->orderBy('a.id', 'DESC');
        
        $query = $queryBuilder->getQuery()
                       ->setFirstResult(0)
                       ->setMaxResults(15);

        return $query->getResult();
    }
}
