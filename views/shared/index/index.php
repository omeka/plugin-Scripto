<?php
$head = array('title' => html_escape('Scripto'));
head($head);
?>
<h1><?php echo $head['title']; ?></h1>
<div id="primary">
<?php echo flash(); ?>

<!-- navigation -->
<p>
<?php if ($this->scripto->isLoggedIn()): ?>
Logged in as <?php echo $this->scripto->getUserName(); ?> 
(<a href="<?php echo uri('scripto/logout'); ?>">logout</a>) 
 | <a href="<?php echo uri('scripto/your-contributions'); ?>">Your contributions</a> 
<?php else: ?>
<a href="<?php echo uri('scripto/login'); ?>">Log in to Scripto</a>
<?php endif; ?>
 | <a href="<?php echo uri('scripto/recent-changes'); ?>">Recent changes</a>
</p>

<h2>Your Watchlist</h2>

<!-- watchlist -->
<?php if (!$this->scripto->isLoggedIn()): ?>
<p>You must log in to Scripto to view your watchlist.</p>
<?php else: ?>
<?php if (empty($this->watchlist)): ?>
<p>There are no document pages in your watchlist.</p>
<?php else: ?>
<table>
    <thead>
    <tr>
        <th>Changes</th>
        <th>Document Page Name</th>
        <th>Changed on</th>
        <th>Length Changed</th>
        <th>Changed by</th>
        <th>Document Title</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($this->watchlist as $revision): ?>
    <?php
    // changes
    $changes = '';
    if ($revision['new']) {
        $changes .= 'Created';
    } else if (0 == $revision['revision_id']) {
        $changes = 'Un/protected';
    } else {
        $changes .= 'Edited';
    }
    $urlHistory = uri(array(
        'item-id' => $revision['document_id'], 
        'file-id' => $revision['document_page_id'], 
        'namespace-index' => $revision['namespace_index'], 
    ), 'scripto_history');
    $changes .= " (<a href=\"$urlHistory\">hist</a>)";
    
    // document page name
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
        <td><a href="<?php echo $urlTranscribe; ?>"><?php if ('Talk' == $revision['namespace_name']): ?>Talk: <?php endif; ?>doc <?php echo $revision['document_id']; ?>, page <?php echo $revision['document_page_id']; ?></a></td>
        <td><?php echo date('H:i:s M d, Y', strtotime($revision['timestamp'])); ?></td>
        <td><?php echo $lengthChanged; ?></td>
        <td><?php echo $revision['user']; ?></td>
        <td><a href="<?php echo $urlItem; ?>"><?php echo $revision['document_title']; ?></a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
<?php endif; ?>
</div>
<?php foot(); ?>