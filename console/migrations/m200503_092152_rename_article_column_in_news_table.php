<?php

use yii\db\Migration;

/**
 * Class m200503_092152_rename_article_column_in_news_table
 */
class m200503_092152_rename_article_column_in_news_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%news}}', 'article', 'type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200503_092152_rename_article_column_in_news_table cannot be reverted.\n";

        return false;
    }
}
