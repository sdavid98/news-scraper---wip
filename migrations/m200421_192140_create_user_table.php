<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m200421_192140_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey()->unsigned(),
            'email' => $this->string(70)->notNull()->unique(),
            'password' => $this->string(80)->notNull(),
            'created' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'newsletter' => $this->integer(1)->defaultValue(0)
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
