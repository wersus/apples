<?php

namespace common\models;

use common\domain\Apple as AppleDomain;
use common\exceptions\AppleException;
use DateInterval;
use DateTime;
use Throwable;
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
    private ?AppleDomain $domain;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'model_apples';
    }

    public static function generate(): void
    {
        foreach (AppleDomain::generate() as $apple) {
            /** @var AppleDomain $apple */
            $model = new self();
            self::toModel($model, $apple);
            $model->save();
        }
    }

    private static function toModel(Apple $model, AppleDomain $apple): Apple
    {
        $model->attributes = [
            'color' => $apple->getColor(),
            'created_at' => $apple->getCreatedAt(),
            'dropped_at' => $apple->getDroppedAt(),
            'status' => $apple->getStatus(),
            'size' => $apple->getSize(),
        ];
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['color', 'string'],
            [['created_at', 'dropped_at'], 'date', 'format' => 'php:' . AppleDomain::DEFAULT_DATE_TIME_FORMAT],
            [['status', 'size'], 'integer'],
            [['status'], 'in', 'range' => array_keys(AppleDomain::$statuses)],
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

    public function afterFind()
    {
        try {
            $this->domain = (new AppleDomain($this->color))
                ->setCreatedAtFromString($this->created_at)
                ->setDroppedAtFromString($this->dropped_at)
                ->setSize($this->size)
                ->setStatus($this->status);
        } catch (AppleException $e) {
            throw new Exception($e->getMessage());
        } catch (Throwable $e) {
            throw new Exception('Something wrong');
        }

        parent::afterFind();
    }

    public function failToGround(): static
    {
        try {
            $this->domain->failToGround();
        } catch (AppleException $e) {
            throw new Exception($e->getMessage());
        } catch (Throwable $e) {
            throw new Exception('Something wrong');
        }
        self::toModel($this, $this->domain);
        return $this;
    }

    public function eat(int $percent): static
    {
        try {
        $this->domain->eat($percent);
        } catch (AppleException $e) {
            throw new Exception($e->getMessage());
        } catch (Throwable $e) {
            throw new Exception('Something wrong');
        }
        self::toModel($this, $this->domain);
        return $this;
    }
}
