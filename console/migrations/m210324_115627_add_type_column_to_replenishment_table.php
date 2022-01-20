<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%replenishment}}`.
 */
class m210324_115627_add_type_column_to_replenishment_table extends Migration
{
    public $tableName = "{{%replenishment}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'type', $this->text()->defaultValue(0));

        $this->addCommentOnColumn($this->tableName, 'type', 'Тип пополнения');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'type');
    }
}
