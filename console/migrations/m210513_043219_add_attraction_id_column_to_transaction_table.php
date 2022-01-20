<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%transaction}}`.
 */
class m210513_043219_add_attraction_id_column_to_transaction_table extends Migration
{
    public $tableName = 'transaction';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName,'attraction_id',$this->integer());
        $this->addCommentOnColumn($this->tableName,'attraction_id','Аттракион');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName,'attraction_id');
    }
}
