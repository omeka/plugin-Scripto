<?php
// Plugin hooks.
add_plugin_hook('install', 'ScriptoPlugin::install');
add_plugin_hook('uninstall', 'ScriptoPlugin::uninstall');
add_plugin_hook('admin_append_to_plugin_uninstall_message', 'ScriptoPlugin::adminAppendToPluginUninstallMessage');
add_plugin_hook('define_routes', 'ScriptoPlugin::defineRoutes');
add_plugin_hook('config_form', 'ScriptoPlugin::configForm');
add_plugin_hook('config', 'ScriptoPlugin::config');
add_plugin_hook('public_append_to_items_show', 'ScriptoPlugin::appendToItemsShow');
add_plugin_hook('admin_append_to_items_show_primary', 'ScriptoPlugin::appendToItemsShow');
add_plugin_hook('admin_append_to_files_form', 'ScriptoPlugin::adminAppendToFilesForm');

// Plugin filters.
add_filter('admin_navigation_main', 'ScriptoPlugin::adminNavigationMain');
add_filter('public_navigation_main', 'ScriptoPlugin::publicNavigationMain');

/**
 * Contains methods specific to the Scripto plugin.
 */
class ScriptoPlugin
{
    /**
     * The name of the Scripto element set.
     */
    const ELEMENT_SET_NAME = 'Scripto';
    
    /**
     * @var Image MIME types compatible with OpenLayers.
     */
    public static $imageMimeTypes = array(
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
    );
    
    /**
     * @var Document MIME types compatible with Google Docs viewer.
     */
    public static $documentMimeTypes = array(
        // pdf
        'application/pdf', 'application/x-pdf', 
        'application/acrobat', 'applications/vnd.pdf', 'text/pdf', 
        'text/x-pdf', 
        // docx
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
        // doc
        'application/msword', 'application/doc', 'appl/text', 
        'application/vnd.msword', 'application/vnd.ms-word', 
        'application/winword', 'application/word', 'application/vnd.ms-office', 
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
    );
    
    /**
     * Install Scripto.
     */
    public static function install()
    {
        $db = get_db();
        
        // Don't install if an element set by the name "Scripto" already exists.
        if ($db->getTable('ElementSet')->findByName(self::ELEMENT_SET_NAME)) {
            throw new Exception('An element set by the name "' 
            . self::ELEMENT_SET_NAME . '" already exists. You must delete that ' 
            . 'element set to install this plugin.');
        }
        
        // Must create the element set from scratch since insert_element_set() 
        // only allows element sets with the Item record type (v1.4).
        $recordTypeId = $db->getTable('RecordType')->findIdFromName('All');
        $elementSet = new ElementSet;
        $elementSet->name = 'Scripto';
        $elementSet->description = '';
        $elementSet->record_type_id = $recordTypeId;
        $elementSet->addElements(array(
            array('name' => 'Transcription', 
                  'description' => 'A written representation of a document.', 
                  'record_type_id' => $recordTypeId)
        ));
        $elementSet->save();
    }
    
    /**
     * Uninstall Scripto.
     */
    public static function uninstall()
    {
        $db = get_db();
        
        // Delete the Scripto element set.
        $db->getTable('ElementSet')->findByName(self::ELEMENT_SET_NAME)->delete();
        
        // Delete options that are specific to Scripto.
        delete_option('scripto_mediawiki_api_url');
        delete_option('scripto_mediawiki_db_name');
        delete_option('scripto_use_openlayers');
        delete_option('scripto_use_google_docs_viewer');
        delete_option('scripto_export_type');
        delete_option('scripto_home_page_text');
    }
    
    /**
     * Appends a warning message to the uninstall confirmation page.
     */
    public static function adminAppendToPluginUninstallMessage()
    {
        echo '<p><strong>Warning</strong>: This will permanently delete the "' 
           . self::ELEMENT_SET_NAME . '" element set and all transcriptions ' 
           . 'imported from MediaWiki. You may deactivate this plugin if you do ' 
           . 'not want to lose data. Uninstalling this plugin will not affect ' 
           . 'your MediaWiki database in any way.</p>';
    }
    
    /**
     * Define routes.
     * 
     * @param Zend_Controller_Router_Rewrite $router
     */
    public static function defineRoutes($router)
    {
        $router->addConfig(new Zend_Config_Ini(dirname(__FILE__) . '/routes.ini', 'routes'));
    }
    
    /**
     * Render the config form.
     */
    public static function configForm()
    {
        // Set form defaults.
        $useOpenlayers = get_option('scripto_use_openlayers');
        if (is_null($useOpenlayers)) {
            $useOpenlayers = 1;
        }
        $useGoogleDocsViewer = get_option('scripto_use_google_docs_viewer');
        if (is_null($useGoogleDocsViewer)) {
            $useGoogleDocsViewer = 0;
        }
        $exportType = get_option('scripto_export_type');
        if (is_null($exportType)) {
            $exportType = 'html';
        }
        
        include 'config_form.php';
    }
    
    /**
     * Handle a submitted config form.
     */
    public static function config()
    {
        // Validate the MediaWiki API URL.
        if (!Scripto::isValidApiUrl($_POST['scripto_mediawiki_api_url'])) {
            throw new Omeka_Validator_Exception('Invalid MediaWiki API URL');
        }
        
        // Validate MediaWiki database name.
        $dbName = trim($_POST['scripto_mediawiki_db_name']);
        if (empty($dbName)) {
            throw new Omeka_Validator_Exception('Invalid MediaWiki database name');
        }
        
        // Set options that are specific to Scripto.
        set_option('scripto_mediawiki_api_url', $_POST['scripto_mediawiki_api_url']);
        set_option('scripto_mediawiki_db_name', $_POST['scripto_mediawiki_db_name']);
        set_option('scripto_use_openlayers', $_POST['scripto_use_openlayers']);
        set_option('scripto_use_google_docs_viewer', $_POST['scripto_use_google_docs_viewer']);
        set_option('scripto_export_type', $_POST['scripto_export_type']);
        set_option('scripto_home_page_text', $_POST['scripto_home_page_text']);
    }
    
    /**
     * Add Scripto to the admin navigation.
     * 
     * @param array $nav
     * @return array
     */
    public static function adminNavigationMain($nav)
    {
        $nav['Scripto'] = uri('scripto');
        return $nav;
    }
    
    /**
     * Add Scripto to the public navigation.
     * 
     * @param array $nav
     * @return array
     */
    public static function publicNavigationMain($nav)
    {
        $nav['Scripto'] = uri('scripto');
        return $nav;
    }
    
    /**
     * add_mime_display_type() callback for image files.
     * 
     * @see Scripto_IndexController::init()
     * @param File $file
     */
    public static function imageViewer($file)
    {
        $imageUrl = $file->getWebPath('archive');
        $imageSize = ScriptoPlugin::getImageSize($imageUrl, 250);
        
        include 'image_viewer.php';
    }
    
    /**
     * add_mime_display_type() callback for document files.
     * 
     * @see Scripto_IndexController::init()
     * @param File $file
     */
    public static function documentViewer($file)
    {
        $uri = Zend_Uri::factory('http://docs.google.com/viewer');
        $uri->setQuery(array('url' => $file->getWebPath('archive'), 
                             'embedded' => 'true'));
        $docsViewerUrl = $uri->getUri();
        
        include 'document_viewer.php';
    }
    
    /**
     * Append the transcribe link to the items show page.
     */
    public static function appendToItemsShow()
    {
        $item = get_current_item();
        $scripto = self::getScripto();
        // Do not show page links if document is not valid.
        if (!$scripto->documentExists($item->id)) {
            return;
        }
        $doc = $scripto->getDocument($item->id);
        
        include 'page_links.php';
    }
    
    public static function adminAppendToFilesForm($file)
    {
        include 'files_form.php';
    }
    
    /**
     * Convenience method to get the Scripto object.
     * 
     * @param string $apiUrl
     * @param string $dbName
     */
    public static function getScripto($apiUrl = null, $dbName = null)
    {
        if (null === $apiUrl) {
            $apiUrl = get_option('scripto_mediawiki_api_url');
        }
        if (null === $dbName) {
            $dbName = get_option('scripto_mediawiki_db_name');
        }
        
        return new Scripto(new ScriptoAdapterOmeka, 
                           array('api_url' => $apiUrl, 'db_name' => $dbName));
    }
    
    /**
     * Return a truncated string with left and right padding.
     * 
     * Primarily used for truncating long document page names that would 
     * otherwise break tables.
     * 
     * @param string $str The string to truncate.
     * @param int $length The trancate length.
     * @param string $default The string to return if the string is empty.
     * @return string
     */
    public static function truncate($str, $length, $default = '')
    {
        $str = trim($str);
        if (empty($str)) {
            return $default;
        }
        if (strlen($str) <= $length) {
            return $str;
        }
        $padding = floor($length / 2);
        return preg_replace('/^(.{' . $padding . '}).*(.{' . $padding . '})$/', '$1... $2', $str);
    }
    
    /**
     * Get dimensions of the provided image.
     * 
     * @param string $filename URI to file.
     * @param int $width Width constraint.
     * @return array
     */
    public static function getImageSize($filename, $width = null)
    {
        $size = getimagesize($filename);
        if (!$size) {
            return false;
        }
        if (is_int($width)) {
            $height = round(($width * $size[1]) / $size[0]);
        } else {
            $width = $size[1];
            $height = $size[0];
        }
        return array('width' => $width, 'height' => $height);
    }
}
