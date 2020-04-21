<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%article}}`.
 */
class m200421_192745_create_article_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('{{%article}}', [
            'id' => $this->primaryKey()->unsigned(),
            'code' => $this->string(20)->notNull()->unique(),
            'filename' => $this->string(60)->notNull()->unique(),
            'created' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'points' => $this->integer()->defaultValue(0),
            'topic_id' => $this->integer()->notNull()->unsigned(),
            'user_id' => $this->integer()->notNull()->unsigned(),
            'external_link' => $this->string(150)->notNull()
        ], $tableOptions);

        $this->createIndex(
            '{{%idx-article-topic_id}}',
            '{{%article}}',
            'topic_id'
        );

        $this->addForeignKey(
            'fk-article-topic_id',
            'article',
            'topic_id',
            'topic',
            'id'
        );

        $this->createIndex(
            '{{%idx-article-user_id}}',
            '{{%article}}',
            'user_id'
        );

        $this->addForeignKey(
            '{{%fk-article-user_id}}',
            '{{%article}}',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%article}}');
    }
}
