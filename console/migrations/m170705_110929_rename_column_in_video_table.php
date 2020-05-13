<?php

use yii\db\Migration;

class m170705_110929_rename_column_in_video_table extends Migration
{
    public function safeUp()
    {
		$this->dropColumn('{{%video}}', 'url_iframe');
    }

    public function safeDown()
    {
        echo "m170705_110929_rename_column_in_video_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170705_110929_rename_column_in_video_table cannot be reverted.\n";

        return false;
    }
    */
}
