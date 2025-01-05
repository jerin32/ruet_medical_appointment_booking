<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $doctor = htmlspecialchars($_POST["doctor"]);
    $date = htmlspecialchars($_POST["date"]);
    $time = htmlspecialchars($_POST["time"]);

    // Check if the same appointment time already exists
    $sql_check = "SELECT * FROM appointments WHERE doctor = '$doctor' AND date = '$date' AND time = '$time'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        // If the time slot is already booked
        echo "<p>The selected time slot is already booked. Please choose a different time.</p>";
    } else {
        // Insert appointment details with an initial status of 'Pending'
        $sql = "INSERT INTO appointments (name, email, doctor, date, time, status) 
                VALUES ('$name', '$email', '$doctor', '$date', '$time', 'Pending')";

        if (mysqli_query($conn, $sql)) {
            // Show pending status to the applicant
            echo "<p>Appointment submitted successfully! Your status is now <strong>Pending</strong>.</p>";
            echo "<p>Once the doctor approves your appointment, a confirmation email will be sent to $email.</p>";

            // Redirect to the home page after 3 seconds
            header("Refresh: 3; URL=project.html");
            echo "<p>You will be redirected to the home page shortly. If not, <a href='project.html'>click here</a>.</p>";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }

    // Close database connection
    mysqli_close($conn);
}
?>
