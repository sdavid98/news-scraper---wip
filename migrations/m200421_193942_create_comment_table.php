<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%comment}}`.
 */
class m200421_193942_create_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey()->unsigned(),
            'article_id' => $this->integer()->notNull()->unsigned(),
            'user_id' => $this->integer()->notNull()->unsigned(),
            'comment_id' => $this->integer()->null()->unsigned(),
            'created' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'last_modified' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'points' => $this->integer()->defaultValue(0),
            'text' => $this->text()->notNull()
        ], $tableOptions);

        $this->createIndex(
            '{{%idx-comment-user_id}}',
            '{{%comment}}',
            'user_id'
        );

        $this->addForeignKey(
            '{{%fk-comment-user_id}}',
            '{{%comment}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%idx-comment-article_id}}',
            '{{%comment}}',
            'article_id'
        );

        $this->addForeignKey(
            '{{%fk-comment-article_id}}',
            '{{%comment}}',
            'article_id',
            '{{%article}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            '{{%fk-comment-comment_id}}',
            '{{%comment}}',
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
        $this->dropTable('{{%comment}}');
    }
}
