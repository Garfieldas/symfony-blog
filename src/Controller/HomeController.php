<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Repository\AuthorRepository;
use App\Form\ArticleFormType;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{

    private $articleRepository;
    private $authorRepository;

    private $manager;
    public function __construct(ArticleRepository $articleRepository, AuthorRepository $authorRepository,
    EntityManagerInterface $manager){
        $this->articleRepository = $articleRepository;
        $this->authorRepository = $authorRepository;
        $this->manager = $manager;
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

    #[Route('/create', name: 'create-article')]
    public function create(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleFormType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newArticle = $form->getData();
            $imagePath = $form->get('imagePath')->getData();
            $coverPath = $form->get('coverPath')->getData();
            if ($imagePath && $coverPath) {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();
                $newFileName2 = uniqid() . '.' . $coverPath->guessExtension();

                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                    $coverPath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName2
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
                $newArticle->setImagePath('/uploads/' . $newFileName);
                $newArticle->setCoverPath('/uploads/' . $newFileName2);
            }
            $this->manager->persist($newArticle);
            $this->manager->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('pages/create-article.html.twig',
        ['form' => $form->createView()]);
    }

}