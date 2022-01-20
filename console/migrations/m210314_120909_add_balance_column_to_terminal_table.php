<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%terminal}}`.
 */
class m210314_120909_add_balance_column_to_terminal_table extends Migration
{
    public $tableName = "{{%terminal}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'balance', $this->money()->defaultValue(0)->notNull());

        $this->addCommentOnColumn($this->tableName,'balance', 'Баланс');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'balance');
    }
}
