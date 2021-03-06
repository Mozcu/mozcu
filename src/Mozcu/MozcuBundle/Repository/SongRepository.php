<?php

namespace Mozcu\MozcuBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Mozcu\MozcuBundle\Entity\Album;
use Mozcu\MozcuBundle\Entity\User;

/**
 * SongRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SongRepository extends EntityRepository
{
    public function liveSearch($name, $limit = 4) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->from('MozcuMozcuBundle:Song', 's')
            ->innerJoin('s.album', 'a')
            ->innerJoin('a.profile', 'p')
            ->innerJoin('p.user', 'u')
            ->where('s.name LIKE :like OR a.artist_name LIKE :like')
            ->andWhere('a.status = :status')
            ->setParameters([
                'like' => '%' . $name .'%',
                'status' => Album::STATUS_ACTIVE,
            ])
            ->andWhere('u.status = :userStatus')
            ->setParameter('userStatus', User::STATUS_ACTIVE);
        
        if($limit > 0) {
            $qb->select('s')
                ->addSelect('LOCATE(:term, s.name) position')
                ->orderBy('position', 'DESC')
                ->setParameter('term', $name);
            $query = $qb->getQuery();
            $query->setFirstResult(0)->setMaxResults($limit);
            $results = $query->getResult();
            return array_map(
                function ($result) { return $result[0]; },
                $results
            );
        }
        
        $qb->select('COUNT(s.id)');
        return $qb->getQuery()->getSingleScalarResult();
    }
    
    public function searchTotalCount($query) {
        return $this->liveSearch($query, 0);
    }
    
    /**
     * 
     * @param \Mozcu\MozcuBundle\Entity\Album $album
     * @return array
     */
    public function getSongIdsFromAlbum(Album $album) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('s.id')
            ->from('MozcuMozcuBundle:Song', 's')
            ->where('s.album = :albumId')
            ->orderBy('s.id', 'ASC')
            ->setParameter('albumId', $album->getId());
        $ids = $qb->getQuery()->getArrayResult();
        
        return array_map(function($row) {
            return $row['id'];
        }, $ids);
    }
}
