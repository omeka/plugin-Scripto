<?php
$head = array('title' => html_escape(__('Scripto')));
echo head($head);
?>
<?php if (!is_admin_theme()): ?>
<h1><?php echo $head['title']; ?></h1>
<?php endif; ?>
<div id="primary">
<?php echo flash(); ?>

<div id="scripto-watchlist" class="scripto">
<!-- navigation -->
<p>
<?php echo __('Logged in as %s', '<a href="' . html_escape(url('scripto')) . '">' . $this->scripto->getUserName() . '</a>'); ?> 
(<a href="<?php echo html_escape(url('scripto/index/logout')); ?>"><?php echo __('logout'); ?></a>) 
 | <a href="<?php echo html_escape(url('scripto/recent-changes')); ?>"><?php echo __('Recent changes'); ?></a> 
</p>

<!-- watchlist -->
<h2><?php echo __('Your Watchlist'); ?></h2>
<?php if (empty($this->watchlist)): ?>
<p><?php echo __('There are no document pages in your watchlist.'); ?></p>
<?php else: ?>
<table>
    <thead>
    <tr>
        <th><?php echo __('Changes'); ?></th>
        <th><?php echo __('Document Page Name'); ?></th>
        <th><?php echo __('Changed on'); ?></th>
        <th><?php echo __('Changed'); ?></th>
        <th><?php echo __('Changed by'); ?></th>
        <th><?php echo __('Document Title'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($this->watchlist as $revision): ?>
    <?php
    // changes
    $changes = __($revision['action']);
    $urlHistory = url(array(
        'item-id' => $revision['document_id'], 
        'file-id' => $revision['document_page_id'], 
        'namespace-index' => $revision['namespace_index'], 
    ), 'scripto_history');
    $changes .= ' (<a href="' . html_escape($urlHistory) . '">' . __('hist') . '</a>)';
    
    // document page name
    $documentPageName = ScriptoPlugin::truncate($revision['document_page_name'], 30);
    $urlTranscribe = url(array(
        'action' => 'transcribe', 
        'item-id' => $revision['document_id'], 
        'file-id' => $revision['document_page_id']
    ), 'scripto_action_item_file');
    if (1 == $revision['namespace_index']) {
        $urlTranscribe .= '#discussion';
    } else {
        $urlTranscribe .= '#transcription';
    }
    
    // document title
    $documentTitle = ScriptoPlugin::truncate($revision['document_title'], 30, __('Untitled'));
    $urlItem = url(array(
        'controller' => 'items', 
        'action' => 'show', 
        'id' => $revision['document_id']
    ), 'id');
    
    // length changed
    $lengthChanged = $revision['new_length'] - $revision['old_length'];
    if (0 <= $lengthChanged) {
        $lengthChanged = "+$lengthChanged";
    }
    ?>
    <tr>
        <td><?php echo $changes; ?></td>
        <td><a href="<?php echo html_escape($urlTranscribe); ?>"><?php if ('Talk' == $revision['namespace_name']): ?><?php echo __('Talk'); ?>: <?php endif; ?><?php echo $documentPageName; ?></a></td>
        <td><?php echo format_date(strtotime($revision['timestamp']), Zend_Date::DATETIME_MEDIUM); ?></td>
        <td><?php echo $lengthChanged; ?></td>
        <td><?php echo $revision['user']; ?></td>
        <td><a href="<?php echo html_escape($urlItem); ?>"><?php echo $documentTitle; ?></a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
</div><!-- #scripto-watchlist -->
</div>
<?php echo foot(); ?>
