<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Annotation as VIA;
use AdminBundle\Bus\Supplier\Query;
use AdminBundle\Bus\Supplier\Command;

/**
 * @Security("is_granted('ROLE_EMPLOYEE')")
 */
class SupplierController extends Controller
{
    /**
     * @VIA\Get(
     *     name="admin_supplier_remains",
     *     path="/supplier/remains/",
     *     parameters={
     *         @VIA\Parameter(model="AdminBundle\Bus\Supplier\Query\GetRemainsQuery")
     *     },
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function remiansAction(Request $request)
    {
        $remains = $this->get('query_bus')->handle(new Query\GetRemainsQuery($request->query->all()));

        return $this->json([
            'html' => $this->renderView('@Admin/Supplier/remains.html.twig', [
                'remains' => $remains,
            ]),
        ]);
    }

    /**
     * @VIA\Post(
     *     name="admin_supplier_unlink",
     *     path="/supplier/unlink/",
     *     parameters={
     *         @VIA\Parameter(model="AdminBundle\Bus\Supplier\Command\UnlinkCommand")
     *     },
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function unlinkAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\UnlinkCommand($request->request->all()));

        return $this->json([]);
    }

    /**
     * @VIA\Post(
     *     name="admin_supplier_restore",
     *     path="/supplier/restore/",
     *     parameters={
     *         @VIA\Parameter(model="AdminBundle\Bus\Supplier\Command\RestoreCommand")
     *     },
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function restoreAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\RestoreCommand($request->request->all()));

        return $this->json([]);
    }

    /**
     * @VIA\Post(
     *     name="admin_supplier_set_not_available",
     *     path="/supplier/setNoAvailable/",
     *     parameters={
     *         @VIA\Parameter(model="AdminBundle\Bus\Supplier\Command\SetNotAvailableCommand")
     *     },
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function setNotAvailableAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetNotAvailableCommand($request->request->all()));

        return $this->json([]);
    }
}
