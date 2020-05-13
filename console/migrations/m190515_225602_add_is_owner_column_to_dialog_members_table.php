<?php

use yii\db\Migration;

/**
 * Handles adding is_owner to table `{{%dialog_members}}`.
 */
class m190515_225602_add_is_owner_column_to_dialog_members_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%dialog_members}}', 'is_owner', $this->integer(1)->notNull()->defaultValue(0)->after('dialog'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
