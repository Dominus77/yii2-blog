<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%blog}}`.
 */
class m200331_103455_create_blog_table extends Migration
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

        // Category Nested Sets
        $this->createTable('{{%blog_category}}', [
            'id' => $this->primaryKey()->comment('ID'),
            'tree' => $this->integer()->defaultValue(null)->comment('Tree'),
            'lft' => $this->integer()->notNull()->comment('L.Key'),
            'rgt' => $this->integer()->notNull()->comment('R.Key'),
            'depth' => $this->integer()->notNull()->comment('Depth'),
            'position' => $this->integer()->notNull()->defaultValue(0)->comment('Position'),
            'title' => $this->string()->notNull()->comment('Title'),
            'slug' => $this->string()->notNull()->comment('Alias'),
            'description' => $this->text()->comment('Description'),
            'created_at' => $this->integer()->notNull()->comment('Created'),
            'updated_at' => $this->integer()->notNull()->comment('Updated'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('Status'),
        ], $tableOptions);

        $this->createIndex('IDX_blog_category_nested_sets', '{{%blog_category}}', ['tree', 'lft', 'rgt', 'position']);

        // Post
        $this->createTable('{{%blog_post}}', [
            'id' => $this->primaryKey()->comment('ID'),
            'title' => $this->string()->notNull()->comment('Title'),
            'slug' => $this->string()->notNull()->comment('Alias'),
            'anons' => $this->text()->comment('Anons'),
            'content' => $this->text()->comment('Content'),
            'category_id' => $this->integer()->comment('Category'),
            'author_id' => $this->integer()->notNull()->comment('Author'),
            'created_at' => $this->integer()->notNull()->comment('Created'),
            'updated_at' => $this->integer()->notNull()->comment('Updated'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('Status'),
            'sort' => $this->integer()->notNull()->defaultValue(0)->comment('Sort')
        ], $tableOptions);

        $this->createIndex('IDX_blog_post_sort', '{{%blog_post}}', 'sort');
        $this->createIndex('IDX_blog_post_author', '{{%blog_post}}', 'author_id');
        $this->addForeignKey(
            'FK_blog_post_author', '{{%blog_post}}', 'author_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE'
        );

        $this->createIndex('IDX_blog_post_category', '{{%blog_post}}', 'category_id');
        $this->addForeignKey(
            'FK_blog_post_category', '{{%blog_post}}', 'category_id', '{{%blog_category}}', 'id', 'CASCADE', 'CASCADE'
        );

        // Tag
        $this->createTable('{{%blog_tags}}', [
            'id' => $this->primaryKey()->comment('ID'),
            'title' => $this->string()->notNull()->comment('Title'),
            'frequency' => $this->integer()->defaultValue(0)->comment('Frequency'),
            'created_at' => $this->integer()->notNull()->comment('Created'),
            'updated_at' => $this->integer()->notNull()->comment('Updated'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('Status'),
        ], $tableOptions);

        $this->createTable('{{%blog_tag_post}}', [
            'tag_id' => $this->integer()->notNull()->comment('ID Tag'),
            'post_id' => $this->integer()->notNull()->comment('ID Post')
        ], $tableOptions);

        $this->createIndex('IDX_blog_tag', '{{%blog_tag_post}}', 'tag_id');
        $this->addForeignKey(
            'FK_blog_tag_post', '{{%blog_tag_post}}', 'tag_id', '{{%blog_tags}}', 'id', 'CASCADE', 'CASCADE'
        );

        $this->createIndex('IDX_blog_post', '{{%blog_tag_post}}', 'post_id');
        $this->addForeignKey(
            'FK_blog_post_tag', '{{%blog_tag_post}}', 'post_id', '{{%blog_post}}', 'id', 'CASCADE', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Tag
        $this->dropForeignKey('FK_blog_post_tag', '{{%blog_tag_post}}');
        $this->dropIndex('IDX_blog_post', '{{%blog_tag_post}}');
        $this->dropForeignKey('FK_blog_tag_post', '{{%blog_tag_post}}');
        $this->dropIndex('IDX_blog_tag', '{{%blog_tag_post}}');
        $this->dropTable('{{%blog_tag_post}}');
        $this->dropTable('{{%blog_tags}}');

        // Category and Post
        $this->dropForeignKey('FK_blog_post_category', '{{%blog_post}}');
        $this->dropIndex('IDX_blog_post_category', '{{%blog_post}}');
        $this->dropForeignKey('FK_blog_post_author', '{{%blog_post}}');
        $this->dropIndex('IDX_blog_post_author', '{{%blog_post}}');
        $this->dropIndex('IDX_blog_post_sort', '{{%blog_post}}');
        $this->dropTable('{{%blog_post}}');
        $this->dropIndex('IDX_blog_category_nested_sets', '{{%blog_category}}');
        $this->dropTable('{{%blog_category}}');
    }
}
