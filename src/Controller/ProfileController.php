<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class ProfileController extends AbstractController
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/profile", name="show_profile")
     */
    public function showProfile(Environment $environment): Response
    {
        $user = $this->getUser();
        if (null === $user || !$user instanceof User) {
            return new Response($this->twig->render('error/unauthorized.html.twig'), Response::HTTP_UNAUTHORIZED);
        }

        return new Response($this->twig->render('profile/show.html.twig'));
    }

}
