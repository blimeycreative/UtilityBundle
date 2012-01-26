<?php
namespace Oxygen\UtilityBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Twig Extension for TinyMce support.
 *
 * @author  naydav <web@naydav.com>
 */
class OxygenUtilityExtension extends \Twig_Extension
{
    /**
     * Container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Initialize tinymce  helper
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'media_uploader' => new \Twig_Function_Method($this, 'mediaUploader', array('is_safe' => array('html')))
        );
    }
    /**
     * Uploader initializations
     */
    public function mediaUploader()
    {
        return $this->getContainer()->get('oxygen.utility.media.factory')->getUploader();
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'oxygen_utility';
    }
}
