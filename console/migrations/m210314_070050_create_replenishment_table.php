<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%replenishment}}`.
 */
class m210314_070050_create_replenishment_table extends Migration
{
    public $tableName = "{{%replenishment}}";
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'card_id' => $this->integer(),
            'amount' => $this->money()->defaultValue(0),
            'user_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addForeignKey('fk-card-card_id-to-card',$this->tableName,'card_id','{{%card}}','id','SET NULL','RESTRICT');
        $this->addForeignKey('fk-user-user_id-to-user',$this->tableName,'user_id','{{%user}}','id','SET NULL','RESTRICT');

        $this->addCommentOnTable($this->tableName, 'Пополнения');
        $this->addCommentOnColumn($this->tableName,'card_id', 'Карта');
        $this->addCommentOnColumn($this->tableName,'amount', 'Сумма');
        $this->addCommentOnColumn($this->tableName,'user_id', 'Оператор');
        $this->addCommentOnColumn($this->tableName,'created_at', 'Создано');
        $this->addCommentOnColumn($this->tableName,'updated_at', 'Обновлено');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
