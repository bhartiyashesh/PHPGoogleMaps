<?php

// Autoloader stuff
require( '../PHPGoogleMaps/Core/Autoloader.php' );
$map_loader = new SplClassLoader('PHPGoogleMaps', '../');
$map_loader->register();

// This is just for my examples
require( '_system/config.php' );
$relevant_code = array(
	'\PHPGoogleMaps\Service\Geocoder',
	'\PHPGoogleMaps\Service\GeocodeError',
	'\PHPGoogleMaps\Service\GeocodeResult',
	'\PHPGoogleMaps\Service\GeocodeCacheInterface',
	'\PHPGoogleMaps\Service\GeocodeCachePDO'
);

// If location is set
if ( isset( $_GET['location'] ) && strlen( $_GET['location'] ) ) {
	// Create a PDO Geocode Cache connection and pass it to the caching geocoder
	$geoPDO = new \PHPGoogleMaps\Service\GeocodeCachePDO( 'localhost', 'mysql', 'poopbackandforth', 'development' );
	$caching_geo = new \PHPGoogleMaps\Service\CachingGeocoder( $geoPDO );
	// Geocode the location with the caching geocoded
	$geocode_result = $caching_geo->geocode( $_GET['location'] );
	print_r($geocode_result);
	if ( $geocode_result instanceof \PHPGoogleMaps\Service\GeocodeResult ) {
		// Create a map
		$map = new \PHPGoogleMaps\Map;
		$marker = \PHPGoogleMaps\Overlay\Marker::createFromLatLng( $geocode_result );
		$map->addObject( $marker );
		$map->disableAutoEncompass();
		$map->setZoom( 13 );
		$map->setCenter( $geocode_result );
	}
	else {
		$location = $geocode_result->location;
		$error = $geocode_result->error;
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Geocoding - <?php echo PAGE_TITLE ?></title>
	<link rel="stylesheet" type="text/css" href="_css/style.css">
	<?php if( isset( $map ) ): ?>
	<?php $map->printHeaderJS() ?>
	<?php $map->printMapJS() ?>
	<?php endif; ?>
</head>
<body>

<h1>Geocoding</h1>
<?php require( '_system/nav.php' ) ?>

<?php if( isset( $error ) ): ?>
	<p>Unable to geocode "<?php echo $location ?>" (<?php echo $error ?>)</p>
<?php else: ?>	
	<?php if( isset( $map ) ): ?>
	<p><?php echo $geocode_result->$location ?></p>
	<p>Was in cache: <?php echo $geocode_result->wasInCache() ? 'yes' : 'no' ?></p>
	<p>Was put in cache: <?php echo $geocode_result->wasPutInCache() ? 'yes' : 'no' ?></p>
	<?php endif; ?>
<?php endif; ?>
<form action="" method="get">
<label for="location">Enter a location</label>
<input type="text" name="location">
<input type="submit" value=" Geocode ">
</form>

<?php if( isset( $map ) ) $map->printMap() ?>

</body>

</html>


