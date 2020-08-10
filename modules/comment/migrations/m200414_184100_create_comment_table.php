<?php

namespace modules\comment\migrations;

use yii\db\Migration;

/**
 * Class m200414_184100_create_comment_table
 */
class m200414_184100_create_comment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // Nested Sets
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'tree' => $this->integer()->defaultValue(null)->comment('Tree'),
            'lft' => $this->integer()->notNull()->comment('L.Key'),
            'rgt' => $this->integer()->notNull()->comment('R.Key'),
            'depth' => $this->integer()->notNull()->comment('Depth'),
            'entity' => $this->string()->notNull()->comment('Entity'),
            'entity_id' => $this->integer()->notNull()->comment('Entity ID'),
            'author' => $this->string()->notNull()->comment('Author'),
            'email' => $this->string()->notNull()->comment('Email'),
            'comment' => $this->text()->comment('Comment'),
            'created_at' => $this->integer()->notNull()->comment('Created'),
            'updated_at' => $this->integer()->notNull()->comment('Updated'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('Status'),
            'confirm' => $this->string()->comment('Confirm Token'),
            'redirect' => $this->string()->comment('Redirect URL'),
        ], $tableOptions);

        $this->createIndex('IDX_comment_nested_sets', '{{%comment}}', [
            'tree', 'lft', 'rgt', 'entity', 'entity_id', 'author', 'confirm'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IDX_comment_nested_sets', '{{%comment}}');
        $this->dropTable('{{%comment}}');
    }
}
