<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Author;
use App\Repository\QApiRepository;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateAuthorCommand extends Command
{
    protected static $defaultName = "create-author";

    /**
     * @var QApiRepository
     */
    private $apiRepository;

    public function __construct(
        QApiRepository $apiRepository
    )
    {
        parent::__construct();
        $this->apiRepository = $apiRepository;
    }

    protected function configure()
    {
        $this->addArgument('firstName', InputArgument::REQUIRED);
        $this->addArgument('lastName', InputArgument::REQUIRED);
        $this->addArgument('birthday', InputArgument::REQUIRED);
        $this->addArgument('biography', InputArgument::REQUIRED);
        $this->addArgument('gender', InputArgument::REQUIRED);
        $this->addArgument('placeOfBirth', InputArgument::REQUIRED);
        $this->addOption('email','em' ,InputOption::VALUE_REQUIRED);
        $this->addOption('password', 'pa',InputOption::VALUE_REQUIRED);
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');
        $birthday = $input->getArgument('birthday');
        $biography = $input->getArgument('biography');
        $gender = $input->getArgument('gender');
        $placeOfBirth = $input->getArgument('placeOfBirth');

        $email = $input->getOption("email");
        $password = $input->getOption("password");

        $user = $this->apiRepository->findUserByEmailAndPassword($email, $password);

        $author = (new Author())
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setBirthday(DateTime::createFromFormat('d-m-Y', $birthday))
            ->setBiography($biography)
            ->setGender($gender)
            ->setPlaceOfBirth($placeOfBirth);

        try {
            $this->apiRepository->createAuthorForUser($author, $user);
        } catch(\DomainException $exception) {
            $output->writeln('Error during creation of author, check your data');
            return 1;
        }
        $output->writeln('Succesfully created author');
        return 0;
    }
}
