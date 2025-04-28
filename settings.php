<?php
session_start();
if (!isset($_SESSION['user_role'])) {
    header("Location: login.html");
    exit();
}
require_once 'db_connection.php'; // Database connection

// Initialize variables
$error = '';
$success = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and update settings
    $company_name = trim($_POST['company_name']);
    $timezone = $_POST['timezone'];
    $maintenance_reminder_days = (int)$_POST['maintenance_reminder_days'];
    $items_per_page = (int)$_POST['items_per_page'];
    
    // Basic validation
    if (empty($company_name)) {
        $error = "Company name is required";
    } elseif ($maintenance_reminder_days < 1 || $maintenance_reminder_days > 30) {
        $error = "Maintenance reminder days must be between 1 and 30";
    } elseif ($items_per_page < 5 || $items_per_page > 100) {
        $error = "Items per page must be between 5 and 100";
    } else {
        // Update settings in database
        try {
            $stmt = $pdo->prepare("UPDATE system_settings SET 
                                  company_name = ?, 
                                  timezone = ?, 
                                  maintenance_reminder_days = ?, 
                                  items_per_page = ? 
                                  WHERE id = 1");
            $stmt->execute([$company_name, $timezone, $maintenance_reminder_days, $items_per_page]);
            $success = "Settings updated successfully!";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Get current settings
try {
    $stmt = $pdo->query("SELECT * FROM system_settings WHERE id = 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error loading settings: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - Fleet Maintenance Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">System Settings</h1>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="settings.php">
                    <div class="form-group">
                        <label for="company_name">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" 
                               value="<?php echo htmlspecialchars($settings['company_name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="timezone">Timezone</label>
                        <select class="form-control" id="timezone" name="timezone" required>
                            <?php
                            $timezones = DateTimeZone::listIdentifiers();
                            foreach ($timezones as $tz) {
                                $selected = ($tz === ($settings['timezone'] ?? 'UTC')) ? 'selected' : '';
                                echo "<option value=\"$tz\" $selected>$tz</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="maintenance_reminder_days">Maintenance Reminder Days (Before Due)</label>
                        <input type="number" class="form-control" id="maintenance_reminder_days" name="maintenance_reminder_days" 
                               min="1" max="30" value="<?php echo $settings['maintenance_reminder_days'] ?? 7; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="items_per_page">Items Per Page</label>
                        <input type="number" class="form-control" id="items_per_page" name="items_per_page" 
                               min="5" max="100" value="<?php echo $settings['items_per_page'] ?? 20; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="logo">Company Logo</label>
                        <input type="file" class="form-control-file" id="logo" name="logo">
                        <?php if (!empty($settings['logo_path'])): ?>
                            <img src="<?php echo $settings['logo_path']; ?>" alt="Company Logo" class="mt-2" style="max-height: 100px;">
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </main>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>