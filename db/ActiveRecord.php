<?php

namespace parker714\yii2s\db;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Class ActiveRecord
 * @method ActiveQuery hasMany($class, array $link) see [[BaseActiveRecord::hasMany()]] for more info
 * @method ActiveQuery hasOne($class, array $link) see [[BaseActiveRecord::hasOne()]] for more info
 *
 * @package parker714\yii2s\db
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * Data record deleted
     */
    const DELETED_YES = 1;

    /**
     * Data record unDeleted
     */
    const DELETED_NO = 0;

    /**
     * Creates an [[ActiveQueryInterface]] instance for query purpose.
     *
     * @return ActiveQuery|object
     * @throws InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(ActiveQuery::className(), [get_called_class()]);
    }
}
