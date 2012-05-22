<?php
$head = array('title' => html_escape('Scripto | Login'));
head($head);
?>
<h1><?php echo $head['title']; ?></h1>
<div id="primary">
<?php echo flash(); ?>

<div id="scripto-login" class="scripto">
<!-- navigation -->
<p><a href="<?php echo html_escape(uri('scripto/index/recent-changes')); ?>">Recent changes</a></p>
<p>Log in to Scripto using your MediaWiki username and password to access your 
account and enable certain Scripto features. Login may not be required by the 
administrator.</p>

<!-- login -->
<form action="<?php echo uri('scripto/index/login'); ?>" method="post">
<div class="field">
    <label for="scripto_mediawiki_username">Username</label>
        <div class="inputs">
        <?php echo $this->formText('scripto_mediawiki_username', null, array('size' => 18)); ?>
    </div>
</div>
<div class="field">
    <label for="scripto_mediawiki_password">Password</label>
        <div class="inputs">
        <?php echo $this->formPassword('scripto_mediawiki_password', null, array('size' => 18)); ?>
    </div>
</div>
<?php echo $this->formHidden('scripto_redirect_url', $this->redirectUrl); ?>
<?php echo $this->formSubmit('scripto_mediawiki_login', 'Login', array('style' => 'display:inline; float:none;')); ?>
</form>
</div><!-- #scripto-login -->
</div>
<?php foot(); ?>