<?php

use yii\db\Migration;

/**
 * Class m231010_231314_add_apples
 */
class m231010_231314_add_apples extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('model_apples', [
            'id' => $this->primaryKey(),
            'color' => $this->text()->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('NOW()'),
            'deleted_at' => $this->dateTime(),
            'dropped_at' => $this->dateTime(),
            'status' => $this->boolean()->notNull()->defaultValue(0),
            'size' => $this->smallInteger()->notNull()->defaultValue(100),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231010_112612_add_apples cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231010_112612_add_apples cannot be reverted.\n";

        return false;
    }
    */
}
