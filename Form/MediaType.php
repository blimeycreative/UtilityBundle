<?php

namespace Oxygen\UtilityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MediaType extends AbstractType {

  public function buildForm(FormBuilder $builder, array $options) {
    $builder
            ->add('name','file',array('label'=>' '))
            ->add('extension', 'hidden')
            //DEFAULT SET TO TIMESTAMP->add('created')
            ->add('location')
    ;
  }

  public function getName() {
    return 'media';
  }
  
  public function getDefaultOptions(array $options){
    return array('data_class' => "Oxygen\UtilityBundle\Entity\Media");
  }

}
