<?php
namespace Oxygen\UtilityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Oxygen\UtilityBundle\Entity\Location;

class LocationFixtures extends AbstractFixture implements OrderedFixtureInterface{
  
  public function load( $manager){
    $location = new Location();
    $location->setName('Upload directory');
    $location->setPath('uploads');
    $manager->persist($location);
    $manager->flush();
    $location = new Location();
    $location->setName('Upload inner directory');
    $location->setPath('uploads/inner');
    $manager->persist($location);
    $manager->flush();
  }
  
  public function getOrder(){
    return 1;
  } 
}
