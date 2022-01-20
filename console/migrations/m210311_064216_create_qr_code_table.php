<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%barcode}}`.
 */
class m210311_064216_create_qr_code_table extends Migration
{
    public $tableName = '{{%qr_code}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'uuid' => $this->string()->unique(),
            'path' => $this->string(),
        ]);

        $this->addCommentOnTable($this->tableName, 'Таблица для uuid');
        $this->addCommentOnColumn($this->tableName,'uuid', 'Уникальный id');
        $this->addCommentOnColumn($this->tableName,'path', 'Путь до qr');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
