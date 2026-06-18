<?php
$servername = "localhost";

$dbname = "sistema_mica";
$username = "root";
$password = "";

$api_key_value = "tPmAT5Ab3j7F9";

$api_key = $S1_h = $S1_p = $S1_v = $S1_t = $S2_r = $S2_n = $S3_n = $S4_long = $S4_lat = $S4_a = $S4_v = $S4_h = $S5_i = $S6_t = $S7_c02 = $S8_n = $S9_rtc = $S10_f = $nodo = "";
$content = "";
$fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/log_api_ambiental.txt","a");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = test_input($_POST["api_key"]);
    if($api_key == $api_key_value) {
        $S1_h = test_input($_POST["S1_h"]);
        $S1_p = test_input($_POST["S1_p"]);
        $S1_v = test_input($_POST["S1_v"]);
        $S1_t = test_input($_POST["S1_t"]);
        $S2_r = test_input($_POST["S2_r"]);
        $S2_n = test_input($_POST["S2_n"]);
        $S3_n = test_input($_POST["S3_n"]);
        $S4_long = test_input($_POST["S4_long"]);
        $S4_lat = test_input($_POST["S4_lat"]);
        $S4_a = test_input($_POST["S4_a"]);
        $S4_v = test_input($_POST["S4_v"]);
        $S4_h = test_input($_POST["S4_h"]);
        $S5_i = test_input($_POST["S5_i"]);
        $S6_t = test_input($_POST["S6_t"]);
        $S7_c02 = test_input($_POST["S7_c02"]);
        $S8_n = test_input($_POST["S8_n"]);
        $S9_rtc = test_input($_POST["S9_rtc"]);
        $S10_f = test_input($_POST["S10_f"]);
        $nodo = test_input($_POST["nodo"]);
	$estado = test_input($_POST["estado"]);
        $tablaBD = "";
        if($estado == 1)
          $tablaBD="nodo_establecimientos_experimentos";
        else
          $tablaBD="nodo_establecimientos";
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "INSERT INTO ".$tablaBD." (S1_h, S1_p, S1_v, S1_t, S2_r, S2_n, S3_n, S4_long, S4_lat, S4_a, S4_v, S4_h, S5_i, S6_t, S7_c02, S8_n, S9_rtc, S10_f, nodo, fecha_unix) VALUES ('" . $S1_h . "', '" . $S1_p . "', '" . $S1_v . "', '" . $S1_t . "', '" . $S2_r . "', '" . $S2_n . "', '" . $S3_n . "', '" . $S4_long . "', '" . $S4_lat . "', '" . $S4_a . "', '" . $S4_v . "', '" . $S4_h . "', '" . $S5_i . "', '" . $S6_t . "', '" . $S7_c02 . "', '" . $S8_n . "', '" . $S9_rtc . "', '" . $S10_f . "', '" . $nodo . "', UNIX_TIMESTAMP())";
        $content = $sql;
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        }
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
	    $content = "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    }
    else {
        echo "Wrong API Key provided.";
	$content = "Wrong API Key provided.";
    }

}
else {
    echo "No data posted with HTTP POST.";
    $content = "No data posted with HTTP POST.";
}
fwrite($fp,date('Y-m-d H:i:s  ').$content."\n");
fclose($fp);

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data);
}
