<?php
// Database connecting function code.
require('connection.php');


// Create a database connection
$conn = DB_connection();
// Check if 'request' parameter is set in POST
if (isset($_POST['request'])) {
    switch ($_POST['request']) {
        case 'sign_up':
            $f_name = htmlspecialchars(trim($_POST['f_name']));
            $l_name = htmlspecialchars(trim($_POST['l_name']));
            $email = htmlspecialchars(trim($_POST['email']));
            $mobile_no = htmlspecialchars(trim($_POST['contact']));
            $password1 = htmlspecialchars(trim($_POST['pswrd1']));
            $password2 = htmlspecialchars(trim($_POST['pswrd2']));

            if (empty($f_name) || empty($l_name) || empty($email) || empty($mobile_no) || empty($password1) || empty($password2)) {
                echo "All fields are required to be filled correctly";
            } else {
                // Hash the password for security
                $password = password_hash($password1, PASSWORD_DEFAULT);

                $query = "INSERT INTO users (`F_NAME`, `L_NAME`, `EMAIL`, `CONTACT`, `PASSWORD`) VALUES ('$f_name', '$l_name', '$email', '$mobile_no', '$password')";
                $result = $conn->query($query);

                if ($result) {
                    header('Location:login.html');
                    echo "alert('Signed up Successfully!')";
                } else {
                    echo "Sorry! Couldn't sign you up.";
                }
            }
            break;

        case 'log_in':
            $email = htmlspecialchars(trim($_POST['email']));
            $password = htmlspecialchars(trim($_POST['password']));

            $query = "SELECT * FROM users WHERE EMAIL = '" . $email . "'";
            $result = $conn->query($query);

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();

                if ($password == $row['PASSWORD']) {
                    session_start();
                    $_SESSION['F_NAME'] = $row['F_NAME'];
                    $_SESSION['USER_ID'] = $row['USER_ID'];
                    $_SESSION['EMAIL'] = $row['EMAIL'];
                    header('location:dashboard.php');
                    exit();
                } else {
                    echo 'Incorrect password';
                }
            } else {
                echo 'User not found';
            }
            break;
        case 'create_task':
            session_start();
            $user_id = $_SESSION['USER_ID'];
            $task_name = htmlspecialchars(trim($_POST['task_name']));
            $description = htmlspecialchars(trim($_POST['description_text']));
            $due_date = $_POST['due_date'];
            $create_date = date('Y-m-d');
            $query = "INSERT INTO tasks (TASK_NAME, TASK_DESCRIPTION, TASK_DUE_DATE, TASK_CREATE_DATE, USER_ID) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssi", $task_name, $description, $due_date, $create_date, $user_id);

            if ($stmt->execute()) {
                header('Location: dashboard.php');
                exit();
            } else {
                echo 'Failed to create the task.';
            }

        case 'update_task':
            $task_id = intval($_POST['task_id']); // Ensure task_id is an integer

            // Retrieve the updated values from the form
            $task_name = htmlspecialchars(trim($_POST['task_name']));
            $description = htmlspecialchars(trim($_POST['description']));
            $due_date = $_POST['due_date'];

            // Update the task in the database
            $query = "UPDATE `tasks` SET `TASK_NAME`='[$task_name]',`TASK_DESCRIPTION`='[$description]',`TASK_DUE_DATE`='[$due_date]' WHERE  `TASK_ID`='[$task_id]'";
            // $query = "UPDATE tasks SET (TASK_NAME, TASK_DESCRIPTION, TASK_DUE_DATE) VALUES (?, ?, ?) TASK_ID=[$task_id]";
            // $query = "UPDATE tasks SET TASK_NAME=?, TASK_DESCRIPTION=?, TASK_DUE_DATE=? WHERE TASK_ID=[$task_id]";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssi", $task_name, $description, $due_date);

            if ($stmt->execute()) {
                // Task updated successfully
                header('Location: dashboard.php');
                exit();
            } else {
                echo 'Failed to update the task.';
            }

        case 'delete_task':
            $task_id = intval($_POST['task_id']); // Ensure task_id is an integer

            // Delete the task from the database
            $query = "DELETE FROM tasks WHERE TASK_ID=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $task_id);

            if ($stmt->execute()) {
                // Task deleted successfully
                header('Location: dashboard.php');
                exit();
            } else {
                echo 'Failed to delete the task.';
            }

        case 'task_status':
                $taskId = $_POST['task_id'];
                $taskStatus = $_POST['task_status'];
            
                $stmt = $conn->prepare("UPDATE tasks SET TASK_STATUS = ? WHERE TASK_ID = ?");
                $stmt->bind_param("si", $taskStatus, $taskId);
                
                if ($stmt->execute()) {
                    echo "Task status updated successfully!";
                } else {
                    echo "Error updating status!";
                }

        default:
            echo 'Invalid request';
    }
} else {
    echo 'Request parameter not set';
}
