<?php

require_once dirname(dirname(__FILE__)).'/class/AbstractEditAction.class.php';

/**
 * history edit action.
 */
class Xworkflow_HistoryEditAction extends Xworkflow_AbstractEditAction
{
    /**
     * get handler.
     *
     * return {Trustdirname}_HistoryHandler
     */
    protected function &_getHandler()
    {
        $handler = &$this->mAsset->getObject('handler', 'History');

        return $handler;
    }

    /**
     * prepare.
     * 
     * @return bool
     */
    public function prepare()
    {
        parent::prepare();
        if (!$this->mObject->isNew()) {
            // don't accept to edit previous history data
            $this->mRoot->mController->executeForward(Legacy_Utils::renderUri($this->mAsset->mDirname, 'item'));
        }
        $this->mObject->set('item_id', $this->mRoot->mContext->mRequest->getRequest('item_id'));
        $this->mObject->set('uid', Legacy_Utils::getUid());
        $this->mObject->loadItem();
        if (!$this->mObject->mItem) {
            // don't accept to edit if item is not found
            $this->mRoot->mController->executeForward(Legacy_Utils::renderUri($this->mAsset->mDirname, 'item'));
        }
        if ($this->mObject->mItem->get('status') != Lenum_WorkflowStatus::PROGRESS) {
            // don't accept to edit if not in progress
            $this->mRoot->mController->executeForward(Legacy_Utils::renderUri($this->mAsset->mDirname, 'item', $this->mObject->getShow('item_id')));
        }
        if (!$this->mObject->mItem->checkStep(Legacy_Utils::getUid())) {
            // don't accept to edit if not my approval step
            $this->mRoot->mController->executeForward(Legacy_Utils::renderUri($this->mAsset->mDirname, 'item', $this->mObject->getShow('item_id')));
        }
        $this->mObject->mItem->loadHistory();
        $this->mObject->set('step', $this->mObject->mItem->get('step'));

        return true;
    }

    /**
     * setup action form.
     */
    protected function _setupActionForm()
    {
        $this->mActionForm = &$this->mAsset->getObject('form', 'History', false, 'edit');
        $this->mActionForm->prepare();
    }

    /**
     * do execute.
     * 
     * @return Enum
     */
    protected function _doExecute()
    {
        $iHandler = Legacy_Utils::getModuleHandler('item', $this->mAsset->mDirname);
        if ($this->mObjectHandler->insert($this->mObject)) {
            $cname = ucfirst($this->mAsset->mTrustDirname).'_Result';
            if ($this->mObject->get('result') == $cname::APPROVE) {
                $iHandler->proceedStep($this->mObject->mItem);
            } else {
                $iHandler->revertStep($this->mObject->mItem);
            }

            return $this->_getFrameViewStatus('SUCCESS');
        }

        return $this->_getFrameViewStatus('ERROR');
    }

    /**
     * execute view input.
     * 
     * @param XCube_RenderTarget &$render
     */
    public function executeViewInput(&$render)
    {
        $render->setTemplateName($this->mAsset->mDirname.'_history_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);
    }

    /**
     * execute view success.
     * 
     * @param XCube_RenderTarget &$render
     */
    public function executeViewSuccess(&$render)
    {
        $this->mRoot->mController->executeForward(Legacy_Utils::renderUri($this->mAsset->mDirname, 'item', $this->mObject->get('item_id'), 'view'));
    }

    /**
     * execute view error.
     * 
     * @param XCube_RenderTarget &$render
     */
    public function executeViewError(&$render)
    {
        $constpref = 'MD_'.strtoupper($this->mAsset->mDirname);
        $this->mRoot->mController->executeRedirect(Legacy_Utils::renderUri($this->mAsset->mDirname, 'item'), 1, constant($constpref.'_ERROR_DBUPDATE_FAILED'));
    }

    /**
     * execute view cancel.
     * 
     * @param XCube_RenderTarget &$render
     */
    public function executeViewCancel(&$render)
    {
        $this->mRoot->mController->executeForward(Legacy_Utils::renderUri($this->mAsset->mDirname, 'item'));
    }
}
