<?php

use yii\db\Migration;

/**
 * Handles adding type to table `photo`.
 */
class m180903_085012_add_type_column_to_photo_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
		$this->addColumn('{{%photo}}', 'type', $this->integer(1)->notNull()->defaultValue(0)->after('service_id'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
    }
}
