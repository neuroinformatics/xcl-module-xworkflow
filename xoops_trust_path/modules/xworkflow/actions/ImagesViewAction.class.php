<?php

/**
 * images view action.
 */
class Xworkflow_ImagesViewAction extends Xworkflow_AbstractAction
{
    /**
     * image path.
     *
     * @var string
     */
    protected $_mImagePath = '';

    /**
     * image mime-type.
     *
     * @var string
     */
    protected $_mImageMime = '';

    /**
     * get action name.
     *
     * @return string
     */
    protected function _getActionName()
    {
        return _IMAGE;
    }

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
        if (empty($fname)) {
            return $this->_getFrameViewStatus('ERROR');
        }
        $fpath = XOOPS_TRUST_PATH.'/modules/'.$this->mAsset->mDirname.'/images/'.$fname;
        if (!file_exists($fpath)) {
            $fpath = XOOPS_TRUST_PATH.'/modules/'.$this->mAsset->mTrustDirname.'/images/'.$fname;
            if (!file_exists($fpath)) {
                return $this->_getFrameViewStatus('ERROR');
            }
        }
        $info = getimagesize($fpath);
        if ($info === false) {
            return $this->_getFrameViewStatus('ERROR');
        }
        $this->_mImagePath = $fpath;
        $this->_mImageMime = $info['mime'];

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
        self::_clearObFilters();
        header('Content-Type: '.$this->_mImageMime);
        readfile($this->_mImagePath);
        register_shutdown_function(array($this, 'onShutdown'));
        ob_start();
        exit();
    }

    /**
     * execute view error.
     *
     * @param XCube_RenderTarget &$render
     */
    public function executeViewError(&$render)
    {
        self::_clearObFilters();
        $error = 'HTTP/1.0 404 Not Found';
        header($error);
        echo $error;
        register_shutdown_function(array($this, 'onShutdown'));
        ob_start();
        exit();
    }

    /**
     * on shutdown callback handler.
     */
    public function onShutdown()
    {
        self::_clearObFilters();
    }

    /**
     * clear ob filters.
     */
    protected static function _clearObFilters()
    {
        $handlers = ob_list_handlers();
        while (!empty($handlers)) {
            ob_end_clean();
            $handlers = ob_list_handlers();
        }
    }
}
