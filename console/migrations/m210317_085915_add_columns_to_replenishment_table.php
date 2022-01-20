<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%replenishment}}`.
 */
class m210317_085915_add_columns_to_replenishment_table extends Migration
{
    public $tableName = '{{%replenishment}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'payment_method', $this->integer());
        $this->addColumn($this->tableName, 'discount_id', $this->integer());
        $this->addColumn($this->tableName, 'discount_amount', $this->money()->null());

        $this->addCommentOnColumn($this->tableName, 'payment_method', 'Способ оплаты');
        $this->addCommentOnColumn($this->tableName, 'discount_id', 'Идентификатор скидки');
        $this->addCommentOnColumn($this->tableName, 'discount_amount', 'Общая сумма после скидки');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'payment_method');
        $this->dropColumn($this->tableName, 'discount_id');
        $this->dropColumn($this->tableName, 'discount_amount');
    }
}
