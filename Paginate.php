<?php

namespace Oxygen\UtilityBundle;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\QueryBuilder;

class Paginate {

  private $container, $query, $alias, $limit, $page, $result, $data, $template, $request, $router, $route, $request_params, $list_limit;

  public function __construct(Container $container, QueryBuilder $query, $alias='a', $limit = 20, $page = 1, $list_limit = 3) {
    $this->container = $container;
    $this->limit = $limit;
    $this->alias = $alias;
    $this->query = $query;
    $this->page = $page;
    $this->request = $container->get('request');
    $this->router = $container->get('router');
    $this->route = $this->request->attributes->get('_route');
    $this->request_params = $this->request->attributes->all() + $this->request->query->all();
    $this->list_limit = $list_limit;
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
      $this->data->offset = $this->page * $this->limit;
      $this->data->url = $this->getUrl();
      $total_query = clone $this->query;
      $local_query = clone $this->query;
      $this->data->count = $this->data->offset + count($local_query
                              ->setMaxResults($this->limit)
                              ->setFirstResult($this->data->offset)
                              ->getQuery()
                              ->getScalarResult());
      $this->data->total_results = $total_query
              ->select('COUNT('.$this->alias.')')
              ->getQuery()
              ->getSingleScalarResult();
      $this->data->total_pages = ceil($this->data->total_results / $this->limit);
      if ($this->page > $this->data->total_pages)
        Exception::pageNumber();
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
      $this->template = $this->container
              ->get('templating')
              ->render('OxygenUtilityBundle:Pagination:pagination.html.twig', array(
          'link_list' => $this->generateLinkList(),
          'data' => $this->data
              ));
  }

  public function generateLinkList() {
    $link_list = array();
    $upper_limit = (int) (($this->page + 1) + ($this->list_limit - 1) / 2);
    $lower_limit = (int) (($this->page + 1) - ($this->list_limit - 1) / 2);
    if ($this->data->total_pages > 1) {
      if ($this->page != 0)
        $link_list[] = $this->getPageLink('First', 1);
      if ($lower_limit > 1)
        $link_list[] = $this->getPageLink('..', ($this->page + 1));
      for ($i = ((int) ($this->page + 1) - ($this->list_limit - 1) / 2 > 1 ? $lower_limit : 1); $i <= ($upper_limit < $this->data->total_pages ? $upper_limit : $this->data->total_pages); $i++)
        $link_list[] = $this->getPageLink($i, $i);
      if ($upper_limit < $this->data->total_pages)
        $link_list[] = $this->getPageLink('..', ($this->page + 1));
      if (($this->page + 1) != $this->data->total_pages)
        $link_list[] = $this->getPageLink('Last', $this->data->total_pages);
    }
    return $link_list;
  }

  public function getPageLink($text, $page) {
    $link = new \stdClass();
    if ($page != $this->page + 1)
      $link->location = $this->getUrl($page);
    $link->text = $text;
    return $link;
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
