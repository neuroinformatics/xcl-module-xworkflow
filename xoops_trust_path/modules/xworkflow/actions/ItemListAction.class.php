<?php

require_once dirname(dirname(__FILE__)).'/class/AbstractListAction.class.php';

/**
 * item list action.
 */
class Xworkflow_ItemListAction extends Xworkflow_AbstractListAction
{
    protected $_mIsMyTask = false;

    /**
     * get handler.
     *
     * return {Trustdirname}_ItemHandler
     */
    protected function &_getHandler()
    {
        $handler = &$this->mAsset->getObject('handler', 'Item');

        return $handler;
    }

    /**
     * get filter form.
     *
     * return {Trustdirname}_ItemFilterForm
     */
    protected function &_getFilterForm()
    {
        $filter = &$this->mAsset->getObject('filter', 'Item', false);
        $filter->prepare($this->_getPageNavi(), $this->_getHandler());

        return $filter;
    }

    /**
     * get base url.
     *
     * @return string
     */
    protected function _getBaseUrl()
    {
        return Legacy_Utils::renderUri($this->mAsset->mDirname, 'item');
    }

    /**
     * execute view index.
     *
     * @param XCube_RenderTarget &$render
     */
    public function executeViewIndex(&$render)
    {
        $render->setTemplateName($this->mAsset->mDirname.'_item_list.html');
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('dataname', 'item');
        $render->setAttribute('actionName', $this->mRoot->mContext->mRequest->getRequest('action'));
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
        $render->setAttribute('isMyTask', $this->_mIsMyTask);
        $cnameUtils = ucfirst($this->mAsset->mTrustDirname).'_Utils';
        $render->setAttribute('clients', $cnameUtils::getClients());
    }
}
