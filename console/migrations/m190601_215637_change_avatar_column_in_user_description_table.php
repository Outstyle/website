<?php

use yii\db\Migration;

/**
 * Class m190601_215637_change_avatar_column_in_user_description_table
 */
class m190601_215637_change_avatar_column_in_user_description_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%user_description}}', 'avatar');
        $this->addColumn('{{%user_description}}', 'avatar', $this->integer(11)->notNull()->defaultValue(0)->after('rating'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190601_215637_change_avatar_column_in_user_description_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190601_215637_change_avatar_column_in_user_description_table cannot be reverted.\n";

        return false;
    }
    */
}
