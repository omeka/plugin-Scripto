<script type="text/javascript">
// Set the OpenLayers image viewer.
jQuery(document).ready(function() {
    var scriptoMap = new OpenLayers.Map('scripto-map');
    var graphic = new OpenLayers.Layer.Image(
        'Document Page',
        <?php echo js_escape($imageUrl); ?>,
        new OpenLayers.Bounds(-<?php echo $imageSize['width']; ?>, -<?php echo $imageSize['height']; ?>, <?php echo $imageSize['width']; ?>, <?php echo $imageSize['height']; ?>),
        new OpenLayers.Size(<?php echo $imageSize['width']; ?>, <?php echo $imageSize['height']; ?>)
    );
    scriptoMap.addLayers([graphic]);
    scriptoMap.zoomToMaxExtent();
});
</script>
<!-- document page viewer -->
<div id="scripto-map" style="height: 300px; border: 1px grey solid; margin-bottom: 12px;"></div>