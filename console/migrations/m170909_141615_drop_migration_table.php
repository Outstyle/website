<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `migration`.
 */
class m170909_141615_drop_migration_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropTable('migration');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        echo 'This table is obsolete and cannot be recreated';
        return false;
    }
}
