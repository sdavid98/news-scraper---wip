<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%keyword}}`.
 */
class m200421_193625_create_keyword_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('{{%keyword}}', [
            'id' => $this->primaryKey()->unsigned(),
            'article_id' => $this->integer()->notNull()->unsigned(),
            'keyword' => $this->string(45)->notNull()->unique()
        ], $tableOptions);

        $this->createIndex(
            '{{%idx-keyword-article_id}}',
            '{{%keyword}}',
            'article_id'
        );

        $this->addForeignKey(
            '{{%fk-keyword-article_id}}',
            '{{%keyword}}',
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
        $this->dropTable('{{%keyword}}');
    }
}
