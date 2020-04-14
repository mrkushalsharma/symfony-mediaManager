<?php


namespace MrkushalSharma\MediaManager\Service;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use MrkushalSharma\MediaManager\Service\PaginationService;
use MrkushalSharma\MediaManager\Entity\Media;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpKernel\KernelInterface;

class MediaService
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PaginationService
     */
    private  $paginationService;
    /**
     * @var KernelInterface
     */
    private $appKernel;

    public $uploadDir = 'uploads/media';

    /**
     * MediaService constructor.
     * @param EntityManagerInterface $em
     * @param PaginationService $paginationService
     * @param KernelInterface $appKernel
     */
    public function __construct(EntityManagerInterface $em, PaginationService $paginationService, KernelInterface $appKernel)
    {
        $this->em = $em;
        $this->paginationService = $paginationService;
        $this->appKernel = $appKernel;
    }

    public function apiPaginationService(QueryBuilder $qb, $perPage, $currentPage)
    {
        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($perPage);
        $pagerfanta->setCurrentPage($currentPage);
        return $pagerfanta;
    }

    public function getPaginatedMedia($filters = []){

        if(array_key_exists('mode', $filters)) {
            if ($filters['mode'] == 'grid') {
                return $this->paginationService->getPagerFanta(
                    $this->em->getRepository(Media::class)->getAllMediaQuery($filters)
                )->setMaxPerPage(40);
            }
        }
        return $this->paginationService->getPagerFanta(
            $this->em->getRepository(Media::class)->getAllMediaQuery($filters)
        );
    }

    public function getAllMedia($filters=[]){
        $qb = $this->em->getRepository(Media::class)->getAllMediaQuery($filters);
        return $qb->getQuery()->getResult();
    }

    public function getFileUploadMaxSize() {
        static $max_size = -1;

        if ($max_size < 0) {
            $post_max_size = $this->parseSize(ini_get('post_max_size'));
            if ($post_max_size > 0) {
                $max_size = $post_max_size;
            }

            $upload_max = $this->parseSize(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }

    public function parseSize($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else {
            return round($size);
        }
    }

}