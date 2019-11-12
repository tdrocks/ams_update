<?php
$server = 'localhost';
$username = 'root';
$password = 'sprinklr@ams';
$database = 'AMS_2';

try{
	$conn = new PDO("mysql:host=$server;dbname=$database;", $username, $password);
} catch(PDOException $e){
	die( "Connection failed: " . $e->getMessage());
}
