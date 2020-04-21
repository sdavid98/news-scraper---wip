<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%userlike}}`.
 */
class m200421_194757_create_userlike_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('{{%userlike}}', [
            'id' => $this->primaryKey()->unsigned(),
            'article_id' => $this->integer()->notNull()->unsigned(),
            'user_id' => $this->integer()->notNull()->unsigned(),
            'comment_id' => $this->integer()->null()->unsigned(),
            'created' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            '{{%idx-userlike-user_id}}',
            '{{%userlike}}',
            'user_id'
        );

        $this->addForeignKey(
            '{{%fk-userlike-user_id}}',
            '{{%userlike}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%idx-userlike-article_id}}',
            '{{%userlike}}',
            'article_id'
        );

        $this->addForeignKey(
            '{{%fk-userlike-article_id}}',
            '{{%userlike}}',
            'article_id',
            '{{%article}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%idx-userlike-comment_id}}',
            '{{%userlike}}',
            'comment_id'
        );

        $this->addForeignKey(
            '{{%fk-userlike-comment_id}}',
            '{{%userlike}}',
            'comment_id',
            '{{%comment}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%userlike}}');
    }
}
