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
$dbconn = pg_connect("host=ec2-107-22-241-243.compute-1.amazonaws.com dbname=d8hdosemfljatl user=onpttrcdoiltqz password=cd228dcd0a3ce5651d6be52fe789986851633cb284551f15e10023367d05da28")
    or die('Could not connect: ' . pg_last_error());

// Performing SQL query
$query ="SELECT lat, lon, stop_name, stop_code  FROM busstop WHERE ST_DWithin(geom::geography, ST_GeogFromText('POINT($lon $lat)'), 1000, false)";
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
            'name' => $row['stop_name'],
            'stop_code' => $row['stop_code']
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
