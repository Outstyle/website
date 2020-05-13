<?php

use yii\db\Migration;

class m170705_103149_change_video_table extends Migration
{
    public function safeUp()
    {
		$this->renameColumn('{{%video}}', 'service', 'service_id');
		$this->renameColumn('{{%video}}', 'title', 'video_title');
		$this->renameColumn('{{%video}}', 'description', 'video_desc');
		$this->renameColumn('{{%video}}', 'created', 'created_at');
		$this->renameColumn('{{%video}}', 'url_img', 'video_img');
		$this->dropColumn('{{%video}}', 'privacy_comments');
		$this->dropColumn('{{%video}}', 'privacy_video');
    }

    public function safeDown()
    {
        echo "m170705_103149_change_video_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170705_103149_change_video_table cannot be reverted.\n";

        return false;
    }
    */
}
