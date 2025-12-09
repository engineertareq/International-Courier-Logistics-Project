<main class="dashboard-main">
<?php
include 'db/config.php';
include 'inc/sidebar.php';
include "inc/nav.php";

$assign_message = "";
$update_message = "";

// ==========================================
// 1. FORM PROCESSING LOGIC
// ==========================================

// --- A. ASSIGN TASK (INSERT) ---
if (isset($_POST["assign_task"])) {
    $rider_id    = $_POST['rider_id'] ?? '';
    $shipment_id = $_POST['shipment_id'] ?? '';
    $task_type   = $_POST['task_type'] ?? '';

    if (!empty($rider_id) && !empty($shipment_id) && !empty($task_type)) {
        try {
            $stmt = $conn->prepare("INSERT INTO assignments (rider_id, shipment_id, type, status, assigned_at) 
                                    VALUES (:rid, :sid, :type, 'assigned', NOW())");
            $stmt->execute([':rid' => $rider_id, ':sid' => $shipment_id, ':type' => $task_type]);
            
            $assign_message = "<div class='alert alert-success p-3 text-center mt-2'>Task Assigned Successfully!</div>";
        } catch (PDOException $e) {
            $assign_message = "<div class='alert alert-danger p-3 text-center mt-2'>Error: " . $e->getMessage() . "</div>";
        }
    } else {
        $assign_message = "<div class='alert alert-warning p-3 text-center mt-2'>All fields are required!</div>";
    }
}

// --- B. UPDATE STATUS (UPDATE) ---
if (isset($_POST['update_status'])) {
    $a_id = $_POST['a_id'];
    $a_status = $_POST['a_status'];

    $sql = "UPDATE assignments SET status = :stat";
    if ($a_status == 'completed') { $sql .= ", completed_at = NOW()"; }
    $sql .= " WHERE id = :id";

    $stmt = $conn->prepare($sql);
    if ($stmt->execute([':stat' => $a_status, ':id' => $a_id])) {
        $update_message = "<div class='alert alert-success p-2 text-center mb-3'>Status Updated!</div>";
    } else {
        $update_message = "<div class='alert alert-danger p-2 text-center mb-3'>Update Failed!</div>";
    }
}

// ==========================================
// 2. FETCH DATA
// ==========================================

// Riders & Shipments for Dropdowns
$riders_list = $conn->query("SELECT r.id, u.name FROM riders r JOIN users u ON r.user_id = u.id WHERE r.active = 1")->fetchAll(PDO::FETCH_ASSOC);
$shipments_list = $conn->query("SELECT id, tracking_no FROM shipments ORDER BY id DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);

// Assignments List with Filter
$filter = $_GET['filter_status'] ?? 'all';
$sql_list = "SELECT a.id, a.status, a.type, a.assigned_at, u.name as rider_name, s.tracking_no 
             FROM assignments a 
             JOIN riders r ON a.rider_id = r.id 
             JOIN users u ON r.user_id = u.id 
             JOIN shipments s ON a.shipment_id = s.id";

if ($filter != 'all') { $sql_list .= " WHERE a.status = :fstat"; }
$sql_list .= " ORDER BY a.assigned_at DESC LIMIT 20";

$stmt_list = $conn->prepare($sql_list);
if ($filter != 'all') { $stmt_list->bindParam(':fstat', $filter); }
$stmt_list->execute();
$assignments_data = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

$status_colors = ['assigned'=>'primary', 'in_progress'=>'info', 'completed'=>'success', 'failed'=>'danger'];
?>

    <div class="dashboard-main-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Task Assignment & Management</h6>
        </div>

        <div class="row gy-4">

            <div class="col-lg-4 col-md-6">
                <div class="card"> 
                    <div class="card-header">
                        <h5 class="card-title mb-0">Assign Task</h5>
                    </div>
                    <form action="" method="post">
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-12">
                                    <label class="form-label">Select Rider</label>
                                    <div class="icon-field">
                                        <span class="icon"><iconify-icon icon="mdi:motorbike"></iconify-icon></span>
                                        <select name="rider_id" class="form-control form-select" required>
                                            <option value="">Choose a Rider...</option>
                                            <?php foreach ($riders_list as $rider): ?>
                                                <option value="<?= $rider['id'] ?>"><?= htmlspecialchars($rider['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Select Shipment</label>
                                    <div class="icon-field">
                                        <span class="icon"><iconify-icon icon="lucide:package"></iconify-icon></span>
                                        <select name="shipment_id" class="form-control form-select" required>
                                            <option value="">Choose Shipment...</option>
                                            <?php foreach ($shipments_list as $shipment): ?>
                                                <option value="<?= $shipment['id'] ?>">#<?= htmlspecialchars($shipment['tracking_no']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Task Type</label>
                                    <div class="icon-field">
                                        <span class="icon"><iconify-icon icon="carbon:task-settings"></iconify-icon></span>
                                        <select name="task_type" class="form-control form-select" required>
                                            <option value="PICKUP">PICKUP</option>
                                            <option value="DELIVERY">DELIVERY</option>
                                            <option value="TRANSFER">TRANSFER</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="assign_task" class="btn btn-warning w-100">Assign Task</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?= $assign_message ?>
                </div>
            </div>

            <div class="col-lg-8 col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="card-title mb-0">Manage Assignments</h5>
                        
                        <form action="" method="GET" class="d-flex align-items-center gap-2">
                            <?php if(isset($_GET['path'])): ?>
                                <input type="hidden" name="path" value="<?= htmlspecialchars($_GET['path']) ?>">
                            <?php endif; ?>
                            <select name="filter_status" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                                <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>All Statuses</option>
                                <option value="assigned" <?= $filter == 'assigned' ? 'selected' : '' ?>>Assigned</option>
                                <option value="in_progress" <?= $filter == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="completed" <?= $filter == 'completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </form>
                    </div>

                    <div class="card-body p-0">
                        <?= $update_message ?>
                        <div class="table-responsive scroll-sm">
                            <table class="table bordered-table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Tracking</th>
                                        <th>Rider</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(count($assignments_data) > 0): ?>
                                        <?php foreach($assignments_data as $row): 
                                            $color = $status_colors[$row['status']] ?? 'secondary';
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-warning-600">#<?= htmlspecialchars($row['tracking_no']) ?></span>
                                                <br>
                                                <span class="text-xs text-secondary-light"><?= $row['type'] ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($row['rider_name']) ?></td>
                                            
                                            <form action="" method="post">
                                                <td>
                                                    <input type="hidden" name="a_id" value="<?= $row['id'] ?>">
                                                    <select name="a_status" class="form-select form-select-sm border-<?= $color ?> text-<?= $color ?>">
                                                        <option value="assigned" <?= $row['status'] == 'assigned' ? 'selected' : '' ?>>Assigned</option>
                                                        <option value="in_progress" <?= $row['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                                        <option value="completed" <?= $row['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                                        <option value="failed" <?= $row['status'] == 'failed' ? 'selected' : '' ?>>Failed</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <button type="submit" name="update_status" class="btn btn-warning-600 btn-sm radius-8">
                                                        <iconify-icon icon="lucide:save"></iconify-icon>
                                                    </button>
                                                </td>
                                            </form>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center p-3 text-secondary-light">No tasks found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>