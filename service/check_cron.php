<?php

file_get_contents("http://rajcrm.com/service/india_mart_leads.php");
file_get_contents("http://rajcrm.com/service/trade_india_leads.php");exit;
$servername = "localhost";
$username = "bizzbroc_shreehari";
$password = "h*&n!z*vUxJZ";
$dbname = "bizzbroc_shreehari";

function generateActivationCode_mobile()
{
	$characters1='0123456789';
	$randStr1="";
	for($j=0;$j<=3;$j++)
	{
		$randStr1=$randStr1.$characters1[rand(0,strlen($characters1)-1)];
	}
	return $randStr1;
}
$random_number = generateActivationCode_mobile();
// Create connection
$conn = new mysqlii($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO cron_table (random_number)
VALUES ($random_number)";

if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>