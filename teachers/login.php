<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE email = ?");
    $stmt->execute([$email]);
    $teacher = $stmt->fetch();
    
    if ($teacher && $password == $teacher['password']) {
        $_SESSION['teacher_id'] = $teacher['teacher_id'];
        $_SESSION['teacher_name'] = $teacher['name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<?php include '../includes/header.php'; ?>
<div style="max-width: 400px; margin: 50px auto; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; color: #2c3e50; margin-bottom: 25px;">Teacher Login</h2>
    
    <?php if (isset($error)): ?>
        <div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form method="post" style="display: flex; flex-direction: column; gap: 15px;">
        <div>
            <label style="display: block; margin-bottom: 5px; color: #34495e;">Email:</label>
            <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        
        <div>
            <label style="display: block; margin-bottom: 5px; color: #34495e;">Password:</label>
            <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        
        <button type="submit" style="background: #3498db; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Login
        </button>
    </form>
</div>
<?php include '../includes/footer.php'; ?>