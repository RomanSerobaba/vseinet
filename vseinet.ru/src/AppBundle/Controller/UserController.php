<?php

namespace AppBundle\Controller;

use AppBundle\Exception\ValidationException;
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
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        $info = $this->get('query_bus')->handle(new Query\GetInfoQuery());
        $contacts = $this->get('query_bus')->handle(new Query\GetContactsQuery());
        $addresses = $this->get('query_bus')->handle(new Query\GetAddressesQuery());

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
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        if ($request->isMethod('GET')) {
            $info = $this->get('query_bus')->handle(new Query\GetInfoQuery());
            $command = new Command\AccountEditCommand((array) $info);
        } else {
            $command = new Command\AccountEditCommand();
        }
        $form = $this->createForm(Form\AccountEditType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);
                    $this->get('session')->getFlashBag()->add('notice', 'Ваш профиль успешно обновлен');

                    return $this->redirectToRoute('user_account');
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getAsArray());
                }
            }
        }

        return $this->render('User/edit_form.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Route(
     *     name="user_contact_edit",
     *     path="/user/contact/{id}/",
     *     requirements={"id": "\d+"},
     *     methods={"GET", "POST"}
     * )
     * @VIA\Route(
     *     name="user_contact_add",
     *     path="/user/contact/add/",
     *     methods={"GET", "POST"}
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function addContactAction(int $id = 0, Request $request)
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        if ($id && $request->isMethod('GET')) {
            $contact = $this->get('query_bus')->handle(new Query\GetContactQuery(['id' => $id]));
            $command = new Command\AddContactCommand((array) $contact);
        } else {
            $command = new Command\AddContactCommand(['id' => $id]);
        }
        $form = $this->createForm(Form\AddContactType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $contact = $this->get('command_bus')->handle($command);

                    $notice = $id ? 'Контакт успешно обновлен' : 'Контакт успешно добавлен';

                    if ($request->isXmlHttpRequest()) {
                        $contact = $this->get('query_bus')->handle(new Query\GetContactQuery(['id' => $contact->getId()]));

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
                'html' => $this->renderView('User/contact_form_ajax.html.twig', [
                    'command' => $command,
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
     *     requirements={"id": "\d+"}
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function deleteContactAction(int $id, Request $request)
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        $this->get('command_bus')->handle(new Command\DeleteContactCommand(['id' => $id]));

        $notice = 'Контакт успешно удален';

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'notice' => $notice,
            ]);
        }

        $this->get('session')->getFlashBag()->add('notice', $notice);

        return $this->redirectToRoute('user_account');
    }

    /**
     * @VIA\Route(
     *     name="user_address_edit",
     *     path="/user/address/{id}/",
     *     requirements={"id": "\d+"},
     *     methods={"GET", "POST"}
     * )
     * @VIA\Route(
     *     name="user_address_add",
     *     path="/user/address/add/",
     *     methods={"GET", "POST"}
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function addAddressAction(int $id = 0, Request $request)
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        if ($request->isMethod('GET')) {
            if ($id) {
                $address = $this->get('query_bus')->handle(new Query\GetAddressQuery(['id' => $id]));
                $command = new Command\AddAddressCommand((array) $address);
            } else {
                if ($geoCityId = $this->getUser()->getGeoCityId()) {
                    $geoCity = $this->getDoctrine()->getManager()->getRepository(GeoCity::class)->find($geoCityId);
                } else {
                    $geoCity = $this->getGeoCity();
                }
                $command = new Command\AddAddressCommand(['geoCityId' => $geoCity->getId(), 'geoCityName' => $geoCity->getName()]);
            }
        } else {
            $command = new Command\AddAddressCommand(['id' => $id]);
        }
        $form = $this->createForm(Form\AddAddressType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $address = $this->get('command_bus')->handle($command);

                    $notice = $id ? 'Адрес доставки успешно изменен' : 'Адрес доставки успешно добавлен';

                    if ($request->isXmlHttpRequest()) {
                        $address = $this->get('query_bus')->handle(new Query\GetAddressQuery(['id' => $address->getId()]));

                        return $this->json([
                            'html' => $this->renderView('User/address.html.twig', [
                                'address' => $address,
                            ]),
                            'notice' => $notice,
                        ]);
                    }

                    $this->get('session')->getFlashBag()->add('notice', $notice);

                    return $this->redirectToRoute('user_account');
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
                'html' => $this->renderView('User/address_form_ajax.html.twig', [
                    'command' => $command,
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
     *     requirements={"id": "\d+"}
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function deleteAddressAction(int $id, Request $request)
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
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

    /**
     * @VIA\Get(
     *     name="user_search_autocomplete",
     *     path="/users/search/",
     *     condition="request.isXmlHttpRequest()"
     * )
     * @Security("is_granted('ROLE_EMPLOYEE')")
     */
    public function searchAction(Request $request)
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        $users = $this->get('query_bus')->handle(new Query\SearchQuery($request->query->all()));

        return $this->json([
            'users' => $users,
        ]);
    }
}
