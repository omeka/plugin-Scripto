<?php
$head = array('title' => html_escape(__('Scripto')));
echo head($head);
?>
<?php if (!is_admin_theme()): ?>
<h1><?php echo $head['title']; ?></h1>
<?php endif; ?>
<div id="primary">
<?php echo flash(); ?>

<div id="scripto-recent-changes" class="scripto">
<!-- navigation -->
<p>
<?php if ($this->scripto->isLoggedIn()): ?>
<?php echo __('Logged in as %s', '<a href="' . html_escape(url('scripto')) . '">' . $this->scripto->getUserName() . '</a>'); ?> 
(<a href="<?php echo html_escape(url('scripto/index/logout')); ?>"><?php echo __('logout'); ?></a>) 
 | <a href="<?php echo html_escape(url('scripto/watchlist')); ?>"><?php echo __('Your watchlist'); ?></a> 
<?php else: ?>
<a href="<?php echo html_escape(url('scripto/index/login')); ?>"><?php echo __('Log in to Scripto'); ?></a>
<?php endif; ?>
</p>

<!-- recent changes -->
<h2><?php echo __('Recent Changes'); ?></h2>
<?php if (empty($this->recentChanges)): ?>
<p><?php echo __('There are no recent changes.'); ?></p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th><?php echo __('Changes'); ?></th>
            <th><?php echo __('Document Page Name'); ?></th>
            <th><?php echo __('Changed on'); ?></th>
            <th><?php echo __('Changed'); ?></th>
            <th><?php echo __('Changed By'); ?></th>
            <th><?php echo __('Document Title'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php $types = array('new' => 'Created', 'edit' => 'Edited'); ?>
    <?php foreach ($this->recentChanges as $recentChange): ?>
    <?php
    $changes = __($recentChange['action']);
        $urlDiff = url(array(
            'item-id' => $recentChange['document_id'], 
            'file-id' => $recentChange['document_page_id'], 
            'namespace-index' => $recentChange['namespace_index'], 
            'old-revision-id' => $recentChange['old_revision_id'], 
            'revision-id' => $recentChange['revision_id'], 
        ), 'scripto_diff');
        $urlHistory = url(array(
            'item-id' => $recentChange['document_id'], 
            'file-id' => $recentChange['document_page_id'], 
            'namespace-index' => $recentChange['namespace_index'], 
        ), 'scripto_history');
        if ($recentChange['new'] || in_array($recentChange['action'], array('protected', 'unprotected'))) {
            $changes .= ' (' . __('diff') . ' | <a href="' . html_escape($urlHistory) . '">' . __('hist') . '</a>)';
        } else {
            $changes .= ' (<a href="' . html_escape($urlDiff) . '">' . __('diff') . '</a> | <a href="' . html_escape($urlHistory) . '">' . __('hist') . '</a>)';
        }
    
    // document page name
    $documentPageName = ScriptoPlugin::truncate($recentChange['document_page_name'], 30);
    $urlTranscribe = url(array(
        'action' => 'transcribe', 
        'item-id' => $recentChange['document_id'], 
        'file-id' => $recentChange['document_page_id']
    ), 'scripto_action_item_file');
    if (1 == $recentChange['namespace_index']) {
        $urlTranscribe .= '#discussion';
    } else {
        $urlTranscribe .= '#transcription';
    }
    
    // document title
    $documentTitle = ScriptoPlugin::truncate($recentChange['document_title'], 30, __('Untitled'));
    $urlItem = url(array(
        'controller' => 'items', 
        'action' => 'show', 
        'id' => $recentChange['document_id']
    ), 'id');
    
    // length changed
    $lengthChanged = $recentChange['new_length'] - $recentChange['old_length'];
    if (0 <= $lengthChanged) {
        $lengthChanged = "+$lengthChanged";
    }
    ?>
    <tr>
        <td><?php echo $changes; ?></td>
        <td><a href="<?php echo html_escape($urlTranscribe); ?>"><?php if (1 == $recentChange['namespace_index']): ?><?php echo __('Talk'); ?>: <?php endif; ?><?php echo $documentPageName; ?></a></td>
        <td><?php echo format_date(strtotime($recentChange['timestamp']), Zend_Date::DATETIME_MEDIUM); ?></td>
        <td><?php echo $lengthChanged; ?></td>
        <td><?php echo $recentChange['user']; ?></td>
        <td><a href="<?php echo html_escape($urlItem); ?>"><?php echo $documentTitle; ?></a></td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
</div><!-- #scripto-recent-changes -->
</div>
<?php echo foot(); ?>
