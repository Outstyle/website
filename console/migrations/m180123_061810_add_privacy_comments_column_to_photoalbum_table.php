<?php

use yii\db\Migration;

/**
 * Handles adding privacy_comments to table `photoalbum`.
 */
class m180123_061810_add_privacy_comments_column_to_photoalbum_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%photoalbum}}', 'privacy_comments', $this->integer(1)->notNull()->defaultValue(0)->after('privacy'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%photoalbum}}', 'privacy_comments');
    }
}
