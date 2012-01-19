<?php 
namespace Oxygen\UtilityBundle;

use Symfony\Component\DependencyInjection\Container;

class MediaFactory{
 
  private $container;
  private $class;

  public function __construct(Container $container, $class) {
    $this->container = $container;
    $this->class = $class;
  }
  
  public function getUploader(){
    $media = new $this->class($this->container);
    return $media->uploader();
  }
  
}
