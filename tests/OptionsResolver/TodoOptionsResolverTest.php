<?php

namespace App\Tests\OptionsResolver;

use App\OptionsResolver\TodoOptionsResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class TodoOptionsResolverTest extends TestCase
{
    private TodoOptionsResolver $optionsResolver;

    public function setUp(): void
    {
        $this->optionsResolver = new TodoOptionsResolver();
    }

    public function testRequiredTitle()
    {
        $params = [];

        $this->expectException(MissingOptionsException::class);

        $this->optionsResolver
            ->configureTitle(true)
            ->resolve($params);
    }

    public function testValidTitle()
    {
        $params = [
            "title" => "My Title"
        ];

        $result = $this->optionsResolver
            ->configureTitle(true)
            ->resolve($params);

        $this->assertEquals("My Title", $result["title"]);
    }

    public function testInvalidTitle()
    {
        $params = [
            "title" => 3
        ];

        $this->expectException(InvalidOptionsException::class);

        $this->optionsResolver
            ->configureTitle(true)
            ->resolve($params);
    }

    public function testRequiredCompleted()
    {
        $params = [];

        $this->expectException(MissingOptionsException::class);

        $this->optionsResolver
            ->configureCompleted(true)
            ->resolve($params);
    }

    public function testValidCompleted()
    {
        $params = [
            "completed" => true
        ];

        $result = $this->optionsResolver
            ->configureCompleted(true)
            ->resolve($params);

        $this->assertEquals(true, $result["completed"]);
    }

    public function testInvalidCompleted()
    {
        $params = [
            "completed" => "Hello World!"
        ];

        $this->expectException(InvalidOptionsException::class);

        $this->optionsResolver
            ->configureCompleted(true)
            ->resolve($params);
    }
}
