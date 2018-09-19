<?php 

namespace AdminBundle\Controller;

use AppBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Annotation as VIA;
use AdminBundle\Bus\Reserves\Query;

/**
 * @Security("is_granted('ROLE_EMPLOYEE')")
 */
class ReservesController extends Controller
{
    /**
     * @VIA\Get(
     *     name="admin_reserves",
     *     path="/reserves/",
     *     parameters={
     *         @VIA\Parameter(model="AdminBundle\Bus\Reserves\Query\GetReservesQuery")
     *     },
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function getReservesAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetReservesQuery($request->query->all()), $reserves);

        return $this->json([
            'html' => $this->renderView('@Admin/Reserves/reserves.html.twig', [
                'reserves' => $reserves,
            ]),
        ]);
    }
}
