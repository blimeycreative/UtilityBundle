<?php

namespace Oxygen\UtilityBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Oxygen\UtilityBundle\Entity\Media
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Oxygen\UtilityBundle\Entity\MediaRepository")
 */
class Media {

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
   * @ORM\Column(name="name", type="string", length=255, unique=true)
   */
  private $name;

  /**
   * @var string $location
   *
   * @ORM\ManyToOne(targetEntity="Location", inversedBy="medias")
   * @ORM\JoinColumn(name="location_id", referencedColumnName="id", onDelete="SET NULL")
   * @Assert\Type(type="Oxygen\UtilityBundle\Entity\Location")
   */
  private $location;

  /**
   * @var string $extension
   *
   * @ORM\Column(name="extension", type="string", length=255)
   */
  private $extension;

  /**
   * @var datetime $created
   *
   * @ORM\Column(name="created", type="datetime")
   */
  private $created;
  
  public function __construct()
    {
        $this->setCreated(new \DateTime());
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
   * Set extension
   *
   * @param string $extension
   */
  public function setExtension($extension) {
    $this->extension = $extension;
  }

  /**
   * Get extension
   *
   * @return string 
   */
  public function getExtension() {
    return $this->extension;
  }

  /**
   * Set created
   *
   * @param datetime $created
   */
  public function setCreated($created) {
    $this->created = $created;
  }

  /**
   * Get created
   *
   * @return datetime 
   */
  public function getCreated() {
    return $this->created;
  }

  /**
   * Set location
   *
   * @param Oxygen\UtilityBundle\Entity\Location $location
   */
  public function setLocation(\Oxygen\UtilityBundle\Entity\Location $location) {
    $this->location = $location;
  }

  /**
   * Get location
   *
   * @return Oxygen\UtilityBundle\Entity\Location 
   */
  public function getLocation() {
    return $this->location;
  }

}