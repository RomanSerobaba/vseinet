<?php

namespace AppBundle\Controller;

use AppBundle\Bus\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\User\Form;
use AppBundle\Bus\User\Query;
use AppBundle\Bus\User\Command;
use AppBundle\Entity\GeoCity;

class UserController extends Controller
{
    /**
     * @VIA\Get(
     *     name="user_account", 
     *     path="/user/account/"
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')") 
     */
    public function accountAction(Request $request)
    {
        $this->get('query_bus')->handle(new Query\GetInfoQuery(), $info);
        $this->get('query_bus')->handle(new Query\GetContactsQuery(), $contacts);
        $this->get('query_bus')->handle(new Query\GetAddressesQuery(), $addresses);

        $account = [
            'info' => $info,
            'contacts' => $contacts,
            'addresses' => $addresses,
        ];

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('User/account_ajax.html.twig', [
                    'account' => $account,
                ]),
            ]);
        }

        return $this->render('User/account.html.twig', [
            'account' => $account,
        ]);
    }

    /**
     * @VIA\Route(
     *     name="user_edit", 
     *     path="/user/edit/", 
     *     methods={"GET", "POST"}
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')") 
     */
    public function editAction(Request $request)
    {
        $command = new Command\UpdateCommand();
        if ($request->isMethod('GET')) {
            $this->get('query_bus')->handle(new Query\GetInfoQuery(), $info);
            $command->init((array) $info);  
            if ($info->cityId) {
                $command->city = $this->getDoctrine()->getRepository(GeoCity::class)->find($info->cityId);
            }
        }
        $form = $this->createForm(Form\EditType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    $notice = 'Ваш профиль успешно обновлен';

                    if ($request->isXmlHttpRequest()) {
                        $this->get('query_bus')->handle(new Query\GetInfoQuery(), $info);
                        
                        return $this->json([
                            'html' => $this->renderView('User/account_info.html.twig', [
                                'info' => $info, 
                            ]),
                            'notice' => $notice,
                        ]);
                    }

                    $this->get('session')->getFlashBag()->add('notice', $notice);

                    return $this->redirectToRoute('user_account');

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
                'html' => $this->renderView('User/edit_form_ajax.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }

        return $this->render('User/edit_form.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);    
    }

    /**
     * @VIA\Route(
     *     name="user_contact_add", 
     *     path="/user/contact/add/", 
     *     methods={"GET", "POST"}
     * )
     * @VIA\Route(
     *     name="user_contact_edit", 
     *     path="/user/contact/{id}/", 
     *     requirements={"id" = "\d+"}, 
     *     methods={"GET", "POST"}
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')") 
     */
    public function addContactAction(int $id = 0, Request $request)
    {
        $command = new Command\AddContactCommand(['id' => $id]);
        if ($id && $request->isMethod('GET')) {
            $this->get('query_bus')->handle(new Query\GetContactQuery(['id' => $id]), $contact);
            $command->init((array) $contact);   
        }
        $form = $this->createForm(Form\AddContactType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    $notice = $id ? 'Контакт успешно обновлен' : 'Контакт успешно добавлен';

                    if ($request->isXmlHttpRequest()) {
                        $this->get('query_bus')->handle(new Query\GetContactQuery(['id' => $command->id]), $contact);

                        return $this->json([
                            'html' => $this->renderView('User/contact.html.twig', [
                                'contact' => $contact,
                            ]),
                            'notice' => $notice,
                        ]);
                    }

                    $this->get('session')->getFlashBag()->add('notice', $notice);

                    return $this->redirectToRoute('user_account');

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
                'html' => $this->renderView('User/contact_form_ajax.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }  

        return $this->render('User/contact_form.html.twig', [
            'command' => $command,
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]); 
    }

    /**
     * @VIA\Get(
     *     name="user_contact_delete", 
     *     path="/user/contact/{id}/delete/", 
     *     requirements={"id" = "\d+"}
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')") 
     */
    public function deleteContactAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteContactCommand(['id' => $id]));
        
        $notice = 'Контакт успешно удален';

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'notice' => $notice,
            ]);
        }

        return $this->redirectToRoute('user_account');
    }

    /**
     * @VIA\Route(
     *     name="user_address_add", 
     *     path="/user/address/add/", 
     *     methods={"GET", "POST"}
     * )
     * @VIA\Route(
     *     name="user_address_edit", 
     *     path="/user/address/{id}/", 
     *     requirements={"id" = "\d+"}, 
     *     methods={"GET", "POST"}
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')") 
     */
    public function addAddressAction(int $id = 0, Request $request)
    {
        $command = new Command\AddAddressCommand(['id' => $id]);
        if ($id && $request->isMethod('GET')) {
            $this->get('query_bus')->handle(new Query\GetAddressQuery(['id' => $id]), $address);
            $command->init((array) $address);
        }
        $form = $this->createForm(Form\AddAddressType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    if ($request->isXmlHttpRequest()) {
                        $this->get('query_bus')->handle(new Query\GetAddressQuery(['id' => $command->id]), $address);

                        return $this->json([
                            'html' => $this->renderView('User/address.html.twig', [
                                'address' => $address,
                            ]),
                        ]);
                    }

                    $flashBag = $this->get('session')->getFlashBag();
                    if ($command->id) {
                        $flashBag->add('notice', 'Адрес доставки успешно изменен');
                    } else {
                        $flashBag->add('notice', 'Адрес доставки успешно добавлен');
                    }

                    return $this->redirectToRoute('user_account');

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
                'html' => $this->renderView('User/address_form_ajax.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }

        return $this->render('User/address_form.html.twig', [
            'command' => $command,
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);   
    }

    /**
     * @VIA\Get(
     *     name="user_address_delete", 
     *     path="/user/address/{id}/delete/", 
     *     requirements={"id" = "\d+"}
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')") 
     */
    public function deleteAddressAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteAddressCommand(['id' => $id]));
        
        $notice = 'Адрес доставки успешно удален';

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'notice' => $notice,
            ]);
        }

        $this->get('session')->getFlashBag()->add('notice', $notice);

        return $this->redirectToRoute('user_account');
    }
}
