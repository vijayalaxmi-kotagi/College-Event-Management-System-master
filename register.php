<?php
$eid = $_GET['id'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>cems</title>
    <?php require 'utils/styles.php'; ?><!--css links. file found in utils folder-->
</head>
<body>
    <?php require 'utils/header.php'; ?>
    <div class="content"><!--body content holder-->
        <div class="container">
            <div class="col-md-6 col-md-offset-3">
                <form method="POST">
                    <!-- Your form fields here -->
                  <label>Student USN:</label><br>
                    <input type="text" name="usn" class="form-control" required><br><br>

                    <label>Student Name:</label><br>
                    <input type="text" name="name" class="form-control" required><br><br>

                    <label>Branch:</label><br>
                    <input type="text" name="branch" class="form-control" required><br><br>

                    <label>Semester:</label><br>
                    <input type="text" name="sem" class="form-control" required><br><br>

                    <label>Email:</label><br>
                    <input type="text" name="email" class="form-control" required><br><br>

                    <label>Phone:</label><br>
                    <input type="text" name="phone" class="form-control" required><br><br>

                    <label>College:</label><br>
                    <input type="text" name="college" class="form-control" required><br><br>

                    <button type="submit" name="update">Submit</button><br><br>
                    <a href="usn.php"><u>Already registered ?</u></a>
                </form>
            </div>
        </div>
    </div>
    <?php require 'utils/footer.php'; ?>
</body>
</html>

<?php
if (isset($_POST["update"])) {
       // Retrieve form data
    $usn = $_POST["usn"];
    $name = $_POST["name"];
    $branch = $_POST["branch"];
    $sem = $_POST["sem"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $college = $_POST["college"];

    // Check if any field is empty
    if (!empty($usn) && !empty($name) && !empty($branch) && !empty($sem) && !empty($email) && !empty($phone) && !empty($college)) {
        // Include database connection file
        include 'classes/db1.php';

        // Check if the user has already registered for this event
        $checkQuery = $conn->prepare("SELECT * FROM registered WHERE usn = ? AND event_id = ?");
        $checkQuery->bind_param("si", $usn, $eid);
        $checkQuery->execute();
        $result = $checkQuery->get_result();

        if ($result->num_rows > 0) {
            // User already registered for this event
            echo "<script>
                alert('You have already registered for this event!');
                window.location.href='usn.php';
                </script>";
        } else {
            // Insert into participent table
            $insertParticipant = $conn->prepare("INSERT INTO participent (usn, name, branch, sem, email, phone, college) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insertParticipant->bind_param("sssisss", $usn, $name, $branch, $sem, $email, $phone, $college);
            
            // Insert into registered table
            $insertRegistered = $conn->prepare("INSERT INTO registered (usn, event_id) VALUES (?, ?)");
            $insertRegistered->bind_param("si", $usn, $eid);

            // Execute both queries
            $participantResult = $insertParticipant->execute();
            $registeredResult = $insertRegistered->execute();

            if ($participantResult && $registeredResult) {
                // Registration successful
                echo "<script>
                    alert('Registered Successfully!');
                    window.location.href='usn.php';
                    </script>";
            } else {
                // Error in registration
                echo "<script>
                    alert('Error in registration!');
                    window.location.href='usn.php';
                    </script>";
            }
        }

        // Close connections
        $checkQuery->close();
        $insertParticipant->close();
        $insertRegistered->close();
        $conn->close();
    } else {
        // All fields are required
        echo "<script>
            alert('All fields are required');
            window.location.href='register.php';
            </script>";
    }
}
?>
