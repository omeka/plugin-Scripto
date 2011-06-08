<?php
$head = array('title' => html_escape('Scripto | Page History'));
head($head);
?>
<h1><?php echo $head['title']; ?></h1>
<div id="primary">
<?php echo flash(); ?>
<!-- navigation -->
<p>
<?php if ($this->scripto->isLoggedIn()): ?>
Logged in as <a href="<?php echo uri('scripto'); ?>"><?php echo $this->scripto->getUserName(); ?></a> 
(<a href="<?php echo uri('scripto/logout'); ?>">logout</a>)
<?php else: ?>
<a href="<?php echo uri('scripto/login'); ?>">Log in to Scripto</a>
<?php endif; ?>
 | <a href="<?php echo uri(array('action' => 'transcribe', 'item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId()), 'scripto_action_item_file'); ?>">Transcribe this document</a></p>

<?php if ($this->doc->getTitle()): ?><h2><?php echo $this->doc->getTitle(); ?></h2><?php endif; ?>
<h3><?php if (1 == $this->namespace_index): ?>Talk: <?php endif; ?><?php echo $this->doc->getPageTitle(); ?></h3>

<?php if (empty($this->history)): ?>
<p>This page has not yet been created.</p>
<?php else: ?>
<table>
    <thead>
    <tr>
        <th>Compare Changes</th>
        <th>Changed on</th>
        <th>Changed by</th>
        <th>Size (bytes)</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($this->history as $revision): ?>
    <?php
    $urlCurrent = uri(array('action' => 'diff', 
                            'revid' => $this->info['last_revision_id'], 
                            'oldrevid' => $revision['revision_id']));
    $urlPrevious = uri(array('action' => 'diff', 
                             'revid' => $revision['revision_id'], 
                             'oldrevid' => $revision['parent_id']));
    ?>
    <tr>
        <td>(<?php if ($revision['revision_id'] != $this->info['last_revision_id']): ?><a href="<?php echo $urlCurrent; ?>">current</a><?php else: ?>current<?php endif; ?> | <?php if (0 != $revision['parent_id']): ?><a href="<?php echo $urlPrevious; ?>">previous</a><?php else: ?>previous<?php endif; ?>)</td>
        <td><?php echo date('H:i:s M d, Y', strtotime($revision['timestamp'])); ?></td>
        <td><?php echo $revision['user']; ?></td>
        <td><?php echo $revision['size']; ?></td>
        <td><?php echo $revision['action']; ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
</div>
<?php foot(); ?>