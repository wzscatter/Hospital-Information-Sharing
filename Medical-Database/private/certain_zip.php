<?php
require_once("initialize.php");
$zip = $_GET['zip'];

$city_record_set = mysqli_query($db,"SELECT * FROM cities_extended where zip = \"$zip\" ORDER BY city");

$city_records = [];
while ($city_record = mysqli_fetch_assoc($city_record_set)) {
    $city_records[] = $city_record;
}

print_r(json_encode($city_records));
?>