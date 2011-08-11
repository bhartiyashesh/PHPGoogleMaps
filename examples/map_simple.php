<?php

require( '../PHPGoogleMaps/Core/Autoloader.php' );
$map_loader = new SplClassLoader('PHPGoogleMaps', '../');
$map_loader->register();

$map = new \PHPGoogleMaps\Map;

$marker1_options = array(
	'title'	=> 'New York, NY',
	'content'	=> '<p><strong>New York, NY info window</strong></p>'
);
$marker1 = \PHPGoogleMaps\Overlay\Marker::createFromLocation( 'New York, NY', $marker1_options );

$map->addObject( $marker1 );
$map->disableAutoEncompass();
$map->setZoom( 10 );
$map->setCenter( $marker1->getLatLng() );
$map->enableStreetView();
$map->printHeaderJS();
$map->printMapJS();
$map->printMap();

?>
