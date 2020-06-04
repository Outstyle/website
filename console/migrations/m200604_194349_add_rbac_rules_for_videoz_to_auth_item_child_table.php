<?php

use yii\db\Migration;

/**
 * Class m200604_194349_add_rbac_rules_for_videoz_to_auth_item_child_table
 */
class m200604_194349_add_rbac_rules_for_videoz_to_auth_item_child_table extends Migration
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
                ['mainadmin', 'videoz/create'],
                ['mainadmin', 'videoz/update'],
                ['mainadmin', 'videoz/delete'],
                ['mainadmin', 'videoz/view'],
                ['mainadmin', 'videoz/index'],

                ['admin', 'videoz/create'],
                ['admin', 'videoz/update'],
                ['admin', 'videoz/delete'],
                ['admin', 'videoz/view'],
                ['admin', 'videoz/index'],

                ['redactor', 'videoz/create'],
                ['redactor', 'videoz/update'],
                ['redactor', 'videoz/delete'],
                ['redactor', 'videoz/view'],
                ['redactor', 'videoz/index'],

            ]

        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200604_194349_add_rbac_rules_for_videoz_to_auth_item_child_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200604_194349_add_rbac_rules_for_videoz_to_auth_item_child_table cannot be reverted.\n";

        return false;
    }
    */
}
