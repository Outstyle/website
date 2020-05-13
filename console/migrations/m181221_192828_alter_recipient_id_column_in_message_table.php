<?php

use yii\db\Migration;

/**
 * Class m181221_192828_alter_recipient_id_column_in_message_table
 */
class m181221_192828_alter_recipient_id_column_in_message_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn('{{%message}}', 'recipient_id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m181221_192828_alter_recipient_id_column_in_messages_table cannot be reverted.\n";

        return false;
    }
}
