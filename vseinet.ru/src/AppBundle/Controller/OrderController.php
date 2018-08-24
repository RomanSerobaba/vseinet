<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Form;
use AppBundle\Bus\Order\Command;
use AppBundle\Bus\Order\Query;

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
        $command = new Command\CreateCommand();

        if ($this->getUser()) {
            if ($this->getUserIsEmployee()) {
                if ($request->isXmlHttpRequest()) {
                    return $this->json([
                        'html' => $this->renderView('Order/manager_creation_ajax.html.twig', [
                        ]),
                    ]);
                }
    
                return $this->render('Order/manager_creation.html.twig', [
                ]);
            } else {
                if ($request->isXmlHttpRequest()) {
                    return $this->json([
                        'html' => $this->renderView('Order/creation_ajax.html.twig', [
                        ]),
                    ]);
                }
    
                return $this->render('Order/creation.html.twig', [
                ]);
            }
        } else {
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'html' => $this->renderView('Order/creation_ajax.html.twig', [
                    ]),
                ]);
            }

            return $this->render('Order/creation.html.twig', [
            ]);
        }
        
    }
}
