<?php

require_once XOOPS_ROOT_PATH.'/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH.'/legacy/class/Legacy_Validator.class.php';

/**
 * history edit form.
 */
class Xworkflow_HistoryEditForm extends XCube_ActionForm
{
    /**
     * get token name.
     * 
     * @return string
     */
    public function getTokenName()
    {
        $dirname = $this->mContext->mModule->mXoopsModule->get('dirname');

        return 'module.'.$dirname.'.HistoryEditForm.TOKEN';
    }

    /**
     * prepare.
     */
    public function prepare()
    {
        $dirname = $this->mContext->mModule->mXoopsModule->get('dirname');
        $constpref = '_MD_'.strtoupper($dirname);
        // Set form properties
        $this->mFormProperties['progress_id'] = new XCube_IntProperty('progress_id');
        $this->mFormProperties['item_id'] = new XCube_IntProperty('item_id');
        $this->mFormProperties['uid'] = new XCube_IntProperty('uid');
        $this->mFormProperties['step'] = new XCube_IntProperty('step');
        $this->mFormProperties['result'] = new XCube_IntProperty('result');
        $this->mFormProperties['comment'] = new XCube_TextProperty('comment');
        $this->mFormProperties['posttime'] = new XCube_IntProperty('posttime');
        // Set field properties
        $this->mFieldProperties['item_id'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['item_id']->setDependsByArray(array('required'));
        $this->mFieldProperties['item_id']->addMessage('required', constant($constpref.'_ERROR_REQUIRED'), constant($constpref.'_LANG_ITEM_ID'));
        $this->mFieldProperties['result'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['result']->setDependsByArray(array('required'));
        $this->mFieldProperties['result']->addMessage('required', constant($constpref.'_ERROR_REQUIRED'), constant($constpref.'_LANG_RESULT'));
    }

    /**
     * load.
     * 
     * @param XoopsSimpleObject &$obj
     */
    public function load(&$obj)
    {
        $this->set('progress_id', $obj->get('progress_id'));
        $this->set('item_id', $obj->get('item_id'));
        $this->set('uid', $obj->get('uid'));
        $this->set('step', $obj->get('step'));
        $this->set('result', $obj->get('result'));
        $this->set('comment', $obj->get('comment'));
        $this->set('posttime', $obj->get('posttime'));
    }

    /**
     * update.
     * 
     * @param XoopsSimpleObject &$obj
     */
    public function update(&$obj)
    {
        //$obj->set('progress_id', $this->get('progress_id'));
        $obj->set('item_id', $this->get('item_id'));
        //$obj->set('uid', $this->get('uid'));
        //$obj->set('step', $this->get('step'));
        $obj->set('result', $this->get('result'));
        $obj->set('comment', $this->get('comment'));
    }
}
