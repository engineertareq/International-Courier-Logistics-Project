<?php
include 'db/config.php';

// Fetch Master Data for Dropdowns
$products = getOptions($pdo, 'products', 'code', 'name');
$countries = getOptions($pdo, 'country_codes', 'code', 'name');
$hubs = getOptions($pdo, 'hubs', 'hub_code', 'name');

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. START TRANSACTION (Crucial for integrity)
        $pdo->beginTransaction();

        // 2. Generate AWB
        $awb = "AWB" . time() . rand(100,999);

        // 3. Insert Shipment Header
        $sql = "INSERT INTO shipments (
            awb_number, product_code, origin_hub, destination_hub,
            shipper_name, shipper_company, shipper_country, shipper_address1,
            receiver_name, receiver_company, receiver_country, receiver_address1,
            incoterm, bill_transport_to, description_of_goods, total_weight_kg
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $awb, $_POST['product_code'], $_POST['origin_hub'], $_POST['destination_hub'],
            $_POST['s_name'], $_POST['s_company'], $_POST['s_country'], $_POST['s_addr'],
            $_POST['r_name'], $_POST['r_company'], $_POST['r_country'], $_POST['r_addr'],
            $_POST['incoterm'], $_POST['bill_transport'], $_POST['desc_goods'], $_POST['total_weight']
        ]);
        
        $shipment_id = $pdo->lastInsertId();

        // 4. Insert Packages (Looping through arrays)
        if (!empty($_POST['pkg_weight'])) {
            $pkg_sql = "INSERT INTO shipment_packages (shipment_id, weight_kg, length_cm, width_cm, height_cm) VALUES (?, ?, ?, ?, ?)";
            $pkg_stmt = $pdo->prepare($pkg_sql);
            
            for ($i = 0; $i < count($_POST['pkg_weight']); $i++) {
                $pkg_stmt->execute([
                    $shipment_id, 
                    $_POST['pkg_weight'][$i], 
                    $_POST['pkg_l'][$i], 
                    $_POST['pkg_w'][$i], 
                    $_POST['pkg_h'][$i]
                ]);
            }
        }

        // 5. Insert Customs Items
        if (!empty($_POST['itm_desc'])) {
            $cus_sql = "INSERT INTO customs_line_items (shipment_id, description, quantity, unit_value, total_value) VALUES (?, ?, ?, ?, ?)";
            $cus_stmt = $pdo->prepare($cus_sql);

            for ($i = 0; $i < count($_POST['itm_desc']); $i++) {
                $total = $_POST['itm_qty'][$i] * $_POST['itm_val'][$i];
                $cus_stmt->execute([
                    $shipment_id, 
                    $_POST['itm_desc'][$i], 
                    $_POST['itm_qty'][$i], 
                    $_POST['itm_val'][$i], 
                    $total
                ]);
            }
        }

        $pdo->commit();
        $message = "<div class='alert alert-success'>Shipment Created! AWB: <strong>$awb</strong></div>";

    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Shipment - DHL Style</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-4">
        <h2 class="mb-4">Create New Shipment</h2>
        <?= $message ?>
        
        <form method="POST">
            <div class="row g-4">
                
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">1. Routing & Product</div>
                        <div class="card-body row g-3">
                            <div class="col-md-3">
                                <label>Product Service</label>
                                <select name="product_code" class="form-select" required>
                                    <?php foreach($products as $p): ?>
                                        <option value="<?= $p['code'] ?>"><?= $p['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Origin Hub</label>
                                <select name="origin_hub" class="form-select">
                                    <?php foreach($hubs as $h): ?>
                                        <option value="<?= $h['hub_code'] ?>"><?= $h['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Destination Hub</label>
                                <select name="destination_hub" class="form-select">
                                    <?php foreach($hubs as $h): ?>
                                        <option value="<?= $h['hub_code'] ?>"><?= $h['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Total Weight (Kg)</label>
                                <input type="number" step="0.01" name="total_weight" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">2. Shipper (Sender)</div>
                        <div class="card-body row g-2">
                            <div class="col-6"><input type="text" name="s_company" class="form-control" placeholder="Company"></div>
                            <div class="col-6"><input type="text" name="s_name" class="form-control" placeholder="Contact Name" required></div>
                            <div class="col-12"><input type="text" name="s_addr" class="form-control" placeholder="Address Line 1" required></div>
                            <div class="col-12">
                                <select name="s_country" class="form-select" required>
                                    <option value="">Select Country...</option>
                                    <?php foreach($countries as $c): ?>
                                        <option value="<?= $c['code'] ?>"><?= $c['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">3. Receiver (Consignee)</div>
                        <div class="card-body row g-2">
                            <div class="col-6"><input type="text" name="r_company" class="form-control" placeholder="Company"></div>
                            <div class="col-6"><input type="text" name="r_name" class="form-control" placeholder="Contact Name" required></div>
                            <div class="col-12"><input type="text" name="r_addr" class="form-control" placeholder="Address Line 1" required></div>
                            <div class="col-12">
                                <select name="r_country" class="form-select" required>
                                    <option value="">Select Country...</option>
                                    <?php foreach($countries as $c): ?>
                                        <option value="<?= $c['code'] ?>"><?= $c['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">4. Billing & Customs</div>
                        <div class="card-body row g-3">
                            <div class="col-md-4">
                                <label>Bill Transport To</label>
                                <select name="bill_transport" class="form-select">
                                    <option value="Shipper">Shipper (Prepaid)</option>
                                    <option value="Receiver">Receiver (Collect)</option>
                                    <option value="Third_Party">Third Party</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Incoterm (Duties)</label>
                                <select name="incoterm" class="form-select">
                                    <option value="DAP">DAP (Receiver pays duties)</option>
                                    <option value="DDP">DDP (Sender pays duties)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>General Description</label>
                                <input type="text" name="desc_goods" class="form-control" placeholder="e.g. Electronics" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <span>5. Packages</span>
                            <button type="button" class="btn btn-sm btn-success" onclick="addPkg()">+ Add</button>
                        </div>
                        <div class="card-body" id="pkg_container">
                            <div class="row g-2 mb-2">
                                <div class="col-3"><input type="number" name="pkg_weight[]" class="form-control" placeholder="Kg"></div>
                                <div class="col-3"><input type="number" name="pkg_l[]" class="form-control" placeholder="L cm"></div>
                                <div class="col-3"><input type="number" name="pkg_w[]" class="form-control" placeholder="W cm"></div>
                                <div class="col-3"><input type="number" name="pkg_h[]" class="form-control" placeholder="H cm"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <span>6. Customs Invoice Items</span>
                            <button type="button" class="btn btn-sm btn-success" onclick="addItem()">+ Add</button>
                        </div>
                        <div class="card-body" id="itm_container">
                            <div class="row g-2 mb-2">
                                <div class="col-6"><input type="text" name="itm_desc[]" class="form-control" placeholder="Item Desc"></div>
                                <div class="col-3"><input type="number" name="itm_qty[]" class="form-control" placeholder="Qty"></div>
                                <div class="col-3"><input type="number" name="itm_val[]" class="form-control" placeholder="Value $"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5">Create Shipment (AWB)</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function addPkg() {
            let html = `<div class="row g-2 mb-2">
                <div class="col-3"><input type="number" name="pkg_weight[]" class="form-control" placeholder="Kg"></div>
                <div class="col-3"><input type="number" name="pkg_l[]" class="form-control" placeholder="L cm"></div>
                <div class="col-3"><input type="number" name="pkg_w[]" class="form-control" placeholder="W cm"></div>
                <div class="col-3"><input type="number" name="pkg_h[]" class="form-control" placeholder="H cm"></div>
            </div>`;
            document.getElementById('pkg_container').insertAdjacentHTML('beforeend', html);
        }

        function addItem() {
            let html = `<div class="row g-2 mb-2">
                <div class="col-6"><input type="text" name="itm_desc[]" class="form-control" placeholder="Item Desc"></div>
                <div class="col-3"><input type="number" name="itm_qty[]" class="form-control" placeholder="Qty"></div>
                <div class="col-3"><input type="number" name="itm_val[]" class="form-control" placeholder="Value $"></div>
            </div>`;
            document.getElementById('itm_container').insertAdjacentHTML('beforeend', html);
        }
    </script>
</body>
</html>