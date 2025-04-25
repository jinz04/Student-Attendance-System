<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['teacher_id']) || !isset($_GET['course_id'])) {
    header("Location: dashboard.php");
    exit();
}

$course_id = $_GET['course_id'];

// Get course details
$stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    die("Course not found");
}

// Get all attendance dates for this course
$stmt = $pdo->prepare("
    SELECT DISTINCT attendance_date 
    FROM attendance
    WHERE course_id = ?
    ORDER BY attendance_date DESC
");
$stmt->execute([$course_id]);
$dates = $stmt->fetchAll();

// Get students enrolled in this course
$stmt = $pdo->prepare("
    SELECT s.student_id, s.name 
    FROM students s
    JOIN student_courses sc ON s.student_id = sc.student_id
    WHERE sc.course_id = ? AND s.status = 'active'
    ORDER BY s.name
");
$stmt->execute([$course_id]);
$students = $stmt->fetchAll();

// Calculate attendance percentage for each student
$attendanceData = [];
foreach ($students as $student) {
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) AS total_days,
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS present_days
        FROM attendance
        WHERE student_id = ? AND course_id = ?
    ");
    $stmt->execute([$student['student_id'], $course_id]);
    $data = $stmt->fetch();
    
    $percentage = ($data['total_days'] > 0) ? round(($data['present_days'] / $data['total_days']) * 100, 2) : 0;
    
    $attendanceData[] = [
        'student_id' => $student['student_id'],
        'name' => $student['name'],
        'total_days' => $data['total_days'],
        'present_days' => $data['present_days'],
        'percentage' => $percentage
    ];
}
?>

<?php include '../includes/header.php'; ?>
<div style="max-width: 1000px; margin: 30px auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50; margin: 0;">Attendance Report for <?php echo htmlspecialchars($course['course_name']); ?></h2>
        <a href="dashboard.php" style="background: #7f8c8d; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none;">Back to Dashboard</a>
    </div>

    <div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px;">
        <h3 style="color: #34495e; margin-top: 0; margin-bottom: 15px;">Attendance Dates</h3>
        <?php if (count($dates) > 0): ?>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php foreach ($dates as $date): ?>
                <div style="display: flex; align-items: center; background: #e0f7fa; border-radius: 20px; overflow: hidden;">
                    <span style="color: #00838f; padding: 6px 12px; font-size: 14px;">
                        <?php echo htmlspecialchars($date['attendance_date']); ?>
                    </span>
                    <a href="edit_attendance.php?course_id=<?php echo $course_id; ?>&date=<?php echo $date['attendance_date']; ?>" 
                       style="background: #f39c12; color: white; padding: 6px 12px; text-decoration: none; font-size: 14px;">
                        Edit
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: #777;">No attendance records found for this course.</p>
        <?php endif; ?>
    </div>

    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;">
        <h3 style="padding: 15px 20px; margin: 0; background: #f8f9fa; color: #34495e; border-bottom: 1px solid #eee;">
            Student Attendance Summary
        </h3>
        
        <?php if (count($attendanceData) > 0): ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f1f8e9;">
                            <th style="padding: 12px 15px; text-align: left;">Student ID</th>
                            <th style="padding: 12px 15px; text-align: left;">Name</th>
                            <th style="padding: 12px 15px; text-align: center;">Present</th>
                            <th style="padding: 12px 15px; text-align: center;">Total</th>
                            <th style="padding: 12px 15px; text-align: left;">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendanceData as $data): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px 15px;"><?php echo htmlspecialchars($data['student_id']); ?></td>
                            <td style="padding: 12px 15px;"><?php echo htmlspecialchars($data['name']); ?></td>
                            <td style="padding: 12px 15px; text-align: center;"><?php echo htmlspecialchars($data['present_days']); ?></td>
                            <td style="padding: 12px 15px; text-align: center;"><?php echo htmlspecialchars($data['total_days']); ?></td>
                            <td style="padding: 12px 15px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="min-width: 40px;"><?php echo htmlspecialchars($data['percentage']); ?>%</span>
                                    <div style="height: 8px; background: #e0e0e0; flex-grow: 1; border-radius: 4px;">
                                        <div style="height: 100%; width: <?php echo $data['percentage']; ?>%; 
                                            background: <?php echo $data['percentage'] > 75 ? '#2ecc71' : ($data['percentage'] > 50 ? '#f39c12' : '#e74c3c'); ?>; 
                                            border-radius: 4px;">
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="padding: 20px; text-align: center; color: #777;">No attendance data available for students in this course.</p>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>