<?php

use yii\db\Migration;

/**
 * Class m181221_171125_rename_status_column_in_message_table
 */
class m181221_171125_rename_status_column_in_message_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->renameColumn('{{%message}}', 'status', 'type');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m181221_171125_rename_status_column_in_message_table cannot be reverted.\n";

        return false;
    }
}
