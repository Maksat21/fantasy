<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%transaction}}`.
 */
class m210428_082331_add_session_id_column_to_transaction_table extends Migration
{
    public $tableName = "{{%transaction}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'session_id', $this->integer());

        $this->addCommentOnColumn($this->tableName, 'session_id', 'Идентификатор сессии');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'session_id');
    }
}
