<?php

namespace common\models;

use DateInterval;
use DateTime;
use Yii;
use yii\db\ActiveRecord;
use \Exception;

/**
 * This is the model class for table "model_apples".
 *
 * @property int $id
 * @property string $color
 * @property string $created_at
 * @property string|null $deleted_at
 * @property string|null $dropped_at
 * @property int $status
 * @property int $size
 */
class Apple extends ActiveRecord
{
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


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'model_apples';
    }


    public static function generate()
    {
        foreach (range(1, rand(1, 5)) as $item) {
            $model = new Apple();
            $model->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['color', 'string'],
            [['created_at', 'deleted_at', 'dropped_at'], 'date', 'format' => 'php:' . self::DEFAULT_DATE_TIME_FORMAT],
            [['status', 'size'], 'integer'],
            [['status'], 'in', 'range' => array_keys(self::$statuses)],

            ['status', 'default', 'value' => 'hanging'],
            ['size', 'default', 'value' => 100],
            ['color', 'default', 'value' => sprintf('#%06X', mt_rand(0, 0xFFFFFF))],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'color' => Yii::t('app', 'Цвет'),
            'created_at' => Yii::t('app', 'Создан в'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
            'dropped_at' => Yii::t('app', 'Упал в'),
            'status' => Yii::t('app', 'Статус'),
            'size' => Yii::t('app', 'Осталось в %'),
        ];
    }

    /**
     * Съесть
     *
     * @param int $percent
     * @return $this
     * @throws Exception
     */
    public function eat(int $percent): self
    {
        if ($this->status == self::STATUS_ON_TREE) {
            throw new Exception('Съесть нельзя, яблоко на дереве', self::ERROR_ON_TREE);
        }

        if ($this->isRotten()) {
            throw new Exception('Яблоко уже гнилое', self::ERROR_IS_ROTTEN);
        }

        if (($this->size - $percent) < 0) {
            throw new Exception('Нельзя съесть больше 100% яблока', self::ERROR_MORE_THEN_100);
        }

        $this->size -= $percent;

        $this->save();
        return $this;
    }

    /**
     * Упасть
     *
     * @return $this
     * @throws Exception
     */
    public function failToGround(): self
    {
        if ($this->status == self::STATUS_ON_GROUND) {
            throw new Exception('Яблоко не может упасть оно уже на земле', self::ERROR_ON_GROUND);
        }

        $this->status = self::STATUS_ON_GROUND;
        $this->dropped_at = (new DateTime('now'))->format(self::DEFAULT_DATE_TIME_FORMAT);
        $this->save();
        return $this;
    }


    /**
     * Удалить
     *
     * @return $this
     * @throws Exception
     */
    public function del(): self
    {
        if ($this->status == self::STATUS_ON_TREE) {
            throw new Exception('Не возможно удалить яблоко оно на дереве', self::ERROR_NOT_DELETE);
        }
        $this->deleted_at = (new DateTime('now'))->format(self::DEFAULT_DATE_TIME_FORMAT);
        $this->status = self::STATUS_DELETED;

        return $this;
    }


    /**
     * Уже протухло?
     *
     * @return bool
     * @throws \Exception
     */
    private function isRotten(): bool
    {
        $dropped = (new DateTime($this->dropped_at));
        $rotten_interval = new DateInterval(self::ROTTEN_INTERVAL);

        return $dropped->add($rotten_interval) <= new DateTime();
    }
}
