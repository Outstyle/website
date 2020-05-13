<?php

use yii\db\Migration;

/**
 * Class m191030_225155_create_setting_script
 */
class m191030_225155_create_setting_script extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('z_setting_script', [
            'id' => $this->primaryKey(),
            'type' => $this->string(128)->null(),
            'label' => $this->string(250)->null(),
            'param' => $this->string(128)->null()->unique(),
            'value' => $this->text()->null()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191030_225155_create_setting_script cannot be reverted.\n";
        $this->dropTable('z_setting_script');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191030_225155_create_setting_script cannot be reverted.\n";

        return false;
    }
    */
}
