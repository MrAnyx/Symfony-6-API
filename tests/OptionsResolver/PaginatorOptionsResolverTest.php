<?php

namespace App\Tests\OptionsResolver;

use App\OptionsResolver\PaginatorOptionsResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class PaginatorOptionsResolverTest extends TestCase
{
    private PaginatorOptionsResolver $optionsResolver;

    public function setUp(): void
    {
        $this->optionsResolver = new PaginatorOptionsResolver();
    }

    public function testValidPage(): void
    {
        $params = [
            "page" => "2"
        ];

        $result = $this->optionsResolver
            ->configurePage()
            ->resolve($params);

        $this->assertEquals(2, $result["page"]);
    }

    public function testNegativePage(): void
    {
        $params = [
            "page" => "-2"
        ];

        $this->expectException(InvalidOptionsException::class);

        $this->optionsResolver
            ->configurePage()
            ->resolve($params);
    }

    public function testDefaultPage()
    {
        $params = [];

        $result = $this->optionsResolver
            ->configurePage()
            ->resolve($params);

        $this->assertEquals(1, $result["page"]);
    }

    public function testStringPage()
    {
        $params = [
            "page" => "Hello World!"
        ];

        $this->expectException(InvalidOptionsException::class);

        $this->optionsResolver
            ->configurePage()
            ->resolve($params);
    }
}
