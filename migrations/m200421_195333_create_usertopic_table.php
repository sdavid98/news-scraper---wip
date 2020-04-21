<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%usertopic}}`.
 */
class m200421_195333_create_usertopic_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        $this->createTable('{{%usertopic}}', [
            'id' => $this->primaryKey()->unsigned(),
            'topic_id' => $this->integer()->notNull()->unsigned(),
            'user_id' => $this->integer()->notNull()->unsigned(),
        ], $tableOptions);

        $this->createIndex(
            '{{%idx-usertopic-user_id}}',
            '{{%usertopic}}',
            'user_id'
        );

        $this->addForeignKey(
            '{{%fk-usertopic-user_id}}',
            '{{%usertopic}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            '{{%idx-usertopic-topic_id}}',
            '{{%usertopic}}',
            'topic_id'
        );

        $this->addForeignKey(
            '{{%fk-usertopic-topic_id}}',
            '{{%usertopic}}',
            'topic_id',
            '{{%topic}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%usertopic}}');
    }
}
