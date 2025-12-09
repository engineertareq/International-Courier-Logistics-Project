<?php
// 1. Start Session & Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db/config.php';

// 2. CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 3. Helper: Flash Messages
$msg = "";
$msg_type = "";
if (isset($_SESSION['flash_msg'])) {
    $msg = $_SESSION['flash_msg'];
    $msg_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_msg'], $_SESSION['flash_type']);
}

// 4. Helper: Sticky Data
function old($field) {
    return isset($_POST[$field]) ? htmlspecialchars($_POST[$field]) : '';
}

// ==========================================
// 5. FORM PROCESSING LOGIC
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF Check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security Error: Invalid Token");
    }

    // --- A. INSERT RATE SLAB ---
    if (isset($_POST["add_slab"])) {
        $zone = $_POST['s_zone_id'];
        $service = $_POST['s_service'];
        $min = $_POST['s_min'];
        $max = $_POST['s_max'];
        $base = $_POST['s_base'];
        $per_kg = $_POST['s_per_kg'];

        if (!empty($zone) && !empty($base)) {
            try {
                $stmt = $conn->prepare("INSERT INTO rate_slabs (zone_id, service_type, min_weight, max_weight, base_price, price_per_kg) 
                                        VALUES (:zid, :svc, :min, :max, :base, :pkg)");
                $stmt->execute([':zid'=>$zone, ':svc'=>$service, ':min'=>$min, ':max'=>$max, ':base'=>$base, ':pkg'=>$per_kg]);
                
                $_SESSION['flash_msg'] = "Rate Slab Added Successfully!";
                $_SESSION['flash_type'] = "success";
                session_write_close();
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            } catch (PDOException $e) {
                $msg = "Error: " . $e->getMessage(); $msg_type = "danger";
            }
        }
    }

    // --- B. MAP COUNTRY TO ZONE ---
    if (isset($_POST["map_country"])) {
        $zone_id = $_POST['mz_zone_id'];
        $country_id = $_POST['mz_country_id'];

        if (!empty($zone_id) && !empty($country_id)) {
            try {
                // Using INSERT IGNORE or ON DUPLICATE to handle existing mappings
                $stmt = $conn->prepare("INSERT IGNORE INTO zone_countries (zone_id, country_id) VALUES (:zid, :cid)");
                $stmt->execute([':zid'=>$zone_id, ':cid'=>$country_id]);
                
                $_SESSION['flash_msg'] = "Country Mapped to Zone!";
                $_SESSION['flash_type'] = "success";
                session_write_close();
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            } catch (PDOException $e) {
                $msg = "Error: " . $e->getMessage(); $msg_type = "danger";
            }
        }
    }

    // --- C. CREATE CUSTOMER PROFILE (Fixed to match DB Schema) ---
    if (isset($_POST["add_customer"])) {
        $user_id = $_POST['c_user_id'];
        $comp_name = trim($_POST['c_company']);
        $contact = trim($_POST['c_contact']);
        $vat = trim($_POST['c_vat']);
        $country = $_POST['c_country_id'];
        // Added missing fields from Schema
        $address = trim($_POST['c_address']);
        $city = trim($_POST['c_city']);
        $zip = trim($_POST['c_zip']);

        if (!empty($user_id) && !empty($comp_name)) {
            try {
                $stmt = $conn->prepare("INSERT INTO customers (user_id, company_name, contact_person_name, vat_id, country_id, billing_address, city, postal_code) 
                                        VALUES (:uid, :comp, :cont, :vat, :cid, :addr, :city, :zip)");
                $stmt->execute([
                    ':uid'=>$user_id, ':comp'=>$comp_name, ':cont'=>$contact, 
                    ':vat'=>$vat, ':cid'=>$country, ':addr'=>$address, 
                    ':city'=>$city, ':zip'=>$zip
                ]);

                $_SESSION['flash_msg'] = "Customer Profile Created!";
                $_SESSION['flash_type'] = "success";
                session_write_close();
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            } catch (PDOException $e) {
                // Handle Unique Constraint on user_id
                if ($e->errorInfo[1] == 1062) {
                    $msg = "Error: This user already has a customer profile.";
                } else {
                    $msg = "Database Error: " . $e->getMessage();
                }
                $msg_type = "danger";
            }
        }
    }

    // --- D. ADD RIDER ---
    if (isset($_POST["add_rider"])) {
        $user_id = $_POST['rd_user_id'];
        $branch_id = $_POST['rd_branch_id'];
        $vehicle = $_POST['rd_vehicle'];

        if (!empty($user_id) && !empty($branch_id)) {
            try {
                $stmt = $conn->prepare("INSERT INTO riders (user_id, branch_id, vehicle_type, availability, active) 
                                        VALUES (:uid, :bid, :veh, 1, 1)");
                $stmt->execute([':uid'=>$user_id, ':bid'=>$branch_id, ':veh'=>$vehicle]);
                
                $_SESSION['flash_msg'] = "Rider Registered Successfully!";
                $_SESSION['flash_type'] = "success";
                session_write_close();
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            } catch (PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    $msg = "Error: This user is already registered as a rider.";
                } else {
                    $msg = "Database Error: " . $e->getMessage();
                }
                $msg_type = "danger";
            }
        }
    }

    // --- E. ASSIGN ROLE ---
    if (isset($_POST["assign_role"])) {
        $user_id = $_POST['ar_user_id'];
        $role_id = $_POST['ar_role_id'];

        if (!empty($user_id) && !empty($role_id)) {
            try {
                $stmt = $conn->prepare("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:uid, :rid)");
                $stmt->execute([':uid'=>$user_id, ':rid'=>$role_id]);
                
                $_SESSION['flash_msg'] = "Role Assigned Successfully!";
                $_SESSION['flash_type'] = "success";
                session_write_close();
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            } catch (PDOException $e) {
                $msg = "Error: " . $e->getMessage(); $msg_type = "danger";
            }
        }
    }
}

// ==========================================
// 6. FETCH DROPDOWN DATA
// ==========================================
$zones_list     = $conn->query("SELECT id, name FROM rate_zones ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$countries_list = $conn->query("SELECT id, name FROM countries ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$users_list     = $conn->query("SELECT id, name, email FROM users ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$branches_list  = $conn->query("SELECT id, name FROM branches ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$roles_list     = $conn->query("SELECT id, name FROM roles ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'inc/sidebar.php'; ?>
<main class="dashboard-main">
    <?php include "inc/nav.php"; ?>
    <div class="dashboard-main-body">

        <h6 class="fw-semibold mb-4">Logistics Management</h6>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show" role="alert">
                <?= $msg ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row gy-4">

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><h5 class="card-title mb-0">Add Rate Slab</h5></div>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-6">
                                    <label class="form-label">Zone</label>
                                    <select name="s_zone_id" class="form-select" required>
                                        <option value="">Select Zone</option>
                                        <?php foreach($zones_list as $z): ?>
                                            <option value="<?= $z['id'] ?>" <?= old('s_zone_id') == $z['id'] ? 'selected' : '' ?>><?= $z['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Service Type</label>
                                    <select name="s_service" class="form-select">
                                        <option value="Standard">Standard</option>
                                        <option value="Express">Express</option>
                                        <option value="Economy">Economy</option>
                                    </select>
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Weight Range (Kg)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.001" name="s_min" class="form-control" placeholder="Min" required>
                                        <input type="number" step="0.001" name="s_max" class="form-control" placeholder="Max" required>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Pricing</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="s_base" class="form-control" placeholder="Base $" required>
                                        <input type="number" step="0.01" name="s_per_kg" class="form-control" placeholder="Extra/Kg" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" name="add_slab" class="btn btn-primary-600">Add Rate</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><h5 class="card-title mb-0">Map Country to Zone</h5></div>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-12">
                                    <label class="form-label">Select Zone</label>
                                    <select name="mz_zone_id" class="form-select" required>
                                        <option value="">Select Zone</option>
                                        <?php foreach($zones_list as $z): ?>
                                            <option value="<?= $z['id'] ?>" <?= old('mz_zone_id') == $z['id'] ? 'selected' : '' ?>><?= $z['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Select Country</label>
                                    <select name="mz_country_id" class="form-select" required>
                                        <option value="">Select Country</option>
                                        <?php foreach($countries_list as $c): ?>
                                            <option value="<?= $c['id'] ?>" <?= old('mz_country_id') == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <button type="submit" name="map_country" class="btn btn-success-600">Map Country</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><h5 class="card-title mb-0">Create Customer Profile</h5></div>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-12">
                                    <label class="form-label">Link to User Account</label>
                                    <select name="c_user_id" class="form-select" required>
                                        <option value="">Select User...</option>
                                        <?php foreach($users_list as $u): ?>
                                            <option value="<?= $u['id'] ?>" <?= old('c_user_id') == $u['id'] ? 'selected' : '' ?>><?= $u['name'] ?> (<?= $u['email'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" name="c_company" class="form-control" value="<?= old('c_company') ?>" required>
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Contact Person</label>
                                    <input type="text" name="c_contact" class="form-control" value="<?= old('c_contact') ?>">
                                </div>

                                <div class="col-6">
                                    <label class="form-label">VAT / Tax ID</label>
                                    <input type="text" name="c_vat" class="form-control" value="<?= old('c_vat') ?>">
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Country</label>
                                    <select name="c_country_id" class="form-select">
                                        <?php foreach($countries_list as $c): ?>
                                            <option value="<?= $c['id'] ?>" <?= old('c_country_id') == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Billing Address</label>
                                    <input type="text" name="c_address" class="form-control" value="<?= old('c_address') ?>" placeholder="Full Address">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">City</label>
                                    <input type="text" name="c_city" class="form-control" value="<?= old('c_city') ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="c_zip" class="form-control" value="<?= old('c_zip') ?>">
                                </div>

                                <div class="col-12">
                                    <button type="submit" name="add_customer" class="btn btn-primary-600">Save Profile</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><h5 class="card-title mb-0">Add Rider</h5></div>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-12">
                                    <label class="form-label">Select User</label>
                                    <select name="rd_user_id" class="form-select" required>
                                        <option value="">Select User...</option>
                                        <?php foreach($users_list as $u): ?>
                                            <option value="<?= $u['id'] ?>" <?= old('rd_user_id') == $u['id'] ? 'selected' : '' ?>><?= $u['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Assign Branch</label>
                                    <select name="rd_branch_id" class="form-select" required>
                                        <?php foreach($branches_list as $b): ?>
                                            <option value="<?= $b['id'] ?>" <?= old('rd_branch_id') == $b['id'] ? 'selected' : '' ?>><?= $b['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Vehicle Type</label>
                                    <select name="rd_vehicle" class="form-select">
                                        <option value="bike">Bike</option>
                                        <option value="van">Van</option>
                                        <option value="scooter">Scooter</option>
                                        <option value="truck">Truck</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <button type="submit" name="add_rider" class="btn btn-warning-600">Register Rider</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><h5 class="card-title mb-0">Assign Role to User</h5></div>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-12">
                                    <label class="form-label">Select User</label>
                                    <select name="ar_user_id" class="form-select" required>
                                        <option value="">Select User...</option>
                                        <?php foreach($users_list as $u): ?>
                                            <option value="<?= $u['id'] ?>" <?= old('ar_user_id') == $u['id'] ? 'selected' : '' ?>><?= $u['name'] ?> (<?= $u['email'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Select Role</label>
                                    <select name="ar_role_id" class="form-select" required>
                                        <option value="">Select Role...</option>
                                        <?php foreach($roles_list as $r): ?>
                                            <option value="<?= $r['id'] ?>" <?= old('ar_role_id') == $r['id'] ? 'selected' : '' ?>><?= $r['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <button type="submit" name="assign_role" class="btn btn-info-600 text-white">Assign Role</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>