<?php
include 'db/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    try {
        // 1. START TRANSACTION (The Secret Sauce)
        // This stops the database from saving "half" a shipment. 
        // It either saves EVERYTHING or NOTHING.
        $conn->beginTransaction();

        // --- A. Insert Main Shipment Info ---
        $tracking_no = "TRK-" . strtoupper(bin2hex(random_bytes(4))); 
        
        $stmt = $conn->prepare("INSERT INTO shipments (
            tracking_no, customer_id, 
            sender_name, sender_address, sender_city, sender_country_id,
            receiver_name, receiver_address, receiver_city, receiver_country_id,
            origin_branch_id, destination_branch_id, 
            service_type, shipment_type, total_weight, cod_amount, status
        ) VALUES (
            :track, :cust, 
            :s_name, :s_addr, :s_city, :s_cid,
            :r_name, :r_addr, :r_city, :r_cid,
            :orig, :dest, 
            :svc, :type, :wgt, :cod, 'Created'
        )");

        $stmt->execute([
            ':track' => $tracking_no,
            ':cust'  => $_POST['customer_id'], // Hidden ID or from Dropdown
            ':s_name'=> $_POST['sender_name'], 
            ':s_addr'=> $_POST['sender_address'], 
            ':s_city'=> $_POST['sender_city'], 
            ':s_cid' => $_POST['sender_country_id'],
            ':r_name'=> $_POST['receiver_name'], 
            ':r_addr'=> $_POST['receiver_address'], 
            ':r_city'=> $_POST['receiver_city'], 
            ':r_cid' => $_POST['receiver_country_id'],
            ':orig'  => $_POST['origin_branch_id'],
            ':dest'  => $_POST['dest_branch_id'],
            ':svc'   => $_POST['service_type'],
            ':type'  => $_POST['shipment_type'],
            ':wgt'   => $_POST['total_weight'],
            ':cod'   => $_POST['cod_amount']
        ]);

        // 2. GET THE ID OF THE SHIPMENT WE JUST MADE
        $shipment_id = $conn->lastInsertId();

        // --- B. Insert Multiple Items (Looping through the form array) ---
        // The HTML form uses name="items[0][desc]", name="items[1][desc]"
        if (isset($_POST['items']) && is_array($_POST['items'])) {
            $item_stmt = $conn->prepare("INSERT INTO shipment_items (shipment_id, description, quantity, weight, value_usd) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($_POST['items'] as $item) {
                // Only save if description is not empty
                if (!empty($item['description'])) {
                    $item_stmt->execute([
                        $shipment_id, 
                        $item['description'], 
                        $item['quantity'], 
                        $item['weight'], 
                        $item['value']
                    ]);
                }
            }
        }

        // --- C. Insert Customs Info (If entered) ---
        if (!empty($_POST['hs_code'])) {
            $customs_stmt = $conn->prepare("INSERT INTO customs_documents (shipment_id, hs_code, value_usd, origin_country_id, description) VALUES (?, ?, ?, ?, ?)");
            $customs_stmt->execute([
                $shipment_id,
                $_POST['hs_code'],
                $_POST['customs_value'], // usually sum of item values
                $_POST['origin_country'],
                $_POST['goods_desc']
            ]);
        }

        // 3. COMMIT EVERYTHING
        // This effectively "Saves" the data to all tables at once.
        $conn->commit();
        
        echo "<div class='alert alert-success'>Shipment Created Successfully! Tracking: <strong>$tracking_no</strong></div>";

    } catch (Exception $e) {
        // If anything fails (e.g., database error), undo everything.
        $conn->rollBack();
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
<form method="post">
    <h3>1. Address Details</h3>
    <input type="text" name="sender_name" placeholder="Sender Name">
    <input type="text" name="receiver_name" placeholder="Receiver Name">
    <h3>2. Shipment Details</h3>
    <select name="service_type"><option>Express</option></select>
    <input type="text" name="cod_amount" placeholder="COD Amount">

    <h3>3. Package Content</h3>
    <div id="items-container">
        
        <div class="item-row">
            <input type="text" name="items[0][description]" placeholder="Item 1 Description">
            <input type="number" name="items[0][quantity]" placeholder="Qty">
            <input type="number" name="items[0][weight]" placeholder="Weight">
            <input type="number" name="items[0][value]" placeholder="Value $">
        </div>

        <div class="item-row">
            <input type="text" name="items[1][description]" placeholder="Item 2 Description">
            <input type="number" name="items[1][quantity]" placeholder="Qty">
            <input type="number" name="items[1][weight]" placeholder="Weight">
            <input type="number" name="items[1][value]" placeholder="Value $">
        </div>
        
    </div>
    <h3>4. International Customs (Optional)</h3>
    <input type="text" name="hs_code" placeholder="HS Code">
    <input type="text" name="customs_value" placeholder="Total Value">

    <button type="submit" class="btn btn-primary">Create Shipment</button>
</form>