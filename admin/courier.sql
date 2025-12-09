CREATE DATABASE courier_system;
USE courier_system;

/* ===================================================
   1. GLOBAL SETTINGS & LOCATIONS
   =================================================== */

CREATE TABLE countries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(40) UNIQUE NOT NULL,
    iso_code VARCHAR(3) UNIQUE NOT NULL -- US, IN, BD
);

CREATE TABLE rate_zones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Mapping Countries to Zones (Many-to-Many)
CREATE TABLE zone_countries (
    zone_id INT NOT NULL,
    country_id INT NOT NULL,
    PRIMARY KEY (zone_id, country_id),
    FOREIGN KEY (zone_id) REFERENCES rate_zones(id) ON DELETE CASCADE,
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE
);

CREATE TABLE branches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    country_id INT NOT NULL,
    address VARCHAR(300),
    phone VARCHAR(20),
    type ENUM('Head Office', 'Hub', 'Depot', 'Local Branch') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (country_id) REFERENCES countries(id)
);

/* ===================================================
   2. USERS & ROLES (RBAC)
   =================================================== */

CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(60) UNIQUE NOT NULL, -- admin, operations_staff, rider, customer
    designation VARCHAR(30)
);

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(60) NOT NULL,
    email VARCHAR(60) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL, -- Increased length for hashed passwords
    branch_id INT, -- For Admin/Staff/Rider affiliation
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id) REFERENCES branches(id)
);

CREATE TABLE user_roles (
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

/* ===================================================
   3. ACTORS (Customers & Riders)
   =================================================== */

CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE, -- Link to login (Nullable for walk-ins)
    company_name VARCHAR(50),
    contact_person_name VARCHAR(50),
    billing_address VARCHAR(200),
    country_id INT,
    city VARCHAR(30),
    postal_code VARCHAR(10),
    vat_id VARCHAR(50), -- For B2B/International billing
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (country_id) REFERENCES countries(id)
);

CREATE TABLE riders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    branch_id INT NOT NULL, -- The rider's base hub
    vehicle_type ENUM('bike', 'van', 'truck', 'scooter'),
    availability BOOLEAN DEFAULT TRUE,
    active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (branch_id) REFERENCES branches(id)
);

/* ===================================================
   4. LOGISTICS DATA (Carriers & Rates)
   =================================================== */

CREATE TABLE carriers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(40) NOT NULL,
    service_code VARCHAR(25) -- e.g. 'FEDEX_INT_PRIORITY'
);

CREATE TABLE rate_slabs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    zone_id INT NOT NULL,
    service_type ENUM('Standard', 'Express', 'Economy') NOT NULL,
    min_weight DECIMAL(10,3),
    max_weight DECIMAL(10,3),
    base_price DECIMAL(10,2),
    price_per_kg DECIMAL(10,2),
    FOREIGN KEY (zone_id) REFERENCES rate_zones(id)
);

CREATE TABLE taxes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50), -- VAT, GST
    rate DECIMAL(5,2), -- 0.18
    country_id INT,
    FOREIGN KEY (country_id) REFERENCES countries(id)
);

/* ===================================================
   5. SHIPMENT CORE
   =================================================== */

CREATE TABLE shipments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tracking_no VARCHAR(20) UNIQUE NOT NULL,
    customer_id INT,
    
    -- Sender
    sender_name VARCHAR(50),
    sender_address VARCHAR(300),
    sender_city VARCHAR(30),
    sender_postal_code VARCHAR(15),
    sender_country_id INT,

    -- Receiver
    receiver_name VARCHAR(50),
    receiver_address VARCHAR(300),
    receiver_city VARCHAR(30),
    receiver_postal_code VARCHAR(20),
    receiver_country_id INT,

    -- Routing
    origin_branch_id INT,
    destination_branch_id INT,
    carrier_id INT, -- Primary carrier if outsourced
    
    -- Details
    service_type ENUM('Standard', 'Express', 'Economy') NOT NULL,
    shipment_type ENUM('document', 'parcel', 'freight') NOT NULL,
    
    -- Weights (3 decimals for precision)
    total_weight DECIMAL(10,3),
    chargeable_weight DECIMAL(10,3), -- Max(actual, volumetric)
    length_cm DECIMAL(10,2),
    width_cm DECIMAL(10,2),
    height_cm DECIMAL(10,2),
    
    -- Financials
    cod_amount DECIMAL(10,2) DEFAULT 0.00,
    status VARCHAR(60) DEFAULT 'Created', -- Flexible varchar for granular status
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (sender_country_id) REFERENCES countries(id),
    FOREIGN KEY (receiver_country_id) REFERENCES countries(id),
    FOREIGN KEY (origin_branch_id) REFERENCES branches(id),
    FOREIGN KEY (destination_branch_id) REFERENCES branches(id),
    FOREIGN KEY (carrier_id) REFERENCES carriers(id)
);

CREATE TABLE shipment_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shipment_id INT NOT NULL,
    description VARCHAR(700),
    quantity INT,
    weight DECIMAL(10,3),
    value_usd DECIMAL(10,2), -- Declared value per item
    FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE
);

/* ===================================================
   6. INTERNATIONAL COMPLIANCE
   =================================================== */

CREATE TABLE customs_documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shipment_id INT NOT NULL,
    hs_code VARCHAR(20), -- Harmonized System Code
    value_usd DECIMAL(10,2),
    origin_country_id INT, -- Country of Manufacture
    description TEXT,
    document_url VARCHAR(255), -- Link to PDF/Image
    FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE,
    FOREIGN KEY (origin_country_id) REFERENCES countries(id)
);

/* ===================================================
   7. OPERATIONS & TRACKING
   =================================================== */

CREATE TABLE shipment_status_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shipment_id INT NOT NULL,
    status VARCHAR(50), -- CREATED, IN_TRANSIT, CUSTOMS_HOLD
    branch_id INT, -- Location of scan
    user_id INT, -- Who scanned it
    note TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rider_id INT NOT NULL,
    shipment_id INT NOT NULL,
    type ENUM('PICKUP', 'DELIVERY', 'TRANSFER'),
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('assigned', 'in_progress', 'completed', 'failed') DEFAULT 'assigned',
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (rider_id) REFERENCES riders(id),
    FOREIGN KEY (shipment_id) REFERENCES shipments(id)
);

/* ===================================================
   8. FINANCE & BILLING
   =================================================== */

CREATE TABLE invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_no VARCHAR(50) UNIQUE NOT NULL,
    shipment_id INT NOT NULL,
    customer_id INT NOT NULL,
    sub_total DECIMAL(10,2),
    tax_amount DECIMAL(10,2),
    total_amount DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'USD',
    issue_date DATE,
    due_date DATE,
    status ENUM('paid', 'pending', 'voided', 'overdue') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shipment_id) REFERENCES shipments(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    amount DECIMAL(10,2),
    method ENUM('credit_card', 'bank_transfer', 'cash', 'cheque'),
    transaction_ref VARCHAR(100) UNIQUE,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('completed', 'failed', 'refunded'),
    FOREIGN KEY (invoice_id) REFERENCES invoices(id)
);

/* ===================================================
   9. CASH ON DELIVERY (COD) HANDLING
   =================================================== */

CREATE TABLE cod_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shipment_id INT UNIQUE NOT NULL,
    amount DECIMAL(10,2), -- Cash collected
    collected_by INT, -- Rider/Staff User ID
    collection_date TIMESTAMP,
    status ENUM('pending', 'collected', 'transferred_to_branch') DEFAULT 'pending',
    FOREIGN KEY (shipment_id) REFERENCES shipments(id),
    FOREIGN KEY (collected_by) REFERENCES users(id)
);

CREATE TABLE cod_settlements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cod_id INT NOT NULL,
    customer_id INT NOT NULL,
    settlement_amount DECIMAL(10,2), -- Amount paid back to shipper
    settlement_reference VARCHAR(100),
    settlement_date DATE,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    FOREIGN KEY (cod_id) REFERENCES cod_orders(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id)
);

/* ===================================================
   10. SYSTEM UTILITIES
   =================================================== */

CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    shipment_id INT,
    type ENUM('sms', 'email', 'whatsapp', 'system'),
    message TEXT,
    status ENUM('sent', 'failed', 'read') DEFAULT 'sent',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (shipment_id) REFERENCES shipments(id)
);

CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100), -- SHIPMENT_CREATED, INVOICE_PAID
    details JSON, -- Store flexible data about the action
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);