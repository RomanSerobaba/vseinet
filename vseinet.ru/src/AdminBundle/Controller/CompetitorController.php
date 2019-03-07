<?php

namespace AdminBundle\Controller;

use AppBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Exception\ValidationException;
use AppBundle\Annotation as VIA;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductToCompetitor;
use AdminBundle\Bus\Competitor\Query;
use AdminBundle\Bus\Competitor\Command;
use AdminBundle\Bus\Competitor\Form;

/**
 * @Security("is_granted('ROLE_EMPLOYEE')")
 */
class CompetitorController extends Controller
{
    /**
     * @VIA\Get(
     *     name="admin_competitor_revisions",
     *     path="/competitor/revisions/",
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function getRevisionsAction(Request $request)
    {
        return $this->getRevisions($request->query->get('baseProductId'));
    }

    /**
     * @VIA\Route(
     *     name="admin_competitor_revision_new",
     *     path="/competitor/revisions/add/",
     *     methods={"GET", "POST"},
     *     condition="request.isXmlHttpRequest()"
     * )
     * @VIA\Route(
     *     name="admin_competitor_revision_edit",
     *     path="/competitor/revisions/{id}/edit/",
     *     requirements={"id": "\d+"},
     *     methods={"GET", "POST"},
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function addRevisionAction(int $id = 0, Request $request)
    {
        $command = new Command\AddRevisionCommand(['id' => $id]);
        if ($request->isMethod('GET')) {
            $em = $this->getDoctrine()->getManager();
            if ($id) {
                $revision = $em->getRepository(ProductToCompetitor::class)->find($id);
                if (!$revision instanceof ProductToCompetitor) {
                    throw new NotFoundHttpException(sprintf('Товар конкурента %d не найден', $id));
                }
                $command->competitorId = $revision->getCompetitorId();
                $command->baseProductId = $revision->getBaseProductId();
                $command->link = $revision->getLink();
                if (empty($command->link)) {
                    $command->competitorPrice = $revision->getCompetitorPrice();
                }
            } else {
                $product = $em->getRepository(Product::class)->findOneBy([
                    'baseProductId' => $request->query->get('baseProductId'),
                    'geoCityId' => $this->getGeoCity()->getId(),
                ]);
                if (!$product instanceof Product) {
                    throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $request->query->get('baseProductId')));
                }
                $command->baseProductId = $product->getBaseProductId();
            }
        }
        $form = $this->createForm(Form\AddRevisionFormType::class, $command);

        if ($request->ismethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $this->get('command_bus')->handle($command);

                    return $this->getRevisions($command->baseProductId);
                } catch (ValidationException $e) {
                    $this->AddFormErrors($e->getAsArray());
                }
            }

            return $this->json([
                'errors' => $this->getFormErrors($form),
            ]);
        }

        return $this->json([
            'html' => $this->renderView('@Admin/Competitor/add_revision_form.html.twig', [
                'form' => $form->createView(),
                'command' => $command,
            ]),
        ]);
    }

    /**
     * @VIA\Post(
     *     name="admin_competitor_revision_delete",
     *     path="/competitorRevisions/{id}/delete/",
     *     requirements={"id": "\d+"},
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function deleteAction(int $id, Request $request)
    {
        $this->get('command_bus')->handle(new Command\DeleteRevisionCommand(['id' => $id]));

        return $this->json([]);
    }

    /**
     * @VIA\Post(
     *     name="admin_competitor_revision_request",
     *     path="/competitorRevisions/{id}/request/",
     *     requirements={"id": "\d+"},
     *     condition="request.isXmlHttpRequest()"
     * )
     */
    public function requestAction(int $id)
    {
        $this->get('command_bus')->handle(new Command\RequestCommand(['id' => $id]));

        return $this->json([]);
    }

    /**
     * @param int $baseProductId
     */
    protected function getRevisions(int $baseProductId)
    {
        $revisions = $this->get('query_bus')->handle(new Query\GetRevisionsQuery(['baseProductId' => $baseProductId]));
        $competitors = $this->getDoctrine()->getManager()->getRepository(Competitor::class)->getActive();

        return $this->json([
            'html' => $this->renderView('@Admin/Competitor/revisions.html.twig', [
                'revisions' => $revisions,
                'competitors' => $competitors,
            ]),
        ]);
    }
}
