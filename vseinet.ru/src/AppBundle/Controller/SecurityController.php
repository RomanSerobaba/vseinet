<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Annotation as VIA;
use AppBundle\Exception\ValidationException;
use AppBundle\Bus\Security\Command;
use AppBundle\Bus\Security\Form;

class SecurityController extends Controller
{
    /**
     * @VIA\Route(
     *     name="login",
     *     path="/login/",
     *     methods={"GET", "POST"}
     * )
     */
    public function loginAction(Request $request)
    {
        $helper = $this->get('security.authentication_utils');

        $form = $this->createForm(Form\LoginType::class, [
            '_username' => $helper->getLastUsername(),
        ]);

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'html' => $this->renderView('Security/login_form.html.twig', [
                    'form' => $form->createView(),
                    'error' => $helper->getLastAuthenticationError(),
                ]),
            ]);
        }

        return $this->render('Security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * @VIA\Get(
     *     name="logout",
     *     path="/logout"
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function logoutAction()
    {
        throw new \RuntimeException('This should never be called directly.');
    }

    /**
     * @VIA\Route(
     *     name="registr",
     *     path="/registr/",
     *     methods={"GET", "POST"}
     * )
     */
    public function registrAction(Request $request)
    {
        $command = new Command\RegistrCommand();
        $form = $this->createForm(Form\RegistrType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    $this->addFlash('notice', 'Регистрация прошла успешно');

                    return $this->redirectToRoute('index');
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getAsArray());
                }
            }
        }

        return $this->render('Security/registr.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Route(
     *     name="forgot",
     *     path="/forgot/",
     *     methods={"GET", "POST"}
     * )
     */
    public function forgotAction(Request $request)
    {
        $command = new Command\ForgotCommand();
        $form = $this->createForm(Form\ForgotType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    return $this->redirectToRoute('check_token');
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getAsArray());
                }
            }
        }

        return $this->render('Security/forgot.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Route(
     *     name="check_token",
     *     path="/check/token/",
     *     methods={"GET", "POST"}
     * )
     */
    public function checkAction(Request $request)
    {
        if ($request->isMethod('GET') && $request->query->has('hash')) {
            $this->get('command_bus')->handle(new Command\CheckTokenCommand($request->query->all()));

            return $this->redirectToRoute('restore_password');
        }

        $command = new Command\CheckTokenCommand();
        $form = $this->createForm(Form\CheckTokenType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    return $this->redirectToRoute('restore_password');
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getAsArray());
                }
            }
        }

        return $this->render('Security/check_token.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Route(
     *     name="restore_password",
     *     path="/restore/password/",
     *     methods={"GET", "POST"}
     * )
     */
    public function restoreAction(Request $request)
    {
        $command = new Command\RestorePasswordCommand();
        $form = $this->createForm(Form\RestorePasswordType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    return $this->redirectToRoute('user_account');
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getAsArray());
                }
            }
        }

        return $this->render('Security/restore_password.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Route(
     *     name="change_password",
     *     path="/change/password/",
     *     methods={"GET", "POST"}
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function passwordAction(Request $request)
    {
        $command = new Command\ChangePasswordCommand();
        $form = $this->createForm(Form\ChangePasswordType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);
                    $this->addFlash('notice', 'Новый пароль успешно сохранен');

                    return $this->redirectToRoute('user_account');
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getAsArray());
                }
            }
        }

        return $this->render('Security/change_password.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }
}
