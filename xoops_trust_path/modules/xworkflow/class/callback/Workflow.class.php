<?php

use Xworkflow\Core\XoopsUtils;

/**
 * workflow delegate.
 */
class Xworkflow_WorkflowDelegate implements Legacy_iWorkflowDelegate
{
    /**
     * add item.
     *
     * 'Legacy_Workflow.AddItem' delegate function
     *
     * @param string $title
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     * @param string $url
     */
    public static function addItem($title, $dirname, $dataname, $target_id,  $url)
    {
        $handler = XoopsUtils::getModuleHandler('ItemObject', self::_getDirname());
        $objs = $handler->getObjects(self::_getItemCriteria($dirname, $dataname, $target_id));
        if (count($objs) == 0) {
            $obj = $handler->create();
            $obj->set('title', $title);
            $obj->set('dirname', $dirname);
            $obj->set('dataname', $dataname);
            $obj->set('target_id', $target_id);
            $obj->set('url', $url);
            $obj->set('uid', XoopsUtils::getUid());
            $obj->setFirstStep();
            $handler->insert($obj);
        } elseif (count($objs) == 1) {
            $obj = array_shift($objs);
            $obj->set('title', $title);
            $obj->set('status', \Lenum_WorkflowStatus::PROGRESS);
            $obj->set('url', $url);
            $obj->set('updatetime', time());
            $obj->setFirstStep();
            $obj->incrementRevision();
            $handler->insert($obj);
        }
    }

    /**
     * delete item.
     *
     * 'Legacy_Workflow.DeleteItem' delegate function
     *
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     */
    public static function deleteItem($dirname, $dataname, $target_id)
    {
        $handler = XoopsUtils::getModuleHandler('ItemObject', self::_getDirname());
        $objs = $handler->getObjects(self::_getItemCriteria($dirname, $dataname, $target_id));
        if (count($objs) == 1) {
            $handler->delete($objs[0]);
        }
    }

    /**
     * get history.
     *
     * 'Legacy_Workflow.GetHistory' delegate function
     *
     * @param mix[]  &$historyArr
     * @param string $dirname
     * @param string $dataname
     * @param int    $target_id
     */
    public static function getHistory(&$historyArr, $dirname, $dataname, $target_id)
    {
        $handler = XoopsUtils::getModuleHandler('ItemObject', self::_getDirname());
        $objs = $handler->getObjects(self::_getItemCriteria($dirname, $dataname, $target_id));
        if (count($objs) == 1) {
            $obj = array_shift($objs);
            $obj->loadHistory();
            foreach ($obj->mHistory as $history) {
                $hisotryArr['step'][] = $history->get('step');
                $hisotryArr['uid'][] = $history->get('uid');
                $hisotryArr['result'][] = $history->get('result');
                $hisotryArr['comment'][] = $history->get('comment');
                $hisotryArr['posttime'][] = $history->get('posttime');
            }
        }
    }

    /**
     * get workflow module dirname.
     *
     * @return string
     */
    protected static function _getDirname()
    {
        return LEGACY_WORKFLOW_DIRNAME;
    }

    /**
     * get item criteria.
     *
     * @param string $dirname
     * @param string $dataname
     * @param int    $id
     *
     * @return CriteriaElement
     */
    protected static function _getItemCriteria($dirname, $dataname, $target_id)
    {
        $cri = new CriteriaCompo();
        $cri->add(new Criteria('dirname', $dirname));
        $cri->add(new Criteria('dataname', $dataname));
        $cri->add(new Criteria('target_id', $target_id));
        $cri->add(new Criteria('deletetime', 0));

        return $cri;
    }
}
