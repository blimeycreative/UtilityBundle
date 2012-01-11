<?php

namespace Oxygen\PaginationBundle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Exception {

  public static function pageNumber() {
    throw new NotFoundHttpException('Unknown page number submitted');
  }

}
