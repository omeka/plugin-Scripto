<?php
$head = array('title' => html_escape('Scripto'));
head($head);
?>
<h1><?php echo $head['title']; ?></h1>
<div id="primary">
<?php echo flash(); ?>

<div id="scripto-watchlist" class="scripto">
<!-- navigation -->
<p>
Logged in as <a href="<?php echo html_escape(uri('scripto')); ?>"><?php echo $this->scripto->getUserName(); ?></a> 
(<a href="<?php echo html_escape(uri('scripto/logout')); ?>">logout</a>) 
 | <a href="<?php echo html_escape(uri('scripto/recent-changes')); ?>">Recent changes</a>
</p>

<!-- watchlist -->
<h2>Your Watchlist</h2>
<?php if (empty($this->watchlist)): ?>
<p>There are no document pages in your watchlist.</p>
<?php else: ?>
<table>
    <thead>
    <tr>
        <th>Changes</th>
        <th>Document Page Name</th>
        <th>Changed on</th>
        <th>Changed</th>
        <th>Changed by</th>
        <th>Document Title</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($this->watchlist as $revision): ?>
    <?php
    // changes
    $changes = ucfirst($revision['action']);
    $urlHistory = uri(array(
        'item-id' => $revision['document_id'], 
        'file-id' => $revision['document_page_id'], 
        'namespace-index' => $revision['namespace_index'], 
    ), 'scripto_history');
    $changes .= ' (<a href="' . html_escape($urlHistory) . '">hist</a>)';
    
    // document page name
    $documentPageName = ScriptoPlugin::truncate($revision['document_page_name'], 30);
    $urlTranscribe = uri(array(
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
    $documentTitle = ScriptoPlugin::truncate($revision['document_title'], 30, 'Untitled');
    $urlItem = uri(array(
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
        <td><a href="<?php echo html_escape($urlTranscribe); ?>"><?php if ('Talk' == $revision['namespace_name']): ?>Talk: <?php endif; ?><?php echo $documentPageName; ?></a></td>
        <td><?php echo date('H:i:s M d, Y', strtotime($revision['timestamp'])); ?></td>
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
<?php foot(); ?>