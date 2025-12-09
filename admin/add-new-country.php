<?php
include 'db/config.php';
include 'inc/sidebar.php';

// Default empty messages to avoid undefined warnings
$country_message = "";
$zone_message = "";

// COUNTRY INSERT FORM PROCESSING

if (isset($_POST["add_country"])) {

    $country_name = trim($_POST['given_country_name'] ?? '');
    $iso_code = trim($_POST['given_iso'] ?? '');

    if ($country_name !== "" && $iso_code !== "") {

        $stmt = $conn->prepare("INSERT INTO countries (name, iso_code) VALUES (:name, :iso)");
        $stmt->bindParam(':name', $country_name);
        $stmt->bindParam(':iso', $iso_code);

        if ($stmt->execute()) {
    $country_message = "
    <div class='d-flex justify-content-center mt-2'>
        <div class='alert alert-success p-3 text-center'>Country Added Successfully!</div>
    </div>";
} else {
    $country_message = "
    <div class='d-flex justify-content-center mt-2'>
        <div class='alert alert-danger p-3 text-center'>Insert failed!</div>
    </div>";
}

    } else {
    $country_message = "
    <div class='d-flex justify-content-center mt-2'>
        <div class='alert alert-warning text-center'>All fields are required!</div>
    </div>";
}
}

// RATE ZONE INSERT FORM PROCESSING

if (isset($_POST["add_zone"])) {

    $zone_name = trim($_POST['given_zone_name'] ?? '');
    $zone_description = trim($_POST['given_description'] ?? '');

    if ($zone_name !== "" && $zone_description !== "") {

        $stmt = $conn->prepare("INSERT INTO rate_zones (name, description) VALUES (:zone_name, :description)");
        $stmt->bindParam(':zone_name', $zone_name);
        $stmt->bindParam(':description', $zone_description);

        if ($stmt->execute()) {
    $zone_message = "
    <div class='d-flex justify-content-center mt-2'>
        <div class='alert alert-success p-3 text-center'>
            Rate Zone Added Successfully!
        </div>
    </div>";
} else {
    $zone_message = "
    <div class='d-flex justify-content-center mt-2'>
        <div class='alert alert-danger p-3 text-center'>
            Insert failed!
        </div>
    </div>";
}

} else {
    $zone_message = "
    <div class='d-flex justify-content-center mt-2'>
        <div class='alert alert-warning p-3 text-center'>
            All fields are required!
        </div>
    </div>";
}
}
?>


<main class="dashboard-main">
    <?php include "inc/nav.php" ?>
    </div>

    <div class="dashboard-main-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Input Layout</h6>
        </div>

        <div class="row gy-4">

            <!-- COUNTRY FORM -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Insert Country</h5>
                    </div>

                    <form action="" method="post">
                        <div class="card-body">
                            <div class="row gy-3">

                                <div class="col-12">
                                    <label class="form-label">Country Name</label>
                                    <div class="icon-field">
                                        <span class="icon"><iconify-icon icon="f7:person"></iconify-icon></span>
                                        <input type="text" name="given_country_name" class="form-control" placeholder="Bangladesh / USA">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">ISO Code</label>
                                    <div class="icon-field">
                                        <span class="icon"><iconify-icon icon="f7:person"></iconify-icon></span>
                                        <input type="text" name="given_iso" class="form-control" placeholder="BD / US">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" name="add_country" class="btn btn-primary-600">Submit</button>
                                </div>

                            </div>
                        </div>
                    </form>

                    <?= $country_message ?>
                </div>
            </div>

            <!-- RATE ZONE FORM -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Insert Rate Zone</h5>
                    </div>

                    <form action="" method="post">
                        <div class="card-body">
                            <div class="row gy-3">

                                <div class="col-12">
                                    <label class="form-label">Zone Name</label>
                                    <div class="icon-field">
                                        <span class="icon"><iconify-icon icon="f7:person"></iconify-icon></span>
                                        <input type="text" name="given_zone_name" class="form-control" placeholder="Asia / Europe">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <div class="icon-field">
                                        <span class="icon"><iconify-icon icon="f7:person"></iconify-icon></span>
                                        <input type="text" name="given_description" class="form-control" placeholder="All Countries Inside Europe">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" name="add_zone" class="btn btn-primary-600">Submit</button>
                                </div>

                            </div>
                        </div>
                    </form>

                    <?= $zone_message ?>
                </div>
            </div>

        </div>
    </div>
</main>
