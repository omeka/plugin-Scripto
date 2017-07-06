<?php
/**
 * Collection Tree
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Scripto plugin.
 * 
 * @package Omeka\Plugins\Scripto
 */
class ScriptoPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * The name of the Scripto element set.
     */
    const ELEMENT_SET_NAME = 'Scripto';
    
    protected $_hooks = array(
        'initialize', 
        'install', 
        'uninstall', 
        'uninstall_message', 
        'define_routes', 
        'config_form', 
        'config', 
        'public_items_show', 
        'admin_items_show', 
    );
    
    protected $_filters = array(
        'admin_navigation_main', 
        'public_navigation_main', 
    );
    
    /**
     * @var MIME types compatible with OpenLayers.
     */
    public static $fileIdentifiersOpenLayers = array(
        'mimeTypes' => array(
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
        'fileExtensions' => array(
            'gif', 'jpeg', 'jpg', 'jpe', 'png', 'bmp', 
        ), 
    );
    
    /**
     * @var MIME types compatible with Google Docs viewer.
     */
    public static $fileIdentifiersGoogleDocs = array(
        'mimeTypes' => array(
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
        ), 
        'fileExtensions' => array(
            'pdf', 
            'docx', 
            'doc', 'dot', 
            'ppt', 'pps', 'pot', 
            'pptx', 
            'xls', 'xlm', 'xla', 'xlc', 'xlt', 'xlw', 
            'xlsx', 
            'tiff', 'tif', 
            'ai', 'eps', 'ps', 
            'psd', 
            'dxf', 
            'xvg', 
            'xps', 
        ), 
    );
    
    /**
     * Initialize Scripto.
     */
    public function hookInitialize()
    {
        // Add translation.
        add_translation_source(dirname(__FILE__) . '/languages');
    }
    
    /**
     * Install Scripto.
     */
    public function hookInstall()
    {
        // Don't install if an element set by the name "Scripto" already exists.
        if ($this->_db->getTable('ElementSet')->findByName(self::ELEMENT_SET_NAME)) {
            throw new Omeka_Plugin_Installer_Exception(
                __('An element set by the name "%s" already exists. You must delete '
                 . 'that element set to install this plugin.', self::ELEMENT_SET_NAME)
            );
        }
        
        $elementSetMetadata = array('name' => self::ELEMENT_SET_NAME);
        $elements = array(
            array('name' => 'Transcription', 
                  'description' => 'A written representation of a document.')
        );
        insert_element_set($elementSetMetadata, $elements);
    }
    
    /**
     * Uninstall Scripto.
     */
    public function hookUninstall()
    {
        // Delete the Scripto element set.
        $this->_db->getTable('ElementSet')->findByName(self::ELEMENT_SET_NAME)->delete();
        
        // Delete options that are specific to Scripto.
        delete_option('scripto_mediawiki_api_url');
        delete_option('scripto_image_viewer');
        delete_option('scripto_use_google_docs_viewer');
        delete_option('scripto_import_type');
        delete_option('scripto_home_page_text');
    }
    
    /**
     * Appends a warning message to the uninstall confirmation page.
     */
    public function hookUninstallMessage()
    {
        echo '<p>' . __(
            '%1$sWarning%2$s: This will permanently delete the "%3$s" element set and ' 
          . 'all transcriptions imported from MediaWiki. You may deactivate this ' 
          . 'plugin if you do not want to lose data. Uninstalling this plugin will ' 
          . 'not affect your MediaWiki database in any way.', 
            '<strong>', '</strong>', self::ELEMENT_SET_NAME) . '</p>';
    }
    
    /**
     * Define routes.
     * 
     * @param Zend_Controller_Router_Rewrite $router
     */
    public function hookDefineRoutes($args)
    {
        $args['router']->addConfig(new Zend_Config_Ini(dirname(__FILE__) . '/routes.ini', 'routes'));
    }
    
    /**
     * Render the config form.
     */
    public function hookConfigForm()
    {
        // Set form defaults.
        $imageViewer = get_option('scripto_image_viewer');
        if (!in_array($imageViewer, array('openlayers'))) {
            $imageViewer = 'default';
        }
        $useGoogleDocsViewer = get_option('scripto_use_google_docs_viewer');
        if (is_null($useGoogleDocsViewer)) {
            $useGoogleDocsViewer = 0;
        }
        $importType = get_option('scripto_import_type');
        if (is_null($importType)) {
            $importType = 'html';
        }
        
        echo get_view()->partial(
            'plugins/scripto-config-form.php', 
            array('image_viewer' => $imageViewer, 
                  'use_google_docs_viewer' => $useGoogleDocsViewer,  
                  'import_type' => $importType)
        );
    }
    
    /**
     * Handle a submitted config form.
     */
    public function hookConfig()
    {
        // Validate the MediaWiki API URL.
        if (!Scripto::isValidApiUrl($_POST['scripto_mediawiki_api_url'])) {
            throw new Omeka_Plugin_Installer_Exception('Invalid MediaWiki API URL');
        }
        
        // Set options that are specific to Scripto.
        set_option('scripto_mediawiki_api_url', $_POST['scripto_mediawiki_api_url']);
        set_option('scripto_mediawiki_cookie_prefix', $_POST['scripto_mediawiki_cookie_prefix']);
        set_option('scripto_image_viewer', $_POST['scripto_image_viewer']);
        set_option('scripto_use_google_docs_viewer', $_POST['scripto_use_google_docs_viewer']);
        set_option('scripto_import_type', $_POST['scripto_import_type']);
        set_option('scripto_home_page_text', $_POST['scripto_home_page_text']);
    }
    
    
    /**
     * Append the transcribe link to the public items show page.
     */
    public function hookPublicItemsShow()
    {
        $this->_appendToItemsShow();
    }
    
    /**
     * Append the transcribe link to the admin items show page.
     */
    public function hookAdminItemsShow()
    {
        $this->_appendToItemsShow();
    }
    
    /**
     * Add Scripto to the admin navigation.
     * 
     * @param array $nav
     * @return array
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array('label' => __('Scripto'), 'uri' => url('scripto'));
        return $nav;
    }
    
    /**
     * Add Scripto to the public navigation.
     * 
     * @param array $nav
     * @return array
     */
    public function filterPublicNavigationMain($nav)
    {
        $nav[] = array('label' => __('Scripto'), 'uri' => url('scripto'));
        return $nav;
    }
    
    /**
     * Append the transcribe link to the items show page.
     */
    protected function _appendToItemsShow()
    {
        $item = get_current_record('item');
        $scripto = self::getScripto();
        // Do not show page links if document is not valid.
        if (!$scripto->documentExists($item->id)) {
            return;
        }
        $doc = $scripto->getDocument($item->id);
?>
<h2><?php echo __('Transcribe This Item'); ?></h2>
<ol>
    <?php foreach ($doc->getPages() as $pageId => $pageName): ?>
    <li><a href="<?php echo url(array('action' => 'transcribe', 
                                      'item-id' => $item->id, 
                                      'file-id' => $pageId), 
                                'scripto_action_item_file'); ?>" id="scripto-transcribe-item"><?php echo $pageName; ?></a></li>
    <?php endforeach; ?>
</ol>
<?php
    }
    
    /**
     * add_file_display_callback() callback for OpenLayers.
     * 
     * @see Scripto_IndexController::init()
     * @param File $file
     */
    public static function openLayers($file)
    {
        $imageUrl = $file->getWebPath('original');
        $imageSize = ScriptoPlugin::getImageSize($imageUrl, 250);
        
?>
<script type="text/javascript">
jQuery(document).ready(function() {
    var scriptoMap = new OpenLayers.Map('scripto-openlayers');
    var graphic = new OpenLayers.Layer.Image(
        'Document Page',
        <?php echo js_escape($imageUrl); ?>,
        new OpenLayers.Bounds(-<?php echo $imageSize['width']; ?>, -<?php echo $imageSize['height']; ?>, <?php echo $imageSize['width']; ?>, <?php echo $imageSize['height']; ?>),
        new OpenLayers.Size(<?php echo $imageSize['width']; ?>, <?php echo $imageSize['height']; ?>)
    );
    scriptoMap.addLayers([graphic]);
    scriptoMap.zoomToMaxExtent();
});
</script>
<div id="scripto-openlayers" style="height: 400px; border: 1px grey solid; margin-bottom: 12px;"></div>
<?php
    }
    
    /**
     * add_file_display_callback() callback for Google Docs.
     * 
     * @see Scripto_IndexController::init()
     * @param File $file
     */
    public static function googleDocs($file)
    {
        $uri = Zend_Uri::factory('https://docs.google.com/viewer');
        $uri->setQuery(array('url' => $file->getWebPath('original'), 
                             'embedded' => 'true'));
        echo '<iframe src="' . $uri->getUri() . '" width="500" height="600" style="border: none;"></iframe>';
    }
    
    /**
     * Convenience method to get the Scripto object.
     * 
     * @param string $apiUrl
     */
    public static function getScripto($apiUrl = null)
    {
        if (null === $apiUrl) {
            $apiUrl = get_option('scripto_mediawiki_api_url');
        }
        $cookiePrefix = get_option('scripto_mediawiki_cookie_prefix');
        
        return new Scripto(new ScriptoAdapterOmeka, array(
            'api_url' => $apiUrl,
            'cookie_prefix' => $cookiePrefix ? $cookiePrefix : null,
        ));
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
