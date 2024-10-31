<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'moderator') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$query = "SELECT * FROM events WHERE status = 'wait'";
$result = mysqli_query($conn, $query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_id = $_POST['event_id'];
    $action = $_POST['action'];

    if ($action == 'Approve') {
        $update_query = "UPDATE events SET status = 'Approved' WHERE id = $event_id";
    } elseif ($action == 'Reject') {
        $update_query = "UPDATE events SET status = 'Not Approved' WHERE id = $event_id";
    }

    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Event status updated successfully.'); window.location.href='moderator_dashboard.php';</script>";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderator Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: url('Resources/campus.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: start;
            min-height: 100vh;
            padding-top: 60px;
            color: #fff;
        }

        .container {
            background: rgba(0, 0, 0, 0.6);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.5);
            max-width: 900px;
            width: 100%;
            text-align: center;
            color: #f4f4f4;
            backdrop-filter: blur(8px);
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: #ffd700;
        }

        h2 {
            font-size: 1.5em;
            margin-bottom: 30px;
            color: #f4f4f4;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: #fff;
            overflow: hidden;
            border-radius: 8px;
        }

        th, td {
            padding: 14px;
            text-align: center;
        }

        th {
            background-color: #444;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.1);
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .container button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8.8px;
            cursor: pointer;
            transition: transform 0.3s, background-color 0.3s;
            margin: 0 5px;
            font-weight: bold;
        }

        .container button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .container button.reject {
            background-color: #f44336;
        }

        .container button.reject:hover {
            background-color: #e53935;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background-color: #555;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: bold;
            transition: transform 0.3s, background-color 0.3s;
        }

        a:hover {
            background-color: #333;
            transform: scale(1.05);
        }

        p {
            font-size: 1.2em;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Moderator Dashboard</h1>
        <h2>Pending Approvals</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Organization</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['organization']); ?></td>
                            <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['event_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="action" value="Approve">Approve</button>
                                    <br><br>
                                    <button type="submit" name="action" value="Reject" class="reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending events for approval.</p>
        <?php endif; ?>

        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
