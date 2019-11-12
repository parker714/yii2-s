<?php

namespace parker714\yii2s\db;

use Yii;
use parker714\yii2s\data\ActiveDataProvider;

/**
 * Class ActiveQuery
 *
 * @see     ActiveRecord
 * @package parker714\yii2s\db
 */
class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * ActiveData returns [[ActiveDataProvider]] instance
     *
     * @param int  $pageSize
     * @param null $page
     *
     * @return ActiveDataProvider
     */
    public function activeData($pageSize = null, $page = null): ActiveDataProvider
    {
        $pageSize = $pageSize ?: Yii::$app->request->getBodyParam('page_size', 20);
        $page     = $page ?: Yii::$app->request->getBodyParam('page', 1);

        $config = [
            'query'      => $this,
            'db'         => Yii::$app->db,
            'pagination' => [
                'pageSize' => $pageSize,
                'page'     => $page > 1 ? $page - 1 : 0,
            ],
        ];

        return new ActiveDataProvider($config);
    }

    /**
     * Active indicates whether a database record has been deleted，0: no delete, 1: deleted
     *
     * @param int $status
     *
     * @return ActiveQuery
     */
    public function active($status = 0)
    {
        return $this->andWhere(['is_deleted' => $status]);
    }
}
