<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%discount}}`.
 */
class m210317_090800_create_discount_table extends Migration
{
    public $tableName = '{{%discount}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'type' => $this->integer()->notNull(),
            'quantity' => $this->integer()->notNull()
        ]);

        $this->addCommentOnTable($this->tableName, 'Таблица скидок/бонусов');
        $this->addCommentOnColumn($this->tableName, 'name', 'Название');
        $this->addCommentOnColumn($this->tableName, 'type', 'Тип');
        $this->addCommentOnColumn($this->tableName, 'quantity', 'Количество');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
