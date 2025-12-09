<?php
// 1. Start Session & Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db/config.php';

// 2. Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 3. Helper: Flash Message Handling
$msg = "";
$msg_type = "";
if (isset($_SESSION['flash_msg'])) {
    $msg = $_SESSION['flash_msg'];
    $msg_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_msg'], $_SESSION['flash_type']);
}

// ==========================================
// 4. FORM PROCESSING LOGIC
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Security Check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security violation: Invalid CSRF token.");
    }

    try {
        // --- A. CREATE NEW SHIPMENT ---
        if (isset($_POST["create_shipment"])) {
            // Unique Tracking Number
            $track_no = "TRK-" . strtoupper(bin2hex(random_bytes(4))) . rand(10,99);
            
            $cust_id = $_POST['shp_cust_id'];
            $s_branch = $_POST['shp_origin'];
            $d_branch = $_POST['shp_dest'];
            $weight = $_POST['shp_weight'];
            
            // Simplified insert (ensure your DB allows NULL for sender/receiver addresses or update schema)
            $stmt = $conn->prepare("INSERT INTO shipments (tracking_no, customer_id, origin_branch_id, destination_branch_id, total_weight, service_type, shipment_type, status) 
                                    VALUES (:trk, :cid, :orig, :dest, :wgt, 'Standard', 'parcel', 'Created')");
            
            $stmt->execute([':trk'=>$track_no, ':cid'=>$cust_id, ':orig'=>$s_branch, ':dest'=>$d_branch, ':wgt'=>$weight]);
            
            $_SESSION['flash_msg'] = "Shipment Created! Tracking No: <strong>$track_no</strong>";
            $_SESSION['flash_type'] = "success";
        }

        // --- B. ADD SHIPMENT ITEMS ---
        elseif (isset($_POST["add_item"])) {
            $sid = $_POST['it_ship_id'];
            $desc = $_POST['it_desc'];
            $val = $_POST['it_val'];
            
            $conn->prepare("INSERT INTO shipment_items (shipment_id, description, value_usd, quantity) VALUES (?, ?, ?, 1)")
                 ->execute([$sid, $desc, $val]);
            
            $_SESSION['flash_msg'] = "Item Added to Shipment!";
            $_SESSION['flash_type'] = "success";
        }

        // --- C. CUSTOMS DOCUMENTS ---
        elseif (isset($_POST["add_doc"])) {
            $sid = $_POST['doc_ship_id'];
            $hs = $_POST['doc_hs'];
            
            $conn->prepare("INSERT INTO customs_documents (shipment_id, hs_code, description) VALUES (?, ?, 'Commercial Invoice')")
                 ->execute([$sid, $hs]);
            
            $_SESSION['flash_msg'] = "Customs Doc Attached!";
            $_SESSION['flash_type'] = "success";
        }

        // --- D. LOG SHIPMENT STATUS ---
        elseif (isset($_POST["log_status"])) {
            $sid = $_POST['log_ship_id'];
            $status = $_POST['log_stat'];
            $bid = $_POST['log_branch'];
            
            // Update main table
            $conn->prepare("UPDATE shipments SET status = ? WHERE id = ?")->execute([$status, $sid]);
            
            // Insert log
            $conn->prepare("INSERT INTO shipment_status_log (shipment_id, status, branch_id) VALUES (?, ?, ?)")
                 ->execute([$sid, $status, $bid]);
                 
            $_SESSION['flash_msg'] = "Status Updated: $status";
            $_SESSION['flash_type'] = "success";
        }

        // --- E. ASSIGN RIDER ---
        elseif (isset($_POST["assign_rider"])) {
            $sid = $_POST['asn_ship_id'];
            $rid = $_POST['asn_rider_id'];
            $type = $_POST['asn_type'];
            
            $conn->prepare("INSERT INTO assignments (shipment_id, rider_id, type, status) VALUES (?, ?, ?, 'assigned')")
                 ->execute([$sid, $rid, $type]);
            
            $_SESSION['flash_msg'] = "Rider Assigned Successfully!";
            $_SESSION['flash_type'] = "success";
        }

        // --- F. GENERATE INVOICE ---
        elseif (isset($_POST["gen_invoice"])) {
            $sid = $_POST['inv_ship_id'];
            $cid = $_POST['inv_cust_id'];
            $amt = $_POST['inv_amount'];
            $inv_no = "INV-" . time() . rand(10,99);
            
            $conn->prepare("INSERT INTO invoices (invoice_no, shipment_id, customer_id, total_amount, issue_date) VALUES (?, ?, ?, ?, NOW())")
                 ->execute([$inv_no, $sid, $cid, $amt]);
            
            $_SESSION['flash_msg'] = "Invoice Generated: $inv_no";
            $_SESSION['flash_type'] = "success";
        }

        // --- G. ADD PAYMENT ---
        elseif (isset($_POST["add_payment"])) {
            $inv_id = $_POST['pay_inv_id'];
            $amt = $_POST['pay_amount'];
            $ref = "TXN-" . time();
            
            $conn->prepare("INSERT INTO payments (invoice_id, amount, method, status, transaction_ref) VALUES (?, ?, 'cash', 'completed', ?)")
                 ->execute([$inv_id, $amt, $ref]);
            
            $_SESSION['flash_msg'] = "Payment Recorded!";
            $_SESSION['flash_type'] = "success";
        }

        // --- H. CREATE COD ORDER ---
        elseif (isset($_POST["add_cod"])) {
            $sid = $_POST['cod_ship_id'];
            $amt = $_POST['cod_amount'];
            
            $conn->prepare("INSERT INTO cod_orders (shipment_id, amount, status) VALUES (?, ?, 'pending')")
                 ->execute([$sid, $amt]);
            
            $_SESSION['flash_msg'] = "COD Requirement Added!";
            $_SESSION['flash_type'] = "success";
        }

        // --- I. SETTLE COD ---
        elseif (isset($_POST["settle_cod"])) {
            $cod_id = $_POST['set_cod_id'];
            $cust_id = $_POST['set_cust_id'];
            $amt = $_POST['set_amount'];
            
            $conn->prepare("INSERT INTO cod_settlements (cod_id, customer_id, settlement_amount, settlement_date, status) VALUES (?, ?, ?, NOW(), 'completed')")
                 ->execute([$cod_id, $cust_id, $amt]);
                 
            $conn->prepare("UPDATE cod_orders SET status='collected' WHERE id=?")->execute([$cod_id]);
            
            $_SESSION['flash_msg'] = "COD Funds Settled!";
            $_SESSION['flash_type'] = "success";
        }

        // --- J. NOTIFICATION ---
        elseif (isset($_POST["send_notif"])) {
            $uid = $_POST['not_user_id'];
            $txt = $_POST['not_msg'];
            
            $conn->prepare("INSERT INTO notifications (user_id, type, message, status) VALUES (?, 'system', ?, 'sent')")
                 ->execute([$uid, $txt]);
            
            $_SESSION['flash_msg'] = "Notification Sent!";
            $_SESSION['flash_type'] = "success";
        }

        // --- K. ACTIVITY LOG ---
        elseif (isset($_POST["log_act"])) {
            $uid = $_POST['act_user_id'];
            $action = $_POST['act_action'];
            
            $conn->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, '{}')")
                 ->execute([$uid, $action]);
            
            $_SESSION['flash_msg'] = "Activity Logged!";
            $_SESSION['flash_type'] = "success";
        }

        // --- GLOBAL REDIRECT (PRG PATTERN) ---
        session_write_close();
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;

    } catch (PDOException $e) {
        // Handle DB Errors
        $_SESSION['flash_msg'] = "Database Error: " . $e->getMessage();
        $_SESSION['flash_type'] = "danger";
        // Do not redirect on error so user can see input (optional, but better to redirect with error msg)
        session_write_close();
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// ==========================================
// 5. FETCH DATA FOR DROPDOWNS
// ==========================================
// We run these AFTER the redirect to ensure we see the latest data
$shipments_list = $conn->query("SELECT id, tracking_no FROM shipments ORDER BY id DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
$customers_list = $conn->query("SELECT id, company_name FROM customers ORDER BY company_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$branches_list  = $conn->query("SELECT id, name FROM branches ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
// Fixed Riders Join
$riders_list    = $conn->query("SELECT r.id, u.name FROM riders r JOIN users u ON r.user_id = u.id")->fetchAll(PDO::FETCH_ASSOC);
$users_list     = $conn->query("SELECT id, name FROM users ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$invoices_list  = $conn->query("SELECT id, invoice_no, total_amount FROM invoices WHERE status = 'pending' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$cod_list       = $conn->query("SELECT id, amount, shipment_id FROM cod_orders WHERE status = 'pending'")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'inc/sidebar.php'; ?>
<main class="dashboard-main">
    <?php include "inc/nav.php"; ?>
    <div class="dashboard-main-body">

    <?php if (!empty($msg)): ?>
        <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show" role="alert">
            <?= $msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row gy-4">

        <div class="col-md-6">
            <div class="card h-100 border-primary">
                <div class="card-header bg-primary text-white"><h5 class="card-title mb-0 text-white">1. Create Shipment</h5></div>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-12">
                                <label>Customer</label>
                                <select name="shp_cust_id" class="form-select" required>
                                    <?php foreach($customers_list as $c): ?><option value="<?=$c['id']?>"><?=$c['company_name']?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label>Origin Branch</label>
                                <select name="shp_origin" class="form-select">
                                    <?php foreach($branches_list as $b): ?><option value="<?=$b['id']?>"><?=$b['name']?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label>Dest Branch</label>
                                <select name="shp_dest" class="form-select">
                                    <?php foreach($branches_list as $b): ?><option value="<?=$b['id']?>"><?=$b['name']?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label>Total Weight (Kg)</label>
                                <input type="number" step="0.001" name="shp_weight" class="form-control" placeholder="1.5">
                            </div>
                            <div class="col-12"><button type="submit" name="create_shipment" class="btn btn-primary w-100">Generate Shipment</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">2. Add Items Content</h5></div>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-12">
                                <label>Select Shipment</label>
                                <select name="it_ship_id" class="form-select">
                                    <?php foreach($shipments_list as $s): ?><option value="<?=$s['id']?>"><?=$s['tracking_no']?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label>Description</label>
                                <input type="text" name="it_desc" class="form-control" placeholder="Blue Cotton Shirt">
                            </div>
                            <div class="col-12">
                                <label>Declared Value ($)</label>
                                <input type="number" name="it_val" class="form-control" placeholder="50.00">
                            </div>
                            <div class="col-12"><button type="submit" name="add_item" class="btn btn-secondary w-100">Add Item</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">3. Update Tracking Status</h5></div>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-12">
                                <label>Select Shipment</label>
                                <select name="log_ship_id" class="form-select">
                                    <?php foreach($shipments_list as $s): ?><option value="<?=$s['id']?>"><?=$s['tracking_no']?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label>New Status</label>
                                <select name="log_stat" class="form-select">
                                    <option value="PICKED_UP">Picked Up</option>
                                    <option value="IN_TRANSIT">In Transit</option>
                                    <option value="OUT_FOR_DELIVERY">Out for Delivery</option>
                                    <option value="DELIVERED">Delivered</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label>Current Location</label>
                                <select name="log_branch" class="form-select">
                                    <?php foreach($branches_list as $b): ?><option value="<?=$b['id']?>"><?=$b['name']?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12"><button type="submit" name="log_status" class="btn btn-info text-white w-100">Update Status</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">4. Assign Rider</h5></div>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-12">
                                <label>Select Shipment</label>
                                <select name="asn_ship_id" class="form-select">
                                    <?php foreach($shipments_list as $s): ?><option value="<?=$s['id']?>"><?=$s['tracking_no']?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label>Select Rider</label>
                                <select name="asn_rider_id" class="form-select">
                                    <?php foreach($riders_list as $r): ?><option value="<?=$r['id']?>"><?=$r['name']?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label>Task Type</label>
                                <select name="asn_type" class="form-select">
                                    <option value="PICKUP">Pickup</option>
                                    <option value="DELIVERY">Delivery</option>
                                </select>
                            </div>
                            <div class="col-12"><button type="submit" name="assign_rider" class="btn btn-warning w-100">Assign Task</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">5. Create Invoice</h5></div>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-12">
                                <label>Shipment</label>
                                <select name="inv_ship_id" class="form-select">
                                    <?php foreach($shipments_list as $s): ?><option value="<?=$s['id']?>"><?=$s['tracking_no']?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label>Customer</label>
                                <select name="inv_cust_id" class="form-select">
                                    <?php foreach($customers_list as $c): ?><option value="<?=$c['id']?>"><?=$c['company_name']?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label>Amount ($)</label>
                                <input type="number" name="inv_amount" class="form-control" placeholder="150.00">
                            </div>
                            <div class="col-12"><button type="submit" name="gen_invoice" class="btn btn-dark w-100">Generate Invoice</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">6. Receive Payment</h5></div>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-12">
                                <label>Pending Invoices</label>
                                <select name="pay_inv_id" class="form-select">
                                    <?php foreach($invoices_list as $i): ?>
                                        <option value="<?=$i['id']?>"><?=$i['invoice_no']?> ($<?=$i['total_amount']?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label>Payment Amount</label>
                                <input type="number" name="pay_amount" class="form-control" placeholder="Full amount">
                            </div>
                            <div class="col-12"><button type="submit" name="add_payment" class="btn btn-success w-100">Confirm Payment</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">7. Add COD Requirement</h5></div>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-12">
                                <label>Shipment</label>
                                <select name="cod_ship_id" class="form-select">
                                    <?php foreach($shipments_list as $s): ?><option value="<?=$s['id']?>"><?=$s['tracking_no']?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label>Amount to Collect ($)</label>
                                <input type="number" name="cod_amount" class="form-control" placeholder="200.00">
                            </div>
                            <div class="col-12"><button type="submit" name="add_cod" class="btn btn-danger w-100">Add COD</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">8. Settle COD to Customer</h5></div>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="card-body">
                        <div class="row gy-3">
                            <div class="col-12">
                                <label>Unsettled COD Orders</label>
                                <select name="set_cod_id" class="form-select">
                                    <?php foreach($cod_list as $cl): ?>
                                        <option value="<?=$cl['id']?>">COD ID: <?=$cl['id']?> ($<?=$cl['amount']?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label>Customer</label>
                                <select name="set_cust_id" class="form-select">
                                    <?php foreach($customers_list as $c): ?><option value="<?=$c['id']?>"><?=$c['company_name']?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label>Settle Amount</label>
                                <input type="number" name="set_amount" class="form-control" placeholder="Amount after fees">
                            </div>
                            <div class="col-12"><button type="submit" name="settle_cod" class="btn btn-outline-danger w-100">Settle Funds</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">9. Customs Data</h5></div>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="card-body">
                        <div class="mb-3">
                            <select name="doc_ship_id" class="form-select"><?php foreach($shipments_list as $s): ?><option value="<?=$s['id']?>"><?=$s['tracking_no']?></option><?php endforeach; ?></select>
                        </div>
                        <div class="mb-3"><input type="text" name="doc_hs" class="form-control" placeholder="HS Code (e.g. 8501.10)"></div>
                        <button type="submit" name="add_doc" class="btn btn-light border w-100">Save Doc</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">10. Send Notification</h5></div>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="card-body">
                        <div class="mb-3">
                            <select name="not_user_id" class="form-select"><?php foreach($users_list as $u): ?><option value="<?=$u['id']?>"><?=$u['name']?></option><?php endforeach; ?></select>
                        </div>
                        <div class="mb-3"><input type="text" name="not_msg" class="form-control" placeholder="Message content"></div>
                        <button type="submit" name="send_notif" class="btn btn-light border w-100">Send Alert</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="card-title mb-0">11. Log Activity</h5></div>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="card-body">
                        <div class="mb-3">
                            <select name="act_user_id" class="form-select"><?php foreach($users_list as $u): ?><option value="<?=$u['id']?>"><?=$u['name']?></option><?php endforeach; ?></select>
                        </div>
                        <div class="mb-3"><input type="text" name="act_action" class="form-control" placeholder="Action Name"></div>
                        <button type="submit" name="log_act" class="btn btn-light border w-100">Log Now</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    </div>
</main>