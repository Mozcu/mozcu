<?php

namespace Mozcu\MozcuBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ProfileRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProfileRepository extends EntityRepository
{
    public function liveSearch($query, $limit = 4) {
        $dql = "FROM MozcuMozcuBundle:Profile p JOIN p.user u LEFT JOIN p.albums a
                WHERE p.name LIKE '%$query%' OR u.username LIKE '%$query%' OR a.artist_name LIKE '%$query%'";
        if($limit > 0) {
            $dql = "SELECT p " . $dql;
            $query = $this->getEntityManager()->createQuery($dql);
            $query->setFirstResult(0)->setMaxResults($limit);
        } else {
            $dql = "SELECT COUNT(p.id) " . $dql;
            $query = $this->getEntityManager()->createQuery($dql);
            return $query->getSingleScalarResult();
        }
        
        return $query->getResult();
    }
    
    public function searchTotalCount($query) {
        return $this->liveSearch($query, 0);
    }
    
    /**
     * 
     * @param string $city
     * @return array
     */
    public function findCitiesByLike($city) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('DISTINCT p.city')
                ->from("MozcuMozcuBundle:profile", "p")
                ->where("p.city LIKE :city")
                ->orderBy('p.city', 'ASC')
                ->setParameter('city', '%' . $city . '%');
        
        $query = $qb->getQuery()
                ->setFirstResult(0)
                ->setMaxResults(10);
        
        return $query->getArrayResult();
    }
}
