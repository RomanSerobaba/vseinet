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
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\Enum\PaymentTypeCode;

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
        if ($request->isMethod('POST')) {
            $command = new Command\CreateCommand($request->request->get('create_form'));
        } else {
            $command = new Command\CreateCommand();

            if (!$this->getUserIsEmployee()) {
                $this->get('query_bus')->handle(new \AppBundle\Bus\User\Query\GetUserDataQuery(), $command->userData);
            }
        }

        $this->get('query_bus')->handle(new \AppBundle\Bus\Cart\Query\GetQuery([
            'discountCode' => $this->get('session')->get('discountCode', null),
        ]), $cart);

        if ($cart->hasStroika && in_array($command->typeCode, [OrderType::NATURAL, OrderType::LEGAL])) {
            $command->geoPointId = $this->getParameter('default.point.id');
            $command->geoCityId = $this->getParameter('default.city.id');
            $command->deliveryTypeCode = DeliveryTypeCode::EX_WORKS;
        }

        $form = $this->createForm(Form\CreateFormType::class, $command);
        $this->get('query_bus')->handle(new \AppBundle\Bus\Cart\Query\GetSummaryQuery([
            'cart' => $cart,
            'geoPointId' => $command->geoPointId,
            'paymentTypeCode' => $command->paymentTypeCode,
            'deliveryTypeCode' => $command->deliveryTypeCode,
            'needLifting' => $command->needLifting,
            'hasLift' => !empty($command->geoAddress) ? $command->geoAddress->hasLift : null,
            'floor' => !empty($command->geoAddress) ? $command->geoAddress->floor : null,
            'transportCompanyId' => $command->transportCompanyId,
        ]), $cart);

        if ($request->isMethod('POST')) {
            // if (!$request->query->get('refreshOnly')) {
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid() && !$request->isXmlHttpRequest()) {
                    try {
                        $this->get('command_bus')->handle($command);
                        // $this->forward('AppBundle:Cart:clear');
                        $this->get('session')->remove('discountCode');

                        if (OrderType::isInnerOrder($command->typeCode)) {
                            return $this->redirectToRoute('authority', ['targetUrl' => '/admin/orders/?id=' . $command->id]);
                        }

                        if ($request->isXmlHttpRequest()) {
                            return $this->json([
                                'id' => $command->id,
                            ]);
                        }

                        $this->get('session')->set('order_successfully_created', TRUE);

                        return $this->redirectToRoute('order_created_page', ['id' => $command->id]);

                    } catch (ValidationException $e) {
                        $this->addFormErrors($form, $e->getMessages());
                    }
                }

                if ($request->isXmlHttpRequest()) {
                    return $this->json([
                        'errors' => $this->getFormErrors($form),
                        'html' => $this->renderView('Order/cart_ajax.html.twig', [
                            'form' => $form->createView(),
                            'cart' => $cart,
                        ]),
                    ]);
                }
            // }
        }

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('Order/' . $command->typeCode . '_creation_ajax.html.twig', [
                    'form' => $form->createView(),
                    'cart' => $cart,
                ]),
            ]);
        }

        return $this->render('Order/creation.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart,
            'errors' => $this->getFormErrors($form),
        ]);

    }

    /**
     * @VIA\Get(
     *     name="order_created_page",
     *     path="/order/success/{id}/",
     *     requirements={"id" = "\d+"}
     * )
     */
    public function createdPageAction(int $id, Request $request)
    {
        $query = new Query\GetOrderQuery(['id' => $id,]);
        $this->get('query_bus')->handle($query, $order);

        if (null === $order || !$this->getUserIsEmployee() && !$this->get('session')->get('order_successfully_created') && (NULL === $this->getUser() ||$order->financialCounteragentId != $this->getUser()->financialCounteragent->getId())) {
            throw new NotFoundHttpException();
        }

        $this->get('session')->remove('order_successfully_created');

        return $this->render('Order/created.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @VIA\Post(
     *     name="search_bank",
     *     path="/bank/search/",
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function searchBankAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\SearchBankQuery($request->request->all()), $banks);

        return $this->json([
            'banks' => $banks,
        ]);
    }
}
