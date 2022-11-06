<?php
// prevent direct access
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(!$isAjax) {
  $user_error = 'Access denied - not an AJAX request...';
  trigger_error($user_error, E_USER_ERROR);
}
 
$lat = $_GET['lat'];
$lon = $_GET['lon'];
// Connecting, selecting database
$dbconn = pg_connect("")
    or die('Could not connect: ' . pg_last_error());

// Performing SQL query
$query ="SELECT lat, lon FROM busloc WHERE ST_DWithin(geom::geography, ST_GeogFromText('POINT($lon $lat)'), 1000, false)";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

# Build GeoJSON feature collection array
$geojson = array(
   'type'      => 'FeatureCollection',
   'features'  => array()
);
# Loop through rows to build feature arrays
while($row = pg_fetch_array($result, null, PGSQL_ASSOC)){
    $feature = array(
        'type' => 'Feature',
        'geometry' => array(
            'type' => 'Point',
            # Pass Longitude and Latitude Columns here
            'coordinates' => array($row['lon'], $row['lat'])
        ),
        # Pass other attribute columns here
        'properties' => array(
            )
        );
    # Add feature arrays to feature collection array
    array_push($geojson['features'], $feature);
}
header('Content-type: application/json');
echo json_encode($geojson, JSON_NUMERIC_CHECK);

// Free resultset
pg_free_result($result);

// Closing connection
pg_close($dbconn);
?>
