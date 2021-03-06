<?php
namespace Oxygen\UtilityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Oxygen\UtilityBundle\Entity\Location;
use Doctrine\Common\Persistence\ObjectManager;

class LocationFixtures extends AbstractFixture implements OrderedFixtureInterface{
  
  public function load(ObjectManager $manager){
    $location = new Location();
    $location->setName('Uploads directory');
    $location->setPath('uploads');
    $manager->persist($location);
    $manager->flush();
    $location = new Location();
    $location->setName('Uploads image directory');
    $location->setPath('uploads/images');
    $manager->persist($location);
    $manager->flush();
  }
  
  public function getOrder(){
    return 1;
  } 
}
