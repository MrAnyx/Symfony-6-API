<?php

namespace App\Tests\Entity;

use App\Entity\Todo;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TodoTest extends KernelTestCase
{
    private EntityManager $em;
    private ValidatorInterface  $validator;

    protected function setUp(): void
    {
        $this->em = self::getContainer()->get('doctrine')->getManager();
        $this->validator = self::getContainer()->get("validator");
    }

    public function testDefaultValues(): void
    {
        $todo = new Todo();

        // Test default values
        $this->assertNull($todo->getId());
        $this->assertNull($todo->getTitle());
        $this->assertNull($todo->getCreatedAt());
        $this->assertNull($todo->getUpdatedAt());
        $this->assertFalse($todo->isCompleted());
    }

    public function testTitle()
    {
        $todo = new Todo();

        // Test entity constraints
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($todo, "title");
        $this->assertInstanceOf(NotBlank::class, $errors[0]->getConstraint());

        $todo->setTitle("Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas");
        /** @var ConstraintViolation[] $errors */
        $errors = $this->validator->validateProperty($todo, "title");
        $this->assertInstanceOf(Length::class, $errors[0]->getConstraint());

        // Test the title setter and getter methods
        $title = 'Test Todo';
        $todo->setTitle($title);
        $this->assertEquals($title, $todo->getTitle());
    }

    public function testCompleted()
    {
        $todo = new Todo();

        // Test the completed setter and getter methods
        $todo->setCompleted(true);
        $this->assertTrue($todo->isCompleted());
    }

    public function testDoctrineEvents()
    {
        $todo = new Todo();

        // Persist the entity (not flush) in order to generate the createdAt and updatedAt fields
        $this->em->persist($todo);

        // Test the createdAt and updatedAt setter and getter methods
        $this->assertInstanceOf(DateTimeImmutable::class, $todo->getCreatedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $todo->getUpdatedAt());

        // Detch the entity to prevent tracking unused entity
        $this->em->detach($todo);
    }
}
