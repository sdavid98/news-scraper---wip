<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%usersaved}}`.
 */
class m200421_195113_create_usersaved_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('{{%usersaved}}', [
            'id' => $this->primaryKey()->unsigned(),
            'article_id' => $this->integer()->notNull()->unsigned(),
            'user_id' => $this->integer()->notNull()->unsigned(),
            'created' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            '{{%idx-usersaved-user_id}}',
            '{{%usersaved}}',
            'user_id'
        );

        $this->addForeignKey(
            '{{%fk-usersaved-user_id}}',
            '{{%usersaved}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%idx-usersaved-article_id}}',
            '{{%usersaved}}',
            'article_id'
        );

        $this->addForeignKey(
            '{{%fk-usersaved-article_id}}',
            '{{%usersaved}}',
            'article_id',
            '{{%article}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%usersaved}}');
    }
}
