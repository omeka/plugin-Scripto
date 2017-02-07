<?php
$titleArray = array(__('Scripto'), __('Transcribe Page'));
$head = array('title' => html_escape(implode(' | ', $titleArray)));
echo head($head);
?>
<?php echo js_tag('OpenLayers'); ?>
<?php echo js_tag('jquery', 'javascripts/vendor'); ?>
<script type="text/javascript">
jQuery(document).ready(function() {
    
    // Handle edit transcription page.
    jQuery('#scripto-transcription-page-edit').click(function() {
        jQuery('#scripto-transcription-page-edit').
            prop('disabled', true).
            text('<?php echo __('Editing transcription...'); ?>');
        jQuery.post(
            <?php echo js_escape(url('scripto/index/page-action')); ?>, 
            {
                page_action: 'edit', 
                page: 'transcription', 
                item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>, 
                wikitext: jQuery('#scripto-transcription-page-wikitext').val()
            }, 
            function(data) {
                jQuery('#scripto-transcription-page-edit').
                    prop('disabled', false).
                    text('<?php echo __('Edit transcription'); ?>');
                jQuery('#scripto-transcription-page-html').html(data);
            }
        ).fail(function(error) {
            alert('<?php echo __('There was an error:'); ?>' + "\n\n"
                + error.responseText + "\n\n"
                + '<?php echo __('Save your work elsewhere and try again. Contact the administrator if this error persists.'); ?>');
            jQuery('#scripto-transcription-page-edit').
                prop('disabled', false).
                text('<?php echo __('Edit transcription'); ?>');
        });
    });
    
    // Handle edit talk page.
    jQuery('#scripto-talk-page-edit').click(function() {
        jQuery('#scripto-talk-page-edit').
            prop('disabled', true).
            text('<?php echo __('Editing discussion...'); ?>');
        jQuery.post(
            <?php echo js_escape(url('scripto/index/page-action')); ?>, 
            {
                page_action: 'edit', 
                page: 'talk', 
                item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>, 
                wikitext: jQuery('#scripto-talk-page-wikitext').val()
            }, 
            function(data) {
                jQuery('#scripto-talk-page-edit').
                    prop('disabled', false).
                    text('<?php echo __('Edit discussion'); ?>');
                jQuery('#scripto-talk-page-html').html(data);
            }
        ).fail(function(error) {
            alert('<?php echo __('There was an error:'); ?>' + "\n\n"
                + error.responseText + "\n\n"
                + '<?php echo __('Save your work elsewhere and try again. Contact the administrator if this error persists.'); ?>');
            jQuery('#scripto-talk-page-edit').
                prop('disabled', false).
                text('<?php echo __('Edit discussion'); ?>');
        });
    });
    
    // Handle default transcription/talk visibility.
    if (window.location.hash == '#discussion') {
        jQuery('#scripto-transcription').hide();
        jQuery('#scripto-page-show').text('<?php echo __('show transcription'); ?>');
    } else {
        window.location.hash = '#transcription'
        jQuery('#scripto-talk').hide();
        jQuery('#scripto-page-show').text('<?php echo __('show discussion'); ?>');
    }
    
    // Handle transcription/talk visibility.
    jQuery('#scripto-page-show').click(function(event) {
        event.preventDefault();
        if ('#transcription' == window.location.hash) {
            window.location.hash = '#discussion';
            jQuery('#scripto-transcription').hide();
            jQuery('#scripto-talk').show();
            jQuery('#scripto-page-show').text('<?php echo __('show transcription'); ?>');
        } else {
            window.location.hash = '#transcription';
            jQuery('#scripto-talk').hide();
            jQuery('#scripto-transcription').show();
            jQuery('#scripto-page-show').text('<?php echo __('show discussion'); ?>');
        }
    });
    
    // Toggle show transcription edit.
    jQuery('#scripto-transcription-edit-show').click(function(event) {
        event.preventDefault();
        var clicks = jQuery(this).data('clicks');
        if (!clicks) {
            jQuery(this).text('<?php echo __('hide edit'); ?>');
            jQuery('#scripto-transcription-edit').slideDown('fast');
        } else {
            jQuery(this).text('<?php echo __('edit'); ?>');
            jQuery('#scripto-transcription-edit').slideUp('fast');
        }
        jQuery(this).data("clicks", !clicks);
    });
    
    // Toggle show talk edit.
    jQuery('#scripto-talk-edit-show').click(function(event) {
        event.preventDefault();
        var clicks = jQuery(this).data('clicks');
        if (!clicks) {
            jQuery(this).text('<?php echo __('hide edit'); ?>');
            jQuery('#scripto-talk-edit').slideDown('fast');
        } else {
            jQuery(this).text('<?php echo __('edit'); ?>');
            jQuery('#scripto-talk-edit').slideUp('fast');
        }
        jQuery(this).data("clicks", !clicks);
    });
    
    <?php if ($this->scripto->isLoggedIn()): ?>
    
    // Handle default un/watch page.
    <?php if ($this->doc->isWatchedPage()): ?>
    jQuery('#scripto-page-watch').
        data('watch', true).
        text('<?php echo __('Unwatch page'); ?>').
        css('float', 'none');
    <?php else: ?>
    jQuery('#scripto-page-watch').
        data('watch', false).
        text('<?php echo __('Watch page'); ?>').
        css('float', 'none');
    <?php endif; ?>
    
    // Handle un/watch page.
    jQuery('#scripto-page-watch').click(function() {
        if (!jQuery(this).data('watch')) {
            jQuery(this).prop('disabled', true).text('<?php echo __('Watching page...'); ?>');
            jQuery.post(
                <?php echo js_escape(url('scripto/index/page-action')); ?>, 
                {
                    page_action: 'watch', 
                    page: 'transcription', 
                    item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                }, 
                function(data) {
                    jQuery('#scripto-page-watch').
                        data('watch', true).
                        prop('disabled', false).
                        text('<?php echo __('Unwatch page'); ?>');
                }
            );
        } else {
            jQuery(this).prop('disabled', true).text('<?php echo __('Unwatching page...'); ?>');
            jQuery.post(
                <?php echo js_escape(url('scripto/index/page-action')); ?>, 
                {
                    page_action: 'unwatch', 
                    page: 'transcription', 
                    item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                }, 
                function(data) {
                    jQuery('#scripto-page-watch').
                        data('watch', false).
                        prop('disabled', false).
                        text('<?php echo __('Watch page'); ?>');
                }
            );
        }
    });
    
    <?php endif; // end isLoggedIn() ?>
    
    <?php if ($this->scripto->canProtect()): ?>
    
    // Handle default un/protect transcription page.
    <?php if ($this->doc->isProtectedTranscriptionPage()): ?>
    jQuery('#scripto-transcription-page-protect').
        data('protect', true).
        text('<?php echo __('Unprotect page'); ?>').
        css('float', 'none');
    <?php else: ?>
    jQuery('#scripto-transcription-page-protect').
        data('protect', false).
        text('<?php echo __('Protect page'); ?>').
        css('float', 'none');
    <?php endif; ?>
    
    // Handle un/protect transcription page.
    jQuery('#scripto-transcription-page-protect').click(function() {
        if (!jQuery(this).data('protect')) {
            jQuery(this).prop('disabled', true).text('<?php echo __('Protecting...'); ?>');
            jQuery.post(
                <?php echo js_escape(url('scripto/index/page-action')); ?>, 
                {
                    page_action: 'protect', 
                    page: 'transcription', 
                    item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                }, 
                function(data) {
                    jQuery('#scripto-transcription-page-protect').
                        data('protect', true).
                        prop('disabled', false).
                        text('<?php echo __('Unprotect page'); ?>');
                }
            );
        } else {
            jQuery(this).prop('disabled', true).text('<?php echo __('Unprotecting page...'); ?>');
            jQuery.post(
                <?php echo js_escape(url('scripto/index/page-action')); ?>, 
                {
                    page_action: 'unprotect', 
                    page: 'transcription', 
                    item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                }, 
                function(data) {
                    jQuery('#scripto-transcription-page-protect').
                        data('protect', false).
                        prop('disabled', false).
                        text('<?php echo __('Protect page'); ?>');
                }
            );
        }
    });
    
    // Handle default un/protect talk page.
    <?php if ($this->doc->isProtectedTalkPage()): ?>
    jQuery('#scripto-talk-page-protect').
        data('protect', true).
        text('<?php echo __('Unprotect page'); ?>').
        css('float', 'none');
    <?php else: ?>
    jQuery('#scripto-talk-page-protect').
        data('protect', false).
        text('<?php echo __('Protect page'); ?>').
        css('float', 'none');
    <?php endif; ?>
    
    // Handle un/protect talk page.
    jQuery('#scripto-talk-page-protect').click(function() {
        if (!jQuery(this).data('protect')) {
            jQuery(this).
                prop('disabled', true).
                text('<?php echo __('Protecting page...'); ?>');
            jQuery.post(
                <?php echo js_escape(url('scripto/index/page-action')); ?>, 
                {
                    page_action: 'protect', 
                    page: 'talk', 
                    item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                }, 
                function(data) {
                    jQuery('#scripto-talk-page-protect').
                        data('protect', true).
                        prop('disabled', false).
                        text('<?php echo __('Unprotect page'); ?>');
                }
            );
        } else {
            jQuery(this).prop('disabled', true).text('<?php echo __('Unprotecting page...'); ?>');
            jQuery.post(
                <?php echo js_escape(url('scripto/index/page-action')); ?>, 
                {
                    page_action: 'unprotect', 
                    page: 'talk', 
                    item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                    file_id: <?php echo js_escape($this->doc->getPageId()); ?>
                }, 
                function(data) {
                    jQuery('#scripto-talk-page-protect').
                        data('protect', false).
                        prop('disabled', false).
                        text('<?php echo __('Protect page'); ?>');
                }
            );
        }
    });
    
    <?php endif; // end canProtect() ?>
    <?php if ($this->scripto->canExport()): ?>
    
    jQuery('#scripto-transcription-page-import').click(function() {
        jQuery(this).prop('disabled', true).text('<?php echo __('Importing page...'); ?>');
        jQuery.post(
            <?php echo js_escape(url('scripto/index/page-action')); ?>, 
            {
                page_action: 'import-page', 
                page: 'transcription', 
                item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>
            }, 
            function(data) {
                jQuery('#scripto-transcription-page-import').
                    prop('disabled', false).
                    text('<?php echo __('Import page'); ?>');
            }
        );
    });
    
    jQuery('#scripto-transcription-document-import').click(function() {
        jQuery(this).prop('disabled', true).text('<?php echo __('Importing document...'); ?>');
        jQuery.post(
            <?php echo js_escape(url('scripto/index/page-action')); ?>, 
            {
                page_action: 'import-document', 
                page: 'transcription', 
                item_id: <?php echo js_escape($this->doc->getId()); ?>, 
                file_id: <?php echo js_escape($this->doc->getPageId()); ?>
            }, 
            function(data) {
                jQuery('#scripto-transcription-document-import').
                    prop('disabled', false).
                    text('<?php echo __('Import document'); ?>');
            }
        );
    });
    
    <?php endif; // end canExport() ?>
});
</script>
<?php if (!is_admin_theme()): ?>
<h1><?php echo $head['title']; ?></h1>
<?php endif; ?>
<div id="primary">
<?php echo flash(); ?>

<div id="scripto-transcribe" class="scripto">
<!-- navigation -->
<p>
<?php if ($this->scripto->isLoggedIn()): ?>
<?php echo __('Logged in as %s', '<a href="' . html_escape(url('scripto')) . '">' . $this->scripto->getUserName() . '</a>'); ?> 
(<a href="<?php echo html_escape(url('scripto/index/logout')); ?>"><?php echo __('logout'); ?></a>) 
 | <a href="<?php echo html_escape(url('scripto/watchlist')); ?>"><?php echo __('Your watchlist'); ?></a> 
<?php else: ?>
<a href="<?php echo html_escape(url('scripto/index/login')); ?>"><?php echo __('Log in to Scripto'); ?></a>
<?php endif; ?>
 | <a href="<?php echo html_escape(url('scripto/recent-changes')); ?>"><?php echo __('Recent changes'); ?></a> 
 | <a href="<?php echo html_escape(url(array('controller' => 'items', 'action' => 'show', 'id' => $this->doc->getId()), 'id')); ?>"><?php echo __('View item'); ?></a>
 | <a href="<?php echo html_escape(url(array('controller' => 'files', 'action' => 'show', 'id' => $this->doc->getPageId()), 'id')); ?>"><?php echo __('View file'); ?></a>
</p> 

<h2><?php if ($this->doc->getTitle()): ?><?php echo $this->doc->getTitle(); ?><?php else: ?><?php echo __('Untitled Document'); ?><?php endif; ?></h2>
<?php if ($this->scripto->canExport()): ?><div><?php echo $this->formButton('scripto-transcription-document-import', __('Import document'), array('style' => 'display:inline; float:none;')); ?></div><?php endif; ?>
<h3><?php echo $this->doc->getPageName(); ?></h3>

<!-- document viewer -->
<?php echo file_markup($this->file, array('imageSize' => 'fullsize')); ?>

<!-- pagination -->
<p>
<?php if (isset($this->paginationUrls['previous'])): ?><a href="<?php echo html_escape($this->paginationUrls['previous']); ?>">&#171; <?php echo __('previous page'); ?></a><?php else: ?>&#171; <?php echo __('previous page'); ?><?php endif; ?>
 | <?php if (isset($this->paginationUrls['next'])): ?><a href="<?php echo html_escape($this->paginationUrls['next']); ?>"><?php echo __('next page'); ?> &#187;</a><?php else: ?><?php echo __('next page'); ?> &#187;<?php endif; ?>
 | <a href="#" id="scripto-page-show"></a>
</p>

<!-- transcription -->
<div id="scripto-transcription">
    <?php if ($this->doc->canEditTranscriptionPage()): ?>
    <div id="scripto-transcription-edit" style="display: none;">
        <div><?php echo $this->formTextarea('scripto-transcription-page-wikitext', $this->doc->getTranscriptionPageWikitext(), array('cols' => '76', 'rows' => '16')); ?></div>
        <div>
            <?php echo $this->formButton('scripto-transcription-page-edit', __('Edit transcription'), array('style' => 'display:inline; float:none;')); ?> 
        </div>
        <p><a href="http://www.mediawiki.org/wiki/Help:Formatting" target="_blank"><?php echo __('wiki formatting help'); ?></a></p>
    </div><!-- #scripto-transcription-edit -->
    <?php else: ?>
    <p><?php echo __('You don\'t have permission to transcribe this page.'); ?></p>
    <?php endif; ?>
    <h2><?php echo __('Current Page Transcription'); ?>
    <?php if ($this->doc->canEditTranscriptionPage()): ?> [<a href="#" id="scripto-transcription-edit-show"><?php echo __('edit'); ?></a>]<?php endif; ?> 
    <?php if ($this->scripto->canProtect()): ?> [<a href="<?php echo html_escape($this->doc->getTranscriptionPageMediawikiUrl()); ?>"><?php echo __('wiki'); ?></a>]<?php endif; ?> 
    [<a href="<?php echo html_escape(url(array('item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId(), 'namespace-index' => 0), 'scripto_history')); ?>"><?php echo __('history'); ?></a>]</h2>
    <div>
        <?php if ($this->scripto->isLoggedIn()): ?><?php echo $this->formButton('scripto-page-watch'); ?> <?php endif; ?>
        <?php if ($this->scripto->canProtect()): ?><?php echo $this->formButton('scripto-transcription-page-protect'); ?> <?php endif; ?>
        <?php if ($this->scripto->canExport()): ?><?php echo $this->formButton('scripto-transcription-page-import', __('Import page'), array('style' => 'display:inline; float:none;')); ?><?php endif; ?>
    </div>
    <div id="scripto-transcription-page-html"><?php echo $this->transcriptionPageHtml; ?></div>
</div><!-- #scripto-transcription -->

<!-- discussion -->
<div id="scripto-talk">
    <?php if ($this->doc->canEditTalkPage()): ?>
    <div id="scripto-talk-edit" style="display: none;">
        <div><?php echo $this->formTextarea('scripto-talk-page-wikitext', $this->doc->getTalkPageWikitext(), array('cols' => '76', 'rows' => '16')); ?></div>
        <div>
            <?php echo $this->formButton('scripto-talk-page-edit', __('Edit discussion'), array('style' => 'display:inline; float:none;')); ?> 
        </div>
        <p><a href="http://www.mediawiki.org/wiki/Help:Formatting" target="_blank"><?php echo __('wiki formatting help'); ?></a></p>
    </div><!-- #scripto-talk-edit -->
    <?php else: ?>
    <p><?php echo __('You don\'t have permission to discuss this page.'); ?></p>
    <?php endif; ?>
    <h2><?php echo __('Current Page Discussion'); ?>
    <?php if ($this->doc->canEditTalkPage()): ?> [<a href="#" id="scripto-talk-edit-show"><?php echo __('edit'); ?></a>]<?php endif; ?> 
    <?php if ($this->scripto->canProtect()): ?> [<a href="<?php echo html_escape($this->doc->getTalkPageMediawikiUrl()); ?>"><?php echo __('wiki'); ?></a>]<?php endif; ?>
    [<a href="<?php echo html_escape(url(array('item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId(), 'namespace-index' => 1), 'scripto_history')); ?>"><?php echo __('history'); ?></a>]</h2>
    <div>
        <?php if ($this->scripto->canProtect()): ?><?php echo $this->formButton('scripto-talk-page-protect'); ?> <?php endif; ?>
    </div>
    <div id="scripto-talk-page-html"><?php echo $this->talkPageHtml; ?></div>
</div><!-- #scripto-talk -->

</div><!-- #scripto-transcribe -->
</div>
<?php echo foot(); ?>
