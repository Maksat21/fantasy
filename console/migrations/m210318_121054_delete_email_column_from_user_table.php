<?php

use yii\db\Migration;

/**
 * Class m210318_121054_delete_email_column_from_user_table
 */
class m210318_121054_delete_email_column_from_user_table extends Migration
{
    public $tableName = '{{%user}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn($this->tableName, 'email');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn($this->tableName, 'email', $this->string()->notNull()->unique());
    }
}
