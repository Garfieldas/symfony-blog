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
            if($newArticle->getImagePath() === null || $newArticle->getCoverPath() === null){
                return new Response('Please upload an image file!');
            }
            $this->manager->persist($newArticle);
            $this->manager->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('pages/create-article.html.twig',
        ['form' => $form->createView()]);
    }
    #[Route('/update/{id}', name: 'update-article')]
    public function update(Request $request, $id): Response
    {
        $article = $this->articleRepository->find($id);
        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagePath = $form->get('imagePath')->getData();
            $coverPath = $form->get('coverPath')->getData();

            // Handling new image upload
            if ($imagePath) {
                $imageFullPath = $this->getParameter('kernel.project_dir') . '/public' . $article->getImagePath();

                if ($article->getImagePath() && file_exists($imageFullPath)) {
                    unlink($imageFullPath); // Delete old file
                }

                $newFileName = uniqid() . '.' . $imagePath->guessExtension();
                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
                $article->setImagePath('/uploads/' . $newFileName);
            }

            // Handle new cover image
            if ($coverPath) {
                $coverFullPath = $this->getParameter('kernel.project_dir') . '/public' . $article->getCoverPath();

                if ($article->getCoverPath() && file_exists($coverFullPath)) {
                    unlink($coverFullPath); // Delete cover image
                }

                $newFileName2 = uniqid() . '.' . $coverPath->guessExtension();
                try {
                    $coverPath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName2
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
                $article->setCoverPath('/uploads/' . $newFileName2);
            }

            // Upload other fields
            $article->setTitle($form->get('title')->getData());
            $article->setParagprah($form->get('paragprah')->getData());
            $article->setAuthor($form->get('author')->getData());

            // Save changes
            $this->manager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('pages/update-article.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/delete/{id}', name: 'delete-article')]
    public function delete($id): Response
    {
        $article = $this->articleRepository->find($id);
        $imageFullPath = $this->getParameter('kernel.project_dir') . '/public' . $article->getImagePath();
        $coverFullPath = $this->getParameter('kernel.project_dir') . '/public' . $article->getCoverPath();
        unlink($imageFullPath);
        unlink($coverFullPath);
        $this->manager->remove($article);
        $this->manager->flush();
        return $this->redirectToRoute('home');
    }
}