<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%card}}`.
 */
class m210314_061925_create_card_table extends Migration
{
    public $tableName = "{{%card}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'code' => $this->string()->unique()->notNull(),
            'balance' => $this->money()->defaultValue(0)->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addCommentOnTable($this->tableName, 'Карты');
        $this->addCommentOnColumn($this->tableName,'code', 'Код карты');
        $this->addCommentOnColumn($this->tableName,'balance', 'Баланс');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
