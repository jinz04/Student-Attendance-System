<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['teacher_id']) || !isset($_GET['course_id'])) {
    header("Location: dashboard.php");
    exit();
}

$course_id = $_GET['course_id'];
$teacher_id = $_SESSION['teacher_id'];

// Get course details
$stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    die("Course not found");
}

// Get students enrolled in this course
$stmt = $pdo->prepare("
    SELECT s.* FROM students s
    JOIN student_courses sc ON s.student_id = sc.student_id
    WHERE sc.course_id = ? AND s.status = 'active'
");
$stmt->execute([$course_id]);
$students = $stmt->fetchAll();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $attendance_date = $_POST['attendance_date'];
    
    // Check if attendance already exists for this date
    $stmt = $pdo->prepare("
        SELECT * FROM attendance 
        WHERE course_id = ? AND attendance_date = ?
        LIMIT 1
    ");
    $stmt->execute([$course_id, $attendance_date]);
    
    if ($stmt->rowCount() > 0) {
        $error = "Attendance for this date already exists. Please edit instead.";
    } else {
        // Insert attendance records
        foreach ($_POST['attendance'] as $student_id => $status) {
            $stmt = $pdo->prepare("
                INSERT INTO attendance 
                (student_id, course_id, teacher_id, attendance_date, status)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $student_id,
                $course_id,
                $teacher_id,
                $attendance_date,
                $status
            ]);
        }
        $success = "Attendance marked successfully!";
    }
}
?>

<?php include '../includes/header.php'; ?>
<div style="max-width: 800px; margin: 30px auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50; margin: 0;">Mark Attendance for <?php echo htmlspecialchars($course['course_name']); ?></h2>
        <a href="dashboard.php" style="background: #7f8c8d; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none;">Back</a>
    </div>

    <?php if (isset($error)): ?>
        <div style="background: #ffebee; color: #c62828; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div style="background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <form method="post" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #34495e; font-weight: 500;">Attendance Date</label>
            <input type="date" name="attendance_date" required 
                   style="width: 200px; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"
                   value="<?php echo date('Y-m-d'); ?>">
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #eee;">Student ID</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #eee;">Name</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #eee;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px;"><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($student['name']); ?></td>
                        <td style="padding: 12px;">
                            <select name="attendance[<?php echo htmlspecialchars($student['student_id']); ?>]" 
                                    style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <button type="submit" 
                style="background: #2ecc71; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Submit Attendance
        </button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>