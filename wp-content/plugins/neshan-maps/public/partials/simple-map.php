<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://platform.neshan.org
 * @since      1.0.0
 *
 * @package    Neshan_Maps
 * @subpackage Neshan_Maps/public/partials
 */
?>

<script type="text/javascript">
    var neshan_<?php echo $id ?>;

    function initNeshanMap<?php echo $id ?>() {
        var layer, source, map, center;

        map = neshan_<?php echo $id ?> = new ol.Map({
            target: 'neshan_map_<?php echo $id; ?>',
            maptype: '<?php echo $data->maptype; ?>',
            key: '<?php echo $data->api_key; ?>',
            view: new ol.View({
                center: ol.proj.fromLonLat([<?php echo $data->lng; ?>, <?php echo $data->lat; ?>]),
                zoom: <?php echo $data->zoom; ?>,
                minZoom: 5,
                maxZoom: 19,
                extent: [4891969.8103, 2856910.3692, 7051774.4815, 4847942.0820]
            })
        });

        source = new ol.source.Vector();

        layer = new ol.layer.Vector({
            source: source
        });

        center = new ol.Feature();

        center.setStyle(new ol.style.Style({
            image: new ol.style.Icon({
                anchor: [0.5, 1],
                anchorXUnits: 'fraction',
                anchorYUnits: 'fraction',
                crossOrigin: 'anonymous',
                opacity: 1,
                src: '<?php echo plugin_dir_url(__FILE__) ?>' + '/../../images/marker_red.png'
            })
        }));

        source.addFeature(center);

        center.setGeometry(new ol.geom.Point(map.getView().getCenter()));

        map.addLayer(layer);
    }
</script>