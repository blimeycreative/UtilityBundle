<?php

namespace Oxygen\PaginationBundle;

use Symfony\Component\DependencyInjection\Container;

class Factory {

  private $container;
  private $class;

  public function __construct(Container $container, $class) {
    $this->container = $container;
    $this->class = $class;
  }

  public function paginate($query, $limit = 20, $page = null) {
    if ($page == null) {
      $page = $this->container->get('request')->attributes->get('page') - 1;
      if ($page < 0) {
        throw Exception::pageNumber();
      }
    }

    $class = $this->class;

    return new $class($this->container, $query, (int) $limit, (int) $page);
  }

}
