<?php

use yii\db\Migration;

/**
 * Class m210319_050956_add_access_token_to_user_table
 */
class m210319_050956_add_access_token_to_user_table extends Migration
{
    public $tableName = "{{%user}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'access_token', $this->text()->defaultValue(null));

        $this->addCommentOnColumn($this->tableName, 'access_token', 'Токен');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'access_token');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210319_050956_add_access_token_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
