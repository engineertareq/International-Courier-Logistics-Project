<main class="dashboard-main">
<?php
include 'db/config.php';
include 'inc/sidebar.php';
include "inc/nav.php";

$update_msg = "";

// ==========================================
// 1. HANDLE STATUS UPDATES (POST)
// ==========================================
if (isset($_POST['update_status'])) {
    $assign_id = $_POST['a_id'];
    $new_status = $_POST['a_status'];

    if (!empty($assign_id) && !empty($new_status)) {
        // If status is 'completed', we also update the timestamp
        $sql = "UPDATE assignments SET status = :stat";
        if ($new_status == 'completed') {
            $sql .= ", completed_at = NOW()";
        }
        $sql .= " WHERE id = :id";

        $stmt = $conn->prepare($sql);
        if ($stmt->execute([':stat' => $new_status, ':id' => $assign_id])) {
            $update_msg = "<div class='alert alert-success py-2'>Status updated to <b>" . strtoupper($new_status) . "</b></div>";
        } else {
            $update_msg = "<div class='alert alert-danger py-2'>Update Failed!</div>";
        }
    }
}

// ==========================================
// 2. FILTERING LOGIC (GET)
// ==========================================
$filter_status = $_GET['filter_status'] ?? 'all';
$sql_query = "SELECT a.id, a.status, a.type, a.assigned_at, 
                     u.name as rider_name, 
                     s.tracking_no 
              FROM assignments a 
              JOIN riders r ON a.rider_id = r.id 
              JOIN users u ON r.user_id = u.id 
              JOIN shipments s ON a.shipment_id = s.id";

// Apply Filter if not 'all'
if ($filter_status != 'all') {
    $sql_query .= " WHERE a.status = :fstat";
}

$sql_query .= " ORDER BY a.assigned_at DESC LIMIT 50";

$stmt = $conn->prepare($sql_query);
if ($filter_status != 'all') {
    $stmt->bindParam(':fstat', $filter_status);
}
$stmt->execute();
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Status options array for dropdowns
$statuses = ['assigned', 'in_progress', 'completed', 'failed'];
?>

    <div class="dashboard-main-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Manage Assignment Status</h6>
        </div>

        <?= $update_msg ?>

        <div class="card h-100 p-0 radius-12">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                
                <form action="" method="GET" class="d-flex align-items-center gap-3">
                    <label class="form-label fw-semibold text-secondary-light text-sm mb-0">Filter By:</label>
                    <select name="filter_status" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <option value="all" <?= $filter_status == 'all' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="assigned" <?= $filter_status == 'assigned' ? 'selected' : '' ?>>Assigned</option>
                        <option value="in_progress" <?= $filter_status == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="completed" <?= $filter_status == 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="failed" <?= $filter_status == 'failed' ? 'selected' : '' ?>>Failed</option>
                    </select>
                </form>

            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table table-hover mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Tracking No</th>
                                <th scope="col">Rider Name</th>
                                <th scope="col">Type</th>
                                <th scope="col">Assigned Date</th>
                                <th scope="col" class="text-center">Current Status</th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($assignments) > 0): ?>
                                <?php foreach ($assignments as $row): ?>
                                    <tr>
                                        <td>
                                            <span class="text-primary-600 fw-bold">#<?= htmlspecialchars($row['tracking_no']) ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="text-secondary-light"><?= htmlspecialchars($row['rider_name']) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-neutral-200 text-neutral-600"><?= htmlspecialchars($row['type']) ?></span>
                                        </td>
                                        <td><?= date('d M Y, h:i A', strtotime($row['assigned_at'])) ?></td>
                                        
                                        <form action="" method="post">
                                            <td class="text-center" style="min-width: 150px;">
                                                <input type="hidden" name="a_id" value="<?= $row['id'] ?>">
                                                
                                                <select name="a_status" class="form-select form-select-sm 
                                                    <?= $row['status'] == 'completed' ? 'border-success text-success' : '' ?>
                                                    <?= $row['status'] == 'failed' ? 'border-danger text-danger' : '' ?>">
                                                    
                                                    <?php foreach ($statuses as $st): ?>
                                                        <option value="<?= $st ?>" <?= $row['status'] == $st ? 'selected' : '' ?>>
                                                            <?= ucfirst(str_replace('_', ' ', $st)) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button type="submit" name="update_status" class="btn btn-primary-600 btn-sm px-12 py-6 radius-8">
                                                    <iconify-icon icon="lucide:save" class="text-xl"></iconify-icon>
                                                </button>
                                            </td>
                                        </form>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary-light">No assignments found for this filter.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>