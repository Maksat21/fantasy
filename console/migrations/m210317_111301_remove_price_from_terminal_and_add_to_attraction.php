<?php

use yii\db\Migration;

/**
 * Class m210317_111301_remove_price_from_terminal_and_add_to_attraction
 */
class m210317_111301_remove_price_from_terminal_and_add_to_attraction extends Migration
{
    public $tableName = "{{%attraction}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn("{{%terminal}}", 'price');

        $this->addColumn($this->tableName, 'price', $this->money()->defaultValue(0)->notNull());

        $this->addCommentOnColumn($this->tableName, 'price', 'Цена');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn("{{%terminal}}", 'price', $this->money()->defaultValue(0)->notNull());

        $this->addCommentOnColumn("{{%terminal}}", 'price', 'Цена');

        $this->dropColumn($this->tableName, 'price');
    }
}
