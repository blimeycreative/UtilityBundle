<?php

namespace Oxygen\UtilityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oxygen\UtilityBundle\Entity\Media;
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
    $file = $this->upload($file, $data);

    mail('luke@oxygenthinking.com', 'test', print_r($file,true));
    $media = new Media();
    $media->setName($file[0]);
    $media->setExtension($file[1]);
    $media->setLocation($file[2]);

    $em = $this->getDoctrine()->getEntityManager();
    $em->persist($media);
    $em->flush();

    return array('id' => $media->getId());
  }

  private function upload(UploadedFile $file, $data) {
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
      $img->writeImage(sprintf('%s/%s-%s.%s', $path, $name, $postfix, $ext));
    }
    return array($name . '-' . $postfix, $ext, $directory);
  }

}
