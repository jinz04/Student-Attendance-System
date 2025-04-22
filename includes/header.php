<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }
        header {
            background: #2c3e50;
            color: white;
            padding: 15px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        nav a {
            color: white;
            margin-right: 15px;
            text-decoration: none;
            transition: opacity 0.3s;
        }
        nav a:hover {
            opacity: 0.8;
        }
        .container {
            padding: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1 style="margin: 0; font-size: 24px;">Student Attendance System</h1>
        <?php if(isset($_SESSION['teacher_id'])): ?>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            </nav>
        <?php endif; ?>
    </header>
    <div class="container">
