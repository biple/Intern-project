<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "certificate_verification";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
    exit();
}

// Get input data from AJAX request
$symbol_no = $_POST['symbol'] ?? '';
$issue_date = $_POST['issuedate'] ?? '';
$passed_year = $_POST['year'] ?? '';

if (empty($symbol) || empty($issuedate) || empty($year)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit();
}

// Prepare and execute query
$sql = "SELECT * FROM certificates WHERE symbol_no = ? AND issue_date = ? AND passed_year = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $symbol_no, $issue_date, $passed_year);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $certificate = $result->fetch_assoc();
    echo json_encode([
        "status" => "success",
        "certificate" => [
            "name" => $certificate['name'],
            "course" => $certificate['course'],
            "certificate_url" => "certificates/" . $certificate['file_name']
        ]
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Sorry, we could not find your certificate with the details you provided. Kindly recheck the details and try again. If you think we made a mistake, please contact GlobalWings for further processing."]);
}

$stmt->close();
$conn->close();
?>
