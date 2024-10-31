<?php
session_start();
require 'db.php';

// Check if user is logged in and is a faculty
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: login.php");
    exit();
}

// Fetch events for the logged-in user
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT title, organization, event_date, event_time, description, status FROM events WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - Faculty Approval System</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: url('Resources/campus.png') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: start;
            min-height: 100vh;
            padding-top: 50px;
            position: relative;
        }

        /* Overlay effect to make text readable */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Dark overlay */
            z-index: 1;
        }

        .dashboard-container {
            position: relative;
            z-index: 2; /* Keeps the content above the overlay */
            max-width: 900px;
            width: 100%;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #B7202E;
            margin-bottom: 30px;
        }

        button#addEventButton {
            background: linear-gradient(to bottom right, #ED1C24, #B7202E);
            border: 0;
            border-radius: 12px;
            color: #FFFFFF;
            cursor: pointer;
            display: inline-block;
            font-family: -apple-system,system-ui,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
            font-size: 16px;
            font-weight: 500;
            line-height: 2.5;
            outline: transparent;
            padding: 0 1rem;
            text-align: center;
            text-decoration: none;
            transition: box-shadow .5s ease-in-out;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            white-space: nowrap;
        }

        button#addEventButton:not([disabled]):focus,
        button#addEventButton:not([disabled]):hover {
            box-shadow: 0 0 .25rem rgba(0, 0, 0, 0.5), -.125rem -.125rem 1rem rgba(239, 71, 101, 0.5), .125rem .125rem 1rem rgba(255, 154, 90, 0.5);
        }

        /* Modal styling */
        #eventFormModal {
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #eventFormModal form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            width: 100%;
            text-align: left;
        }

        label {
            font-weight: bold;
            color: #333;
            margin-top: 10px;
            display: block;
        }

        input[type="text"], input[type="date"], input[type="time"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        button[type="submit"], button#closeModal {
            background-color: #ED1C24;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover, button#closeModal:hover {
            background-color: #B7202E;
        }

        .events {
            margin-top: 30px;
        }

        .event-card {
            background-color: #FAFBFB;
            padding: 20px;
            border: 1px solid #ED1C24;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: left;
        }

        .event-card h4 {
            color: #B7202E;
        }

        .event-card p {
            margin: 5px 0;
        }

        /* Status box styles */
        .status-approved {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
            text-align: center;
            width: 150px;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
            text-align: center;
            width: 150px;
        }

        .status-wait {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
            text-align: center;
            width: 100px;
        }

        a {
            color: #ED1C24;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
        }

        a:hover {
            text-decoration: underline;
        }

        .animated-button {
            width: 180px;
            height: 60px;
            cursor: pointer;
            background: transparent;
            border: 1px solid #e04c2b;
            outline: none;
            border-radius: 4px;
            transition: 0.4s ease-in-out;
            font-size: 24px;
            font-weight: 1000;
            line-height: 2.5;
            text-align: center;
            text-decoration: none;
        }

        svg {
            position: center;
            left: 0;
            top: 0;
            fill: none;
            stroke: #fff;
            stroke-dasharray: 150 480;
            stroke-dashoffset: 150;
            transition: 1s ease-in-out;
        }

        .animated-button:hover {
            transition: 0.4s ease-in-out;
            background: #e04c2b;
            color: white;
            border-radius: 10px;
        }

        .animated-button:hover svg {
            stroke-dashoffset: -480;
        }

        .animated-button span {
            color: white;
            font-size: 18px;
            font-weight: 100;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome, Faculty</h2>
        <button id="addEventButton">Add Event</button>

        <!-- Event Form Modal -->
        <div id="eventFormModal" style="display:none;">
            <form method="POST" action="add_events.php">
                <label for="title">Title:</label>
                <input type="text" name="title" required>

                <label for="organization">Organization:</label>
                <input type="text" name="organization" required>

                <label for="event_date">Date:</label>
                <input type="date" name="event_date" required>

                <label for="event_time">Time:</label>
                <input type="time" name="event_time" required>

                <label for="description">Description (optional):</label>
                <textarea name="description"></textarea>

                <button type="submit">Submit</button>
                <button type="button" id="closeModal">Cancel</button>
            </form>
        </div>

        <!-- Fetch and display events -->
        <div class="events">
            <h3>Your Events</h3>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($event = $result->fetch_assoc()): ?>
                    <div class="event-card">
                        <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                        <p><strong>Organization:</strong> <?php echo htmlspecialchars($event['organization']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
                        <p><strong>Time:</strong> <?php echo htmlspecialchars($event['event_time']); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                        <p><strong>Status:</strong> 
                            <?php 
                            if ($event['status'] == 'Approved') {
                                echo '<span class="status-approved">Approved</span>';
                            } elseif ($event['status'] == 'Not Approved') {
                                echo '<span class="status-rejected">Rejected</span>';
                            } else {
                                echo '<span class="status-wait">Wait</span>';
                            }
                            ?>
                        </p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No events found.</p>
            <?php endif; ?>
        </div>

        <a href="logout.php" class="animated-button">Logout</a>
    </div>

    <script>
        const addEventButton = document.getElementById('addEventButton');
        const eventFormModal = document.getElementById('eventFormModal');
        const closeModalButton = document.getElementById('closeModal');

        addEventButton.addEventListener('click', () => {
            eventFormModal.style.display = 'flex';
        });

        closeModalButton.addEventListener('click', () => {
            eventFormModal.style.display = 'none';
        });
    </script>
</body>
</html>
