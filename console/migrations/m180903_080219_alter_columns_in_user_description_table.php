<?php

use yii\db\Migration;

/**
 * Class m180903_080219_alter_columns_in_user_description_table
 */
class m180903_080219_alter_columns_in_user_description_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn('{{%user_description}}', 'avatar');
        $this->dropColumn('{{%user_description}}', 'avatar_small');
        $this->addColumn('{{%user_description}}', 'avatar', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180903_080219_alter_columns_in_user_description_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180903_080219_alter_columns_in_user_description_table cannot be reverted.\n";

        return false;
    }
    */
}
