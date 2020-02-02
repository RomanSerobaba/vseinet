<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Annotation as VIA;

class DebugController extends Controller
{
    /**
     * @VIA\Get(
     *     name="system_info",
     *     path="/sysinfo/"
     * )
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function sysinfoAction()
    {
        ob_start();
        phpinfo();
        $response = new Response(ob_get_contents());
        ob_end_clean();

        return $response;
    }

    /**
     * @VIA\Get(
     *     name="profiler_toggle",
     *     path="/profiler/toggle/",
     *     condition="!request.isXmlHttpRequest()"
     * )
     * @Security("is_granted('ROLE_PROGRAMMER')")
     */
    public function profilerToggleAction(Request $request)
    {
        $session = $request->getSession();
        if ($session->get('profiler.enabled')) {
            $session->remove('profiler.enabled');

            $profiler = $this->get('profiler');
            $profiler->disable();
            $profiler->purge();
        } else {
            $session->set('profiler.enabled', true);
        }

        return !empty($request->headers->get('referer')) ? $this->redirect($request->headers->get('referer')) : $this->redirectToRoute('index');
    }
}
