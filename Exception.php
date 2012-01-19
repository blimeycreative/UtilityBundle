<?php

namespace Oxygen\UtilityBundle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Exception {

  public static function pageNumber() {
    throw new NotFoundHttpException('Unknown page number submitted');
  }

}
