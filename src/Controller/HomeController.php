<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Repository\AuthorRepository;

class HomeController extends AbstractController
{

    private $articleRepository;
    private $authorRepository;
    public function __construct(ArticleRepository $articleRepository, AuthorRepository $authorRepository){
        $this->articleRepository = $articleRepository;
        $this->authorRepository = $authorRepository;
    }
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        $articles = $this->articleRepository->findAll();
        return $this->render('pages/home.html.twig',
        ['articles' => $articles]);
    }
    #[Route('/article/{id}', name: 'article')]
    public function article($id): Response
    {
        $article = $this->articleRepository->find($id);
        $authorId = $article->getAuthor();
        $author = $this->authorRepository->find($authorId);

        return $this->render('pages/article.html.twig',
        ['article' => $article, 'author' => $author]);
    }

}