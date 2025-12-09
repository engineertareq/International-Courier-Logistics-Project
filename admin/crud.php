<?php
// Start Session for CSRF tokens and Flash messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db/config.php';

// --- SECURITY: Generate CSRF Token ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// --- HELPER: Flash Message Handling ---
$msg = "";
$msg_type = "";
if (isset($_SESSION['flash_msg'])) {
    $msg = $_SESSION['flash_msg'];
    $msg_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_msg'], $_SESSION['flash_type']); // Clear after showing
}

// --- HELPER: Sticky Form Data ---
function old($field_name) {
    return isset($_POST[$field_name]) ? htmlspecialchars($_POST[$field_name]) : '';
}

// ==========================================
// 1. FORM PROCESSING LOGIC
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. CSRF Check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security violation: Invalid CSRF token.");
    }

    // --- A. INSERT BRANCH ---
    if (isset($_POST["add_branch"])) {
        $name = trim($_POST['b_name']);
        $country_id = $_POST['b_country_id'];
        $address = trim($_POST['b_address']);
        $type = $_POST['b_type'];

        if (!empty($name) && !empty($country_id)) {
            $stmt = $conn->prepare("INSERT INTO branches (name, country_id, address, type) VALUES (:name, :cid, :addr, :type)");
            if ($stmt->execute([':name' => $name, ':cid' => $country_id, ':addr' => $address, ':type' => $type])) {
                $_SESSION['flash_msg'] = "Branch Added Successfully!";
                $_SESSION['flash_type'] = "success";
                
                // FIX: Save Session & Keep URL Query Params
                session_write_close();
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            } else {
                $msg = "Database Error: Could not add branch."; $msg_type = "danger";
            }
        }
    }

    // --- B. INSERT USER (TRANSACTION FIX) ---
    if (isset($_POST["add_user"])) {
        $name = trim($_POST['u_name']);
        $email = trim($_POST['u_email']);
        $raw_pass = $_POST['u_pass'];
        $branch_id = $_POST['u_branch_id'];
        $role_id = $_POST['u_role_id']; 
        
        $hashed_pass = password_hash($raw_pass, PASSWORD_DEFAULT);

        if (!empty($name) && !empty($email) && !empty($role_id)) {
            try {
                // START TRANSACTION
                $conn->beginTransaction();

                // 1. Create User
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, branch_id) VALUES (:name, :email, :pass, :bid)");
                $stmt->execute([':name' => $name, ':email' => $email, ':pass' => $hashed_pass, ':bid' => $branch_id]);
                $new_user_id = $conn->lastInsertId();

                // 2. Assign Role immediately
                $stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:uid, :rid)");
                $stmt->execute([':uid' => $new_user_id, ':rid' => $role_id]);

                // COMMIT
                $conn->commit();

                $_SESSION['flash_msg'] = "User Created & Role Assigned!";
                $_SESSION['flash_type'] = "success";
                
                // FIX: Save Session & Keep URL Query Params
                session_write_close();
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;

            } catch (Exception $e) {
                // ROLLBACK ON ERROR
                $conn->rollBack();
                $msg = "Failed: " . $e->getMessage(); 
                $msg_type = "danger";
            }
        }
    }

    // --- C. INSERT CARRIER ---
    if (isset($_POST["add_carrier"])) {
        $name = trim($_POST['c_name']);
        $code = trim($_POST['c_code']);

        if (!empty($name)) {
            $stmt = $conn->prepare("INSERT INTO carriers (name, service_code) VALUES (:name, :code)");
            if ($stmt->execute([':name' => $name, ':code' => $code])) {
                $_SESSION['flash_msg'] = "Carrier Added!";
                $_SESSION['flash_type'] = "success";
                
                // FIX: Save Session & Keep URL Query Params
                session_write_close();
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
    }

    // --- D. INSERT TAX ---
    if (isset($_POST["add_tax"])) {
        $name = trim($_POST['t_name']);
        $rate = floatval($_POST['t_rate']); // Force Float
        $country_id = $_POST['t_country_id'];

        if (!empty($name)) {
            $stmt = $conn->prepare("INSERT INTO taxes (name, rate, country_id) VALUES (:name, :rate, :cid)");
            if ($stmt->execute([':name' => $name, ':rate' => $rate, ':cid' => $country_id])) {
                $_SESSION['flash_msg'] = "Tax Rule Added!";
                $_SESSION['flash_type'] = "success";
                
                // FIX: Save Session & Keep URL Query Params
                session_write_close();
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
    }
}

// ==========================================
// 2. FETCH DROPDOWN DATA
// ==========================================
$countries_list = $conn->query("SELECT id, name FROM countries ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$branches_list  = $conn->query("SELECT id, name FROM branches ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$roles_list     = $conn->query("SELECT id, name FROM roles ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include 'inc/sidebar.php'; ?>
<main class="dashboard-main">
    <?php include "inc/nav.php" ?>
    
    <div class="dashboard-main-body">
        <h6 class="fw-semibold mb-4">Master Data Entry</h6>
        
        <?php if (!empty($msg)): ?>
            <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show" role="alert">
                <?= $msg ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row gy-4">

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><h5 class="card-title mb-0">Add Branch / Hub</h5></div>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-12">
                                    <label class="form-label">Branch Name</label>
                                    <input type="text" name="b_name" value="<?= old('b_name') ?>" class="form-control" placeholder="New York Main Hub" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Country</label>
                                    <select name="b_country_id" class="form-select" required>
                                        <option value="">Select Country</option>
                                        <?php foreach($countries_list as $c): ?>
                                            <option value="<?= $c['id'] ?>" <?= old('b_country_id') == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Type</label>
                                    <select name="b_type" class="form-select">
                                        <option value="Head Office" <?= old('b_type') == 'Head Office' ? 'selected' : '' ?>>Head Office</option>
                                        <option value="Hub" <?= old('b_type') == 'Hub' ? 'selected' : '' ?>>Hub</option>
                                        <option value="Depot" <?= old('b_type') == 'Depot' ? 'selected' : '' ?>>Depot</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="b_address" value="<?= old('b_address') ?>" class="form-control" placeholder="Address">
                                </div>
                                <div class="col-12"><button type="submit" name="add_branch" class="btn btn-primary-600">Save Branch</button></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><h5 class="card-title mb-0">Create User</h5></div>
                    <form method="post" autocomplete="off">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-12">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="u_name" value="<?= old('u_name') ?>" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="u_email" value="<?= old('u_email') ?>" class="form-control" autocomplete="new-password" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="u_pass" class="form-control" autocomplete="new-password" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Assign Branch</label>
                                    <select name="u_branch_id" class="form-select" required>
                                        <option value="">Select Branch</option>
                                        <?php foreach($branches_list as $b): ?>
                                            <option value="<?= $b['id'] ?>" <?= old('u_branch_id') == $b['id'] ? 'selected' : '' ?>><?= $b['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-primary">Assign Role (Required)</label>
                                    <select name="u_role_id" class="form-select" required>
                                        <option value="">Select Role</option>
                                        <?php foreach($roles_list as $r): ?>
                                            <option value="<?= $r['id'] ?>" <?= old('u_role_id') == $r['id'] ? 'selected' : '' ?>>
                                                <?= $r['name'] ?> 
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12"><button type="submit" name="add_user" class="btn btn-primary-600">Create User</button></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><h5 class="card-title mb-0">Add Carrier</h5></div>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-12">
                                    <label class="form-label">Carrier Name</label>
                                    <input type="text" name="c_name" value="<?= old('c_name') ?>" class="form-control" placeholder="FedEx / DHL" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Service Code</label>
                                    <input type="text" name="c_code" value="<?= old('c_code') ?>" class="form-control" placeholder="FEDEX_INT_PRIORITY">
                                </div>
                                <div class="col-12"><button type="submit" name="add_carrier" class="btn btn-primary-600">Save Carrier</button></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><h5 class="card-title mb-0">Tax Rules</h5></div>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-12">
                                    <label class="form-label">Tax Name</label>
                                    <input type="text" name="t_name" value="<?= old('t_name') ?>" class="form-control" placeholder="VAT / GST" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Rate (Decimal)</label>
                                    <input type="number" step="0.01" name="t_rate" value="<?= old('t_rate') ?>" class="form-control" placeholder="0.15" required>
                                    <small class="text-muted">Enter 0.15 for 15%</small>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Country</label>
                                    <select name="t_country_id" class="form-select" required>
                                        <option value="">Select Country</option>
                                        <?php foreach($countries_list as $c): ?>
                                            <option value="<?= $c['id'] ?>" <?= old('t_country_id') == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12"><button type="submit" name="add_tax" class="btn btn-primary-600">Save Tax Rule</button></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div> 
    </div>
</main>