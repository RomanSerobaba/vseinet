<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Exception\ValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Enum\OrderType;
use AppBundle\Bus\Order\{ Command, Query, Form };
use AppBundle\Enum\OrderItemStatus;
use AppBundle\Bus\Catalog\Paging;

class OrderController extends Controller
{
    /**
     * @VIA\Route(
     *     name="order_status",
     *     path="/order/status/",
     *     methods={"GET", "POST"}
     * )
     */
    public function statusAction(Request $request)
    {
        $query = new Query\GetStatusQuery();
        $form = $this->createForm(Form\GetStatusFormType::class, $query);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('query_bus')->handle($query, $order);

                    if ($request->isXmlHttpRequest()) {
                        $count = count($order->items);
                        if (5 < $count) {
                            $order->items = array_slice($order->items, 0, 5);
                            $more = $count - 5;
                        } else {
                            $more = 0;
                        }

                        return $this->json([
                            'html' => $this->renderView('Order/status.html.twig', [
                                'order' => $order,
                                'more' => $more, 
                            ]),
                        ]);
                    }

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                }
            }

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'errors' => $this->getFormErrors($form),
                ]);
            }   
        }

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('Order/status_form.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }

        return $this->render('Order/status_tracker.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
            'order' => $order ?? null,
            'statuses' => OrderItemStatus::getChoices(),
        ]);
    }

    /**
     * @VIA\Get(
     *     name="order_history",
     *     path="/order/history/",
     *     parameters={
     *         @VIA\Parameter(model="AppBundle\Bus\Order\Query\GetHistoryQuery")
     *     }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')") 
     */
    public function historyAction(Request $request)
    {   
        $query = new Query\GetHistoryQuery($request->query->all());
        $this->get('query_bus')->handle($query, $history);

        $paging = new Paging([
            'total' => $history->total,
            'page' => $query->page,
            'perpage' => $query->limit,
            'lines' => 8,
            'baseUrl' => $this->generateUrl('order_history'),
            'attributes' => ['mode' => $query->mode],
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('Order/history_ajax.html.twig', (array) $query + [
                    'history' => $history,
                    'paging' => $paging,
                ]),
            ]);
        }

        return $this->render('Order/history.html.twig', (array) $query + [
            'history' => $history,
            'paging' => $paging,
        ]);
    }

    /**
     * @VIA\Route(
     *     name="order_creation_page",
     *     path="/order/",
     *     methods={"GET", "POST"}
     * )
     */
    public function creationPageAction(Request $request)
    {
        $type = $request->query->get('type') ?? OrderType::NATURAL;

        $command = new Command\CreateCommand();
        $command->typeCode = $type;
        $form = $this->createForm(Form\CreateFormType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    // if ($request->isXmlHttpRequest()) {
                    //     $this->get('query_bus')->handle(new Query\GetAddressQuery(['id' => $command->id]), $address);

                    //     return $this->json([
                    //         'html' => $this->renderView('User/address.html.twig', [
                    //             'address' => $address,
                    //         ]),
                    //     ]);
                    // }

                    // $flashBag = $this->get('session')->getFlashBag();

                    // if ($command->id) {
                    //     $flashBag->add('notice', 'Адрес доставки успешно изменен');
                    // } else {
                    //     $flashBag->add('notice', 'Адрес доставки успешно добавлен');
                    // }

                    return $this->redirectToRoute('order_created');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                }
            }

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'errors' => $this->getFormErrors($form),
                ]);
            }
        }

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('Order/' . $type . '_creation_ajax.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }

        return $this->render('Order/creation.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
            'currentType' => $type,
        ]);
        
    }
}
