<?php
include 'db/config.php';

// Fetch Unbilled Shipments
$sql = "SELECT id, awb_number, bill_transport_to, shipper_company, receiver_company, total_weight_kg 
        FROM shipments 
        WHERE id NOT IN (SELECT shipment_id FROM invoice_lines)";
$unbilled = $pdo->query($sql)->fetchAll();

$msg = "";

if (isset($_POST['generate'])) {
    $ship_id = $_POST['shipment_id'];
    
    // 1. Calculate Costs (Mock Logic - in real world this comes from Rate Cards)
    // Let's say $10 base + $5 per KG
    $stmt = $pdo->prepare("SELECT * FROM shipments WHERE id = ?");
    $stmt->execute([$ship_id]);
    $shp = $stmt->fetch();
    
    $freight = 10 + ($shp['total_weight_kg'] * 5);
    $fuel    = $freight * 0.18; // 18% fuel surcharge
    $total   = $freight + $fuel;
    
    $inv_no = "INV-" . date('Y') . "-" . rand(1000,9999);
    
    // 2. Create Invoice Header
    $bill_to = ($shp['bill_transport_to'] == 'Shipper') ? $shp['shipper_company'] : $shp['receiver_company'];
    
    $ins_sql = "INSERT INTO invoices (invoice_number, bill_to_company, invoice_date, due_date, currency, total_freight, total_surcharges, grand_total, status)
                VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'USD', ?, ?, ?, 'Unpaid')";
    $pdo->prepare($ins_sql)->execute([$inv_no, $bill_to, $freight, $fuel, $total]);
    
    // 3. Link Shipment to Invoice
    $line_sql = "INSERT INTO invoice_lines (invoice_number, shipment_id, charge_amount) VALUES (?, ?, ?)";
    $pdo->prepare($line_sql)->execute([$inv_no, $ship_id, $total]);
    
    $msg = "<div class='alert alert-success'>Invoice Generated: <strong>$inv_no</strong> for $$total</div>";
    
    // Refresh list
    $unbilled = $pdo->query($sql)->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Generate Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h2>Finance / Invoice Generation</h2>
        <?= $msg ?>
        
        <div class="card mt-4">
            <div class="card-header">Pending Shipments (Unbilled)</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>AWB</th>
                            <th>Shipper</th>
                            <th>Receiver</th>
                            <th>Bill To</th>
                            <th>Weight</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($unbilled as $row): ?>
                        <tr>
                            <td><?= $row['awb_number'] ?></td>
                            <td><?= $row['shipper_company'] ?></td>
                            <td><?= $row['receiver_company'] ?></td>
                            <td><span class="badge bg-info"><?= $row['bill_transport_to'] ?></span></td>
                            <td><?= $row['total_weight_kg'] ?> kg</td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="shipment_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="generate" class="btn btn-sm btn-success">Generate Invoice</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>