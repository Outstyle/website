<?php

use yii\db\Migration;

/**
 * Handles adding serviceid to table `photo`.
 */
class m180320_110300_add_serviceid_column_to_photo_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%photo}}', 'service_id', $this->integer(2)->notNull()->defaultValue(0)->after('img'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%photo}}', 'service_id');
    }
}
