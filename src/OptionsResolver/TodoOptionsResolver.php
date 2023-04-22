<?php

namespace App\OptionsResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

class TodoOptionsResolver extends OptionsResolver
{
    public function __construct()
    {
        $this
            ->setRequired("title")
            ->setAllowedTypes("title", "string");
    }

    public function configureCompleted()
    {
        $this
            ->setRequired("completed")
            ->setAllowedTypes("completed", "bool");
    }
}
