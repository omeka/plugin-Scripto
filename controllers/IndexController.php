<?php
class Scripto_IndexController extends Omeka_Controller_Action
{
    public function init()
    {
        // Change the display strategy for certain files.
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ('transcribe' == $request->getActionName()) {
            
            // Image viewer.
            add_mime_display_type(
                array(
                    // gif
                    'image/gif', 'image/x-xbitmap', 'image/gi_', 
                    // jpg
                    'image/jpeg', 'image/jpg', 'image/jpe_', 'image/pjpeg', 
                    'image/vnd.swiftview-jpeg', 
                    // png
                    'image/png', 'application/png', 'application/x-png', 
                    // bmp
                    'image/bmp', 'image/x-bmp', 'image/x-bitmap', 
                    'image/x-xbitmap', 'image/x-win-bitmap', 
                    'image/x-windows-bmp', 'image/ms-bmp', 'image/x-ms-bmp', 
                    'application/bmp', 'application/x-bmp', 
                    'application/x-win-bitmap', 
                ), 
                'ScriptoPlugin::imageViewer');
            
            // Document viewer.
            add_mime_display_type(
                array(
                    // pdf
                    'application/pdf', 'application/x-pdf', 
                    'application/acrobat', 'applications/vnd.pdf', 'text/pdf', 
                    'text/x-pdf', 
                    // docx
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                    // doc
                    'application/msword', 'application/doc', 'appl/text', 
                    'application/vnd.msword', 'application/vnd.ms-word', 
                    'application/winword', 'application/word', 
                    'application/x-msw6', 'application/x-msword', 
                    // ppt
                    'application/vnd.ms-powerpoint', 'application/mspowerpoint', 
                    'application/ms-powerpoint', 'application/mspowerpnt', 
                    'application/vnd-mspowerpoint', 'application/powerpoint', 
                    'application/x-powerpoint', 'application/x-m', 
                    // pptx
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation', 
                    // xls
                    'application/vnd.ms-excel', 'application/msexcel', 
                    'application/x-msexcel', 'application/x-ms-excel', 
                    'application/vnd.ms-excel', 'application/x-excel', 
                    'application/x-dos_ms_excel', 'application/xls', 
                    // xlsx
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                    // tiff
                    'image/tiff', 
                    // ps, ai
                    'application/postscript', 'application/ps', 
                    'application/x-postscript', 'application/x-ps', 
                    'text/postscript', 'application/x-postscript-not-eps', 
                    // eps
                    'application/eps', 'application/x-eps', 'image/eps', 
                    'image/x-eps', 
                    // psd
                    'image/vnd.adobe.photoshop', 'image/photoshop', 
                    'image/x-photoshop', 'image/psd', 'application/photoshop', 
                    'application/psd', 'zz-application/zz-winassoc-psd', 
                    // dxf
                    'application/dxf', 'application/x-autocad', 
                    'application/x-dxf', 'drawing/x-dxf', 'image/vnd.dxf', 
                    'image/x-autocad', 'image/x-dxf', 
                    'zz-application/zz-winassoc-dxf', 
                    // xvg
                    'image/svg+xml', 
                    // xps
                    'application/vnd.ms-xpsdocument', 
                ),
                'ScriptoPlugin::documentViewer');
        }
    }
    
    public function indexAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            $watchlist = array();
            if ($scripto->isLoggedIn()) {
                $watchlist = $scripto->getWatchlist(100);
            }
        } catch (Scripto_Exception $e) {
            $this->flashError($e->getMessage());
        }
        
        $this->view->scripto = $scripto;
        $this->view->watchlist = $watchlist;
    }
    
    public function loginAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            if ($this->_getParam('scripto_mediawiki_login')) {
                $scripto->login($this->_getParam('scripto_mediawiki_username'), 
                                $this->_getParam('scripto_mediawiki_password'));
                $this->flashSuccess('Successfully logged into Scripto.');
            }
            if ($scripto->isLoggedIn()) {
                $this->_helper->redirector->goto('index');
            }
        } catch (Scripto_Service_Exception $e) {
            $this->flashError($e->getMessage());
        }
        
        $this->view->scripto = $scripto;
    }
    
    public function logoutAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            $scripto->logout();
            $this->flashSuccess('Successfully logged out of Scripto.');
        } catch (Scripto_Exception $e) {
            $this->flashError($e->getMessage());
        }
        
        $this->_helper->redirector->goto('index');
    }
    
    public function yourContributionsAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            if (!$scripto->isLoggedIn()) {
                $this->_helper->redirector->goto('index');
            }
            $documentPages = $scripto->getUserDocumentPages(100);
        } catch (Scripto_Exception $e) {
            $this->flashError($e->getMessage());
        }
        
        $this->view->scripto = $scripto;
        $this->view->documentPages = $documentPages;
    }
    
    public function recentChangesAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            $recentChanges = $scripto->getRecentChanges(100);
        } catch (Scripto_Exception $e) {
            $this->flashError($e->getMessage());
        }
        
        $this->view->scripto = $scripto;
        $this->view->recentChanges = $recentChanges;
    }
    
    public function transcribeAction()
    {
        try {
            // Set the document and its pages.
            $scripto = ScriptoPlugin::getScripto();
            $doc = $scripto->getDocument($this->_getParam('item-id'));
            $doc->setPage($this->_getParam('file-id'));
            
            $file = $this->getDb()->getTable('File')->find($doc->getPageId());
            $transcriptionPageHtml = Scripto::removeHtmlAttributes($doc->getTranscriptionPageHtml());
            $talkPageHtml = Scripto::removeHtmlAttributes($doc->getTalkPageHtml());
            $pages = $doc->getPages();
            
            // Set the pagination.
            $paginationUrls = array();
            foreach ($pages as $pageId => $pageName) {
                if (isset($current)) {
                    $paginationUrls['next'] = $this->view->url(array(
                        'action' => 'transcribe', 
                        'item-id' => $doc->getId(), 
                        'file-id' => $pageId
                    ), 'scripto_action_item_file');
                    break;
                }
                if ($pageId == $doc->getPageId()) {
                    $current = true;
                } else {
                    $paginationUrls['previous'] = $this->view->url(array(
                        'action' => 'transcribe', 
                        'item-id' => $doc->getId(), 
                        'file-id' => $pageId
                    ), 'scripto_action_item_file');
                }
            }
            
        } catch (Scripto_Exception $e) {
            $this->flashError($e->getMessage());
            $this->_helper->redirector->goto('index');
        }
        
        $this->view->file = $file;
        $this->view->transcriptionPageHtml = $transcriptionPageHtml;
        $this->view->talkPageHtml = $talkPageHtml;
        $this->view->paginationUrls = $paginationUrls;
        $this->view->scripto = $scripto;
        $this->view->doc = $doc;
    }
    
    public function historyAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            $doc = $scripto->getDocument($this->_getParam('item-id'));
            $doc->setPage($this->_getParam('file-id'));
            if (1 == $this->_getParam('namespace-index')) {
                $info = $doc->getTalkPageInfo();
                $history = $doc->getTalkPageHistory(100);
            } else {
                $info = $doc->getTranscriptionPageInfo();
                $history = $doc->getTranscriptionPageHistory(100);
            }
        } catch (Scripto_Exception $e) {
            $this->flashError($e->getMessage());
            $this->_helper->redirector->goto('index');
        }
        
        $this->view->scripto = $scripto;
        $this->view->doc = $doc;
        $this->view->info = $info;
        $this->view->history = $history;
        $this->view->namespace_index = $this->_getParam('namespace-index');
    }
    
    public function differenceAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            $doc = $scripto->getDocument($this->_getParam('item-id'));
            $doc->setPage($this->_getParam('file-id'));
            $diff = $scripto->getRevisionDiff($this->_getParam('old-revision-id'), $this->_getParam('revision-id'));
        } catch (Scripto_Exception $e) {
            $this->flashError($e->getMessage());
            $this->_helper->redirector->goto('index');
        }
        
        $this->view->scripto = $scripto;
        $this->view->doc = $doc;
        $this->view->diff = $diff;
    }
    
    public function pageActionAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        
        // Only allow AJAX requests.
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->setHttpResponseCode(403);
            return;
        }
        
        // Allow only valid pages.
        $pages = array('transcription', 'talk');
        if (!in_array($this->_getParam('page'), $pages)) {
            $this->getResponse()->setHttpResponseCode(400);
            return;
        }
        
        // Only allow valid page actions.
        $pageActions = array('edit', 'watch', 'unwatch', 'protect', 'unprotect');
        if (!in_array($this->_getParam('page_action'), $pageActions)) {
            $this->getResponse()->setHttpResponseCode(400);
            return;
        }
        
        try {
            $scripto = ScriptoPlugin::getScripto();
            $doc = $scripto->getDocument($this->_getParam('item_id'));
            $doc->setPage($this->_getParam('file_id'));
            
            $body = null;
            switch ($this->_getParam('page_action')) {
                case 'edit':
                    if ('talk' == $this->_getParam('page')) {
                        $doc->editTalkPage($this->_getParam('wikitext'));
                        $body = $doc->getTalkPageHtml();
                    } else {
                        $doc->editTranscriptionPage($this->_getParam('wikitext'));
                        $body = $doc->getTranscriptionPageHtml();
                    }
                    break;
                case 'watch':
                    $doc->watchTranscriptionPage();
                    break;
                case 'unwatch':
                    $doc->unwatchTranscriptionPage();
                    break;
                case 'protect':
                    if ('talk' == $this->_getParam('page')) {
                        $doc->protectTalkPage();
                    } else {
                        $doc->protectTranscriptionPage();
                    }
                    break;
                case 'unprotect':
                    if ('talk' == $this->_getParam('page')) {
                        $doc->unprotectTalkPage();
                    } else {
                        $doc->unprotectTranscriptionPage();
                    }
                    break;
                default:
                    $this->getResponse()->setHttpResponseCode(500);
                    return;
             }
            
            $this->getResponse()->setBody($body);
        } catch (Scripto_Exception $e) {
            $this->getResponse()->setHttpResponseCode(500);
        }
    }
}
