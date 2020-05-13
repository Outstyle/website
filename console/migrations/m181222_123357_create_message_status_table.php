<?php

use yii\db\Migration;

/**
 * Handles the creation of table `message_status`.
 */
class m181222_123357_create_message_status_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%message_status}}', [
            'id' => $this->primaryKey(),
            'dialog' => $this->integer(11),
            'user' => $this->integer(11),
            'message_id' => $this->integer(11),
            'status' => $this->integer(2)->notNull()->defaultValue(0),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%message_status}}');
    }
}
