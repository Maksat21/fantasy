<?php

use yii\db\Migration;

/**
 * Class m210504_061113_add_session_id_to_replenishment_table
 */
class m210504_061113_add_session_id_to_replenishment_table extends Migration
{
    public $tableName = "{{%replenishment}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'session_id', $this->integer());

        $this->addCommentOnColumn($this->tableName, 'session_id', 'Идентификатор сессии');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'session_id');
    }
}
