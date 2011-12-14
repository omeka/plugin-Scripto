<p>This plugin requires you to download and install <a href="http://www.mediawiki.org/wiki/MediaWiki">MediaWiki</a>, 
a popular free web-based wiki software application that Scripto uses to manage 
user and transcription data. Once you have successfully installed MediaWiki, you 
can complete the following form and install the plugin.</p>
<p>Scripto will assume files belonging to an item are in logical order, first to 
last page.</p>
<div class="field">
    <label for="scripto_mediawiki_api_url" class="required">MediaWiki API URL</label>
    <div class="inputs">
        <?php echo __v()->formText('scripto_mediawiki_api_url', 
                                   get_option('scripto_mediawiki_api_url'), 
                                   array('size' => 50)); ?>
        <p class="explanation">URL to your <a href="http://www.mediawiki.org/wiki/API:Quick_start_guide#What_you_need_to_access_the_API">MediaWiki installation API</a>.</p>
    </div>
</div>
<div class="field">
    <label for="scripto_use_openlayers">Image viewer</label>
    <div class="inputs">
        <?php echo __v()->formRadio('scripto_image_viewer', 
                                    $imageViewer, 
                                    null, 
                                    array('openlayers' => 'OpenLayers', 
                                          'zoomit' => 'Zoom.it', 
                                          'default' => 'Omeka default'), 
                                    null); ?> 
        <p class="explanation">Select an image viewer to use when transcribing 
        image files. <a href="http://openlayers.org/">OpenLayers</a> and 
        <a href="http://zoom.it/">Zoom.it</a> can display JPEG, PNG, GIF, and 
        BMP formats. Zoom.it can also display TIFF and ICO formats. By using 
        Zoom.it you awknowledge that you have read and agreed to the 
        <a href="http://zoom.it/pages/terms/">Microsoft Zoom.it Terms of Service</a>.
        </p>
    </div>
</div>
<div class="field">
    <label for="scripto_use_google_docs_viewer">Use Google Docs Viewer?</label>
    <div class="inputs">
        <?php echo __v()->formCheckbox('scripto_use_google_docs_viewer', 
                                       null, 
                                       array('checked' => (bool) $useGoogleDocsViewer)); ?>
        <p class="explanation">Use Google Docs Viewer when transcribing document 
        files? Document files include PDF, DOC, PPT, XLS, TIFF, PS, and PSD 
        formats. By using this service you acknowledge that you have read and 
        agreed to the <a href="http://docs.google.com/viewer/TOS?hl=en">Google 
        Docs Viewer Terms of Service</a>.</p>
    </div>
</div>
<div class="field">
    <label for="scripto_import_type">Import type</label>
    <div class="inputs">
        <?php echo __v()->formRadio('scripto_import_type', 
                                    $importType, 
                                    null, 
                                    array('html' => 'HTML', 
                                          'plain_text' => 'plain text'), 
                                    null); ?>
        <p class="explanation">Import transcriptions as HTML or plain text? 
        Importing will copy document and page transcriptions from MediaWiki to 
        their corresponding items and files in Omeka. Choose HTML if you want to 
        preserve formatting. Choose plain text if formatting is not important.</p>
    </div>
</div>
<div class="field">
    <label for="scripto_home_page_text">Home page text</label>
        <div class="inputs">
        <?php echo __v()->formTextarea('scripto_home_page_text', 
                                       get_option('scripto_home_page_text'), 
                                       array('cols' => '64', 'rows' => '12')); ?>
        <p class="explanation">Enter text that will appear on the Scripto home 
        page. Use this to display custom messages to your users, such as 
        instructions on how to use Scripto and how to register for a MediaWiki 
        account. Default text will appear if nothing is entered. You may use 
        HTML. (Wrapping &lt;p&gt;&lt;/p&gt; tags recommended.)</p>
    </div>
</div>