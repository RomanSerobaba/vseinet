<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Bus\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\User\Form;
use Symfony\Component\Form\FormError;
use AppBundle\Bus\User\Query;
use AppBundle\Bus\User\Command;
use GeoBundle\Entity\GeoCity;

class UserController extends Controller
{
    /**
     * @VIA\Route(name="user_registr", path="/user/registr/", methods={"GET", "POST"})
     */
    public function registrAction(Request $request) 
    {
        $this->checkIsAnonimous();

        $command = new Command\RegistrCommand();
        $form = $this->createForm(Form\RegistrType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    return $this->redirectToRoute('index');
        
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new BadRequestHttpException($e);
                }
            }
        }

        return $this->render('AppBundle:User:registr_form.html.twig', [
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

        $command = new Command\LoginCommand();
        $form = $this->createForm(Form\LoginType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);
                    
                    if ($request->isXmlHttpRequest()) {
                        return $this->json([]);
                    }

                    return $this->redirect($this->getReturnUrl());

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new BadRequestHttpException($e);
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
                'html' => $this->renderView('AppBundle:User:login_form_ajax.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }

        return $this->render('AppBundle:User:login_form.html.twig', [
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

        $command = new Command\ForgotCommand();
        $form = $this->createForm(Form\ForgotType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);
                    
                    return $this->redirectToRoute('user_check_token');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new BadRequestHttpException($e);
                }
            }
        }

        return $this->render('AppBundle:User:forgot_form.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Route(name="user_check_token", path="/user/check/", methods={"GET", "POST"})
     */
    public function checkAction(Request $request)
    {
        $this->checkIsAnonimous();

        if ($request->isMethod('GET') && $request->query->has('hash')) {
            try {
                $this->get('command_bus')->handle(new Command\CheckTokenCommand($request->query->all()));
                
                return $this->redirectToRoute('user_restore_password');

            } catch (\Exception $e) {
                throw new BadRequestHttpException($e);
            }
        }
        
        $command = new Command\CheckTokenCommand();
        $form = $this->createForm(Form\CheckTokenType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    return $this->redirectToRoute('user_restore_password');
                    
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new BadRequestHttpException($e);
                }
            }
        }    

        return $this->render('AppBundle:User:check_token_form.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Route(name="user_restore_password", path="/user/restore/password/", methods={"GET", "POST"})
     */
    public function restoreAction(Request $request)
    {
        $this->checkIsAutorized();
        
        $command = new Command\RestorePasswordCommand();
        $form = $this->createForm(Form\RestorePasswordType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);
                    
                    return $this->redirectToRoute('user_account');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new BadRequestHttpException($e);
                }
            }
        }

        return $this->render('AppBundle:User:restore_password_form.html.twig', [
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
                'html' => $this->renderView('AppBundle:User:account_ajax.html.twig', [
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

        $command = new Command\UpdateCommand();
        if ($request->isMethod('GET')) {
            $this->get('query_bus')->handle(new Query\GetQuery(), $info);
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

                    if ($request->isXmlHttpRequest()) {
                        $this->get('query_bus')->handle(new Query\GetQuery(), $info);
                        
                        return $this->json([
                            'html' => $this->renderView('AppBundle:User:account_info.html.twig', [
                                'info' => $info, 
                            ]),
                        ]);

                        return $this->json([]);
                    }

                    return $this->redirectToRoute('user_account');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new BadRequestHttpException($e);
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
                'html' => $this->renderView('AppBundle:User:edit_form_ajax.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }

        return $this->render('AppBundle:User:edit_form.html.twig', [
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
                'html' => $this->renderView('AppBundle:User:history_ajax.html.twig', [
                    'history' => $history,
                ]),
            ]);
        }

        return $this->render('AppBundle:User:history.html.twig', [
            'history' => $history,
        ]);
    }

    /**
     * @VIA\Route(name="user_change_password", path="/user/change/password/", methods={"GET", "POST"})
     */
    public function passwordAction(Request $request)
    {
        $this->checkIsAutorized();

        $command = new Command\ChangePasswordCommand();
        $form = $this->createForm(Form\ChangePasswordType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    if ($request->isXmlHttpRequest()) {
                        return $this->json([]);
                    }
                    
                    return $this->redirectToRoute('user_account');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new BadRequestHttpException($e);
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
                'html' => $this->renderView('AppBundle:User:password_form_ajax.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }

        // var_dump($command, $this->getFormErrors($form)); exit;

        return $this->render('AppBundle:User:password_form.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Route(name="user_contact_add", path="/user/contact/add/", methods={"GET", "POST"})
     */
    public function addContactAction(Request $request)
    {
        $this->checkIsAutorized();   

        $command = new Command\CreateContactCommand();
        $form = $this->createForm(Form\CreateContactType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    if ($request->isXmlHttpRequest()) {
                        $this->get('query_bus')->handle(new Query\GetContactQuery(['id' => $command->id]), $contact);

                        return $this->json([
                            'html' => $this->renderView('AppBundle:User:contact.html.twig', [
                                'contact' => $contact,
                            ]),
                        ]);
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
                'html' => $this->renderView('AppBundle:User:contact_create_form_ajax.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }  

        return $this->render('AppBundle:User:contact_create_form.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]); 
    }

    /**
     * @VIA\Route(name="user_contact_edit", path="/user/contact/{id}/", requirements={"id" = "\d+"}, methods={"GET", "POST"})
     */
    public function editContactAction(int $id, Request $request)
    {
        $this->checkIsAutorized(); 

        $command = new Command\UpdateContactCommand(['id' => $id]);
        if ($request->isMethod('GET')) {
            $this->get('query_bus')->handle(new Query\GetContactQuery(['id' => $id]), $contact);
            $command->init((array) $contact);   
        }
        $form = $this->createForm(Form\ContactEditType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    if ($request->isXmlHttpRequest()) {
                        $this->get('query_bus')->handle(new Query\GetContactQuery(['id' => $command->id]), $contact);
                           
                        return $this->json([
                            'html' => $this->renderView('AppBundle:User:contact.html.twig', [
                                'contact' => $contact,
                            ]),
                        ]);
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
                'html' => $this->renderView('AppBundle:User:contact_edit_form_ajax.html.twig', [
                    'form' => $form->createView(),
                ]),
            ]);
        }  

        return $this->render('AppBundle:User:contact_edit_form.html.twig', [
            'id' => $id,
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]); 
    }

    /**
     * @VIA\Get(name="user_contact_delete", path="/user/contact/{id}/delete/", requirements={"id" = "\d+"})
     */
    public function deleteContactAction(int $id, Request $request)
    {
        $this->checkIsAutorized();

        $this->get('command_bus')->handle(new Command\DeleteContactCommand(['id' => $id]));
        
        if ($request->isXmlHttpRequest()) {
            return $this->json([]);
        }

        return $this->redirectToRoute('user_account');
    }

    /**
     * @VIA\Route(name="user_address_add", path="/user/address/add/", methods={"GET", "POST"})
     */
    public function addAddressAction(Request $request)
    {
        $this->checkIsAutorized();

        $command = new Command\CreateAddressCommand();
        $form = $this->createForm(Form\AddressType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($form->getData());

                    if ($request->isXmlHttpRequest()) {
                        $this->get('query_bus')->handle(new Query\GetAddressQuery(['id' => $command->id]), $address);

                        return $this->json([
                            'html' => $this->renderView('AppBundle:User:address.html.twig', [
                                'address' => $address,
                            ]),
                        ]);
                    }

                    return $this->redirectToRoute('user_account');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new BadRequestHttpException($e);
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
     * @VIA\Route(name="user_address_edit", path="/user/address/{id}/", requirements={"id" = "\d+"}, methods={"GET", "POST"})
     */
    public function editAddressAction(int $id, Request $request)
    {
        $this->checkIsAutorized();

        $command = new Command\UpdateAddressCommand();
        if ($Request->isMethod('GET')) {
            $this->get('query_bus')->handle(new Query\GetAddressQuery(['id' => $id]), $address);
            $command->init((array) $address);
        }
        $form = $this->createForm(Form\AddressType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    if ($request->isXmlHttpRequest()) {
                        $this->get('query_bus')->handle(new Query\GetAddressQuery(['id' => $command->id]), $address);
                           
                        return $this->json([
                            'html' => $this->renderView('AppBundle:User:address.html.twig', [
                                'address' => $address,
                            ]),
                        ]);
                    }

                    return $this->redirectToRoute('user_addresses');

                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                } catch (\Exception $e) {
                    throw new BadRequestHttpException($e);
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
     * @VIA\Get(name="user_address_delete", path="/user/address/{id}/delete/", requirements={"id" = "\d+"})
     */
    public function deleteAddressAction(int $id, Request $request)
    {
        $this->checkIsAutorized();

        $this->get('command_bus')->handle(new Command\DeleteAddressCommand(['id' => $id]));
        
        if ($Request->isXmlHttpRequest()) {
            return $this->json([]);
        }

        return $this->redirectToRoute('user_account');
    }

    protected function checkIsAnonimous()
    {
        if (!$this->get('user.identity')->isAnonimous()) {
            throw new BadRequestHttpException();
        }
    }

    protected function checkIsAutorized()
    {
        if (!$this->get('user.identity')->isAuthorized()) {
            throw new BadRequestHttpException();
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
            if ($child->isSubmitted() && !$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }
        }

        return $errors;
    }
}
