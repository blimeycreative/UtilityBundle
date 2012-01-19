<?php

namespace Oxygen\UtilityBundle;

use Symfony\Component\DependencyInjection\Container;
use Oxygen\UtilityBundle\Entity\Media as MediaEntity;
use Oxygen\UtilityBundle\Form\MediaType;
class Media {
  
  private $container;
  
  public function __construct(Container $container){
    $this->container = $container;
  }

  public function uploader() {
    $this->mediaForm();
    return $this->container
              ->get('templating')
              ->render('OxygenUtilityBundle:Media:uploader.html.twig', array(
                'form' => $this->form->createView()
            ));
  }

  private function mediaForm() {
    $this->media = new MediaEntity();
    $this->form = $this->container->get('form.factory')->create(new MediaType(), $this->media, array());
  }

}
