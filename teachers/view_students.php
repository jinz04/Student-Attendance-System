<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['teacher_id']) || !isset($_GET['course_id'])) {
    header("Location: dashboard.php");
    exit();
}

$course_id = $_GET['course_id'];
$stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

$stmt = $pdo->prepare("SELECT s.* FROM students s JOIN student_courses sc ON s.student_id = sc.student_id WHERE sc.course_id = ? AND s.status = 'active'");
$stmt->execute([$course_id]);
$students = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<div style="max-width: 1000px; margin: 30px auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50; margin: 0;">Students in <?php echo $course['course_name']; ?></h2>
        <a href="dashboard.php" style="background: #7f8c8d; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none;">Back to Dashboard</a>
    </div>

    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #3498db; color: white;">
                    <th style="padding: 12px 15px; text-align: left;">Student ID</th>
                    <th style="padding: 12px 15px; text-align: left;">Name</th>
                    <th style="padding: 12px 15px; text-align: left;">Email</th>
                    <th style="padding: 12px 15px; text-align: left;">Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 15px;"><?php echo $student['student_id']; ?></td>
                    <td style="padding: 12px 15px;"><?php echo $student['name']; ?></td>
                    <td style="padding: 12px 15px;"><?php echo $student['email']; ?></td>
                    <td style="padding: 12px 15px;"><?php echo $student['phone_number'] ?? 'N/A'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>