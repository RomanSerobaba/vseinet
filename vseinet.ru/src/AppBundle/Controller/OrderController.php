<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Cart\Query;
use AppBundle\Bus\Cart\Command;
use AppBundle\Form;

class OrderController extends Controller
{
    /**
     * @internal order status form.
     */
    public function statusFormAction()
    {
        if (!$this->get('request_stack')->getParentRequest() instanceof Request) {
            throw new NotFoundHttpException(); 
        }

        $status = new Form\OrderStatus();
        $form = $this->createForm(Form\OrderStatusType::class, $status); 

        return $this->render('AppBundle:Order:status_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @VIA\Post(name="order_status_check", path="orders/status")
     */
    public function checkStatusAction()
    {
        $status = new Form\OrderStatus();
        $form = $this->createForm(Form\OrderStatusType::class, $status);
        $form->handleRequest($this->get('request_stack')->getCurrentRequest());
        if ($form->isSubmitted() && $form->isValid()) {

            //return $this->redirectToRoute('task_success');

        }

        return $this->render('AppBundle:Order:status_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @VIA\Get(name="order_status_page", path="/orders/status/")
     */
    public function statusPageAction()
    {
        
    }

    /**
     * @VIA\Get(name="order_create", path="/order/")
     */
    public function createAction(Request $request)
    {
        // $this->get('query_bus')->handle(new Query\GetInfoQuery(), $info);
        // $this->get('query_bus')->handle(new Query\GetContactsQuery(), $contacts);
        // $this->get('query_bus')->handle(new Query\GetAddressesQuery(), $addresses);

        $account = [
            // 'info' => $info,
            // 'contacts' => $contacts,
            // 'addresses' => $addresses,
        ];

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('Order/creation_ajax.html.twig', [
                    'account' => $account,
                ]),
            ]);
        }

        return $this->render('Order/creation.html.twig', [
            'account' => $account,
        ]);        
    }
}
