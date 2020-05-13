<?php

use yii\db\Migration;

/**
 * Class m180720_210033_create_config_table
 * @package modules\config\migrations
 */
class m180720_210033_create_config_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%config}}', [
            'id' => $this->primaryKey()->comment('ID'),
            'param' => $this->string(128)->notNull()->comment('Params'),
            'value' => $this->text()->notNull()->comment('Value'),
            'default' => $this->text()->notNull()->comment('Default'),
            'label' => $this->string(255)->defaultValue(null)->comment('Label'),
            'type' => $this->string(128)->notNull()->comment('Type'),
        ], $tableOptions);

        $this->createIndex('IDX_config_param', '{{%config}}', 'param');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%config}}');
    }
}
