<?php

use yii\db\Migration;

class m170909_145604_alter_columns_in_attachments_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('{{%attachments}}', 'elem_type');
        $this->dropColumn('{{%attachments}}', 'attachment_type');
        $this->addColumn('{{%attachments}}', 'elem_type', $this->integer()->notNull());
        $this->addColumn('{{%attachments}}', 'attachment_type', $this->integer()->notNull());
    }

    public function safeDown()
    {
        echo "m170909_145604_alter_columns_in_attachments_table cannot be reverted.\n";

        return false;
    }
}
