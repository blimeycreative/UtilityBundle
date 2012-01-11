<?php

namespace Oxygen\PaginationBundle;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\QueryBuilder;

class Paginate {

  private $container, $query, $limit, $page, $result, $data, $template, $request, $router, $route, $request_params;

  public function __construct(Container $container, QueryBuilder $query, $limit = 20, $page = 1) {
    $this->container = $container;
    $this->limit = $limit;
    $this->query = $query;
    $this->page = $page;
    $this->request = $container->get('request');
    $this->router = $container->get('router');
    $this->route = $this->request->attributes->get('_route');
    $this->request_params = $this->request->attributes->all() + $this->request->query->all();
    return $this;
  }

  public function getPagination() {
    $pagination_object = new \stdClass();

    $pagination_object->data = $this->getData();
    $pagination_object->result = $this->getResult();
    $pagination_object->template = $this->getTemplate();

    return $pagination_object;
  }

  public function getResult() {
    if (!$this->result)
      $this->setResult();
    return $this->result;
  }

  public function setResult($val = false) {
    if ($val)
      $this->result = $val;
    else {
      if (!$this->data || !(is_object($this->data) && property_exists($this->data, 'offset')))
        $this->setData();
      $this->result = $this->query->setMaxResults($this->limit)->setFirstResult($this->data->offset)->getQuery();
    }
  }

  public function getData() {
    if (!$this->data)
      $this->setData();
    return $this->data;
  }

  public function setData($val = false) {
    if ($val)
      $this->data = $val;
    else {
      $this->data = new \stdClass();
      $query = clone $this->query;
      $this->data->total_results = $query
              ->select('COUNT(u)')
              ->getQuery()
              ->getSingleScalarResult();
      $this->data->total_pages = ceil($this->data->total_results / $this->limit);
      if ($this->page > $this->data->total_pages)
        Exception::pageNumber();
      $this->data->offset = $this->page * $this->limit;
      $this->data->url = $this->getUrl();
    }
  }

  public function getTemplate() {
    if (!$this->template)
      $this->setTemplate();
    return $this->template;
  }

  public function setTemplate($val = false) {
    if ($val)
      $this->template = $val;
    else
      $this->template = <<<TEMPLATE
<ul>
  <li><a href="{$this->getUrl(1)}">First</a></li>
  {$this->getPages()}
  <li><a href="{$this->getUrl(($this->data->total_pages))}">Last</a></li>
</ul>
TEMPLATE;
  }

  public function getPages() {
    $string = '';
    for ($i = 1; $i <= $this->data->total_pages; $i++) {
      $string.='<li>';
      if ($i != $this->page + 1)
        $string .= '<a href="' . $this->getUrl($i) . '">';
      $string .= $i;
      if ($i != $this->page + 1)
        $string .= '</a>';
      $string .= '</li>';
    }
    return $string;
  }

  public function getUrl($page = false) {
    if ($page)
      $this->request_params['page'] = $page;

    foreach ($this->request_params as $key => $value) {
      if ($key{0} == '_') {
        unset($this->request_params[$key]);
      }
    }

    return $this->router->generate($this->route, $this->request_params);
  }

}
