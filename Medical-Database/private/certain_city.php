<?php
require_once("initialize.php");
$city = $_GET['city'];

$zip_record_set = mysqli_query($db,"SELECT * FROM cities_extended where city = \"$city\" ORDER BY zip");

$zip_records = [];
while ($zip_record = mysqli_fetch_assoc($zip_record_set)) {
    $zip_records[] = $zip_record;
}

print_r(json_encode($zip_records));
?>