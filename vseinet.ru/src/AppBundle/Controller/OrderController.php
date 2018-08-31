<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Order\{ Command, Query, Form };
use AppBundle\Enum\OrderItemStatus;

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
     * @VIA\Get(name="order_creation_page", path="/order/")
     */
    public function creationPageAction(Request $request)
    {
        // $command = new Command\AddAddressCommand(['id' => $id]);
        // if ($id && $request->isMethod('GET')) {
        //     $this->get('query_bus')->handle(new Query\GetAddressQuery(['id' => $id]), $address);
        //     $command->init((array) $address);
        // }
        // $form = $this->createForm(Form\AddAddressType::class, $command);

        // if ($request->isMethod('POST')) {
        //     $form->handleRequest($request);
        //     if ($form->isSubmitted() && $form->isValid()) {
        //         try {
        //             $this->get('command_bus')->handle($command);

        //             if ($request->isXmlHttpRequest()) {
        //                 $this->get('query_bus')->handle(new Query\GetAddressQuery(['id' => $command->id]), $address);

        //                 return $this->json([
        //                     'html' => $this->renderView('User/address.html.twig', [
        //                         'address' => $address,
        //                     ]),
        //                 ]);
        //             }

        //             $flashBag = $this->get('session')->getFlashBag();
        //             if ($command->id) {
        //                 $flashBag->add('notice', 'Адрес доставки успешно изменен');
        //             } else {
        //                 $flashBag->add('notice', 'Адрес доставки успешно добавлен');
        //             }

        //             return $this->redirectToRoute('user_account');

        //         } catch (ValidationException $e) {
        //             $this->addFormErrors($form, $e->getMessages());
        //         }
        //     }

        //     if ($request->isXmlHttpRequest()) {
        //         return $this->json([
        //             'errors' => $this->getFormErrors($form),
        //         ]);
        //     }
        // }
        if ($this->getUserIsEmployee()) {
            $types = [
                'natural' => 'На физ. лицо',
                'legal' => 'На юр. лицо',
                'retail' => 'Продажа с магазина',
                'resupply' => 'Пополнение складских запасов',
                'consumables' => 'Расходные материалы',
                'equipment' => 'Оборудование',
            ];
        } else {
            $types = [
                'natural' => 'На физ. лицо',
                'legal' => 'На юр. лицо',
            ];
        }

        $type = $request->query->get('type') ?? key($types);

        $command = new Command\CreateCommand();
        $command->typeCode = $type;
        $form = $this->createForm(Form\CreateFormType::class, $command);

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('Order/' . $type . '_creation_ajax.html.twig', [
                ]),
            ]);
        }

        return $this->render('Order/creation.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
            'choicesTypes' => $types,
            'currentType' => $type,
        ]);
        
    }
}
