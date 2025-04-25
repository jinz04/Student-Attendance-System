<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT c.* FROM courses c JOIN teacher_courses tc ON c.course_id = tc.course_id WHERE tc.teacher_id = ?");
$stmt->execute([$_SESSION['teacher_id']]);
$courses = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<div style="max-width: 1000px; margin: 30px auto;">
    <h2 style="color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;">Welcome, <?php echo $_SESSION['teacher_name']; ?></h2>
    
    <h3 style="color: #34495e; margin-top: 30px;">Your Courses</h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
        <?php foreach ($courses as $course): ?>
        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h4 style="margin: 0 0 10px 0; color: #2980b9;"><?php echo $course['course_name']; ?></h4>
            <p style="color: #7f8c8d; margin: 5px 0;">Code: <?php echo $course['course_code']; ?></p>
            <p style="color: #7f8c8d; margin: 5px 0 15px 0;">Semester: <?php echo $course['semester']; ?></p>
            
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="view_students.php?course_id=<?php echo $course['course_id']; ?>" 
                   style="background: #3498db; color: white; padding: 8px 12px; border-radius: 4px; text-decoration: none; font-size: 14px;">
                    Students
                </a>
                <a href="mark_attendance.php?course_id=<?php echo $course['course_id']; ?>" 
                   style="background: #2ecc71; color: white; padding: 8px 12px; border-radius: 4px; text-decoration: none; font-size: 14px;">
                    Mark Attendance
                </a>
                <a href="attendance_report.php?course_id=<?php echo $course['course_id']; ?>" 
                   style="background: #9b59b6; color: white; padding: 8px 12px; border-radius: 4px; text-decoration: none; font-size: 14px;">
                    Reports
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
