<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\QApiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class AuthorController extends AbstractController
{
    /**
     * @var QApiRepository
     */
    private $apiRepository;
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        QApiRepository $apiRepository,
        Environment $twig
    )
    {
        $this->apiRepository = $apiRepository;
        $this->twig = $twig;
    }

    /**
     * @Route("/authors", name="authors_list")
     */
    public function showAuthorsList(): Response
    {
        $user = $this->getUser();
        if (null === $user || !$user instanceof User) {
            return new Response($this->twig->render('error/unauthorized.html.twig'), Response::HTTP_UNAUTHORIZED);
        }

        $authors = $this->apiRepository->fetchAuthorsForUser($user);
        return new Response($this->twig->render('author/list.html.twig', ['authors' => $authors]));
    }

    /**
     * @Route("/authors/{authorId}", name="author_detail_view")
     */
    public function showAuthorDetailView($authorId): Response
    {
        $user = $this->getUser();
        if (null === $user || !$user instanceof User) {
            return new Response($this->twig->render('error/unauthorized.html.twig'), Response::HTTP_UNAUTHORIZED);
        }

        $authorId = (int) $authorId;
        try {
            $author = $this->apiRepository->fetchSingleAuthorWithGivenIdForUser($authorId, $user);
        } catch (\DomainException $exception) {
            return new Response($this->twig->render('error/notfound.html.twig', ['resource' => 'author', 'id' => $authorId]));
        }
        return new Response($this->twig->render('author/detail_view.html.twig', ['author' => $author]));
    }

    /**
     * @Route("/authors/delete/{authorId}", name="delete_author")
     */
    public function deleteAuthor($authorId): Response
    {
        $user = $this->getUser();
        if (null === $user || !$user instanceof User) {
            return new Response($this->twig->render('error/unauthorized.html.twig'), Response::HTTP_UNAUTHORIZED);
        }

        $authorId = (int) $authorId;
        try {
            $this->apiRepository->deleteAuthorWithGivenIdForUser($authorId, $user);
        } catch (\DomainException $exception) {
            return new Response($this->twig->render('error/notfound.html.twig', ['resource' => 'author', 'id' => $authorId]));
        }

        return new Response(sprintf('Succesfully deleted author with id %d',  $authorId));
    }

}
