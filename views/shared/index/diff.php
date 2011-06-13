<?php
$head = array('title' => html_escape('Scripto | Page Difference'));
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

<!-- navigation -->
<p>
<?php if ($this->scripto->isLoggedIn()): ?>
Logged in as <a href="<?php echo uri('scripto'); ?>"><?php echo $this->scripto->getUserName(); ?></a> 
(<a href="<?php echo uri('scripto/index/logout'); ?>">logout</a>) 
 | <a href="<?php echo uri('scripto/watchlist'); ?>">Your watchlist</a> 
<?php else: ?>
<a href="<?php echo uri('scripto/index/login'); ?>">Log into Scripto</a>
<?php endif; ?>
 | <a href="<?php echo uri(array('action' => 'transcribe', 'item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId()), 'scripto_action_item_file'); ?>">Transcribe this document</a>
 | <a href="<?php echo uri(array('item-id' => $this->doc->getId(), 'file-id' => $this->doc->getPageId(), 'namespace-index' => $this->namespace_index), 'scripto_history'); ?>">Page history</a>
</p> 

<?php if ($this->doc->getTitle()): ?><h2><?php echo $this->doc->getTitle(); ?></h2><?php endif; ?>
<h3><?php if (1 == $this->namespace_index): ?>Talk: <?php endif; ?><?php echo $this->doc->getPageName(); ?></h3>

<!-- difference -->
<table id="scripto-diff"><?php echo $this->diff; ?></table>
</div>
<?php foot(); ?>