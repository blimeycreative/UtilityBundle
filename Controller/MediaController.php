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
    $file = $this->getRequest()->files->get('Filedata');
    $data = $this->getRequest()->request->all();
    $media = $this->saveFile($file, $data);
    return array();
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
    $directory = $this->getDoctrine()->getRepository('OxygenUtilityBundle:Location')->find($data['directory']);
    if (!$directory)
      throw $this->createNotFoundException('no directory found');
    $dirs = explode('/', $directory->getPath());
    $path = '';
    foreach ($dirs as $dir) {
      $path .= ($path == '' ? $dir : '/' . $dir);
      if (!is_dir($path))
        mkdir($path, 0777, true);
    }
    $em = $this->getDoctrine()->getEntityManager();

    $media = new Media();
    $media->setName($name);
    $media->setExtension($ext);
    $em->persist($media);
    $em->flush();

    $file = $this->resizeAndSave($path, $size, $name . '-' . $media->getId(), $ext, $tmp);

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
