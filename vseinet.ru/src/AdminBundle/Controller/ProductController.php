<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Annotation as VIA;
use AppBundle\Entity\BaseProduct;
use AdminBundle\Bus\Product\Command;
use AdminBundle\Bus\Product\Form;
use AdminBundle\Bus\Product\Query;

/**
 * @Security("is_granted('ROLE_EMPLOYEE')")
 */
class ProductController extends Controller
{
    /**
     * @VIA\Post(
     *     name="admin_product_merge",
     *     path="/product/merge/",
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function mergeProductsAction(Request $request)
    {
        $url = sprintf('/api/v1/baseProducts/%d/', $request->request->get('recipientId'));
        $body = [
            'mergeIds' => [$request->request->get('sourceId')],
        ];
        $this->get('user.api.client')->patch($url, [], $body);

        return $this->json([]);
    }

    /**
     * @VIA\Route(
     *     name="admin_product_move",
     *     path="/product/{id}/move/",
     *     methods={"GET", "POST"},
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function moveProductAction(int $id, Request $request)
    {
        $command = new Command\MoveCommand();

        $command->product = $this->getDoctrine()->getManager()->getRepository(BaseProduct::class)->find($id);
        if (!$command->product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $id));
        }

        $form = $this->createForm(Form\MoveFormType::class, $command);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    return $this->json([]);
                } catch (ValidationException $e) {
                    $this->addFormErrors($form, $e->getMessages());
                }
            }

            return $this->json([
                'errors' => $this->getFormErrors($form),
            ]);
        }

        return $this->json([
            'html' => $this->renderView('@Admin/Product/move_form.html.twig', [
                'form' => $form->createView(),
                'command' => $command,
            ]),
        ]);
    }

    /**
     * @VIA\Route(
     *     name="product_set_price",
     *     path="/products/{id}/price/",
     *     methods={"GET", "POST"},
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function setPriceAction(int $id, Request $request)
    {
        $command = new Command\SetPriceCommand(['id' => $id]);
        $form = $this->createForm(Form\SetPriceFormType::class, $command);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    return $this->json([]);
                } catch (ValidationException $e) {
                    $this->AddFormErrors($form, $e->getAsArray());
                }
            }

            return $this->json([
                'errors' => $this->getFormErrors($form),
            ]);
        }

        return $this->json([
            'html' => $this->renderView('@Admin/Product/set_price_form.html.twig', [
                'form' => $form->createView(),
                'command' => $command,
            ]),
        ]);
    }

    /**
     * @VIA\Post(
     *     name="product_reset_price",
     *     path="/products/{id}/reset/",
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function resetPriceAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\ResetPriceCommand(['id' => $id]));

        return $this->json([]);
    }

    /**
     * @VIA\Get(
     *     name="product_get_price",
     *     path="/products/{id}/get/",
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function getAction(int $id)
    {
        $product = $this->get('query_bus')->handle(new Query\GetQuery(['id' => $id]));

        return $this->json(['product' => $product]);
    }

    /**
     * @VIA\Post(
     *     name="product_photo_set_first",
     *     path="/productPhotos/first/",
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function setFirstImageAction(Request $request)
    {
        $this->get('command_bus')->handle(new Command\SetFirstImageCommand(['id' => $request->request->get('id')]));

        return $this->json([]);
    }
}
