<?php


namespace MrkushalSharma\MediaManager\Service;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use MrkushalSharma\MediaManager\Entity\Media;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;

class MediaService
{

    private $pager;

    private $firstIndex = 1;

    private $requestStack;

    private $request;

    private $limit;

    private $router;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var KernelInterface
     */
    private $appKernel;

    public $uploadDir = 'uploads/media';

    /**
     * MediaService constructor.
     * @param EntityManagerInterface $em
     * @param KernelInterface $appKernel
     * @param RequestStack $request
     * @param RouterInterface $router
     */
    public function __construct(
        KernelInterface $appKernel,
        EntityManagerInterface $em,
        RequestStack $request,
        RouterInterface $router)
    {
        $this->em = $em;
        $this->appKernel = $appKernel;
        $this->requestStack = $request;
        $this->request = $request->getCurrentRequest();
        $this->router = $router;
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
                return $this->getPagerFanta(
                    $this->em->getRepository(Media::class)->getAllMediaQuery($filters)
                )->setMaxPerPage(40);
            }
        }
        return $this->getPagerFanta(
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

    public function getArrayPagerFanta($array)
    {
        $this->request = $this->requestStack->getCurrentRequest();

        $adapter = new ArrayAdapter($array);

        $this->pager = new Pagerfanta($adapter);

        $this->limit = $this->setLimit();

        $this->pager->setMaxPerPage($this->limit);

        if ($this->request->get('page')) {
            $this->pager->setCurrentPage($this->request->get('page'));
        }

        return $this->pager;
    }

    public function getPagerFanta(QueryBuilder $builder)
    {
        $this->request = $this->requestStack->getCurrentRequest();

        $this->limit = $this->setLimit();

        $adapter = new DoctrineORMAdapter($builder, false);

        $this->pager = new Pagerfanta($adapter);

        $this->pager->setMaxPerPage($this->limit);

        if ($this->request->get('page')) {
            $this->pager->setCurrentPage($this->request->get('page'));
        }

        $this->firstIndex = (($this->pager->getCurrentPage() - 1) * $this->pager->getMaxPerPage()) + 1;

        return $this->pager;
    }

    public function getFirstIndex()
    {
        return $this->firstIndex;
    }

    private function setLimit()
    {
        $this->request = $this->requestStack->getCurrentRequest();


        if ($this->request->get('limit') and is_numeric($this->request->get('limit'))) {
            return $this->request->get('limit');
        }

        return 20;
    }

    public function getRoute($route = '', $field = '', $extraParams = [])
    {
        if ($route == "" or $route == '#') {
            return '#';
        }

        $this->request = $this->requestStack->getCurrentRequest();

        $params = $this->request->query->all();

        unset($params['page']);
        unset($params['limit']);

        if ($field != '') {
            $params['sort'] = $field;
            $params['order'] = (isset($params['order']))
                ? ($params['order'] == 'desc') ? 'asc' : 'desc'
                : 'asc';
        }

        $params = array_merge($params, $extraParams);

        $router = $this->router->generate($route, $params);

        return $router;
    }
}