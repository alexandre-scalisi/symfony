<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Like;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\LikeRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $articles = $articleRepository->findAll();

        $articles = $paginator->paginate($articles, $request->query->getInt('page', 1), 12);

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $article
                ->setCreatedAt(new DateTime('now'))
                ->setAuthor($this->getUser());

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="article_delete", methods={"POST"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/{id}", name="article_show", methods={"GET", "POST"})
     */
    public function show(Article $article, Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $comment
                ->setCreatedAt(new DateTime('now'))
                ->setAuthor($this->getUser())
                ->setArticle($article);
            $entityManager->persist($comment);


            $entityManager->flush();
        }


        return $this->renderForm('article/show.html.twig', [
            'article' => $article,
            'form' => $form,
            'comment' => $comment
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }


    /**
     * Route AJAX (requête crée dans assets/js/like.js)
     * @Route("/{id}/like/{isLiked<1|0>}", name="article_like", methods={"POST"})
     */
    public function like(Request $request, bool $isLiked, Article $article, LikeRepository $likeRepository)
    {
        $user = $this->getUser();

        if (!$user) return $this->json('', 403);

        $selected = null;
        $like = $likeRepository->findOneBy([
            'article' => $article,
            'liker' => $user
        ]);

        $entityManager = $this->getDoctrine()->getManager();

        if (!$like) {
            $like = new Like();
            $like->setLiker($user)
                ->setArticle($article)
                ->setIsLiked($isLiked);
            $entityManager->persist($like);
            $article->addLike($like);
            $selected = $isLiked;
        } else if ($like && $like->getIsLiked() !== $isLiked) {
            $selected = !$like->getIsLiked();
            $like->setIsLiked($isLiked);
        } else {
            $entityManager->remove($like);
        }

        $entityManager->flush();

        return $this->json([
            'selected' => $selected,
            'avg' => $article->getLikesAverage(),
        ]);
    }
}