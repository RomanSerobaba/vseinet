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
use AppBundle\ApiClient\ApiClientException;

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
                    $order = $this->get('query_bus')->handle($query);

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
        $history = $this->get('query_bus')->handle($query);

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
            $data = $request->request->get('create_form');
            $this->get('session')->set('form.orderCreation', $data);
        } else {
            $data = $this->get('session')->get('form.orderCreation', []);
        }

        $command = new Command\CreateCommand($data);
        $cart = $this->get('query_bus')->handle(new \AppBundle\Bus\Cart\Query\GetQuery([
            'discountCode' => $this->get('session')->get('discountCode', null),
            'geoPoinId' => $this->getUserIsEmployee() ? $this->getUser()->defaultGeoPointId : NULL,
        ]));

        if ($cart->hasStroika && in_array($command->typeCode, [OrderType::NATURAL, OrderType::LEGAL])) {
            $command->geoPointId = $this->getParameter('default.point.id');
            $command->geoCityId = $this->getParameter('default.city.id');
            $command->deliveryTypeCode = DeliveryTypeCode::EX_WORKS;
        }

        $canCreateRetailOrder = false;

        if ($this->getUserIsEmployee() && $this->getUser()->defaultGeoPointId) {
            $canCreateRetailOrder = true;

            foreach ($cart->products as $product) {
                if ($product->reserveQuantity < $product->quantity) {
                    $canCreateRetailOrder = false;
                }
            }
        }

        $form = $this->createForm(Form\CreateFormType::class, $command);
        $cart = $this->get('query_bus')->handle(new \AppBundle\Bus\Cart\Query\GetSummaryQuery([
            'cart' => $cart,
            'geoPointId' => $command->geoPointId,
            'paymentTypeCode' => $command->paymentTypeCode,
            'deliveryTypeCode' => $command->deliveryTypeCode,
            'needLifting' => $command->needLifting,
            'hasLift' => !empty($command->address) ? $command->address->hasLift : null,
            'floor' => !empty($command->address) ? $command->address->floor : null,
            'transportCompanyId' => $command->transportCompanyId,
        ]));

        if ($request->isMethod('POST') && !$request->query->get('refreshOnly')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid() && !empty($data['submit'])) {
                try {
                    $this->get('command_bus')->handle($command);
                    // $this->forward('AppBundle:Cart:clear');
                    $this->get('session')->remove('discountCode');
                    $this->get('session')->remove('form.orderCreation');

                    $this->get('session')->set('order_successfully_created', TRUE);

                    if ($request->isXmlHttpRequest()) {
                        return $this->json([
                            'id' => $command->id,
                            'isInnerOrder' => OrderType::isInnerOrder($command->typeCode),
                        ]);
                    }

                    // if (OrderType::isInnerOrder($command->typeCode)) {
                    //     return $this->redirectToRoute('authority', ['targetUrl' => $this->getParameter('admin.host') . '/admin/orders/?id=' . $command->id]);
                    // }

                    return $this->redirectToRoute('order_created_page', ['id' => $command->id]);
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (ApiClientException $e) {
                    $paramErrors = $e->getParamErrors();

                    if (!empty($paramErrors)) {
                        $messages = array_combine(array_column($paramErrors, 'name'), array_column($paramErrors, 'message'));
                        $this->addFormErrors($form, $messages);
                    } else {
                        $this->addFormErrors($form, ['' => $e->getMessage() . ' ' . $e->getDebugTokenLink()]);
                    }
                }
            }

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'errors' => $this->getFormErrors($form),
                    'html' => $this->renderView('Order/cart_ajax.html.twig', [
                        'form' => $form->createView(),
                        'canCreateRetailOrder' => $canCreateRetailOrder,
                        'cart' => $cart,
                    ]),
                ]);
            }
        }

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('Order/' . $command->typeCode . '_creation_ajax.html.twig', [
                    'form' => $form->createView(),
                    'canCreateRetailOrder' => $canCreateRetailOrder,
                    'cart' => $cart,
                ]),
            ]);
        }

        return $this->render('Order/creation.html.twig', [
            'form' => $form->createView(),
            'canCreateRetailOrder' => $canCreateRetailOrder,
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
        $order = $this->get('query_bus')->handle($query);

        if (null === $order || !$this->getUserIsEmployee() && !$this->get('session')->get('order_successfully_created') && (NULL === $this->getUser() ||$order->financialCounteragentId != $this->getUser()->financialCounteragent->getId())) {
            throw new NotFoundHttpException();
        }

        $this->get('session')->remove('order_successfully_created');

        return $this->render('Order/created.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @VIA\Get(
     *     name="get_bank",
     *     path="/bank/",
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function getBankAction(Request $request)
    {
        $bank = $this->get('query_bus')->handle(new Query\GetBankQuery($request->query->all()));

        return $this->json([
            'data' => $bank,
        ]);
    }
}
