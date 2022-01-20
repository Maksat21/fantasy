<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transfer}}`.
 */
class m210331_053400_create_transfer_table extends Migration
{
    public $tableName = '{{%transfer}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'card_from_id' => $this->integer()->notNull(),
            'card_to_id' => $this->integer()->notNull(),
            'amount' => $this->money()->defaultValue(0)->notNull(),
            'user_id' => $this->integer(),
            'reason' => $this->integer()->notNull(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
