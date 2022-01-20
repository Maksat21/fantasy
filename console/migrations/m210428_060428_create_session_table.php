<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%session}}`.
 */
class m210428_060428_create_session_table extends Migration
{
    public $tableName = '{{%session}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'session_start' => $this->dateTime(),
            'session_end' => $this->dateTime(),
            'transaction_count' => $this->integer(),
            'transaction_amount' => $this->money(),
        ]);


        $this->addCommentOnTable($this->tableName, 'Сессии');
        $this->addCommentOnColumn($this->tableName, 'user_id', 'Пользователь');
        $this->addCommentOnColumn($this->tableName, 'session_start', 'Начало сессии');
        $this->addCommentOnColumn($this->tableName, 'session_end', 'Конец сессии');
        $this->addCommentOnColumn($this->tableName, 'transaction_count', 'Количество транзакций');
        $this->addCommentOnColumn($this->tableName, 'transaction_amount', 'Сумма транзакций');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
