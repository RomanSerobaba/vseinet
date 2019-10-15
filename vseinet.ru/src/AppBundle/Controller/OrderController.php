<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Exception\ValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Enum\OrderType;
use AppBundle\Bus\Order\Command;
use AppBundle\Bus\Order\Query;
use AppBundle\Bus\Order\Form;
use AppBundle\Enum\PaymentTypeCode;
use AppBundle\Bus\Catalog\Paging;
use AppBundle\Enum\DeliveryTypeCode;
use AppBundle\ApiClient\ApiClientException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Bus\User\Query\GetUserDataQuery;
use AppBundle\Bus\User\Command\IdentifyCommand;
use AppBundle\Entity\Contact;
use AppBundle\Entity\OrderItemStatus;
use AppBundle\Enum\ContactTypeCode;

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
                    $this->addFormErrors($form, $e->getAsArray());
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

        $em = $this->getDoctrine()->getManager();
        $statuses = [];
        foreach ($em->getRepository(OrderItemStatus::class)->findAll() as $status) {
            $statuses[$status->getCode()] = $status->getClientName();
        }

        return $this->render('Order/status_tracker.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
            'order' => $order ?? null,
            'statuses' => $statuses,
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
     *     name="order_receipts_of_product",
     *     path="/order/receiptsOfProduct/{id}/",
     *     requirements={"id"="\d+"},
     *     methods={"GET", "POST"},
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function receiptsOfProduct(int $id, Request $request)
    {
        $command = new Command\ReceiptsOfProductCommand();

        $baseProduct = $this->getDoctrine()->getManager()->getRepository(BaseProduct::class)->find($id);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d  не найден', $id));
        }
        $command->baseProductId = $baseProduct->getId();

        if ($request->isMethod('GET')) {
            $command->userData = $this->get('query_bus')->handle(new GetUserDataQuery());
        }

        $form = $this->createForm(Form\ReceiptsOfProductFormType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $command->userData = $this->get('command_bus')->handle(new IdentifyCommand(['userData' => $command->userData]));
                    $orderId = $this->get('command_bus')->handle($command);
                    $order = $this->get('query_bus')->handle(new Query\GetOrderQuery(['id' => $orderId]));

                    return $this->json([
                        'notice' => $this->renderView('Order/receipts_of_product_success.html.twig', [
                            'order' => $order,
                            'baseProduct' => $baseProduct,
                        ]),
                    ]);
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getAsArray());
                } catch (ApiClientException $e) {
                    $paramErrors = $e->getParamErrors();

                    if (!empty($paramErrors)) {
                        $messages = array_combine(array_column($paramErrors, 'name'), array_column($paramErrors, 'message'));
                        $this->addFormErrors($form, $messages);
                    } else {
                        $this->addFormErrors($form, ['' => $e->getMessage().' '.$e->getDebugTokenLink()]);
                    }
                }
            }

            return $this->json([
                'errors' => $this->getFormErrors($form),
            ]);
        }

        return $this->json([
            'html' => $this->renderView('Order/receipts_of_product_form.html.twig', [
                'form' => $form->createView(),
                'baseProduct' => $baseProduct,
            ]),
        ]);
    }

    /**
     * @VIA\Get(
     *     name="order_creation_credit",
     *     path="/order/credit/{id}/",
     *     requirements={"id"="\d+"}
     * )
     */
    public function creationCreditAction(int $id)
    {
        $this->forward('AppBundle:Cart:add', ['id' => $id]);
        $this->get('session')->set('form.orderCreation', ['paymentTypeCode' => PaymentTypeCode::CREDIT]);

        return $this->redirectToRoute('order_creation_page');
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
            if ($this->getUser() && !$this->getUserIsEmployee()) {
                if (empty($data['client']['fullname'])) {
                    $person = $this->getUser()->person;
                    $data['client']['fullname'] = implode(' ', [$person->getLastName(), $person->getFirstName(), $person->getSecondName()]);
                }
                if (empty($data['client']['phone'])) {
                    $em = $this->getDoctrine()->getManager();
                    $personId = $this->getUser()->getPersonId();
                    $contact = $em->getRepository(Contact::class)->findOneBy(['personId' => $personId, 'contactTypeCode' => ContactTypeCode::MOBILE, 'isMain' => true]);
                    if (!empty($contact)) {
                        $data['client']['phone'] = $contact->getValue();
                    }
                }
                if (empty($data['client']['email'])) {
                    $em = $this->getDoctrine()->getManager();
                    $personId = $this->getUser()->getPersonId();
                    $contact = $em->getRepository(Contact::class)->findOneBy(['personId' => $personId, 'contactTypeCode' => ContactTypeCode::EMAIL, 'isMain' => true]);
                    if (!empty($contact)) {
                        $data['client']['email'] = $contact->getValue();
                    }
                }
                if (empty($data['client']['additionalPhone'])) {
                    $em = $this->getDoctrine()->getManager();
                    $personId = $this->getUser()->getPersonId();
                    $contacts = $em->getRepository(Contact::class)->findBy(['personId' => $personId, 'contactTypeCode' => [ContactTypeCode::MOBILE, ContactTypeCode::PHONE], 'isMain' => false]);
                    if (!empty($contacts)) {
                        $phones = [];
                        foreach ($contacts as $contact) {
                            $phones[$contact->getValue()] = $contact->getValue();
                        }
                        $data['client']['additionalPhone'] = implode(', ', $phones);
                    }
                }

            }
        }

        $command = new Command\CreateCommand($data);
        $cart = $this->get('query_bus')->handle(new \AppBundle\Bus\Cart\Query\GetQuery([
            'discountCode' => $this->get('session')->get('discountCode'),
            'geoPointId' => $this->getUserIsEmployee() ? $this->getUser()->defaultGeoPointId : null,
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

        // $this->get('validation_bus')->handle($command);

        $form = $this->createForm(Form\CreateFormType::class, $command);
        /** @var \AppBundle\Bus\Cart\Query\DTO\CartSummary $cart */
        $cart = $this->get('query_bus')->handle(new \AppBundle\Bus\Cart\Query\GetSummaryQuery([
            'products' => $cart->products,
            'discountCode' => $cart->discountCode,
            'discountCodeId' => $cart->discountCodeId,
            'geoPointId' => $command->geoPointId,
            'paymentTypeCode' => $command->paymentTypeCode,
            'deliveryTypeCode' => $command->deliveryTypeCode,
            'needLifting' => $command->needLifting,
            'hasLift' => !empty($command->address) ? $command->address->hasLift : null,
            'floor' => !empty($command->address) ? $command->address->floor : null,
            'transportCompanyId' => $command->transportCompanyId,
            'orderTypeCode' => $command->typeCode,
        ]));

        if ($request->isMethod('POST') && empty($cart->products)) {
            return $this->redirectToRoute('order_creation_page');
        }

        if ($request->isMethod('POST') && !$request->query->get('refreshOnly')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid() && !empty($data['submit']) && $request->request->get('submit')) {
                try {
                    $this->get('command_bus')->handle($command);
                    $this->forward('AppBundle:Cart:clear');
                    $this->get('session')->remove('discountCode');
                    $this->get('session')->remove('form.orderCreation');

                    $this->get('session')->set('order_successfully_created', true);

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
                    $this->addFormErrors($form, $e->getAsArray());
                } catch (ApiClientException $e) {
                    $paramErrors = $e->getParamErrors();

                    if (!empty($paramErrors)) {
                        $messages = array_combine(array_column($paramErrors, 'name'), array_column($paramErrors, 'message'));
                        $this->addFormErrors($form, $messages);
                    } else {
                        $this->addFormErrors($form, ['' => $e->getMessage().' '.$e->getDebugTokenLink()]);
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
                'html' => $this->renderView('Order/'.$command->typeCode.'_creation_ajax.html.twig', [
                    'form' => $form->createView(),
                    'canCreateRetailOrder' => $canCreateRetailOrder,
                    'cart' => $cart,
                ]),
                'cart_html' => $this->renderView('Order/cart_ajax.html.twig', [
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
     *     requirements={"id"="\d+"}
     * )
     */
    public function createdPageAction(int $id, Request $request)
    {
        $query = new Query\GetOrderQuery(['id' => $id]);
        $order = $this->get('query_bus')->handle($query);

        if (null === $order || !$this->getUserIsEmployee() && !$this->get('session')->get('order_successfully_created') && (null === $this->getUser() || $order->financialCounteragentId != $this->getUser()->financialCounteragent->getId())) {
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

    /**
     * @VIA\Get(
     *     name="get_counteragent",
     *     path="/counteragent/",
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function getCounteragentAction(Request $request)
    {
        $counteragent = $this->get('query_bus')->handle(new Query\GetCounteragentQuery($request->query->all()));

        return $this->json([
            'data' => $counteragent,
        ]);
    }
}
