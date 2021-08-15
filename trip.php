<?php
$fromloc = rawurlencode($_GET['fromloc']);
$toloc = rawurlencode($_GET['toloc']);
$fromlat = rawurlencode($_GET['fromlat']);
$fromlon = rawurlencode($_GET['fromlon']);
$tolat = rawurlencode($_GET['tolat']);
$tolon = rawurlencode($_GET['tolon']);
$mode = rawurlencode($_GET['mode']);
$datetime = rawurlencode($_GET['date'] . 'T' . $_GET['time'] . ':00+13:00');
$api_url = "https://api.at.govt.nz/v2/public-restricted/journeyplanner/silverRailIVU/plan?from=$fromloc&to=$toloc&fromLoc=$fromlat,$fromlon&toLoc=$tolat,$tolon&timeMode=$mode&date=$datetime&modes=BUS,TRAIN,FERRY&operators=&optimize=QUICK&maxWalk=2000&maxChanges=-1&routes=&subscription-key=323741614c1c4b9083299adefe100aa6";
$options = array('http' => array(
        'ignore_errors' => TRUE,
        'user_agent' => 'Mozilla/5.0'
    ));
$context  = stream_context_create($options);
$jsonapi = json_decode(file_get_contents($api_url, 240, $context), true);
$rnumb = 1;
$legstring = '';
$fare = '';
$accordian = 1;
if($http_response_header[0] == "HTTP/1.1 200 OK"){
echo "<button class='btn btn-dark bluebutton' style='width: 300px;margin-left: 25px; margin-top: 10px;margin-bottom: 10px;' onClick='backFormClone();'><i class='fas fa-angle-double-left'></i> Go back</button>";
foreach($jsonapi["response"]["itineraries"] as $route){
echo "<table class='table table-dark' style='width: 300px; margin: 0 auto; font-size: 15px; margin-bottom: 10px; border-radius: 5px;cursor: pointer;' id='#bustrip" . $accordian . "'>";
echo "<thead><tr data-toggle='collapse' data-target='#bustrip" . $accordian . "' class='clickable'>";
echo "<td>Departs at " . $route["startTimeStr"] . "</td>";
echo "<td>" . $route["durationStr"] . "</td>";
?>
</thead>
<tbody>
<?php
foreach($route["legs"] as $rlegs){
  if ($rlegs["mode"] == "BUS"){
    $legstring .= "<i class='fas fa-bus'></i> " . $rlegs["routeCode"] . " ";
  };
};
echo "<tr data-toggle='collapse' data-target='#bustrip" . $accordian . "' class='clickable'>";
?>
<td><?php echo $legstring; ?></td>
<td>HOP $<?php echo ($route["fareHopAdult"] / 100); ?></td>
</tr>
<tr>
<td colspan="3" style="background-color: #9bb761; border-bottom-right-radius: 5px; border-bottom-left-radius: 5px; color: #ffffff;">
<div id="bustrip<?php echo $accordian++; ?>" class="collapse"><?php
echo "<b>HOP</b> $".($route["fareHopAdult"] / 100)." | $".($route["fareHopChild"] / 100)." (child) | $".($route["fareHopTertiary"] / 100)." (tertiary)<br />";
echo "<b>CASH</b> $".($route["fareAdult"] / 100)." | $".($route["fareChild"] / 100)." (child)<br />";
echo "<hr />";
foreach($route["legs"] as $rlegs){
  if ($rlegs["mode"] == "WALK"){
  echo "<b>".$rlegs["from"]."</b><br />";
  echo  "<div style='border-left: 4px dotted #17242f; width: 100%; margin-left: 10px;'>";
  echo "<br /><p style='margin: 0;margin-left: 10px;'><img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAMwSURBVGhD7dpZqE1RHMfxa1ZmimSeJQl5UEgeKGMUiQeeSFISJS9KGUPyoGRIkniRIaXkQRnKkAck4gUpJSLz7Pu72vXv39r7nnOcs/c65Vefuve0zjn/tVt7r7X2Pg05ZSou4C0+4RaWowXqJhvxO8V5tEb0mYNQB6ydiD7XECre+oz2iDat8BOh4r0piDbtECo6ZCaizguECvcGIepsRahw6wqiT0fcRagDonllJOoiXXEKvhN3MAp1l4FYjKUYh2b4n7zSAxuwC+uNleiE6KMO7MZH+HMicRvNEWVK6YA1DVGl3A4kjiOKdIfGf7kdSGhPojmm0AzDa4QKLMcyFBbt6K4jVNh3HMYa81qWqygs6+ALUgeOQhOesgW+TcgvDEHuGQ5tgmwxTzEUNhdh22TZjFwTGlI6ov4yqmWHFoK2nRU6ELnOKWthC5BD8NGFwLeztgde2wYtLGseFeeP5HOElhpaENp23gjccK/JB+xBP9Qs++C/eDpCOQjfNvEeGkYrzGueLhwnMAZVj+/IEaTlMWxb6zIU3ZRQsaE2li4avVC19MUD6MMvIW31qi/1xVhaytiMxjF8Q6i9VH0po6tR579/pmYWQsUkFiKUPtBNunfw79GByz0L4AuxBiMrWntpwn0Gtf8KHZzcoyuO5hbfgcQSlJKWGI+ejf8VlHMIdUJeoRvqItqfPEKoI5J1xYsiGg563qHo6pXWGQ29yYg2msB+YH7jf9mdeYg2iDLaJKlIzQmldGYHoswBJEWW0hkNsRmILrr9aQtVZ7QiUNI6o6tYU5NsrmkLLfZskVrS2FuiaZ1ZjWgyCaUUqM74ReV9RBFdffyw0i2itCETWsZMROEJ7VV04qdFj6JfwrbXjYtCEzq6miM6ICva2tr36GZdFxSSAfA3GbQd1h6jqei9/mlvISe9hod+gmELEW1hS41+zmHfew+5Zy9sEXIS5WQe/GdMQG6ZC7/neIJyH95okekfXed20vfGG9gv1y5uLCrJJtjPyu2k918sq1BptITxJ/0i1Dx6Jmi/VI+c/zVnYD9zNmoe/bbkLL7gNKrxoKY/bkLDaj8qfGTd0PAHwlESzXt0ZdUAAAAASUVORK5CYII=' style='-webkit-filter: brightness(0) invert(1); filter: brightness(0) invert(1);width: 7%;'><br /><b>" . $rlegs["distance"] .", " .$rlegs["durationStr"]."</b><br />";
  echo $rlegs["toStr"]."<br /><br /></p>";
  echo '</div>';
}else if ($rlegs["mode"] == "BUS"){
      echo "<i class='fas fa-dot-circle' style='margin-left: 4px;color: #17242f;'></i><b> ".$rlegs["startTimeStr"]."</b><br />";
  echo "<div style='border-left: 4px solid #17242f; width: 100%; margin-left: 10px;'>";
  echo "<span type='hidden' id='".$rlegs["tripId"]."'></span>";
    echo "<br /><p style='margin: 0;margin-left: 10px;'><i class='fas fa-bus'></i><b> " . $rlegs["routeCode"] . "</b><br />";
    echo $rlegs["routeName"]."<br /><br />";
    echo "Departs from " .$rlegs["from"]."<br />";
    echo "<var type='hidden' id='".$rlegs["stops"][0]["code"]."'></var><b>Stop ".$rlegs["stops"][0]["code"]."</b><br /><br />";
    echo "Arrives at " .$rlegs["to"]."<br />";
    echo "<var type='hidden' id='".array_values(array_slice($rlegs["stops"], -1))[0]["code"]."'></var><b>Stop ".array_values(array_slice($rlegs["stops"], -1))[0]["code"]."</b><br /><br />";
    echo '</p></div>';
  }
};
?>
</div>
</td>
</tr>
<?php
$legstring = '';
$fare = '';
echo '</tbody></table>';
?>

<?php
};
}else if ($http_response_header[0] == "HTTP/1.1 500 Internal Server Error"){?>
  <div class="alert alert-danger" role="alert" onClick='backFormClone();' style="cursor: pointer;cursor: pointer; margin: 0 auto; width: 300px; text-align: center; margin-top: 100%;">
    No journey could be found!<br />
    <i class='fas fa-angle-double-left'></i> Try Again!
  </div>
  <?php
};
?>
<div id="array"></div>
<script>
$(".table").on('shown.bs.collapse', function(e) {
var list = e.currentTarget.getElementsByTagName("span");
var stopcode = e.currentTarget.getElementsByTagName("var");
var n = 0;
var routeBulkArray = [];
var routeArray = [];
$( ".maincont" ).prepend( "<div class='routeLoading' style='z-index: 9990; background: #4090c0a3; height: 100%; width: 100%; position: absolute;'><p class='text-center' style='margin-top: 25%;'>Building Route</p></div>" );
setTimeout(function() {
for (var i = 0; i < list.length; i++) {
  $.ajax({
    type: "GET",
    url: "https://api.at.govt.nz/v2/gtfs/stops/tripId/" + list[i].id + "?ivu=ns&subscription-key=323741614c1c4b9083299adefe100aa6",
    dataType: 'json',
    async: false,
    success: function(response) {
      var result = response.response;
      var p = 0;
      for (var l = 0; n < stopcode.length; n++) {
        if (l > 1) {
          break;
        }
        for (var k = 0; k < result.length; k++) {
          if (result[k].stop_code == stopcode[n].id) {
            if (p == 0) {
              routeArray.push(new L.Routing.Waypoint([result[k].stop_lat, result[k].stop_lon]));
            } else if (p == 1) {
              routeArray.push(new L.Routing.Waypoint([result[k].stop_lat, result[k].stop_lon]));
              routeBulkArray.push(routeArray);
		          routeArray = [];
		          var p = 0;
            }
            p++;
          }
        }
        l++;
      }
    }
  })
}
///////////////////////////////
if(routeBulkArray.length > 0){
var router = new L.Routing.OSRMv1();
for (var b = 0; b < routeBulkArray.length; b++) {
    router.route(routeBulkArray[b], function (err, routes) {
        if (!err) {
            L.routing.line(routes[0], {styles:[{color: '#3598db', opacity: 1, weight: 5}]}).addTo(mymap);
			console.log(routes[0]);
        } else if (err){
			console.log(err);
		}
    });
  }
$('.routeLoading').remove();
}
}, 0);
///////////////////////////////
});
  $(".table").on('hide.bs.collapse', function () {
        clearAM();
  });
</script>
