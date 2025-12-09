<?php
// invoice_functions.php

function calculateShippingCost($weight_kg, $zone_rate) {
    // Basic logic: Base rate $20 + $5 per extra KG
    $base_price = 20.00;
    $per_kg_price = 5.00;
    
    $freight = $base_price + ($weight_kg * $per_kg_price);
    
    // Fuel Surcharge is typically dynamic (e.g., 12.5% of freight)
    $fuel = $freight * 0.125; 
    
    return ['freight' => $freight, 'fuel' => $fuel];
}

function calculateTaxAndDuties($declared_value, $incoterm) {
    // If Incoterm is DDP (Delivered Duty Paid), Sender pays duties.
    // If DAP, Receiver pays (so it's 0 on this specific invoice unless we billing receiver)
    
    $duties = 0.00;
    $tax = 0.00;
    
    // Mock Logic: 5% Duty + 10% VAT
    if ($declared_value > 0) {
        $duties = $declared_value * 0.05;
        $tax = ($declared_value + $duties) * 0.10;
    }
    
    return ['duties' => $duties, 'tax' => $tax];
}
?>