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
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
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
     * @Route("/{id}/like", name="article_like", methods={"POST"})
     */

    public function like(Request $request, Article $article, LikeRepository $likeRepository): Response
    {
        $user = $this->getUser();
        
        if(!$user) return $this->json('', 403);
        
        
        if($article->getIsLikedByUser($user) !== null) {
            $like = $likeRepository->findOneBy([
                'article' => $article,
                'liker' => $user
            ]);

            if($like->getIsLiked() === false && $request->getContent() === "false" || $like->getIsLiked() === true && $request->getContent() === "true") 
                return $this->unclick($like, $article);

            return $this->changedMyMind($like, $request, $article);

        } else return $this->newVote($user, $article, $request);
        
    }

    private function unclick($like, $article) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($like);
        $entityManager->flush();
        $avg = $article->getLikesAverage();
        return $this->json([
            'message' => 'Vous avez bien désélectionné',
            'selected' => 'none',
            'avg' => $avg
        ]);
    }

    private function changedMyMind($like, $request, $article) {
        $entityManager = $this->getDoctrine()->getManager();
        $like->setIsLiked($request->getContent() === "true" ? true : false);
            $entityManager->flush();
            $avg = $article->getLikesAverage();
            return $this->json([
                'message' => 'Vous avez bien changé d\'avis',
                'selected' => $like->getIsLiked(),
                'avg' => $avg
            ]);
    }

    private function newVote($user, $article, $request) {
        $entityManager = $this->getDoctrine()->getManager();
        $like = new Like();
        $like->setLiker($user)
            ->setArticle($article)
            ->setIsLiked($request->getContent() === "true" ? true : false);
        $entityManager->persist($like);
        $article->addLike($like);
        $entityManager->flush();
        $avg = $article->getLikesAverage();
        return $this->json([
            'message' => "Vous avez bien selectionné",
            'selected' => $like->getIsLiked(),
            'avg' => $avg
        ]);
    }

    
}
