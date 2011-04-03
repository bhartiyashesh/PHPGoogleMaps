<?php

require( '../PHPGoogleMaps/Core/Autoloader.php' );
$map_loader = new SplClassLoader('PHPGoogleMaps', '../');
$map_loader->register();

require( '_system/config.php' );

if ( isset( $_GET['location'] ) && strlen( $_GET['location'] ) ) {
	$geocode_result = \PHPGoogleMaps\Service\Geocoder::geocode( $_GET['location'] );
	if ( $geocode_result instanceof \PHPGoogleMaps\Service\GeocodeResult ) {
		if ( $geocode_result->result_count > 1 ) {
			$location = $_GET['location'];
			$location_options = $geocode_result->raw_results;
		}
		else {
			$location = $geocode_result->formatted_address;
			$position = $geocode_result;
		}
	}
	else {
		$location = $_GET['location'];
		$error = $geocode_result->error;
	}
}
if ( isset( $_GET['geocoded_location'] ) ) {
	list( $location, $lat, $lng ) = explode( '|', $_GET['geocoded_location'] );
	$position = new \PHPGoogleMaps\Core\LatLng( $lat, $lng );
}

if ( isset( $position ) ) {
	$map = new \PHPGoogleMaps\Map;
	$marker = \PHPGoogleMaps\Overlay\Marker::createFromLatLng( $position, array( 'content' => $location ) );
	$map->addObject( $marker );
	$map->disableAutoEncompass();
	$map->setZoom( 13 );
	$map->setCenter( $position );
}

?>

<!DOCTYPE html>
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

<h1>Advanced Geocoding</h1>
<?php require( '_system/nav.php' ) ?>

<p>Geocoder::geocode() returns the full geocode result from Google. This example uses the full result to ask the user to choose their desired address when multiple addresses fit the location they enter. <a href="?location=main+st">Main ST</a>, for example, will return multiple matching addresses.</p>

<form action="" method="get">
<?php if( isset( $location_options ) ): ?>
<p>There are <?php echo count( $location_options ) ?> locations that match <strong><?php echo $location ?></strong></p>
<label for="locations">Select a location</label>
<select name="geocoded_location">
<?php foreach( $location_options as $location_option ): ?>
	<option value="<?php echo $location_option->formatted_address ?>|<?php echo $location_option->geometry->location->lat ?>|<?php echo $location_option->geometry->location->lng ?>"><?php echo $location_option->formatted_address ?></option>
<?php endforeach; ?>
</select>
<input type="submit" value="Use this address">
<br><br>
<?php endif; ?>
<label for="location">Enter a location</label>
<input type="text" name="location">
<input type="submit" value=" Geocode ">
</form>

<?php if( isset( $location ) && !isset( $location_options ) ): ?><p><?php echo $location ?></p><?php endif; ?>
<?php if( isset( $error ) ): ?><p>Unable to geocode "<?php echo $location ?>" (<?php echo $error ?>)</p><?php endif; ?>
<?php if( isset( $map ) ) $map->printMap() ?>

</body>

</html>


