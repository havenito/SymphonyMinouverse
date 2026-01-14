<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->redirectToRoute('main');
    }

    #[Route('/main', name: 'main')]
    public function index(PostRepository $postRepository, CategoryRepository $categoryRepository): Response
    {
        $posts = $postRepository->findBy([], ['publishedAt' => 'DESC'], 5);
        $categories = $categoryRepository->findAll();

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }
}