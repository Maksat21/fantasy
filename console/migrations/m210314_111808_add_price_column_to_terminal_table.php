<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%terminal}}`.
 */
class m210314_111808_add_price_column_to_terminal_table extends Migration
{
    public $tableName = "{{%terminal}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'price', $this->money()->defaultValue(0)->notNull());

        $this->addCommentOnColumn($this->tableName,'price', 'Цена');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'price');
    }
}
