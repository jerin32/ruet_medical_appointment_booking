<?php
// Include database configuration
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs
    $appointment_id = htmlspecialchars($_POST["appointment_id"]);
    $action = htmlspecialchars($_POST["action"]);

    // Determine the new status based on the action
    $new_status = ($action === 'accept') ? 'Accepted' : 'Rejected';

    // Start a transaction to ensure atomicity
    mysqli_begin_transaction($conn);

    try {
        // Update the appointment status in the database
        $sql_update = "UPDATE appointments SET status = '$new_status' WHERE id = $appointment_id";
        if (!mysqli_query($conn, $sql_update)) {
            throw new Exception("Error updating appointment: " . mysqli_error($conn));
        }

        // Fetch the applicant's email and details for sending an email
        $sql_fetch = "SELECT name, email, doctor, date, time FROM appointments WHERE id = $appointment_id";
        $result = mysqli_query($conn, $sql_fetch);
        if (!$result || mysqli_num_rows($result) === 0) {
            throw new Exception("Error fetching appointment details: " . mysqli_error($conn));
        }

        $appointment = mysqli_fetch_assoc($result);

        // Prepare email content if the appointment is accepted
        $email_sent = false;
        if ($new_status === 'Accepted') {
            $to_name = $appointment['name'];
            $to_email = $appointment['email'];
            $doctor = $appointment['doctor'];
            $date = $appointment['date'];
            $time = $appointment['time'];

            // Send email using EmailJS API
            $url = "https://api.emailjs.com/api/v1.0/email/send";
            $data = json_encode(array(
                "service_id" => "service_i38ppei",  // Replace with your EmailJS service ID
                "template_id" => "template_g82gh79", // Replace with your EmailJS template ID
                "user_id" => "FP7hZa19T3dEynVel",   // Replace with your EmailJS user ID
                "accessToken" => "0N9yLtVFrB9HpLnYZbfNt",  // Replace with your EmailJS access token
                "template_params" => array(
                    "to_name" => $to_name,
                    "to_email" => $to_email,
                    "doctor" => $doctor,
                    "date" => $date,
                    "time" => $time
                )
            ));

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                throw new Exception("Email sending failed: " . curl_error($curl));
            } else {
                $email_sent = true; // Set flag to indicate successful email
            }

            curl_close($curl);
        }

        // Commit the transaction
        mysqli_commit($conn);

        // Display a success message
        echo "<p style='color: green;'>Appointment updated successfully!</p>";
        if ($email_sent) {
            echo "<p style='color: green;'>Email successfully sent!</p>";
        }

        // Redirect to the home page after 5 seconds
        header("Refresh: 5; URL=project.html");
        echo "<p>You will be redirected to the home page shortly. If not, <a href='project.html'>click here</a>.</p>";
        exit;
    } catch (Exception $e) {
        // Roll back the transaction on error
        mysqli_rollback($conn);

        // Display an error message
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }

    // Close the database connection
    mysqli_close($conn);
}
?>
