<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Exception\ValidationException;
use AppBundle\Annotation as VIA;
use AppBundle\Bus\Main\Query;
use AppBundle\Bus\Main\Command;
use AppBundle\Bus\Main\Form;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Competitor;
use AppBundle\Bus\User\Query\GetUserDataQuery;
use AppBundle\Bus\User\Command\IdentifyCommand;

class MainController extends Controller
{
    /**
     * @VIA\Get(
     *     name="index",
     *     path="/"
     * )
     */
    public function indexAction()
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        return $this->render('Main/index.html.twig');
    }

    /**
     * @VIA\Post(
     *     name="error_report",
     *     path="/error/report/",
     *     parameters={
     *         @VIA\Parameter(model="AppBundle\Bus\Main\Command\ErrorReportCommand")
     *     },
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function errorReportAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\ErrorReportCommand($request->request->all()));

        return $this->json([
            'notice' => 'Спасибо, Ваше замечание принято!',
        ]);
    }

    /**
     * @VIA\Route(
     *     name="cheaper_request",
     *     path="/cheaper/request/{id}/",
     *     requirements={"id": "\d+"},
     *     methods={"GET", "POST"},
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function cheaperRequestAction(int $id, Request $request)
    {
        if (!$this->getUser() || $this->getUser()->getId() != 1503) {
            echo '<html><head><title>Интернет-магазин Vseinet.ru</title></head><body style="text-align:center;font-size:60px;margin-top:20%;">Извините за временные неудобства!<br/>На сайте ведутся технические работы.</body></html>';die();
        }
        $command = new Command\CheaperRequestCommand();

        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($id);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException();
        }
        $command->baseProductId = $baseProduct->getId();
        $competitors = $em->getRepository(Competitor::class)->getActive();

        if ($request->isMethod('GET')) {
            $command->userData = $this->get('query_bus')->handle(new GetUserDataQuery());
            $command->geoCityId = $this->getGeoCity()->getId();
        }
        $form = $this->createForm(Form\CheaperRequestFormType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $command->userData = $this->get('command_bus')->handle(new IdentifyCommand(['userData' => $command->userData]));
                    $this->get('command_bus')->handle($command);

                    return $this->json([
                        'notice' => 'Ваш запрос отправлен',
                    ]);
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getAsArray());
                }
            }

            return $this->json([
                'errors' => $this->getFormErrors($form),
            ]);
        }

        return $this->json([
            'html' => $this->renderView('Main/cheaper_request_form.html.twig', [
                'form' => $form->createView(),
                'product' => $baseProduct,
                'competitors' => $competitors,
            ]),
        ]);
    }

    /**
     * @VIA\Get(
     *     name="credit_calculators",
     *     path="/credit/calculators/{id}/",
     *     requirements={"id": "\d+"},
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function creditCalculatorsAction(int $id)
    {
        $product = $this->getDoctrine()->getManager()->getRepository(BaseProduct::class)->find($id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException();
        }

        return $this->json([
            'html' => $this->renderView('Main/credit_calculators.html.twig', [
                'product' => $product,
            ]),
        ]);
    }

    /**
     * @VIA\Route(
     *     name="complaint",
     *     path="/complaint/",
     *     methods={"GET", "POST"}
     * )
     */
    public function complaintAction(Request $request)
    {
        $command = new Command\ComplaintCommand();

        if ($request->isMethod('GET')) {
            $command->userData = $this->get('query_bus')->handle(new GetUserDataQuery());
        }
        $form = $this->createForm(Form\ComplaintFormType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle(new IdentifyCommand(['userData' => $command->userData]));
                    $this->get('command_bus')->handle($command);

                    $this->addFlash('notice', 'Спасибо за Ваше сообщение, мы рассмотрим его, примем меры и при необходимости свяжемся с Вами.');

                    return $this->redirectToRoute('index');
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getAsArray());
                }
            }
        }

        return $this->render('Main/complaint_form.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @VIA\Get(
     *     name="suggestion",
     *     path="/suggestion/",
     *     methods={"GET", "POST"}
     * )
     */
    public function suggestionAction(Request $request)
    {
        $command = new Command\ClientSuggestionCommand();
        if ($request->isMethod('GET')) {
            $command->userData = $this->get('query_bus')->handle(new GetUserDataQuery());
        }
        $form = $this->createForm(Form\ClientSuggestionFormType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $command->userData = $this->get('command_bus')->handle(new IdentifyCommand(['userData' => $command->userData]));
                    $this->get('command_bus')->handle($command);

                    $this->addFlash('notice', 'Спасибо за Ваше предложение, мы рассмотрим его, примем меры и при необходимости свяжемся с Вами.');

                    return $this->redirectToRoute('index');
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getAsArray());
                }
            }
        }

        return $this->render('Main/client_suggestion_form.html.twig', [
            'form' => $form->createView(),
            'errors' => $this->getFormErrors($form),
        ]);
    }

    /**
     * @internal
     */
    public function getMenuAction()
    {
        $menu = $this->get('query_bus')->handle(new Query\GetMenuQuery());
        foreach ($menu as &$item) {
            $products = $this->get('query_bus')->handle(new Query\GetBlockSpecialsQuery(['categoryId' => $item->id, 'count' => 1]));
            if (!empty($products)) {
                $item->product = reset($products);
            }
        }

        return $this->render('Main/menu.html.twig', [
            'menu' => $menu,
        ]);
    }

    /**
     * @internal
     */
    public function getServicesAction()
    {
        $data = $this->get('query_bus')->handle(new Query\GetServicesQuery());

        return $this->render('Main/services.html.twig', $data);
    }

    /**
     * @internal
     */
    public function getBannerMainAction()
    {
        $data = $this->get('query_bus')->handle(new Query\GetBannerMainQuery());

        return $this->render('Main/banner_main.html.twig', $data);
    }

    /**
     * @internal
     */
    public function getBlockSpecialsAction(int $categoryId = 0, $title = null)
    {
        $products = $this->get('query_bus')->handle(new Query\GetBlockSpecialsQuery(['categoryId' => $categoryId, 'count' => 6]));

        return $this->render('Main/block_specials.html.twig', [
            'products' => $products,
            'title' => $title ?? 'Тотальная распродажа',
        ]);
    }

    /**
     * @internal
     */
    public function getBlockPopularsAction()
    {
        $products = $this->get('query_bus')->handle(new Query\GetBlockPopularsQuery(['count' => 4]));

        return $this->render('Main/block_populars.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @internal
     */
    public function getBlockLastviewAction()
    {
        $products = $this->get('query_bus')->handle(new Query\GetBlockLastviewQuery(['count' => 6]));

        return $this->render('Main/block_lastview.html.twig', [
            'products' => $products,
        ]);
    }
}
