<?php

namespace parker714\yii2s\data;

/**
 * Class ActiveDataProvider
 *
 * @package parker714\yii2s\data
 */
class ActiveDataProvider extends \yii\data\ActiveDataProvider
{
    /**
     * Pagination list
     *
     * @return array
     */
    public function pagination()
    {
        $currentPage = $this->getPagination()->getPage() + 1;
        $lastPage    = max((int)ceil($this->getTotalCount() / $this->getPagination()->getPageSize()), 1);
        return [
            'total'        => $this->getTotalCount(),
            'current_page' => $currentPage,
            'page_size'    => $this->getPagination()->getPageSize(),
            'last_page'    => $lastPage,
            'has_more'     => $currentPage < $lastPage,
            'list'         => $this->getModels(),
        ];
    }

    /**
     * Run a map over each of the current page items.
     *
     * @param callable $callback
     *
     * @return static
     */
    public function map(callable $callback)
    {
        $this->setModels(array_map($callback, $this->getModels()));
        return $this;
    }

    /**
     * Return the values from a single column in the current page items.
     *
     * @param $column
     *
     * @return array
     */
    public function column($column)
    {
        return array_column($this->getModels(), $column);
    }
}