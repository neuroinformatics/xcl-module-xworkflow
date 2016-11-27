<?php

use Xworkflow\Core\CacheUtils;

/**
 * css view action.
 */
class Xworkflow_CssViewAction extends Xworkflow_AbstractAction
{
    /**
     * css name.
     *
     * @var string
     */
    protected $mCssName = '';

    /**
     * get id.
     *
     * @return string
     */
    protected function _getId()
    {
        $req = $this->mRoot->mContext->mRequest;
        $dataId = $req->getRequest(_REQUESTED_DATA_ID);
        if (isset($_SERVER['PATH_INFO']) && preg_match('/^\/([a-z0-9]+)(?:\/([a-z0-9][a-zA-Z0-9\._\-]*))?(?:\/([a-z0-9]+))?$/', $_SERVER['PATH_INFO'], $matches)) {
            if (isset($matches[2])) {
                $dataId = $matches[2];
            }
        }

        return isset($dataId) ? trim($dataId) : @trim($req->getRequest('file'));
    }

    /**
     * get default view.
     *
     * @return Enum
     */
    public function getDefaultView()
    {
        $fname = $this->_getId();
        $fpath = XOOPS_TRUST_PATH.'/modules/'.$this->mAsset->mTrustDirname.'/templates/'.$fname;
        if (empty($fname) || !file_exists($fpath)) {
            return $this->_getFrameViewStatus('ERROR');
        }
        $this->mCssName = $fname;

        return $this->_getFrameViewStatus('SUCCESS');
    }

    /**
     * execute.
     *
     * @return Enum
     */
    public function execute()
    {
        return $this->getDefaultView();
    }

    /**
     * execute view success.
     *
     * @param XCube_RenderTarget &$render
     */
    public function executeViewSuccess(&$render)
    {
        $render->setTemplateName($this->mAsset->mDirname.'_style.css');
        $renderSystem = &$this->mModule->getRenderSystem();
        $renderSystem->render($render);
        $css = $render->getResult();
        CacheUtils::outputData(false, false, 'text/css', $css);
    }

    /**
     * execute view error.
     *
     * @param XCube_RenderTarget &$render
     */
    public function executeViewError(&$render)
    {
        CacheUtils::errorExit(404);
    }
}
