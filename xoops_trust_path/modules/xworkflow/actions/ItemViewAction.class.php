<?php

require_once dirname(dirname(__FILE__)).'/class/AbstractViewAction.class.php';

/**
 * item view action.
 */
class Xworkflow_ItemViewAction extends Xworkflow_AbstractViewAction
{
    /**
     * flag for if item is my step.
     *
     * @var int
     */
    public $mMyStep = false;

    /**
     *  get handler.
     * 
     * return {Trustdirname}_ItemHandler
     */
    protected function &_getHandler()
    {
        $handler = &$this->mAsset->getObject('handler', 'Item');

        return $handler;
    }

    /**
     * get page title.
     * 
     * @return string
     */
    protected function _getPagetitle()
    {
        return $this->mObject->getShow('title');
    }

    /**
     * prepare.
     * 
     * @return bool
     */
    public function prepare()
    {
        if (!parent::prepare()) {
            return false;
        }
        $this->mObject->loadHistory();
        if ($this->mObject->checkStep(Legacy_Utils::getUid())) {
            $this->mMyStep = true;
            $this->_setupActionForm();
        }

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
     * execute view success.
     * 
     * @param XCube_RenderTarget &$render
     */
    public function executeViewSuccess(&$render)
    {
        $render->setTemplateName($this->mAsset->mDirname.'_item_view.html');
        $render->setAttribute('object', $this->mObject);
        $render->setAttribute('approval', $this->mMyStep);
        if ($this->mMyStep == true) {
            $render->setAttribute('actionForm', $this->mActionForm);
        }
        $cnameUtils = ucfirst($this->mAsset->mTrustDirname).'_Utils';
        $render->setAttribute('clients', $cnameUtils::getClients());
    }

    /**
     * execute view error.
     * 
     * @param XCube_RenderTarget &$render
     */
    public function executeViewError(&$render)
    {
        $constpref = '_MD_'.strtoupper($this->mAsset->mDirname);
        $this->mRoot->mController->executeRedirect(Legacy_Utils::renderUri($this->mAsset->mDirname, 'item'), 1, constant($constpref.'_ERROR_CONTENT_IS_NOT_FOUND'));
    }
}
