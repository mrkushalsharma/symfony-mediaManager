<?php


namespace MrkushalSharma\MediaManager\Repository;

use Doctrine\ORM\EntityRepository;
use MrkushalSharma\MediaManager\Entity\Media;

class MediaRepository extends EntityRepository
{
    public function getAllMediaQuery($filters = []){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('m')
            ->from(Media::class, 'm')
            ->where('m.deleted = 0')
        ;

        if(array_key_exists('name', $filters)){
            if(!empty($filters['name'])){
                $qb->andWhere(
                    $qb->expr()->like('m.title', $qb->expr()->literal('%'.$filters['name'].'%'))
                );
            }
        }

        if (array_key_exists('filetype', $filters)){
            if(!empty($filters['filetype'])){
                if($filters['filetype'] == 'image' or $filters['filetype'] == 'audio' or $filters['filetype'] == 'video'){
                    $qb->andWhere(
                        $qb->expr()->like('m.fileType', $qb->expr()->literal('%'.$filters['filetype'].'%'))
                    );
                }

                if($filters['filetype'] == 'other'){
                    $qb->andwhere(
                        $qb->expr()->orX(
                            $qb->expr()->like('m.fileType', $qb->expr()->literal('%application%')),
                            $qb->expr()->like('m.fileType', $qb->expr()->literal('%text%'))
                        )
                    );
                }
            }
        }

        if(array_key_exists('date', $filters)){
            if(!empty($filters['date'])){
                if($filters['date'] != 'all'){
                    $startDate = new \DateTimeImmutable("{$filters['date']}-01T00:00:00");
                    $endDate = $startDate->modify('last day of this month')->setTime(23, 59, 59);
                    $qb->andWhere("m.createdAt BETWEEN :start AND :end")
                        ->setParameter('start', $startDate)
                        ->setParameter('end', $endDate);
                }
            }
        }


        if(array_key_exists('title', $filters)){
            if(!empty($filters['title'])){
                $qb->andWhere(
                    $qb->expr()->like('m.title', $qb->expr()->literal('%'.$filters['title'].'%'))
                );
            }
        }

        $qb->orderBy('m.id', 'DESC');

        return $qb;
    }

    public function countMedia($filters =[]){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(m.id)')
            ->from(Media::class, 'm')
            ->where('m.deleted = 0')
        ;
        ;

        if(array_key_exists('name', $filters)){
            if(!empty($filters['name'])){
                $qb->andWhere(
                    $qb->expr()->like('m.title', $qb->expr()->literal('%'.$filters['name'].'%'))
                );
            }
        }

        if (array_key_exists('filetype', $filters)){
            if(!empty($filters['filetype'])){
                if($filters['filetype'] == 'image' or $filters['filetype'] == 'audio' or $filters['filetype'] == 'video'){
                    $qb->andWhere(
                        $qb->expr()->like('m.fileType', $qb->expr()->literal('%'.$filters['filetype'].'%'))
                    );
                }

                if($filters['filetype'] == 'other'){
                    $qb->andwhere(
                        $qb->expr()->orX(
                            $qb->expr()->like('m.fileType', $qb->expr()->literal('%application%')),
                            $qb->expr()->like('m.fileType', $qb->expr()->literal('%text%'))
                        )
                    );
                }
            }
        }

        if(array_key_exists('date', $filters)){
            if(!empty($filters['date'])){
                if($filters['date'] != 'all'){
                    $startDate = new \DateTimeImmutable("{$filters['date']}-01T00:00:00");
                    $endDate = $startDate->modify('last day of this month')->setTime(23, 59, 59);
                    $qb->andWhere("m.createdAt BETWEEN :start AND :end")
                        ->setParameter('start', $startDate)
                        ->setParameter('end', $endDate);
                }
            }
        }


        if(array_key_exists('title', $filters)){
            if(!empty($filters['title'])){
                $qb->andWhere(
                    $qb->expr()->like('m.title', $qb->expr()->literal('%'.$filters['title'].'%'))
                );
            }
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}