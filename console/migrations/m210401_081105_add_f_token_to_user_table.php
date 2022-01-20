<?php

use yii\db\Migration;

/**
 * Class m210401_081105_add_f_token_to_user_table
 */
class m210401_081105_add_f_token_to_user_table extends Migration
{
    public $tableName = "{{%user}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'f_token', $this->text()->defaultValue(null));

        $this->addCommentOnColumn($this->tableName, 'f_token', 'Firebase Токен');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'f_token');
    }
}
