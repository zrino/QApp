<?php

declare(strict_types=1);

namespace App\Entity;

final class Author
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var \DateTime
     */
    private $birthday;

    /**
     * @var string
     */
    private $biography;

    /**
     * @var string
     */
    private $gender;

    /**
     * @var string
     */
    private $placeOfBirth;

    /**
     * @var Book[]
     */
    private $books = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Author
    {
        $this->id = $id;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): Author
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): Author
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getBirthday(): \DateTime
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTime $birthday): Author
    {
        $this->birthday = $birthday;
        return $this;
    }

    public function getBiography(): string
    {
        return $this->biography;
    }

    public function setBiography(string $biography): Author
    {
        $this->biography = $biography;
        return $this;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(string $gender): Author
    {
        $this->gender = $gender;
        return $this;
    }

    public function getPlaceOfBirth(): string
    {
        return $this->placeOfBirth;
    }

    public function setPlaceOfBirth(string $placeOfBirth): Author
    {
        $this->placeOfBirth = $placeOfBirth;
        return $this;
    }

    public function getBooks(): array
    {
        return $this->books;
    }

    public function setBooks(array $books): void
    {
        $this->books = $books;
    }


}
