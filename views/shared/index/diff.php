<?php
$head = array('title' => html_escape('Scripto | Revision Difference'));
head($head);
?>
<style type="text/css">
#scripto-diff tr {border: none !important;}
#scripto-diff td {padding: 2px !important;}
td.diff-marker {width: 10px;}
td.diff-deletedline {background-color: #FFEDED;}
td.diff-addedline {background-color: #EDFFEF;}
ins.diffchange {background-color: #BDFFC8;}
del.diffchange {background-color: #FFBDBD;}
</style>
<h1><?php echo $head['title']; ?></h1>
<div id="primary">
<?php echo flash(); ?>

<div id="scripto-diff" class="scripto">
<!-- navigation -->
<p>
<?php if ($this->scripto->isLoggedIn()): ?>
Logged in as <a href="<?php echo uri('scripto'); ?>"><?php echo $this->scripto->getUserName(); ?></a> 
(<a href="<?php echo uri('scripto/index/logout'); ?>">logout</a>) 
 | <a href="<?php echo uri('scripto/watchlist'); ?>">Your watchlist</a> 
<?php else: ?>
<a href="<?php echo uri('scripto/index/login'); ?>">Log into Scripto</a>
<?php endif; ?>
 | <a href="<?php echo uri('scripto/recent-changes'); ?>">Recent changes</a> 
 | <a href="<?php echo uri(array('controller' => 'items', 'action' => 'show', 'id' => $this->doc->getId()), 'id'); ?>">View item</a>
 | <a href="<?php echo uri(array('action' => 'transcribe', 'item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId()), 'scripto_action_item_file'); ?>#<?php if (1 == $this->namespaceIndex): ?>discussion<?php else: ?>transcription<?php endif; ?>">Transcribe document</a>
 | <a href="<?php echo uri(array('item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId(), 'namespace-index' => $this->namespaceIndex), 'scripto_history'); ?>">Page history</a>
</p> 

<?php if ($this->doc->getTitle()): ?><h2><?php echo $this->doc->getTitle(); ?></h2><?php endif; ?>
<h3><?php if (1 == $this->namespaceIndex): ?>Talk: <?php endif; ?><?php echo $this->doc->getPageName(); ?></h3>

<!-- difference -->
<?php
$actions = array('Protected', 'Unprotected', 'Created');
$pattern = '/^(' . implode('|', $actions) . ').+$/';
$actionOldRevision = preg_replace($pattern, '$1', $this->oldRevision['comment']);
$actionRevision = preg_replace($pattern, '$1', $this->revision['comment']);
?>
<table>
    <thead>
    <tr>
        <th colspan="2">Revision as of <?php echo date('H:i:s, M d, Y', strtotime($this->oldRevision['timestamp'])); ?><br />
        <?php echo $actionOldRevision; ?> by <?php echo $this->oldRevision['user']; ?></th>
        <th colspan="2">Revision as of <?php echo date('H:i:s, M d, Y', strtotime($this->revision['timestamp'])); ?><br />
        <?php echo $actionRevision; ?> by <?php echo $this->revision['user']; ?></th>
    </tr>
    </thead>
    <tbody>
    <?php echo $this->diff; ?>
    </tbody>
</table>
<h2>Revision as of <?php echo date('H:i:s, M d, Y', strtotime($this->revision['timestamp'])); ?></h2>
<div><?php echo $this->revision['html']; ?></div>
</div><!-- end #scripto-diff -->
</div>
<?php foot(); ?>