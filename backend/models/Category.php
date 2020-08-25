<?php

namespace backend\models;

use Yii;
use common\components\helpers\StringHelper;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property string $created
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'url'], 'required'],
            [['name', 'url'], 'unique'],
            [['name', 'url'], 'trim'],
            [['name', 'url'], 'string', 'max' => 32],
            ['url', 'filter', 'filter' => function ($value) {
                return StringHelper::slugify($value);
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'url' => 'Url'
        ];
    }

    /**
     * Gets all categories names and urls
     * @param array     $where        Args for WHERE clause
     * @param string    $orderBy      Sort by which column?
     * @param boolean   $forSelect    Return only key/value array for populating select, i.e.?
     * @return array
     */
    public static function getCategories($where = [], $orderBy = 'id', $forSelect = false)
    {
        $categories = self::getDb()->cache(function ($db) use ($where, $orderBy) {
            return self::find()->where($where)->orderBy($orderBy)->all();
        }, 3600);

        $c = [];
        foreach ($categories as $category) {
            if ($forSelect == true) {
                $c[$category->id] = $category->name;
            } else {
                $c[$category->id] = $category;
            }
        }
        return $c;
    }

    /**
     * Relations
     */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['category' => 'id']);
    }
    public function getEvents()
    {
        return $this->hasMany(Events::className(), ['category' => 'id']);
    }
}
