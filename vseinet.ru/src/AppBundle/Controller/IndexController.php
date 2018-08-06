<?php 

namespace AppBundle\Controller;

use AppBundle\Annotation as VIA;

/**
 * @VIA\Section("Homepage")
 */
class IndexController extends RestController 
{
    /**
     * @VIA\Get(
     *     path="/_l"
     * )
     * @VIA\Response(
     *     status=200
     * )
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig', [ 
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR, 
        ]);
    }
}