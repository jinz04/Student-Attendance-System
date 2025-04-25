<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['teacher_id']) || !isset($_GET['course_id']) || !isset($_GET['date'])) {
    header("Location: dashboard.php");
    exit();
}

$course_id = $_GET['course_id'];
$date = $_GET['date'];
$teacher_id = $_SESSION['teacher_id'];

// Get course details
$stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

// Get existing attendance records
$stmt = $pdo->prepare("
    SELECT a.*, s.name 
    FROM attendance a
    JOIN students s ON a.student_id = s.student_id
    WHERE a.course_id = ? AND a.attendance_date = ?
    ORDER BY s.name
");
$stmt->execute([$course_id, $date]);
$attendanceRecords = $stmt->fetchAll();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Delete old records for this date (simplest way to handle updates)
    $stmt = $pdo->prepare("DELETE FROM attendance WHERE course_id = ? AND attendance_date = ?");
    $stmt->execute([$course_id, $date]);
    
    // Insert updated records
    foreach ($_POST['attendance'] as $attendance_id => $status) {
        $student_id = $_POST['student_id'][$attendance_id];
        $stmt = $pdo->prepare("
            INSERT INTO attendance 
            (attendance_id, student_id, course_id, teacher_id, attendance_date, status)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE status = ?
        ");
        $stmt->execute([
            $attendance_id,
            $student_id,
            $course_id,
            $teacher_id,
            $date,
            $status,
            $status
        ]);
    }
    
    $success = "Attendance updated successfully!";
    header("Refresh:1"); // Refresh to show updated data
}
?>

<?php include '../includes/header.php'; ?>
<div style="max-width: 800px; margin: 30px auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50; margin: 0;">
            Edit Attendance for <?php echo htmlspecialchars($course['course_name']); ?>
            <small style="font-size: 16px; color: #7f8c8d;">(<?php echo $date; ?>)</small>
        </h2>
        <a href="attendance_report.php?course_id=<?php echo $course_id; ?>" 
           style="background: #7f8c8d; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none;">
            Back to Report
        </a>
    </div>

    <?php if (isset($success)): ?>
        <div style="background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="post" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
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
                    <?php foreach ($attendanceRecords as $record): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px;">
                            <?php echo htmlspecialchars($record['student_id']); ?>
                            <input type="hidden" name="student_id[<?php echo $record['attendance_id']; ?>]" value="<?php echo $record['student_id']; ?>">
                        </td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($record['name']); ?></td>
                        <td style="padding: 12px;">
                            <select name="attendance[<?php echo $record['attendance_id']; ?>]" 
                                    style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="present" <?php echo $record['status'] == 'present' ? 'selected' : ''; ?>>Present</option>
                                <option value="absent" <?php echo $record['status'] == 'absent' ? 'selected' : ''; ?>>Absent</option>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" 
                    style="background: #f39c12; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                Save Changes
            </button>
            <a href="attendance_report.php?course_id=<?php echo $course_id; ?>" 
               style="background: #7f8c8d; color: white; padding: 12px 20px; border-radius: 4px; text-decoration: none; text-align: center;">
                Cancel
            </a>
        </div>
    </form>
</div>
<?php include '../includes/footer.php'; ?>