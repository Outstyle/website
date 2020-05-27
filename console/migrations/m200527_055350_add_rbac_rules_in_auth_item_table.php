<?php

use yii\db\Migration;

/**
 * Class m200527_055350_add_rbac_rules_in_auth_item_table
 */
class m200527_055350_add_rbac_rules_in_auth_item_table extends Migration
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
                ['videonews/create', 2, 'Создание видеоновости', '1588906646', '1588906646'],
                ['videonews/update', 2, 'Редактирование видеоновости', '1588906646', '1588906646'],
                ['videonews/delete', 2, 'Удаление видеоновости', '1588906646', '1588906646'],
                ['videonews/view', 2, 'Просмотр видеоновостей', '1588906646', '1588906646'],
                ['videonews/index', 2, 'Доступ к видеоновостям', '1588906646', '1588906646'],

                ['reviews/create', 2, 'Создание ревью', '1588906646', '1588906646'],
                ['reviews/update', 2, 'Редактирование ревью', '1588906646', '1588906646'],
                ['reviews/delete', 2, 'Удаление ревью', '1588906646', '1588906646'],
                ['reviews/view', 2, 'Просмотр ревью', '1588906646', '1588906646'],
                ['reviews/index', 2, 'Доступ к ревью', '1588906646', '1588906646'],

                ['releases/create', 2, 'Создание релиза', '1588906646', '1588906646'],
                ['releases/update', 2, 'Редактирование релиза', '1588906646', '1588906646'],
                ['releases/delete', 2, 'Удаление релиза', '1588906646', '1588906646'],
                ['releases/view', 2, 'Просмотр релиза', '1588906646', '1588906646'],
                ['releases/index', 2, 'Доступ к релизам', '1588906646', '1588906646'],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200527_055350_add_rbac_rules_in_auth_item_table cannot be reverted.\n";

        return false;
    }
}
