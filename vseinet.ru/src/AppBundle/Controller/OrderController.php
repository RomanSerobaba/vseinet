<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Enum\OrderType;
use AppBundle\Bus\Order\{ Command, Query, Form };

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
                    $this->get('query_bus')->handle($query, $orderItems);

                    if ($request->isXmlHttpRequest()) {
                        return $this->json([
                            'status' => $status,
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

        $type = $request->query->get('type') ?? OrderType::NATURAL;

        $command = new Command\CreateCommand();
        $command->typeCode = $type;
        $form = $this->createForm(Form\CreateFormType::class, $command);

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
