<?php

use yii\db\Migration;

/**
 * Handles adding user_id to table `{{%attachments}}`.
 */
class m170909_132928_add_user_id_column_to_attachments_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('{{%attachments}}', 'user_id', $this->integer()->notNull());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('{{%attachments}}', 'user_id');
    }
}
