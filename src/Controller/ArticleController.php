<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;

/**
 * Article controller.
 * @Route("/api", name="api_")
 */
class ArticleController extends AbstractFOSRestController
{
    /**
     * @var object
     */
    private $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * Lists all Articles.
     * @Rest\Get("/articles")
     *
     * @return Response
     */
    public function getArticleAction(Request $request)
    {
        $offset = $request->query->get('offset', 0);
        $limit = $request->query->get('limit', 10);

        $articles = $this->articleRepository->getAllArticles($offset, $limit);
        $view = $this->view($articles, Response::HTTP_OK , []);
        
        return $this->handleView($view);
    }

    /**
     * Create Aerticle.
     * @Rest\Post("/article")
     *
     * @return Response
     */
    public function postArticleAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
            if ($form->isSubmitted() && $form->isValid()) {
              $em = $this->getDoctrine()->getManager();
              $em->persist($article);
              $em->flush();
              return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
            }
        
        return $this->handleView($this->view($form->getErrors()));
    }
}
