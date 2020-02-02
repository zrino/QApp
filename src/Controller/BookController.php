<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Form\BookType;
use App\Repository\QApiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class BookController extends AbstractController
{

    /**
     * @var QApiRepository
     */
    private $apiRepository;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        QApiRepository $apiRepository,
        Environment $twig,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->apiRepository = $apiRepository;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/books/delete/{bookId}", name="delete_book")
     */
    public function deleteBook($bookId): Response
    {
        $user = $this->getUser();
        if (null === $user || !$user instanceof User) {
            return new Response($this->twig->render('error/unauthorized.html.twig'), Response::HTTP_UNAUTHORIZED);
        }

        $bookId = (int) $bookId;
        try {
            $this->apiRepository->deleteBook($bookId, $user);
        } catch (\DomainException $exception) {
            return new Response($this->twig->render('error/notfound.html.twig', ['resource' => 'book', 'id' => $bookId]));
        }

        return new Response($this->twig->render('book/successfully_deleted.html.twig', ['id' => $bookId]));
    }

    /**
     * @Route("/books/add", name="create_book")
     */
    public function createNewBook(Request $request): Response
    {
        $user = $this->getUser();
        if (null === $user || !$user instanceof User) {
            return new Response($this->twig->render('error/unauthorized.html.twig'), Response::HTTP_UNAUTHORIZED);
        }
        $book = new Book();
        $form = $this->formFactory->create(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->apiRepository->createBookForGivenUser($book, $user);
            } catch (\DomainException $exception) {

                return new Response($this->twig->render('error/could_not_create.html.twig'), Response::HTTP_BAD_REQUEST);
            }
            return new RedirectResponse($this->urlGenerator->generate("author_detail_view", ['authorId' => $book->getAuthor()->getId()]));
        }

        return new Response($this->twig->render('book/create.html.twig', ['bookForm' => $form->createView()]));
    }
}
