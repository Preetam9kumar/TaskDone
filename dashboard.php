<?php
//the session to access user information
session_start();
// Check if the user is logged in
if (!isset($_SESSION['F_NAME'])) {
  header('Location: login.html');
  exit();
}
require('connection.php');
$conn = DB_connection();


function getStatusClass($status)
{
  switch ($status) {
    case 'Pending':
      return 'badge badge-pending';
    case 'In Progress':
      return 'badge badge-in-progress';
    case 'Completed':
      return 'badge badge-completed';
    default:
      return 'badge bg-secondary';
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Dashboard</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./bootstrap-5.0.2-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="./bootstrap-5.0.2-dist/css/bootstrap.css">
  <link rel="stylesheet" href="./fontawesome-free-6.4.0-webcss/all.min.css">
  <link rel="stylesheet" href="./fontawesome-free-6.4.0-web/css/all.css">
  <link rel="stylesheet" href="assets/taskCard.css">
  <style>
    .tab-content {
      height: 560px;
      overflow-y: Auto;
      padding: 10px;
      border: 5px solid black;
    }

    html {
      height: 100vh;
    }

    body {
      width: 100%;
      height: 100vh;
      position: fixed;
      top: 0;
      bottom: 0;
    }
  </style>
</head>

<body>
  <div class="container_fluid">
    <header class=" d-flex flex-column">
      <div class="row justify-content-between align-items-center text-light" style="background-color: #042d56f2;">
        <div class="col-3 d-flex justify-content-center align-items-center">
          <span><img src="assets/My.png" alt="Profile" class="rounded-circle p-2" height="80px"></span>
          <span class="fs-2 fw-semibold">
            <?php echo $_SESSION['F_NAME']; ?>
          </span>
        </div>
        <div class="col-6 justify-content-center align-items-center d-flex text-info border-1 border-light mb-0 mt-4" style="background-color: #0f4c88f2; border-start-start-radius: 10px; border-start-end-radius: 15px; border-start-end-radius: 15px;">
          <button type="button" class="btn btn-primary m-2" id="show-tab1">To-Do</button>
          <button type="button" class="btn btn-primary m-2" id="show-tab2">In Progress</button>
          <button type="button" class="btn btn-primary m-2" id="show-tab3">Done</button>
        </div>
        <div class="col-3 justify-content-center align-items-center d-flex">
          <button type="button" class="btn btn-primary me-1" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="Task Manager">Add task</button>
          <button class="btn btn-primary btn-rounded ms-1" onclick="logOut()">Logout</button>
        </div>
      </div>
    </header>
    <div class="container-fluid tab-content" style="background-color: #a4a8b8f2;">
      <div class="container" id="tab1-content">
        <div class="d-flex flex-wrap gap-3">
          <?php
          $query = "SELECT * FROM tasks where TASK_STATUS = 'todo'";
          // $data =mysqli_query($conn,$query);
          $result =  $conn->query($query);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
              <div class="task-card rounded shadow-sm p-3 d-flex flex-column">
                <!-- Task Header -->
                <div class="task-header d-flex align-items-center justify-content-between">
                  <h5 class="fw-bold text-dark m-0"><?= htmlspecialchars($row['TASK_NAME']) ?></h5>
                  <span class="badge bg-primary text-white">#<?= htmlspecialchars($row['TASK_ID']) ?></span>
                </div>
                <!-- Task Dates and status-->
                <div class="task-header d-flex align-items-center justify-content-between">
                  <div class="task-dates d-flex flex-column justify-content-between text-secondary small">
                    <span><i class="fa-regular fa-clock"></i> Created: <?= htmlspecialchars($row['TASK_CREATE_DATE']) ?></span>
                    <span><i class="fa-regular fa-calendar-days"></i> Due: <?= htmlspecialchars($row['TASK_DUE_DATE']) ?></span>
                  </div>
                  <div class="task-status">
                    <label class="small text-secondary">Update Status:</label>
                    <select class="form-select form-select-sm" onchange="updateTaskStatus(<?= $row['TASK_ID'] ?>, this.value)">
                      <option value="todo" <?= $row['TASK_STATUS'] === 'Pending' ? 'selected' : '' ?>>ToDo</option>
                      <option value="inprogress" <?= $row['TASK_STATUS'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                      <option value="done" <?= $row['TASK_STATUS'] === 'Completed' ? 'selected' : '' ?>>Done</option>
                    </select>
                  </div>
                </div>

                <!-- Task Description -->
                <p class="text-muted small mt-2">
                  <?= htmlspecialchars($row['TASK_DESCRIPTION']) ?>
                </p>

                <!-- Action Buttons -->
                <div class="task-actions mt-3 d-flex justify-content-between">
                  <button type="button" class="btn btn-sm btn-outline-primary px-3" data-bs-toggle="modal"
                    data-bs-target="#exampleModal2" onclick="updateTask(<?= $row['TASK_ID'] ?>)">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-danger px-3" onclick="deleteTask(<?= $row['TASK_ID'] ?>)">
                    <i class="fa-solid fa-trash"></i> Delete
                  </button>
                </div>
              </div>

          <?php }
          }
          ?>
          <!-- PHP code to fetch and display tasks in the "To-Do" section -->
        </div>
      </div>
      <div class="container" id="tab2-content">
        <div class="d-flex flex-wrap gap-3">
          <?php
          $query = "SELECT * FROM tasks where TASK_STATUS = 'inprogress'";
          // $data =mysqli_query($conn,$query);
          $result =  $conn->query($query);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
              <div class="task-card rounded shadow-sm p-3 d-flex flex-column">
                <!-- Task Header -->
                <div class="task-header d-flex align-items-center justify-content-between">
                  <h5 class="fw-bold text-dark m-0"><?= htmlspecialchars($row['TASK_NAME']) ?></h5>
                  <span class="badge bg-primary text-white">#<?= htmlspecialchars($row['TASK_ID']) ?></span>
                </div>
                <!-- Task Dates and status-->
                <div class="task-header d-flex align-items-center justify-content-between">
                  <div class="task-dates d-flex flex-column justify-content-between text-secondary small">
                    <span><i class="fa-regular fa-clock"></i> Created: <?= htmlspecialchars($row['TASK_CREATE_DATE']) ?></span>
                    <span><i class="fa-regular fa-calendar-days"></i> Due: <?= htmlspecialchars($row['TASK_DUE_DATE']) ?></span>
                  </div>
                  <div class="task-status">
                    <label class="small text-secondary">Update Status:</label>
                    <select class="form-select form-select-sm" onchange="updateTaskStatus(<?= $row['TASK_ID'] ?>, this.value)">
                      <option value="todo" <?= $row['TASK_STATUS'] === 'Pending' ? 'selected' : '' ?>>ToDo</option>
                      <option value="inprogress" <?= $row['TASK_STATUS'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                      <option value="done" <?= $row['TASK_STATUS'] === 'Completed' ? 'selected' : '' ?>>Done</option>
                    </select>
                  </div>
                </div>

                <!-- Task Description -->
                <p class="text-muted small mt-2">
                  <?= htmlspecialchars($row['TASK_DESCRIPTION']) ?>
                </p>

                <!-- Action Buttons -->
                <div class="task-actions mt-3 d-flex justify-content-between">
                  <button type="button" class="btn btn-sm btn-outline-primary px-3" data-bs-toggle="modal"
                    data-bs-target="#exampleModal2" onclick="updateTask(<?= $row['TASK_ID'] ?>)">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-danger px-3" onclick="deleteTask(<?= $row['TASK_ID'] ?>)">
                    <i class="fa-solid fa-trash"></i> Delete
                  </button>
                </div>
              </div>
          <?php }
          }
          ?>
          <!-- PHP code to fetch and display tasks in the "To-Do" section -->
        </div>
      </div>
      <div class="container" id="tab3-content">
        <div class="d-flex flex-wrap gap-3">
          <?php
          $query = "SELECT * FROM tasks where TASK_STATUS = 'done'";
          // $data =mysqli_query($conn,$query);
          $result =  $conn->query($query);
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
              <div class="task-card rounded shadow-sm p-3 d-flex flex-column">
                <!-- Task Header -->
                <div class="task-header d-flex align-items-center justify-content-between">
                  <h5 class="fw-bold text-dark m-0"><?= htmlspecialchars($row['TASK_NAME']) ?></h5>
                  <span class="badge bg-primary text-white">#<?= htmlspecialchars($row['TASK_ID']) ?></span>
                </div>
                <!-- Task Dates and status-->
                <div class="task-header d-flex align-items-center justify-content-between">
                  <div class="task-dates d-flex flex-column justify-content-between text-secondary small">
                    <span><i class="fa-regular fa-clock"></i> Created: <?= htmlspecialchars($row['TASK_CREATE_DATE']) ?></span>
                    <span><i class="fa-regular fa-calendar-days"></i> Due: <?= htmlspecialchars($row['TASK_DUE_DATE']) ?></span>
                  </div>
                  <div class="task-status">
                    <label class="small text-secondary ">Update Status:</label>
                    <select class="form-select form-select-sm <?= getStatusClass($row['TASK_STATUS']) ?>" onchange="updateTaskStatus(<?= $row['TASK_ID'] ?>, this.value)">
                      <option value="todo" <?= $row['TASK_STATUS'] === 'Pending' ? 'selected' : '' ?>>ToDo</option>
                      <option value="inprogress" <?= $row['TASK_STATUS'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                      <option value="done" <?= $row['TASK_STATUS'] === 'Completed' ? 'selected' : '' ?>>Done</option>
                    </select>
                  </div>
                </div>

                <!-- Task Description -->
                <p class="text-muted small mt-2">
                  <?= htmlspecialchars($row['TASK_DESCRIPTION']) ?>
                </p>

                <!-- Action Buttons -->
                <div class="task-actions mt-3 d-flex justify-content-between">
                  <button type="button" class="btn btn-sm btn-outline-primary px-3" data-bs-toggle="modal"
                    data-bs-target="#exampleModal2" onclick="updateTask(<?= $row['TASK_ID'] ?>)">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-danger px-3" onclick="deleteTask(<?= $row['TASK_ID'] ?>)">
                    <i class="fa-solid fa-trash"></i> Delete
                  </button>
                </div>
              </div>
          <?php }
          }
          ?>
          <!-- PHP code to fetch and display tasks in the "To-Do" section -->
        </div>
      </div>
    </div>
    <div class="container-fluid m-auto" style="background-color: #0f4c88f2;">
      <footer class="p-4">
        <a href="#" class="p-3 text-decoration-none text-light">FAQ</a>
        <a href="#" class="p-3 text-decoration-none text-light">Contact Us</a>
        <a href="#" class="p-3 text-decoration-none text-light">Terms of Use</a>
        <a href="#" class="p-3 text-decoration-none text-light">Privacy Policy</a>
        <a href="#" class="p-3 text-decoration-none text-light">Refund Policy</a>
        <a href="#" class="p-3 text-decoration-none text-light">&copy; 2023 | taskDone</a>
      </footer>
    </div>
    <!-- modal for add a new task -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Task</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" action="">
            <div class="modal-body">
              <div class="mb-3">
                <label for="task_name" class="col-form-label">Task Name:</label>
                <input type="text" class="form-control" id="task_name" name="task_name">
              </div>
              <div class="mb-3">
                <label for="description_text" class="col-form-label">Description:</label>
                <textarea class="form-control" id="description_text" name="description_text"></textarea>
              </div>
              <div class="mb-3">
                <label for="due_date" class="col-form-label">Due Date:</label>
                <input type="date" class="form-control" id="due_date" name="due_date"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" onclick="createTask()" class="btn btn-primary">Create Task</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- modal for update/edit task -->
    <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Update\Edite Task</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" action="">
            <div class="modal-body">
              <div class="mb-3">
                <label for="task_name" class="col-form-label">Task Name:</label>
                <input type="text" class="form-control" id="task_name" name="task_name">
              </div>
              <div class="mb-3">
                <label for="description_text" class="col-form-label">Description:</label>
                <textarea class="form-control" id="description_text" name="description_text"></textarea>
              </div>
              <div class="mb-3">
                <label for="due_date" class="col-form-label">Due Date:</label>
                <input type="date" class="form-control" id="due_date" name="due_date"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" onclick="updateTask()" class="btn btn-primary">Update Task</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Include your JavaScript code for task management here -->
  </div>
</body>
<!-- Include jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="jquery-3.7.1.js"></script>
<script src="./bootstrap-5.0.2-dist/js/bootstrap.js"></script>

<!-- JavaScript for task management -->
<script>
  $(document).ready(function() {
    // Hide both content divs initially
    $("#tab1-content").hide();
    $("#tab2-content").hide();
    $("#tab3-content").hide();

    // Show Alumni content when "Show Alumni" button is clicked
    $("#show-tab1").click(function() {
      $("#tab3-content").hide();
      $("#tab2-content").hide();
      $("#tab1-content").show();
    });

    // Show Student content when "Show Student" button is clicked
    $("#show-tab2").click(function() {
      $("#tab3-content").hide();
      $("#tab1-content").hide();
      $("#tab2-content").show();
    });

    // Show Student content when "Show Student" button is clicked
    $("#show-tab3").click(function() {
      $("#tab2-content").hide();
      $("#tab1-content").hide();
      $("#tab3-content").show();
    });
  });
  // to create/add a new task in todo list
  function createTask() {
    const form = document.querySelector('form'); // Select the form element

    // Create a FormData object from the form
    const formData = new FormData(form);

    // Check if any required fields are empty (you can add more checks as needed)
    if (!formData.get('task_name') || !formData.get('description_text')) {
      alert("Please fill all required fields correctly");
      return;
    }

    if (confirm("Do you want to Create Task?")) {
      formData.append('request', 'create_task');

      const xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
          alert("Task added Successfully !");
          return;
        }
      }

      xhttp.open("POST", "serverDB_logics.php");
      xhttp.send(formData);
    }
  }
  // Function to delete a task by task ID
  function deleteTask(taskId) {
    if (confirm('Are you sure you want to delete this task?')) {
      const xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (xhttp.readyState === 4) {
          if (xhttp.status === 200) {
            // Task deleted successfully, update the UI as needed
            // For example, remove the task element from the task list
            const taskElement = document.querySelector(`[data-task-id="${taskId}"]`);
            if (taskElement) {
              taskElement.remove();
            }
          } else {
            // Handle error or display an error message
            console.error('Failed to delete the task.');
          }
        }
      };

      xhttp.open('POST', 'your_php_script.php'); // Replace with the actual path to your PHP script
      xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhttp.send(`request=delete_task&task_id=${taskId}`);
    }
  }

  // Function to update a task by task ID
  function updateTask(taskId) {
    // Collect updated task data from a form or other source
    const updatedTaskData = {
      taskName: document.getElementById('task_name').value,
      description: document.getElementById('description_text').value,
      dueDate: document.getElementById('due_date').value
    };
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (xhttp.readyState === 4) {
        if (xhttp.status === 200) {
          // Task updated successfully, update the UI as needed
          // For example, update task details in the task element
          const taskElement = document.querySelector(`[data-task-id="${taskId}"]`);
          if (taskElement) {
            taskElement.querySelector('.task_name').textContent = updatedTaskData.taskName;
            taskElement.querySelector('.description_text').textContent = updatedTaskData.description;
            taskElement.querySelector('.due_date').textContent = updatedTaskData.dueDate;
          }
        } else {
          // Handle error or display an error message
          console.error('Failed to update the task.');
        }
      }
    };
    xhttp.open('POST', 'serverDB_logics.php'); // Replace with the actual path to your PHP script
    xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhttp.send(`request=update_task&task_id=${taskId}&task_name=${updatedTaskData.taskName}&description=${updatedTaskData.description}&due_date=${updatedTaskData.dueDate}`);
  }

  function logOut() {
    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (xhttp.readyState == 4 && xhttp.status == 200) {
        alert("You logout Successfully !");
        return;
      }
    }
    xhttp.open("GET", "logout.php");
    xhttp.send();
  }

  function updateTaskStatus(taskId, newStatus) {
    fetch('serverDB_logics.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `request=task_status&task_id=${taskId}&task_status=${newStatus}`
      })
      .then(response => response.text())
      .then(data => {
        console.log(data); // Optional: Log response
      })
      .catch(error => console.error('Error:', error));
  }
</script>

</html>