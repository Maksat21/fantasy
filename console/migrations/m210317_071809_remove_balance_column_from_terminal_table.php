<?php

use yii\db\Migration;

/**
 * Class m210317_071809_remove_balance_column_from_terminal_table
 */
class m210317_071809_remove_balance_column_from_terminal_table extends Migration
{
    public $tableName = "{{%terminal}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn($this->tableName, 'balance');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn($this->tableName, 'balance', $this->money()->defaultValue(0)->notNull());

        $this->addCommentOnColumn($this->tableName,'balance', 'Баланс');
    }
}
