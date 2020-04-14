<?php


namespace MrkushalSharma\MediaManager\Service;


use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class PaginationService
{
    private $pager;

    private $firstIndex = 1;

    private $requestStack;

    private $request;

    private $limit;

    private $router;

    public function __construct(RequestStack $request, RouterInterface $router)
    {
        $this->requestStack = $request;
        $this->request = $request->getCurrentRequest();
        $this->router = $router;
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