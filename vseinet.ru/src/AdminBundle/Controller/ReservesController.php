<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Annotation as VIA;
use AdminBundle\Bus\Reserves\Query;
use AdminBundle\Bus\Reserves\Command;
use AdminBundle\Bus\Reserves\Form;

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
        $reserves = $this->get('query_bus')->handle(new Query\GetReservesQuery($request->query->all()));

        return $this->json([
            'html' => $this->renderView('@Admin/Reserves/reserves.html.twig', [
                'reserves' => $reserves,
                'baseProductId' => $request->query->get('baseProductId'),
            ]),
        ]);
    }

    /**
     * @VIA\Route(
     *     name="admin_handmade_pricetag_edit",
     *     path="/products/{id}/pricetag/",
     *     methods={"GET", "POST"},
     *     condition="request.isXmlHttpRequest()"
     * )
     * @Security("is_granted('ROLE_PURCHASER') or is_granted('ROLE_ADMIN')")
     */
    public function setPricetagAction(int $id, Request $request)
    {
        $command = new Command\SetPricetagCommand(['id' => $id, 'geoPointId' => $request->query->get('geoPointId')]);
        $form = $this->createForm(Form\SetPricetagFormType::class, $command);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    return $this->json([]);
                } catch (ValidationException $e) {
                    $this->AddFormErrors($form, $e->getAsArray());
                }
            }

            return $this->json([
                'errors' => $this->getFormErrors($form),
            ]);
        }

        return $this->json([
            'html' => $this->renderView('@Admin/Reserves/set_pricetag_form.html.twig', [
                'form' => $form->createView(),
                'command' => $command,
            ]),
        ]);
    }
}
