<p><?php echo __(
    'This plugin requires you to download and install %1$sMediaWiki%2$s, a popular free ' 
  . 'web-based wiki software application that Scripto uses to manage user and transcription ' 
  . 'data. Once you have successfully installed MediaWiki, you can complete the following ' 
  . 'form and install the plugin.', 
    '<a href="http://www.mediawiki.org/wiki/MediaWiki">', '</a>'
); ?></p>

<p><?php echo __(
    'Scripto will assume files belonging to an item are in logical order, first to last page.'
); ?></p>

<div class="field">
    <div class="two columns alpha">
        <label for="scripto_mediawiki_api_url"><?php echo __('MediaWiki API URL'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'URL to your %1$sMediaWiki installation API%2$s.', 
            '<a href="http://www.mediawiki.org/wiki/API:Quick_start_guide#What_you_need_to_access_the_API">', '</a>'
        ); ?></p>
        <?php echo $this->formText(
            'scripto_mediawiki_api_url', 
            get_option('scripto_mediawiki_api_url')
        ); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="scripto_mediawiki_cookie_prefix"><?php echo __('MediaWiki cookie prefix'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'Enter your %sMediaWiki cookie prefix%s. This is most likely your MediaWiki database name. Only required for MediaWiki installations since 1.27.0.',
            '<a href="https://www.mediawiki.org/wiki/Manual:$wgCookiePrefix">',
            '</a>'); ?>
        </p>
        <?php echo $this->formText(
            'scripto_mediawiki_cookie_prefix',
            get_option('scripto_mediawiki_cookie_prefix')
        ); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="scripto_image_viewer"><?php echo __('Image viewer'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'Select an image viewer to use when transcribing image files. %1$sOpenLayers%2$s '
          . 'can display JPEG, PNG, GIF, and BMP formats.',
            '<a href="http://openlayers.org/">', '</a>'
        ); ?></p>
        <?php echo $this->formRadio(
            'scripto_image_viewer', 
            $this->image_viewer, 
            null, 
            array('default' => __('Omeka default'), 
                  'openlayers' => __('OpenLayers')),
            null
        ); ?> 
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="scripto_use_google_docs_viewer"><?php echo __('Use Google Docs Viewer?'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'Use Google Docs Viewer when transcribing document files? Document files ' 
          . 'include PDF, DOC, PPT, XLS, TIFF, PS, and PSD formats. By using this service ' 
          . 'you acknowledge that you have read and agreed to the %1$sGoogle Terms of Service%2$s.', 
          '<a href="https://www.google.com/intl/en/policies/terms/">', '</a>'
        ); ?></p>
        <?php echo $this->formCheckbox(
            'scripto_use_google_docs_viewer', 
            null, 
            array('checked' => (bool) $this->use_google_docs_viewer)
        ); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="scripto_import_type"><?php echo __('Import type'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'Import transcriptions as HTML or plain text? Importing will copy document ' 
          . 'and page transcriptions from MediaWiki to their corresponding items and ' 
          . 'files in Omeka. Choose HTML if you want to preserve formatting. Choose ' 
          . 'plain text if formatting is not important.'
        ); ?></p>
        <?php echo $this->formRadio(
            'scripto_import_type', 
            $this->import_type, 
            null, 
            array('html' => __('HTML'), 
                  'plain_text' => __('plain text')), 
            null
        ); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="scripto_home_page_text"><?php echo __('Home page text'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'Enter text that will appear on the Scripto home page. Use this to display ' 
          . 'custom messages to your users, such as instructions on how to use Scripto ' 
          . 'and how to register for a MediaWiki account. Default text will appear if ' 
          . 'nothing is entered. You may use HTML. (Wrapping %s tags recommended.)', 
          '&lt;p&gt;&lt;/p&gt;'
        ); ?></p>
        <?php echo $this->formTextarea(
            'scripto_home_page_text', 
            get_option('scripto_home_page_text')
        ); ?>
    </div>
</div>
