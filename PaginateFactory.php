<?php

namespace Oxygen\UtilityBundle;

use Symfony\Component\DependencyInjection\Container;

class PaginateFactory {

  private $container;
  private $class;

  public function __construct(Container $container, $class) {
    $this->container = $container;
    $this->class = $class;
  }

  public function paginate($query, $limit = 20, $alias = 'a', $page = null) {
    if ($page == null) {
      $page = $this->container->get('request')->attributes->get('page') - 1;
      if ($page < 0) {
        throw Exception::pageNumber();
      }
    }
    $class = $this->class;
    $this->class = $class::getInstance($this->container, $query, $alias, (int) $limit, (int) $page);
    return $this->class;
  }

  public function getPagination($name) {
    if (is_object($this->class))
      return $this->class->getPagination($name);
    return false;
  }

}
