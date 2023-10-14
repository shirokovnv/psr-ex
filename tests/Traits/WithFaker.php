<?php

declare(strict_types=1);

namespace Shirokovnv\PsrEx\Tests\Traits;

use Faker\Factory;
use Faker\Generator;

trait WithFaker
{
    /**
     * @var Generator
     */
    protected Generator $faker;

    /**
     * @return void
     */
    public function setUpFaker(): void
    {
        $this->faker = Factory::create();
    }

    /**
     * @return void
     */
    public function tearDownFaker(): void
    {
        if (isset($this->faker)) {
            unset($this->faker);
        }
    }
}
