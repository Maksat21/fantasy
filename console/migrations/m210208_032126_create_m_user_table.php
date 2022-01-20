<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%m_user}}`.
 */
class m210208_032126_create_m_user_table extends Migration
{
    public $tableName = "{{%terminal}}";

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'login' => $this->string(),
            'access_token' => $this->text(),
            'password_hash' => $this->string(),
            'f_token' => $this->string(),
            'os_type' => $this->smallInteger(),
            'app_ver' => $this->string(),
            'status' => $this->smallInteger(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime()
        ]);

        $this->addCommentOnTable($this->tableName,'POS терминалы');
        $this->addCommentOnColumn($this->tableName,'title','Название');
        $this->addCommentOnColumn($this->tableName,'login','Логин');
        $this->addCommentOnColumn($this->tableName,'access_token','Access токен');
        $this->addCommentOnColumn($this->tableName,'password_hash','Пароль');
        $this->addCommentOnColumn($this->tableName,'f_token','FireBase токен');
        $this->addCommentOnColumn($this->tableName,'os_type','Тип ОС');
        $this->addCommentOnColumn($this->tableName,'app_ver','Версия приложения');
        $this->addCommentOnColumn($this->tableName,'status','Статус');
        $this->addCommentOnColumn($this->tableName,'created_at','Дата создания');
        $this->addCommentOnColumn($this->tableName,'updated_at','Дата обновления');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
