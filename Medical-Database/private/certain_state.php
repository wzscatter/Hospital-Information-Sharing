<?php
require_once("initialize.php");
$state_code = $_GET['state_code'];

$city_record_set = mysqli_query($db,"SELECT * FROM cities where state_code = \"$state_code\" ORDER BY city");
$zip_record_set = mysqli_query($db,"SELECT * FROM cities_extended where state_code = \"$state_code\" ORDER BY zip");

$city_zip = [];
$city_records = [];
$zip_records = [];

while ($city_record = mysqli_fetch_assoc($city_record_set)) {
    $city_records[] = $city_record;
}

while ($zip_record = mysqli_fetch_assoc($zip_record_set)) {
    $zip_records[] = $zip_record;
}

$city_zip[] = $city_records;
$city_zip[] = $zip_records;
print_r(json_encode($city_zip));
?>