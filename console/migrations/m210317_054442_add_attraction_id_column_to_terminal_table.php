<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%terminal}}`.
 */
class m210317_054442_add_attraction_id_column_to_terminal_table extends Migration
{
    public $tableName = '{{%terminal}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'attraction_id', $this->integer());

        $this->addCommentOnColumn($this->tableName,'attraction_id', 'Идентификатор аттракциона');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'attraction_id');
    }
}
