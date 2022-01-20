<?php

use yii\db\Migration;

/**
 * Class m210317_044624_add_role_to_terminal_table
 */
class m210317_044624_add_role_to_terminal_table extends Migration
{
    public $tableName = "{{%terminal}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'role', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'role');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210317_044624_add_role_to_terminal_table cannot be reverted.\n";

        return false;
    }
    */
}
