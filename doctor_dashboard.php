<?php
include 'config.php';

// Fetch pending appointments
$sql = "SELECT * FROM appointments WHERE status = 'pending'";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #333;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Doctor Dashboard</h1>
    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Email</th>
                <th>Date</th>
                <th>Time</th>
                <th>Doctor</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['date']}</td>
                        <td>{$row['time']}</td>
                        <td>{$row['doctor']}</td>
                        <td>
                            <form method='POST' action='update_appointment.php' style='display:inline;'>
                                <input type='hidden' name='appointment_id' value='{$row['id']}'>
                                <button type='submit' name='action' value='accept'>Accept</button>
                                <button type='submit' name='action' value='reject'>Reject</button>
                            </form>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No pending appointments</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
