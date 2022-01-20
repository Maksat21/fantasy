<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%attraction}}`.
 */
class m210317_112126_add_columns_to_attraction_table extends Migration
{
    public $tableName = "{{%attraction}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'status', $this->smallInteger());
        $this->addColumn($this->tableName, 'created_at', $this->dateTime());
        $this->addColumn($this->tableName, 'updated_at', $this->dateTime());

        $this->addCommentOnColumn($this->tableName, 'status', 'Статус');
        $this->addCommentOnColumn($this->tableName, 'created_at', 'Время создания');
        $this->addCommentOnColumn($this->tableName, 'updated_at', 'Время обновления');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'status');
        $this->dropColumn($this->tableName, 'created_at');
        $this->dropColumn($this->tableName, 'updated_at');
    }
}
