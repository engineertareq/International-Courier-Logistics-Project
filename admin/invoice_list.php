<?php
include 'db/config.php';
include 'invoice_functions.php';

// A. HANDLE GENERATION
if (isset($_POST['generate_invoice'])) {
    $ship_id = $_POST['ship_id'];
    
    // 1. Fetch Shipment Details
    $stmt = $pdo->prepare("SELECT * FROM shipments WHERE id = ?");
    $stmt->execute([$ship_id]);
    $ship = $stmt->fetch();
    
    // 2. Calculate Costs
    $costs = calculateShippingCost($ship['chargeable_weight_kg'], 1); // Zone 1 hardcoded for demo
    $taxes = calculateTaxAndDuties($ship['customs_value'], $ship['incoterm']);
    
    // If DDP, bill sender for duties. If DAP, usually duties are billed to receiver separately.
    // For this demo, we assume we are generating the 'Transport Invoice'
    
    $grand_total = $costs['freight'] + $costs['fuel'] + $taxes['tax'];
    
    // 3. Create Invoice Record
    $inv_no = "INV-" . date('ym') . "-" . str_pad($ship['id'], 4, '0', STR_PAD_LEFT);
    $bill_to = ($ship['bill_transport_to'] == 'Shipper') ? $ship['shipper_company'] : $ship['receiver_company'];
    
    try {
        $pdo->beginTransaction();
        
        $sql = "INSERT INTO invoices (invoice_number, bill_to_company, invoice_date, due_date, total_freight, fuel_surcharge, tax_amount, grand_total, status)
                VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 15 DAY), ?, ?, ?, ?, 'Unpaid')";
        $pdo->prepare($sql)->execute([$inv_no, $bill_to, $costs['freight'], $costs['fuel'], $taxes['tax'], $grand_total]);
        
        // Link Shipment
        $pdo->prepare("INSERT INTO invoice_lines (invoice_number, shipment_id, charge_amount) VALUES (?, ?, ?)")
            ->execute([$inv_no, $ship['id'], $grand_total]);
            
        $pdo->commit();
        $msg = "Invoice Generated: $inv_no";
    } catch (Exception $e) {
        $pdo->rollBack();
        $msg = "Error: " . $e->getMessage();
    }
}

// B. FETCH DATA
// 1. Unbilled Shipments
$unbilled = $pdo->query("SELECT * FROM shipments WHERE id NOT IN (SELECT shipment_id FROM invoice_lines) ORDER BY id DESC")->fetchAll();

// 2. Existing Invoices
$invoices = $pdo->query("SELECT * FROM invoices ORDER BY invoice_date DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Invoice Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light p-4">

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-file-invoice-dollar"></i> Invoice Management</h2>
    </div>

    <div class="card mb-5 shadow-sm">
        <div class="card-header bg-warning text-dark fw-bold">Pending Billing (Ready to Generate)</div>
        <div class="card-body">
            <table class="table table-hover">
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
                        <td><strong><?= $row['awb_number'] ?></strong></td>
                        <td><?= $row['shipper_company'] ?></td>
                        <td><?= $row['receiver_company'] ?></td>
                        <td><span class="badge bg-secondary"><?= $row['bill_transport_to'] ?></span></td>
                        <td><?= $row['chargeable_weight_kg'] ?> kg</td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="ship_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="generate_invoice" class="btn btn-sm btn-success">
                                    <i class="fa-solid fa-magic"></i> Generate
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($unbilled)) echo "<tr><td colspan='6' class='text-center text-muted'>No pending shipments found.</td></tr>"; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">Generated Invoices</div>
        <div class="card-body">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Invoice #</th>
                        <th>Billed To</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Print</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($invoices as $inv): ?>
                    <tr>
                        <td><?= $inv['invoice_number'] ?></td>
                        <td><?= $inv['bill_to_company'] ?></td>
                        <td><?= $inv['invoice_date'] ?></td>
                        <td class="fw-bold text-end">$<?= number_format($inv['grand_total'], 2) ?></td>
                        <td>
                            <?php 
                            $badge = ($inv['status']=='Paid') ? 'bg-success' : 'bg-danger'; 
                            echo "<span class='badge $badge'>{$inv['status']}</span>";
                            ?>
                        </td>
                        <td class="text-center">
                            <a href="print_invoice.php?inv=<?= $inv['invoice_number'] ?>" target="_blank" class="btn btn-sm btn-outline-dark">
                                <i class="fa-solid fa-print"></i> Print PDF
                            </a>
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