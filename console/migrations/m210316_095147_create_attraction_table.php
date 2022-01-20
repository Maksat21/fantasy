<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%attraction}}`.
 */
class m210316_095147_create_attraction_table extends Migration
{
    public $tableName = '{{%attraction}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
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
