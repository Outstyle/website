<?php

use yii\db\Migration;

/**
 * Handles adding modified to table `dialog`.
 */
class m181123_175101_add_modified_column_to_dialog_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%dialog}}', 'modified', $this->timestamp());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%dialog}}', 'modified');
    }
}
