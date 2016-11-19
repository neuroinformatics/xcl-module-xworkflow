<?php

require_once XOOPS_ROOT_PATH.'/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH.'/legacy/class/Legacy_Validator.class.php';

/**
 * approval delete form.
 */
class Xworkflow_ApprovalDeleteForm extends XCube_ActionForm
{
    /**
     * get token name.
     *
     * @return string
     */
    public function getTokenName()
    {
        $dirname = $this->mContext->mModule->mXoopsModule->get('dirname');

        return 'module.'.$dirname.'.ApprovalDeleteForm.TOKEN';
    }

    /**
     * prepare.
     */
    public function prepare()
    {
        $dirname = $this->mContext->mModule->mXoopsModule->get('dirname');
        $constpref = '_MD_'.strtoupper($dirname);
        // Set form properties
        $this->mFormProperties['approval_id'] = new XCube_IntProperty('approval_id');
        // Set field properties
        $this->mFieldProperties['approval_id'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['approval_id']->setDependsByArray(array('required'));
        $this->mFieldProperties['approval_id']->addMessage('required', constant($constpref.'_ERROR_REQUIRED'), constant($constpref.'_LANG_APPROVAL_ID'));
    }

    /**
     * load.
     *
     * @param XoopsSimpleObject &$obj
     */
    public function load(&$obj)
    {
        $this->set('approval_id', $obj->get('approval_id'));
    }

    /**
     * update.
     *
     * @param XoopsSimpleObject &$obj
     */
    public function update(&$obj)
    {
        $obj->set('approval_id', $this->get('approval_id'));
    }
}
