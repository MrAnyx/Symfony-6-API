<?php

namespace App\Controller;

use App\Repository\TodoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api", "api_")]
class TodoController extends AbstractController
{
    #[Route('/todos', name: 'todos', methods: ["GET"])]
    public function index(TodoRepository $todoRepository): JsonResponse
    {
        $todos = $todoRepository->findAll();

        return $this->json($todos);
    }
}
