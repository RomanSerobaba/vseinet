<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\User\Form;
use Symfony\Component\Form\FormError;
use AppBundle\Bus\User\Query;
use AppBundle\Bus\User\Command;

class UserController extends Controller
{
    /**
     * @VIA\Route(name="user_registr", path="/user/registr/", methods={"GET", "POST"})
     */
    public function registrAction(Request $request) 
    {
        $this->checkIsAnonimous();

        $form = $this->createForm(Form\RegistrType::class, new Command\RegistrCommand());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($form->getData());

                    return $this->redirectToRoute('index');
        
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new NotFoundHttpException();
                }
            }
        }

        return $this->render('AppBundle:User:registr.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Route(name="user_login", path="/user/login/", methods={"GET", "POST"})
     */
    public function loginAction(Request $request)
    {
        $this->checkIsAnonimous();

        $form = $this->createForm(Form\LoginType::class, new Command\LoginCommand());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($form->getData());
                    
                    if ($request->isXmlHttpRequest()) {
                        return $this->json([]);
                    }

                    return $this->redirect($this->getReturnUrl());

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new NotFoundHttpException();
                }
            }

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'errors' => $this->getFormErrors($form),
                ]);
            }

        } else {
            $this->setReturnUrl($request);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('AppBundle:User:login_form.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }

        return $this->render('AppBundle:User:login.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Route(name="user_forgot", path="/user/forgot/", methods={"GET", "POST"})
     */
    public function forgotAction(Request $request)
    {
        $this->checkIsAnonimous();

        $form = $this->createForm(Form\ForgotType::class, new Command\ForgotCommand());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $this->get('command_bus')->handle($form->getData());
                    
                    return $this->redirectToRoute('check');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new NotFoundHttpException();
                }
            }
        }

        return $this->render('AppBundle:User:forgot.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Route(name="/user_check", path="/user/check/", methods={"GET", "POST"})
     */
    public function checkAction(Request $request)
    {
        $this->checkIsAnonimous();

        if ($request->isMethod('GET') && $request->query->has('hash')) {
            try {
                $this->get('command_bus')->handle(new Command\CheckTokenCommand($request->query->all()));
                
                return $this->redirectToRoute('restore');

            } catch (\Exception $e) {
                throw new NotFoundHttpException();
            }
        }
        
        $form = $this->createForm(Form\CheckTokenType::class, new Command\CheckTokenCommand());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($form->getData());

                    return $this->redirectToRoute('restore');
                    
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new NotFoundHttpException();
                }
            }
        }    

        return $this->render('AppBundle:User:check_token.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Route(name="user_restore", path="/user/restore/", methods={"GET", "POST"})
     */
    public function restoreAction(Request $request)
    {
        $this->checkIsAutorized();
        
        $form = $this->createForm(Form\RestorePasswordType::class, new Command\RestorePasswordCommand());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($form->getData());
                    
                    return $this->redirectToRoute('account');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new NotFoundHttpException();
                }
            }
        }

        return $this->render('AppBundle:User:restore_password.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Get(name="user_logout", path="/user/logout/")
     */
    public function logoutAction(Request $request)
    {
        $this->checkIsAutorized();
        
        $this->get('command_bus')->handle(new Command\LogoutCommand());

        return $this->redirectToRoute('index');     
    }

    /**
     * @VIA\Get(name="user_account", path="/user/account/")
     */
    public function accountAction(Request $request)
    {
        $this->checkIsAutorized();

        $this->get('query_bus')->handle(new Query\GetAccountQuery(), $account);

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('AppBundle:User:account_data.html.twig', [
                    'account' => $account,
                ]),
            ]);
        }

        return $this->render('AppBundle:User:account.html.twig', [
            'account' => $account,
        ]);
    }

    /**
     * @VIA\Route(name="user_edit", path="/user/edit/", methods={"GET", "POST"})
     */
    public function editAction(Request $request)
    {
        $this->checkIsAutorized();

        $this->get('query_bus')->handle(new Query\GetQuery(), $info);
        $form = $this->createForm(Form\AccountType::class, new Command\UpdateCommand((array) $info));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($form->getData());

                    if ($request->isXmlHttpRequest()) {
                        return $this->json([]);
                    }

                    return $this->redirectToRoute('user_account');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new NotFoundHttpException();
                }
            }

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'errors' => $this->getFormErrors($form);
                ]);
            }
        }

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('AppBundle:User:edit_form.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }

        return $this->render('AppBundle:User:edit.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }


    /**
     * @VIA\Route(name="user_history", path="/user/history/", methods={"GET", "POST"})
     */
    public function historyAction(Request $request)
    {
        $this->checkIsAutorized();

        $this->get('query_bus')->handle(new Query\GetHistoryQuery(), $history);

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('AppBundle:User:history_list.html.twig', [
                    'history' => $history,
                ]),
            ]);
        }

        return $this->render('AppBundle:User:history.html.twig', [
            'history' => $history,
        ]);
    }

    /**
     * @VIA\Route(name="user_password", path="/user/password/", methods={"GET", "POST"})
     */
    public function passwordAction(Request $request)
    {
        $this->checkIsAutorized();

        $form = $this->createForm(Form\ChangePasswordType::class, new Command\ChangePasswordCommand());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($form->getData());

                    if ($request->isXmlHttpRequest()) {
                        return $this->json([]);
                    }
                    
                    return $this->redirectToRoute('change_password');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new NotFoundHttpException();
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
                'html' => $this->renderView('AppBundle:User:change_password_form.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }

        return $this->render('AppBundle:User:password.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Route(name="user_contact_new", path="/user/contact/new/", methods={"GET", "POST"})
     */
    public function newContactAction(Request $request)
    {
        $this->checkIsAutorized();   

        $uuid = $this->get('uuid.manager')->generate();
        $form = $this->createForm(Form\ContactType::class, new Command\CreateContactCommand(['uuid' => $uuid]));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($form->getData());

                    if ($request->isXmlHttpRequest()) {
                        $id = $this->get('uuid.manager')->loadId($uuid);
                        $this->get('query_bus')->handle(new Query\GetContactQuery(['id' => $id]), $contact);

                        return $this->json([
                            'html' => $this->renderView('AppBundle:User:contact.html.twig', [
                                'contact' => $contact,
                            ]),
                        ]);
                    }

                    return $this->redirectToRoute('user_account');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new NotFoundHttpException();
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
                'html' => $this->renderView('AppBundle:User:contact_form_ajax.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }  

        return $this->render('AppBundle:User:contact_form.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]); 
    }

    /**
     * @VIA\Route(name="user_contact_edit", path="/user/contact/{id}/", requrements={"id" = "\d+"}, methods={"GET", "POST"})
     */
    public function editContactAction(int $id, Request $request)
    {
        $this->checkIsAutorized();

        $this->get('query_bus')->handle(new Query\GetContactQuery(['id' => $id]), $contact);
        $form = $this->createForm(Form\ContactType::class, new Command\UpdateContactCommand($contact));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($form->getData());

                    if ($request->isXmlHttpRequest()) {
                        $this->get('query_bus')->handle(new Query\GetContactQuery(['id' => $id]), $contact);
                           
                        return $this->json([
                            'html' => $this->renderView('AppBundle:User:contact.html.twig', [
                                'contact' => $contact,
                            ]),
                        ]);
                    }

                    return $this->redirectToRoute('user_account');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new NotFoundHttpException();
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
                'html' => $this->renderView('AppBundle:User:contact_form_ajax.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }  

        return $this->render('AppBundle:User:contact_form.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]); 
    }

    public function addAddressAction(Request $request)
    {
        $this->checkIsAutorized()

        $uuid = $this->get('uuid.manager')->generate();
        $form = $this->createForm(Form\AddressType::class, new Command\CreateAddressCommand(['uuid' => $uuid]));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($form->getData());

                    if ($request->isXmlHttpRequest()) {
                        $id = $this->get('uuid.manager')->loadId($uuid);
                        $this->get('query_bus')->handle(new Query\GetContactQuery(['id' => $id]), $contact);

                        return $this->json([
                            'html' => $this->renderView('AppBundle:User:contact.html.twig', [
                                'contact' => $contact,
                            ]),
                        ]);
                    }

                    return $this->redirectToRoute('user_account');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new NotFoundHttpException();
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
                'html' => $this->renderView('AppBundle:User:address_form_ajax.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }

        return $this->render('AppBundle:User:address_form.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);   
    }


    /**
     * @VIA\Route(name="user_address_edit", path="/user/address/{id}/", requrements={"id" = "\d+"}, methods={"GET", "POST"})
     */
    public function editAddressAction(int $id, Request $request)
    {
        $this->checkIsAutorized()

        $this->get('query_bus')->handle(new Query\GetAddressQuery(['id' => $id]), $address);
        $form = $this->createForm(Form\AddressType::class, new Command\UpdateAddressCommand($address));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($form->getData());

                    if ($request->isXmlHttpRequest()) {
                        return $this->json([]);
                    }

                    return $this->redirectToRoute('user_addresses');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new NotFoundHttpException();
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
                'html' => $this->renderView('AppBundle:User:address_form.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }

        return $this->render('AppBundle:User:address.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    protected function checkIsAnonimous()
    {
        if (!$this->get('user.identity')->isAnonimous()) {
            throw new NotFoundHttpException();
        }
    }

    protected function checkIsAutorized()
    {
        if (!$this->get('user.identity')->isAuthorized()) {
            throw new NotFoundHttpException();
        }
    }

    protected function getUserId()
    {
        return $this->get('user.identity')->getUser()->getId();
    }

    protected function setReturnUrl(Request $request)
    {
        $session = $this->get('session');
        if (!$session->has('return_url')) {
            $session->set('return_url', $request->headers->get('referer'));
        }
    }

    protected function getReturnUrl()
    {
        $session = $this->get('session');
        $returnUrl = $session->get('return_url') ?? $this->generateUrl('index');
        $session->remove('return_url');

        return $returnUrl;
    }

    protected function addFormErrors($form, array $messages) 
    {
        foreach ($messages as $key => $message) {
            $form->get($key)->addError(new FormError($message));   
        }
    }

    protected function getFormErrors($form)
    {
        $errors = [];
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }
        }

        return $errors;
    }
}
