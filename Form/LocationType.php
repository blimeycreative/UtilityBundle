<?php

namespace Oxygen\UtilityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class LocationType extends AbstractType {

  public function buildForm(FormBuilder $builder, array $options) {
    $builder
            ->add('name')
            ->add('path')
    ;
  }

  public function getName() {
    return 'media_location';
  }

  public function getDefaultOptions(array $options) {
    return array('data_class' => "Oxygen\UtilityBundle\Entity\Location");
  }

}
