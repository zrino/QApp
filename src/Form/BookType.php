<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Author;
use App\Entity\User;
use App\Repository\QApiRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Security;

final class BookType extends AbstractType
{
    /**
     * @var QApiRepository
     */
    private $apiRepository;
    /**
     * @var Security
     */
    private $security;

    public function __construct(
        QApiRepository $apiRepository,
        Security $security
    )
    {
        $this->apiRepository = $apiRepository;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        if (null === $user || !$user instanceof User) {
            throw new \LogicException('Invalid buildForm call');
        }

        $builder
            ->add('title')
            ->add('releaseDate', DateType::class)
            ->add('updatedAt', DateType::class)
            ->add('description')
            ->add('isbn')
            ->add('format')
            ->add('numberOfpages', IntegerType::class)
            ->add('author', ChoiceType::class, [
                'choices' => $this->apiRepository->fetchAuthorsForUser($user),
                'choice_label' => function(Author $author) {
                    return sprintf("%s %s", $author->getFirstName(), $author->getLastName());
                }
            ])
            ->add('submit', SubmitType::class);
    }
}
