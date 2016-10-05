<?php
/**
 * The index controller for the Scripto plugin.
 */
class Scripto_IndexController extends Omeka_Controller_AbstractActionController
{
    /**
     * Initiate this controller.
     */
    public function init()
    {
        // Change the display strategy for certain files on the transcribe 
        // action.
        if ('transcribe' == $this->getRequest()->getActionName()) {
            
            // Image viewers.
            switch (get_option('scripto_image_viewer')) {
                case 'openlayers':
                    add_file_display_callback(ScriptoPlugin::$fileIdentifiersOpenLayers, 'ScriptoPlugin::openLayers');
                    break;
                default:
                    // Do nothing. Use Omeka default file display stategy.
                    break;
            }
            
            // Google Docs viewer.
            if (get_option('scripto_use_google_docs_viewer')) {
                add_file_display_callback(ScriptoPlugin::$fileIdentifiersGoogleDocs, 'ScriptoPlugin::googleDocs');
            }
        }
    }
    
    /**
     * View document pages to which you have contributed.
     */
    public function indexAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            $documentPages = array();
            // Don't bother getting the user document pages if not logged in.
            if ($scripto->isLoggedIn()) {
                $documentPages = $scripto->getUserDocumentPages(500);
            }
        } catch (Scripto_Exception $e) {
            $this->_helper->flashMessenger($e->getMessage());
        }
        
        $this->view->scripto = $scripto;
        $this->view->documentPages = $documentPages;
        $this->view->homePageText = trim(get_option('scripto_home_page_text'));
    }
    
    /**
     * Log in to Scripto.
     */
    public function loginAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            // Handle a login.
            if ($this->_getParam('scripto_mediawiki_login')) {
                $scripto->login($this->_getParam('scripto_mediawiki_username'), 
                                $this->_getParam('scripto_mediawiki_password'));
                $this->_helper->flashMessenger(__('Successfully logged into Scripto.'), 'success');
            }
            // Redirect if logged in.
            if ($scripto->isLoggedIn()) {
                if ($this->_getParam('scripto_redirect_url')) {
                    $this->_helper->redirector->gotoUrl($this->_getParam('scripto_redirect_url'));
                } else {
                    $this->_helper->redirector->goto('index');
                }
            }
        } catch (Scripto_Service_Exception $e) {
            $this->_helper->flashMessenger($e->getMessage());
        }
        
        // Set the URL to redirect to on a sucessful login.
        $redirectUrl = null;
        if ($this->_getParam('scripto_redirect_url')) {
            // Assume login error and reassign the parameter.
            $redirectUrl = $this->_getParam('scripto_redirect_url');
        } else if ('scripto' == $this->getRequest()->getModuleName() && $_SERVER['HTTP_REFERER']) {
            // Assign HTTP referer to scripto_redirect_url parameter only if 
            // coming from the Scripto application.
            $redirectUrl = $_SERVER['HTTP_REFERER'];
        }
        
        $this->view->redirectUrl = $redirectUrl;
        $this->view->scripto = $scripto;
    }
    
    /**
     * Log out of Scripto.
     */
    public function logoutAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            $scripto->logout();
            $this->_helper->flashMessenger(__('Successfully logged out of Scripto.'), 'success');
        } catch (Scripto_Exception $e) {
            $this->_helper->flashMessenger($e->getMessage());
        }
        
        // Always redirect.
        $this->_helper->redirector->goto('index');
    }
    
    /**
     * View your watchlist.
     */
    public function watchlistAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            // Anonymous users 
            if (!$scripto->isLoggedIn()) {
                $this->_helper->redirector->goto('index');
            }
            $watchlist = $scripto->getWatchlist(500);
        } catch (Scripto_Exception $e) {
            $this->_helper->flashMessenger($e->getMessage());
        }
        
        $this->view->scripto = $scripto;
        $this->view->watchlist = $watchlist;
    }
    
    /**
     * View recent changes to the document pages.
     */
    public function recentChangesAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            $recentChanges = $scripto->getRecentChanges(500);
        } catch (Scripto_Exception $e) {
            $this->_helper->flashMessenger($e->getMessage());
        }
        
        $this->view->scripto = $scripto;
        $this->view->recentChanges = $recentChanges;
    }
    
    /**
     * View transcription interface.
     */
    public function transcribeAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            $doc = $scripto->getDocument($this->_getParam('item-id'));
            $doc->setPage($this->_getParam('file-id'));
            
            // Set the File object.
            $file = $this->_helper->db->getTable('File')->find($doc->getPageId());
            
            // Set the page HTML.
            $transcriptionPageHtml = Scripto::removeHtmlAttributes($doc->getTranscriptionPageHtml());
            $talkPageHtml = Scripto::removeHtmlAttributes($doc->getTalkPageHtml());
            
            // Set all the document's pages.
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
            $this->_helper->flashMessenger($e->getMessage());
            $this->_helper->redirector->goto('index');
        }
        
        $this->view->file = $file;
        $this->view->transcriptionPageHtml = $transcriptionPageHtml;
        $this->view->talkPageHtml = $talkPageHtml;
        $this->view->paginationUrls = $paginationUrls;
        $this->view->scripto = $scripto;
        $this->view->doc = $doc;
    }
    
    /**
     * View page history.
     */
    public function historyAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            $doc = $scripto->getDocument($this->_getParam('item-id'));
            $doc->setPage($this->_getParam('file-id'));
            
            // Set the history depending on namespace index.
            if (1 == $this->_getParam('namespace-index')) {
                $info = $doc->getTalkPageInfo();
                $history = $doc->getTalkPageHistory(100);
            } else {
                $info = $doc->getTranscriptionPageInfo();
                $history = $doc->getTranscriptionPageHistory(100);
            }
        } catch (Scripto_Exception $e) {
            $this->_helper->flashMessenger($e->getMessage());
            $this->_helper->redirector->goto('index');
        }
        
        $this->view->scripto = $scripto;
        $this->view->doc = $doc;
        $this->view->info = $info;
        $this->view->history = $history;
        $this->view->namespaceIndex = $this->_getParam('namespace-index');
    }
    
    /**
     * View a page revision.
     */
    public function revisionAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            $doc = $scripto->getDocument($this->_getParam('item-id'));
            $doc->setPage($this->_getParam('file-id'));
            $revision = $scripto->getRevision($this->_getParam('revision-id'));
            
            // Handle a revert.
            if ($this->_getParam('scripto-page-revert')) {
                if (1 == $this->_getParam('namespace-index')) {
                    $doc->editTalkPage($revision['wikitext']);
                } else {
                    $doc->editTranscriptionPage($revision['wikitext']);
                }
                $this->_helper->flashMessenger(__('Successfully reverted the page to a previous revision.'), 'success');
                $this->_helper->redirector->gotoRoute(array('item-id' => $doc->getId(), 
                                                            'file-id' => $doc->getPageId(), 
                                                            'namespace-index' => $this->_getParam('namespace-index')), 
                                                      'scripto_history');
            }
            
        } catch (Scripto_Exception $e) {
            $this->_helper->flashMessenger($e->getMessage());
        }
        
        $this->view->scripto = $scripto;
        $this->view->doc = $doc;
        $this->view->revision = $revision;
        $this->view->namespaceIndex = $this->_getParam('namespace-index');
    }
    
    /**
     * View diff between page revisions.
     */
    public function diffAction()
    {
        try {
            $scripto = ScriptoPlugin::getScripto();
            $doc = $scripto->getDocument($this->_getParam('item-id'));
            $doc->setPage($this->_getParam('file-id'));
            $diff = $scripto->getRevisionDiff($this->_getParam('old-revision-id'), $this->_getParam('revision-id'));
            $oldRevision = $scripto->getRevision($this->_getParam('old-revision-id'));
            $revision = $scripto->getRevision($this->_getParam('revision-id'));
        } catch (Scripto_Exception $e) {
            $this->_helper->flashMessenger($e->getMessage());
            $this->_helper->redirector->goto('index');
        }
        
        $this->view->scripto = $scripto;
        $this->view->doc = $doc;
        $this->view->diff = $diff;
        $this->view->namespaceIndex = $this->_getParam('namespace-index');
        $this->view->oldRevision = $oldRevision;
        $this->view->revision = $revision;
    }
    
    /**
     * Handle AJAX requests from the transcribe action.
     * 
     * 403 Forbidden
     * 400 Bad Request
     * 500 Internal Server Error
     */
    public function pageActionAction()
    {
        // Don't render the view script.
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
        $pageActions = array('edit', 'watch', 'unwatch', 'protect', 'unprotect', 
                             'import-page', 'import-document');
        if (!in_array($this->_getParam('page_action'), $pageActions)) {
            $this->getResponse()->setHttpResponseCode(400);
            return;
        }
        
        // Handle the page action.
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
                    $doc->watchPage();
                    break;
                case 'unwatch':
                    $doc->unwatchPage();
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
                case 'import-page':
                    $doc->exportPage(get_option('scripto_import_type'));
                    break;
                case 'import-document':
                    $doc->export(get_option('scripto_import_type'));
                    break;
                default:
                    $this->getResponse()->setHttpResponseCode(400);
                    return;
             }
            
            $this->getResponse()->setBody($body);
        } catch (Scripto_Exception $e) {
            $this->getResponse()
                ->setHttpResponseCode(500)
                ->setBody($e->getMessage());
        }
    }
}
