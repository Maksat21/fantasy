<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%card}}`.
 */
class m210318_093149_add_status_column_to_card_table extends Migration
{
    public $tableName = "{{%card}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'status', $this->smallInteger()->defaultValue(0));

        $this->addCommentOnColumn($this->tableName, 'status', 'Статус');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'status');
    }
}
