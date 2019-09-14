<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Annotation as VIA;

/**
 * @Security("is_granted('ROLE_EMPLOYEE')")
 */
class MainController extends Controller
{
    /**
     * @VIA\Get(
     *     name="authority",
     *     path="/authority/"
     * )
     */
    public function authorityAction(Request $request)
    {
        $targetUrl = $request->query->get('targetUrl');
        if (empty($targetUrl)) {
            $targetUrl = $this->generateUrl('admin');
        }
        $auth = $this->get('user.api.client')->getAuth();

        return $this->render('@Admin/Main/authority.html.twig', $auth + ['targetUrl' => $targetUrl]);
    }

    /**
     * @VIA\Get(
     *     name="clockin",
     *     path="/clockin/"
     * )
     */
    public function clockInAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user->ipAddress || $user->ipAddress == $this->get('request_stack')->getMasterRequest()->getClientIp()) {
            $url = sprintf('/api/v1/work/%s/', null === $user->clockInTime ? 'start' : 'stop');
            $this->get('user.api.client')->put($url);
            $user->person = null;
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
