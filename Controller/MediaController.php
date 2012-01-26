<?php

namespace Oxygen\UtilityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oxygen\UtilityBundle\Entity\Media;
use Oxygen\UtilityBundle\Entity\Location;
use Oxygen\UtilityBundle\Form\MediaType;
use Oxygen\UtilityBundle\Utility\Utility;

class MediaController extends Controller {

  /**
   * @Route("/media/upload", name="media_upload")
   * @Template
   */
  public function uploadAction() {
    ini_set('memory_limit', '1024M');
    ini_set('max_execution_time', 300);
    $file = $this->getRequest()->files->get('Filedata');
    $data = $this->getRequest()->request->all();
    return array('media' => $this->saveFile($file, $data));
  }

  public function resizeAndSave($path, $size, $name, $ext, $tmp) {
    $saved = false;
    foreach ($this->container->getParameter('media.image.sizes') as $postfix => $sizes) {
      $img = new \Imagick($tmp);
      if ($size[0] >= $sizes['width'] || $size[1] >= $sizes['height']) {
        if ($postfix == 'original')
          $img->thumbnailImage($sizes['width'], $sizes['height'], true);
        else {
          $img->cropThumbnailImage($sizes['width'], $sizes['height']);
        }
      }
      else
        $img->cropThumbnailImage($sizes['width'], $sizes['height']);
      // Route starts in web folder

      $saved = $img->writeImage(sprintf('%s/%s-%s.%s', $path, $name, $postfix, $ext));
    }
    return array($name, $ext, $saved);
  }

  private function saveFile(UploadedFile $file, $data) {
    $tmp = $file->getFileInfo()->getPathname();
    $name = Utility::slugify(substr($data['Filename'], 0, -4));
    $ext = Utility::slugify($file->getExtension());
    $ext = $ext ? $ext : $file->guessExtension();
    $size = getimagesize($tmp);
    $em = $this->getDoctrine()->getEntityManager();
    $directory = $em->getRepository('OxygenUtilityBundle:Location')->find($data['directory']);
    if (!$directory)
      throw $this->createNotFoundException('no directory found');
    if (!is_dir($directory->getPath()))
      mkdir($directory->getPath(), 0777, true);

    $media = $em->getRepository('OxygenUtilityBundle:Media')->findOneBy(array('name' => $name));
    if (!$media) {
      $media = new Media();
      $media->setName($name);
    }
    $media->setExtension($ext);
    $em->persist($media);
    $em->flush();
    
    $file = $this->resizeAndSave($directory->getPath(), $size, $name, $ext, $tmp);
    
    if ($file[2]) {
      $media->setName($file[0]);
      $media->setExtension($file[1]);
      $media->setLocation($directory); 
      $em->persist($media);
    } else
      $em->remove($media);
    $em->flush();
    return $file[2] ? $media : false;
  }

}
