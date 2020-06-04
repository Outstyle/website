<?php

use yii\db\Migration;

/**
 * Class m200604_194126_add_rbac_rules_for_videoz_in_auth_item_table
 */
class m200604_194126_add_rbac_rules_for_videoz_in_auth_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert(
            '{{%auth_item}}',
            [
                'name',
                'type',
                'description',
                'created_at',
                'updated_at'
            ],
            [
                ['videoz/create', 2, 'Создание видео', '1588906646', '1588906646'],
                ['videoz/update', 2, 'Редактирование видео', '1588906646', '1588906646'],
                ['videoz/delete', 2, 'Удаление видео', '1588906646', '1588906646'],
                ['videoz/view', 2, 'Просмотр видео', '1588906646', '1588906646'],
                ['videoz/index', 2, 'Доступ к видео', '1588906646', '1588906646'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200604_194126_add_rbac_rules_for_videoz_in_auth_item_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200604_194126_add_rbac_rules_for_videoz_in_auth_item_table cannot be reverted.\n";

        return false;
    }
    */
}
