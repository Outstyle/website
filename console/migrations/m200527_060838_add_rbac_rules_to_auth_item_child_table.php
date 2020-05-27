<?php

use yii\db\Migration;

/**
 * Class m200527_060838_add_rbac_rules_to_auth_item_child_table
 */
class m200527_060838_add_rbac_rules_to_auth_item_child_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert(
            '{{%auth_item_child}}',
            [
                'parent',
                'child'
            ],
            [
                ['mainadmin', 'videonews/create'],
                ['mainadmin', 'videonews/update'],
                ['mainadmin', 'videonews/delete'],
                ['mainadmin', 'videonews/view'],
                ['mainadmin', 'videonews/index'],

                ['mainadmin', 'reviews/create'],
                ['mainadmin', 'reviews/update'],
                ['mainadmin', 'reviews/delete'],
                ['mainadmin', 'reviews/view'],
                ['mainadmin', 'reviews/index'],

                ['mainadmin', 'releases/create'],
                ['mainadmin', 'releases/update'],
                ['mainadmin', 'releases/delete'],
                ['mainadmin', 'releases/view'],
                ['mainadmin', 'releases/index'],

                ['admin', 'videonews/create'],
                ['admin', 'videonews/update'],
                ['admin', 'videonews/delete'],
                ['admin', 'videonews/view'],
                ['admin', 'videonews/index'],

                ['admin', 'reviews/create'],
                ['admin', 'reviews/update'],
                ['admin', 'reviews/delete'],
                ['admin', 'reviews/view'],
                ['admin', 'reviews/index'],

                ['admin', 'releases/create'],
                ['admin', 'releases/update'],
                ['admin', 'releases/delete'],
                ['admin', 'releases/view'],
                ['admin', 'releases/index'],

                ['redactor', 'videonews/create'],
                ['redactor', 'videonews/update'],
                ['redactor', 'videonews/delete'],
                ['redactor', 'videonews/view'],
                ['redactor', 'videonews/index'],

                ['redactor', 'reviews/create'],
                ['redactor', 'reviews/update'],
                ['redactor', 'reviews/delete'],
                ['redactor', 'reviews/view'],
                ['redactor', 'reviews/index'],

                ['redactor', 'releases/create'],
                ['redactor', 'releases/update'],
                ['redactor', 'releases/delete'],
                ['redactor', 'releases/view'],
                ['redactor', 'releases/index'],

            ]

        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200527_060838_add_rbac_rules_to_auth_item_child_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200527_060838_add_rbac_rules_to_auth_item_child_table cannot be reverted.\n";

        return false;
    }
    */
}
