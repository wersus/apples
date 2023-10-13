<?php

namespace common\tests\unit\models;

use Codeception\Test\Unit;
use common\domain\Apple;

class AppleTest extends Unit
{
    public function eatRottenTest(): void
    {
        try {

            (new Apple())
                ->failToGround()
                ->setDroppedAt(\DateTimeImmutable::createFromFormat('Y-m-d', '2020-01-01'))
                ->eat(10);

        } catch (\Throwable $throwable) {
            $this->assertEquals(Apple::ERROR_IS_ROTTEN, $throwable->getCode());
        }
    }

    public function eatDropedTest(): void
    {
        try {

            (new Apple())
                ->failToGround()
                ->failToGround()
                ->eat(10);

        } catch (\Throwable $throwable) {
            $this->assertEquals(Apple::ERROR_ON_GROUND, $throwable->getCode());
        }
    }

    public function eatOnTreeTest(): void
    {
        try {

            (new Apple())
                ->eat(10);

        } catch (\Throwable $throwable) {
            $this->assertEquals(Apple::ERROR_ON_TREE, $throwable->getCode());
        }
    }

    public function eatMoreThen100Test(): void
    {
        try {

            (new Apple())
                ->eat(50)
                ->eat(51);

        } catch (\Throwable $throwable) {
            $this->assertEquals(Apple::ERROR_MORE_THEN_100, $throwable->getCode());
        }
    }
}