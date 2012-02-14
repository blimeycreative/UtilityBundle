<?php

namespace Oxygen\UtilityBundle;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\QueryBuilder;

class Paginate {

  private $container, $query, $alias, $limit, $page, $request, $router, $route, $request_params, $list_limit;
  private $result, $data, $template = array();
  private static $instance;

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

  public static function getInstance(Container $container, QueryBuilder $query, $alias='a', $limit = 20, $page = 1, $list_limit = 3) {
    if (!self::$instance)
      self::$instance = new Paginate($container, $query, $alias, $limit, $page, $list_limit);
    return self::$instance;
  }

  public function getResults($name = 'standard') {
    $this->getData($name);
    $this->getTemplate($name);
    return $this->getResult($name);
  }

  public function getResult($name) {
    if (!isset($this->result[$name]))
      $this->setResult(false, $name);
    return $this->result[$name];
  }

  public function setResult($val = false, $name = 'standard') {
    if ($val)
      $this->result[$name] = $val;
    else {
      if (!$this->data[$name] || !(is_object($this->data[$name]) && property_exists($this->data[$name], 'offset')))
        $this->setData($name);
      $this->result[$name] = $this->query->setMaxResults($this->limit)->setFirstResult($this->data[$name]->offset)->getQuery();
    }
  }

  public function getData($name) {
    if (!isset($this->data))
      $this->setData(false, $name);
    return $this->data;
  }

  public function setData($val = false, $name = "standard") {
    if ($val)
      $this->data[$name] = $val;
    else {
      $data = new \stdClass();
      $data->offset = $this->page * $this->limit;
      $data->url = $this->getUrl();
      $total_query = clone $this->query;
      $local_query = clone $this->query;
      $data->count = $data->offset + count($local_query
                              ->setMaxResults($this->limit)
                              ->setFirstResult($data->offset)
                              ->getQuery()
                              ->getScalarResult());
      $data->total_results = $total_query
              ->select('COUNT(' . $this->alias . ')')
              ->getQuery()
              ->getSingleScalarResult();
      $data->total_pages = ceil($data->total_results / $this->limit);
      if ($this->page > $data->total_pages)
        Exception::pageNumber();
      $this->data[$name] = $data;
    }
  }

  public function getTemplate($name) {
    if (!isset($this->template[$name]))
      $this->setTemplate(false, $name);
    return $this->template[$name];
  }

  public function setTemplate($val = false, $name = 'standard') {
    if ($val)
      $this->template[$name] = $val;
    else
      $this->template[$name] = $this->container
              ->get('templating')
              ->render('OxygenUtilityBundle:Pagination:pagination.html.twig', array(
          'link_list' => $this->generateLinkList($name),
          'data' => $this->data[$name]
              ));
  }

  public function generateLinkList($name) {
    $link_list = array();
    $upper_limit = (int) (($this->page + 1) + ($this->list_limit - 1) / 2);
    $lower_limit = (int) (($this->page + 1) - ($this->list_limit - 1) / 2);
    if ($this->data[$name]->total_pages > 1) {
      if ($this->page != 0)
        $link_list[] = $this->getPageLink('First', 1);
      if ($lower_limit > 1)
        $link_list[] = $this->getPageLink('..', ($this->page + 1));
      for ($i = ((int) ($this->page + 1) - ($this->list_limit - 1) / 2 > 1 ? $lower_limit : 1); $i <= ($upper_limit < $this->data[$name]->total_pages ? $upper_limit : $this->data[$name]->total_pages); $i++)
        $link_list[] = $this->getPageLink($i, $i);
      if ($upper_limit < $this->data[$name]->total_pages)
        $link_list[] = $this->getPageLink('..', ($this->page + 1));
      if (($this->page + 1) != $this->data[$name]->total_pages)
        $link_list[] = $this->getPageLink('Last', $this->data[$name]->total_pages);
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

  public function getPagination($name) {
    if (isset($this->template[$name]))
      return $this->template[$name];
    return false;
  }

}
