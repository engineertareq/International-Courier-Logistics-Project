<?php
include 'db/config.php';

if (!isset($_GET['inv'])) die("Invoice not found");
$inv_no = $_GET['inv'];

// 1. Fetch Invoice Header
$stmt = $pdo->prepare("SELECT * FROM invoices WHERE invoice_number = ?");
$stmt->execute([$inv_no]);
$invoice = $stmt->fetch();

if (!$invoice) die("Invalid Invoice");

// 2. Fetch Line Items (Shipments linked to this invoice)
$stmt = $pdo->prepare("
    SELECT il.*, s.awb_number, s.shipper_country, s.receiver_country, s.total_weight_kg, s.created_at 
    FROM invoice_lines il 
    JOIN shipments s ON il.shipment_id = s.id 
    WHERE il.invoice_number = ?
");
$stmt->execute([$inv_no]);
$lines = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Invoice <?= $inv_no ?></title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; padding: 40px; }
        .invoice-box { max-width: 800px; margin: auto; border: 1px solid #eee; padding: 30px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .logo { font-size: 30px; font-weight: bold; color: #d40511; /* DHL Red */ font-style: italic; }
        .invoice-details { text-align: right; }
        .invoice-details h1 { margin: 0; font-size: 24px; color: #555; }
        
        .addresses { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .addr-box h3 { font-size: 14px; text-transform: uppercase; color: #999; margin-bottom: 5px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th { background: #f9f9f9; text-align: left; padding: 10px; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #ddd; }
        table td { padding: 10px; border-bottom: 1px solid #eee; font-size: 14px; }
        .text-right { text-align: right; }
        
        .totals { float: right; width: 40%; }
        .totals .row { display: flex; justify-content: space-between; padding: 5px 0; }
        .totals .grand-total { font-weight: bold; font-size: 18px; border-top: 2px solid #333; margin-top: 10px; padding-top: 10px; }
        
        .footer { clear: both; margin-top: 60px; font-size: 12px; color: #777; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
        
        @media print {
            .invoice-box { border: none; box-shadow: none; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <div class="header">
        <div class="logo">COURIER PRO</div> <div class="invoice-details">
            <h1>INVOICE</h1>
            <p>
                <strong>Number:</strong> <?= $invoice['invoice_number'] ?><br>
                <strong>Date:</strong> <?= date('d M Y', strtotime($invoice['invoice_date'])) ?><br>
                <strong>Due Date:</strong> <?= date('d M Y', strtotime($invoice['due_date'])) ?>
            </p>
        </div>
    </div>

    <div class="addresses">
        <div class="addr-box" style="width: 45%;">
            <h3>Bill To:</h3>
            <strong><?= $invoice['bill_to_company'] ?></strong><br>
            <?= $invoice['bill_to_address'] ?? 'Registered Address' ?><br>
            Client Account: #CUST-<?= rand(100,999) ?>
        </div>
        <div class="addr-box" style="width: 45%; text-align: right;">
            <h3>Pay To:</h3>
            <strong>Courier Pro International</strong><br>
            123 Logistics Way<br>
            Global Hub, NY 10001<br>
            Tax ID: US-99887766
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>AWB / Tracking</th>
                <th>Service Date</th>
                <th>Route</th>
                <th>Weight</th>
                <th class="text-right">Amount (USD)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($lines as $line): ?>
            <tr>
                <td><?= $line['awb_number'] ?></td>
                <td><?= date('d-M', strtotime($line['created_at'])) ?></td>
                <td><?= $line['shipper_country'] ?> <span style="color:#999">&rarr;</span> <?= $line['receiver_country'] ?></td>
                <td><?= $line['total_weight_kg'] ?> kg</td>
                <td class="text-right"><?= number_format($line['charge_amount'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <div class="row">
            <span>Base Freight:</span>
            <span>$<?= number_format($invoice['total_freight'], 2) ?></span>
        </div>
        <div class="row">
            <span>Fuel Surcharge:</span>
            <span>$<?= number_format($invoice['fuel_surcharge'], 2) ?></span>
        </div>
        <div class="row">
            <span>Tax / VAT:</span>
            <span>$<?= number_format($invoice['tax_amount'], 2) ?></span>
        </div>
        <div class="row grand-total">
            <span>TOTAL DUE:</span>
            <span>$<?= number_format($invoice['grand_total'], 2) ?></span>
        </div>
    </div>

    <div class="footer">
        <p>Payment Terms: Net 30 Days. Please include invoice number on check or wire transfer.</p>
        <button class="btn-print" onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #333; color: #fff; border: none; margin-top: 10px;">Print Invoice</button>
    </div>
</div>

</body>
</html>