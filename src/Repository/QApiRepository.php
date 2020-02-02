<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\User;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class QApiRepository
{
    /**
     * @var HttpClientInterface
     */
    private $client;

    public function __construct(
        string $qApiUrl
    )
    {
        $this->client = HttpClient::createForBaseUri($qApiUrl);
    }

    /**
     * @return Author[]
     */
    public function fetchAuthorsForUser(User $user): array
    {
        $response = $this->client->request(
            'GET',
            '/api/authors',
            [
                'headers' => [
                    'Authorization' => sprintf('Bearer %s', $user->getApiToken())
                ]
            ]
        );

        $arrayOfRawAuthors = json_decode($response->getContent(), true);
        $authorCollection = [];
        foreach ($arrayOfRawAuthors as $author) {
            $authorCollection[] = ((new Author())
                ->setId($author["id"])
                ->setFirstName($author['first_name'])
                ->setLastName($author['last_name'])
                ->setPlaceOfBirth($author["place_of_birth"])
                ->setGender($author["gender"])
                ->setBirthday(new \DateTime($author["birthday"]))
            );
        }

        return $authorCollection;
    }

    public function fetchSingleAuthorWithGivenIdForUser(int $id, User $user): Author
    {
        $response = $this->client->request(
            'GET',
            sprintf('/api/authors/%d', $id),
            [
                'headers' => [
                    'Authorization' => sprintf('Bearer %s', $user->getApiToken())
                ]
            ]
        );

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new \DomainException('Author not found');
        }

        $rawAuthor = json_decode($response->getContent(), true);
        $author = (new Author())
            ->setId($rawAuthor["id"])
            ->setFirstName($rawAuthor['first_name'])
            ->setLastName($rawAuthor['last_name'])
            ->setPlaceOfBirth($rawAuthor["place_of_birth"])
            ->setGender($rawAuthor["gender"])
            ->setBirthday(new \DateTime($rawAuthor["birthday"]))
            ->setBiography($rawAuthor['biography']);

        $books = [];
        foreach ($rawAuthor['books'] as $rawBook) {
            $books[] = (new Book())
                ->setId($rawBook['id'])
                ->setTitle($rawBook['title'])
                ->setReleaseDate(new \DateTime($rawBook['release_date']))
                ->setUpdatedAt(new \DateTime($rawBook['updated_at']))
                ->setIsbn($rawBook['isbn'])
                ->setFormat($rawBook['format'])
                ->setNumberOfPages($rawBook['number_of_pages']);
        }

        $author->setBooks($books);
        return $author;
    }

    public function deleteAuthorWithGivenIdForUser(int $authorId, User $user): void
    {
        $response = $this->client->request(
            'DELETE',
            sprintf('/api/authors/%d', $authorId),
            [
                'headers' => [
                    'Authorization' => sprintf('Bearer %s', $user->getApiToken())
                ]
            ]
        );

        if ($response->getStatusCode() !== Response::HTTP_NO_CONTENT) {
            throw new \DomainException('Author not found');
        }
    }

    public function deleteBook(int $bookId, User $user): void
    {
        $response = $this->client->request(
            'DELETE',
            sprintf('/api/books/%d', $bookId),
            [
                'headers' => [
                    'Authorization' => sprintf('Bearer %s', $user->getApiToken())
                ]
            ]
        );

        if ($response->getStatusCode() !== Response::HTTP_NO_CONTENT) {
            throw new \DomainException('Book not found');
        }
    }

    public function createAuthorForUser(Author $author, User $user): void
    {
        $response = $this->client->request(
            'POST',
            '/api/authors',
            [
                'headers' => [
                    'Authorization' => sprintf('Bearer %s', $user->getApiToken()),
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'first_name' => $author->getFirstName(),
                    'last_name' => $author->getLastName(),
                    'birthday' => $author->getBirthday()->format("Y-m-d\TH:i:s.Z\Z"),
                    'biography' => $author->getBiography(),
                    'gender' => $author->getGender(),
                    'place_of_birth' => $author->getPlaceOfBirth(),
                ])
            ]
        );

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new \DomainException('Error during creation of author');
        }
    }

    public function createBookForGivenUser(Book $book, User $user): void
    {
        $body = [
            'author' => [
                'id' => $book->getAuthor()->getId()
            ],
            'title' => $book->getTitle(),
            'release_date' => $book->getReleaseDate()->format("Y-m-d\TH:i:s.Z\Z"),
            'updated_at' => $book->getUpdatedAt()->format("Y-m-d\TH:i:s.Z\Z"),
            'description' => $book->getDescription(),
            'isbn' => $book->getIsbn(),
            'format' => $book->getFormat(),
            'number_of_pages' => $book->getNumberOfPages()
        ];

        $response = $this->client->request(
            'POST',
            '/api/books',
            [
                'headers' => [
                    'Authorization' => sprintf('Bearer %s', $user->getApiToken()),
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($body)
            ]
        );

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            # TODO: fix this
            throw new \DomainException('Error during creation of book');
        }

    }

    public function findUserByEmailAndPassword(string $email, string $password): User
    {
        return $this->getUser($email, $password);
    }

    private function getUser(string $email, string $password): User
    {
        $response = $this->client->request(
            'POST',
            '/api/token',
            [
                'body' => json_encode([
                    'email' => $email,
                    'password' => $password
                ])
            ]
        );

        if ($response->getStatusCode() === 403) {
            throw new \DomainException('Unauthorized');
        }


        $content = json_decode($response->getContent(), true);
        $user = (new User())
            ->setFirstName($content['user']["first_name"])
            ->setLastName($content['user']['last_name'])
            ->setGender($content['user']['gender'])
            ->setEmail($content['user']['email'])
            ->setApiToken($content['token_key']);

        return $user;
    }
}
