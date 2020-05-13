<?php

use yii\db\Migration;

/**
 * Handles adding status to table `{{%dialog_members}}`.
 */
class m190530_125203_add_status_column_to_dialog_members_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%dialog_members}}', 'status', $this->integer(2)->notNull()->defaultValue(0)->after('is_owner'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
