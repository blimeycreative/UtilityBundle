<?php

namespace Oxygen\UtilityBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Oxygen\UtilityBundle\Entity\Location
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Oxygen\UtilityBundle\Entity\LocationRepository")
 */
class Location {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string $name
   *
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $name;

  /**
   * @var string $path
   *
   * @ORM\Column(name="path", type="string", length=255)
   */
  private $path;

  /**
   * @ORM\OneToMany(targetEntity="Media", mappedBy="location")
   */
  private $medias;

  public function __construct() {
    $this->medias = new ArrayCollection();
  }
  
  public function __toString() {
    return $this->name;
  }

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set name
   *
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Get name
   *
   * @return string 
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set path
   *
   * @param string $path
   */
  public function setPath($path) {
    $this->path = $path;
  }

  /**
   * Get path
   *
   * @return string 
   */
  public function getPath() {
    return $this->path;
  }

  /**
   * Add medias
   *
   * @param Oxygen\UtilityBundle\Entity\Media $medias
   */
  public function addMedia(\Oxygen\UtilityBundle\Entity\Media $media) {
    $this->medias[] = $media;
  }

  /**
   * Get medias
   *
   * @return Doctrine\Common\Collections\Collection 
   */
  public function getMedias() {
    return $this->medias;
  }

}