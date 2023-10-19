<?php

namespace common\domain;

use DateInterval;
use DateTimeImmutable;
use common\exceptions\AppleException;

class Apple
{
    private string $color;
    private DateTimeImmutable $created_at;
    private ?DateTimeImmutable $dropped_at;
    private int $status;
    private int $size;

    public function __construct(?string $color = null)
    {
        $this->color = $color ?: $this->generateColor();
        $this->created_at = new DateTimeImmutable();
        $this->size = 100;
        $this->status = self::STATUS_ON_TREE;
        $this->dropped_at = null;

        return $this;
    }


    /**
     * Статус "На дереве"
     * по умолчанию
     */
    public const STATUS_ON_TREE = 0;

    /**
     * Статус "На земле"
     */
    public const STATUS_ON_GROUND = 1;

    /**
     * Статус "Удалён"
     */
    public const STATUS_DELETED = 2;

    public static array $statuses = [
        self::STATUS_ON_TREE => 'На дереве',
        self::STATUS_ON_GROUND => 'На земле',
        self::STATUS_DELETED => 'Удалён',
    ];

    /**
     * Формат даты
     *
     * лучше хранить в settings
     * обычно есть какая то надстройка для подобного
     */
    public const DEFAULT_DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Интервал за который яблоко портиться
     */
    public const ROTTEN_INTERVAL = 'PT5H';


    public const ERROR_IS_ROTTEN = 1;
    public const ERROR_ON_TREE = 2;
    public const ERROR_MORE_THEN_100 = 3;
    public const ERROR_ON_GROUND = 4;
    public const ERROR_NOT_DELETE = 5;


    public static function generate(int $count = 5): array
    {
        return array_map(fn() => new Apple(), range(1, $count));
    }

    /**
     * Съесть
     *
     * @param int $percent
     * @return $this
     * @throws AppleException
     */
    public function eat(int $percent): self
    {
        if ($this->status == self::STATUS_ON_TREE) {
            throw new AppleException('Съесть нельзя, яблоко на дереве', self::ERROR_ON_TREE);
        }

        if ($this->isRotten()) {
            throw new AppleException('Яблоко уже гнилое', self::ERROR_IS_ROTTEN);
        }

        if (($this->size - $percent) < 0) {
            throw new AppleException('Нельзя съесть больше 100% яблока', self::ERROR_MORE_THEN_100);
        }

        $this->size -= $percent;

        return $this;
    }

    /**
     * Упасть
     *
     * @return $this
     * @throws AppleException
     */
    public function failToGround(): self
    {
        if ($this->status == self::STATUS_ON_GROUND) {
            throw new AppleException('Яблоко не может упасть оно уже на земле', self::ERROR_ON_GROUND);
        }

        $this->status = self::STATUS_ON_GROUND;
        $this->dropped_at = new DateTimeImmutable();

        return $this;
    }


    /**
     * Удалить
     *
     * @return $this
     * @throws AppleException
     */
    public function del(): self
    {
        if ($this->status == self::STATUS_ON_TREE) {
            throw new AppleException('Не возможно удалить яблоко оно на дереве', self::ERROR_NOT_DELETE);
        }
        //$this->deleted_at = (new DateTime('now'))->format(self::DEFAULT_DATE_TIME_FORMAT);
        $this->status = self::STATUS_DELETED;

        return $this;
    }


    /**
     * Уже протухло?
     *
     * @return bool
     */
    public function isRotten(): bool
    {
        $dropped_at = $this->dropped_at;
        return is_a($dropped_at, \DateTimeInterface::class) && $dropped_at->add(new DateInterval(self::ROTTEN_INTERVAL)) <= new DateTimeImmutable();
    }

    public function setColor(string $color): Apple
    {
        $this->color = $color;
        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    private function generateColor(): string
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    public function setCreatedAtFromString(string $created_at): self
    {
        $this->created_at = DateTimeImmutable::createFromFormat(self::DEFAULT_DATE_TIME_FORMAT, $created_at);
        return $this;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at->format(self::DEFAULT_DATE_TIME_FORMAT);
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @throws AppleException
     */
    public function setSize(int $size): self
    {
        if ($size > 100 || $size < 0) {
            throw new AppleException('Wrong size');
        }

        $this->size = $size;
        return $this;
    }

    public function getDroppedAt(): ?string
    {
        return $this->dropped_at ? ($this->dropped_at)->format(self::DEFAULT_DATE_TIME_FORMAT) : null;
    }

    public function setDroppedAt(DateTimeImmutable $dropped_at): self
    {
        $this->dropped_at = $dropped_at;
        return $this;
    }

    public function setDroppedAtFromString(?string $dropped_at): self
    {
        if ($dropped_at) {
            $this->dropped_at = DateTimeImmutable::createFromFormat(self::DEFAULT_DATE_TIME_FORMAT, $dropped_at);
        }
        return $this;
    }

}
