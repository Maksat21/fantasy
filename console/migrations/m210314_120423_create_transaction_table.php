<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transaction}}`.
 */
class m210314_120423_create_transaction_table extends Migration
{
    public $tableName = "{{%transaction}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'terminal_id' => $this->integer(),
            'card_id' => $this->integer(),
            'amount' => $this->money()->defaultValue(0)->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey('fk-transaction-terminal_id-to-terminal',$this->tableName,'terminal_id','{{%terminal}}','id','SET NULL','RESTRICT');
        $this->addForeignKey('fk-transaction-card_id-to-card',$this->tableName,'card_id','{{%card}}','id','SET NULL','RESTRICT');

        $this->addCommentOnTable($this->tableName, 'Транзакции');
        $this->addCommentOnColumn($this->tableName,'terminal_id', 'Терминал');
        $this->addCommentOnColumn($this->tableName,'card_id', 'Карта');
        $this->addCommentOnColumn($this->tableName,'amount', 'Цена');
        $this->addCommentOnColumn($this->tableName,'created_at', 'Создано');
        $this->addCommentOnColumn($this->tableName,'updated_at', 'Обновлено');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
