<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public $tableName = "{{%user}}";

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'fullname' => $this->string(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'role' => $this->smallInteger()->defaultValue(2),
            'city_id' => $this->smallInteger(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->insert($this->tableName, [
            'username' => 'admin',
            'fullname' => 'admin admin admin',
            'auth_key' => 'knrFK88QyEjIBMga3UlpuTeHyygLMMqk',
            'password_hash' => '$2y$13$HQXwsLWTYwXrLy/Dd5hLT.shamK/T1VnshuRPZzjBMLhItoqFXOym',
            'email' => 'admin@admin.admin',
            'role' => '1',
            'status' => '10',
            'created_at' => Yii::$app->formatter->asTimestamp(date('Y-m-d h:i:s')),
            'updated_at' => Yii::$app->formatter->asTimestamp(date('Y-m-d h:i:s')),
        ]);

        $this->addCommentOnTable($this->tableName,'Пользователи');
        $this->addCommentOnColumn($this->tableName,'username','Логин');
        $this->addCommentOnColumn($this->tableName,'fullname','Полное имя');
        $this->addCommentOnColumn($this->tableName,'auth_key','Ключ авторизации');
        $this->addCommentOnColumn($this->tableName,'password_hash','Хэш пароля');
        $this->addCommentOnColumn($this->tableName,'password_reset_token','Хэш сброса пароля');
        $this->addCommentOnColumn($this->tableName,'email','Электронная почта');
        $this->addCommentOnColumn($this->tableName,'role','Роль');
        $this->addCommentOnColumn($this->tableName,'city_id','Город');
        $this->addCommentOnColumn($this->tableName,'status','Статус');
        $this->addCommentOnColumn($this->tableName,'created_at','Дата создания');
        $this->addCommentOnColumn($this->tableName,'updated_at','Дата обновления');
    }

    public function down()
    {
        $this->dropTable($this->tableName);
    }
}