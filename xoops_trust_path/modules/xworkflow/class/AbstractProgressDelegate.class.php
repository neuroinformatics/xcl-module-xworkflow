<?php

/**
 * Interface of workflow delegate.
 */
abstract class Legacy_AbstractWorkflowDelegate implements Legacy_iWorkflowDelegateInterface
{
    /**
     * addItem.
     *
     * @param string $dirname
     * @param string $target
     * @param int    $id
     * @param string $url
     */
    abstract public function addItem($dirname, $target, $id, $url);

    /**
     * deleteItem.
     *
     * @param string $dirname
     * @param string $target
     * @param int    $id
     */
    abstract public function deleteItem($dirname, $target, $id);

    /**
     * getHistory.
     *
     * @param XoopsSimpleObject[] &$historyArr
     * @param string              $dirname
     * @param string              $target
     * @param int                 $id
     */
    abstract public function getHistory(&$historyArr, $dirname, $target, $id);
}
