<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%attraction}}`.
 */
class m210317_071919_add_balance_column_to_attraction_table extends Migration
{
    public $tableName = "{{%attraction}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'balance', $this->money()->defaultValue(0)->notNull());

        $this->addCommentOnColumn($this->tableName,'balance', 'Цена');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'balance');
    }
}
