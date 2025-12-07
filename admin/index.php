<?php
session_start();

if (!isset($_SESSION['admin_log_in'])) {
    header("Location: login.php");
    exit();
}
?>
<?php 
include 'db/config.php';
include 'inc/sidebar.php';
?>



<main class="dashboard-main">
    <?php include "inc/nav.php" ?>

    <div class="dashboard-main-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
  <h6 class="fw-semibold mb-0">Shipment</h6>
  <ul class="d-flex align-items-center gap-2">
    <li class="fw-medium">
      <a href="index.html" class="d-flex align-items-center gap-1 hover-text-primary">
        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
        Dashboard
      </a>
    </li>
    <li>-</li>
    <li class="fw-medium">Shipment</li>
  </ul>
</div>

        <div class="mt-4">
            <div class="row gy-4">

                <div class="col-xxxl-9">
                    <div class="row g-3">
                        <div class="col-lg-3 col-sm-6">
                            <div class="card shadow-none border radius-12 bg-gradient-start-1 h-100">
                                <div class="card-body p-16">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-8">
                                        <div>
                                            <p class="fw-medium text-secondary-light mb-1 text-sm">Total Shipments</p>
                                            <h6 class="mb-0">5,750</h6>
                                        </div>
                                        <div
                                            class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                                            <iconify-icon icon="gridicons:multiple-users"
                                                class="text-white text-2xl mb-0"></iconify-icon>
                                        </div>
                                    </div>
                                    <p
                                        class="fw-medium text-sm text-secondary-light mt-12 mb-0 d-flex align-items-center gap-2">
                                        <span
                                            class="d-inline-flex align-items-center gap-1 border border-success-600 px-8 rounded-pill text-success-main">
                                            <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon>
                                            1.8%
                                        </span>
                                        vs Last month
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card shadow-none border radius-12 bg-gradient-start-2 h-100">
                                <div class="card-body p-16">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-8">
                                        <div>
                                            <p class="fw-medium text-secondary-light mb-1 text-sm">Total Order</p>
                                            <h6 class="mb-0">17,300</h6>
                                        </div>
                                        <div
                                            class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                                            <iconify-icon icon="fa-solid:award"
                                                class="text-white text-2xl mb-0"></iconify-icon>
                                        </div>
                                    </div>
                                    <p
                                        class="fw-medium text-sm text-secondary-light mt-12 mb-0 d-flex align-items-center gap-2">
                                        <span
                                            class="d-inline-flex align-items-center gap-1 border border-success-600 px-8 rounded-pill text-success-main"><iconify-icon
                                                icon="bxs:up-arrow" class="text-xs"></iconify-icon> 1.8%</span>
                                        vs Last month
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card shadow-none border radius-12 bg-gradient-start-3 h-100">
                                <div class="card-body p-16">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-8">
                                        <div>
                                            <p class="fw-medium text-secondary-light mb-1 text-sm">Revenue</p>
                                            <h6 class="mb-0">$50,375</h6>
                                        </div>
                                        <div
                                            class="w-50-px h-50-px bg-primary-600 rounded-circle d-flex justify-content-center align-items-center">
                                            <iconify-icon icon="fluent:people-20-filled"
                                                class="text-white text-2xl mb-0"></iconify-icon>
                                        </div>
                                    </div>
                                    <p
                                        class="fw-medium text-sm text-secondary-light mt-12 mb-0 d-flex align-items-center gap-2">
                                        <span
                                            class="d-inline-flex align-items-center gap-1 border border-danger-600 px-8 rounded-pill text-danger-main"><iconify-icon
                                                icon="bxs:down-arrow" class="text-xs"></iconify-icon> 1.8%</span>
                                        vs Last month
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <div class="card shadow-none border radius-12 bg-gradient-start-4 h-100">
                                <div class="card-body p-16">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-8">
                                        <div>
                                            <p class="fw-medium text-secondary-light mb-1 text-sm">Delivered</p>
                                            <h6 class="mb-0">725</h6>
                                        </div>
                                        <div
                                            class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                                            <iconify-icon icon="solar:wallet-bold"
                                                class="text-white text-2xl mb-0"></iconify-icon>
                                        </div>
                                    </div>
                                    <p
                                        class="fw-medium text-sm text-secondary-light mt-12 mb-0 d-flex align-items-center gap-2">
                                        <span
                                            class="d-inline-flex align-items-center gap-1 border border-success-600 px-8 rounded-pill text-success-main"><iconify-icon
                                                icon="bxs:up-arrow" class="text-xs"></iconify-icon> 1.8%</span>
                                        vs Last month
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card rounded-4 overflow-hidden border-0 mt-24">
                        <div class="card-header">
                            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                                <h6 class="mb-2 fw-bold text-lg mb-0">Sales Figure</h6>
                            </div>
                        </div>

                        <div class="card-body p-24">
                            <ul class="d-flex flex-wrap align-items-center justify-content-center gap-3">
                                <li class="d-flex align-items-center gap-2">
                                    <span class="w-12-px h-8-px rounded-pill bg-warning-600"></span>
                                    <span class="text-secondary-light text-sm fw-semibold line-height-1">
                                        Car Box
                                    </span>
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <span class="w-12-px h-8-px rounded-pill bg-primary-600"></span>
                                    <span class="text-secondary-light text-sm fw-semibold line-height-1">
                                        Truck Cargo
                                    </span>
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <span class="w-12-px h-8-px rounded-pill bg-info"></span>
                                    <span class="text-secondary-light text-sm fw-semibold line-height-1">
                                        Ship Cargo
                                    </span>
                                </li>
                            </ul>
                            <div id="salesFigureChart" class="barChart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xxxl-3">
                    <div class="shadow-7 p-0 radius-12 bg-base overflow-hidden h-100">
                        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between py-12 px-20">
                            <h6 class="mb-0 fw-semibold text-lg">Live Tracking</h6>
                            <a href="javascript:void(0)"
                                class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                                View All
                                <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                            </a>
                        </div>
                        <div class="card-body pt-0 ps-20 pb-20 pe-20">
                            <div class="">
                                <div class="">
                                    <iframe
                                        src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d117254.51552865302!2d90.79898285652153!3d23.308370214969766!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1spalazzo%20pants!5e0!3m2!1sen!2sbd!4v1761979641389!5m2!1sen!2sbd"
                                        height="185" style="border:0;" allowfullscreen="" loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"
                                        class="w-100 radius-12 overflow-hidden filter-grayscale-1"></iframe>
                                </div>
                                <div class="d-flex align-items-center my-20 gap-8 justify-content-between">
                                    <div>
                                        <p class="fw-medium text-secondary-light mb-2">Tracking ID</p>
                                        <h6 class="mb-0 fw-semibold text-xl">#TR0152</h6>
                                    </div>
                                    <span
                                        class="bg-primary-50 text-primary-600 px-16 py-2 radius-2 fw-medium text-sm">On
                                        the way</span>
                                </div>
                                <div class="left-line-dotted position-relative d-flex flex-column gap-12">
                                    <div
                                        class="left-line__circle d-flex align-items-center ps-16 position-relative justify-content-between gap-16">
                                        <div class="">
                                            <span class="fw-semibold text-primary-light text-sm d-block">Checking</span>
                                            <span
                                                class="fw-normal text-secondary-light text-sm d-block">5/15/2025</span>
                                        </div>
                                        <span class="fw-normal text-secondary-light text-sm">18:43 PM</span>
                                    </div>
                                    <div
                                        class="left-line__circle d-flex align-items-center ps-16 position-relative justify-content-between gap-16">
                                        <div class="">
                                            <span class="fw-semibold text-primary-light text-sm d-block">In
                                                transit</span>
                                            <span
                                                class="fw-normal text-secondary-light text-sm d-block">5/16/2025</span>
                                        </div>
                                        <span class="fw-normal text-secondary-light text-sm">09:15 AM</span>
                                    </div>
                                    <div
                                        class="left-line__circle incomplete-circle d-flex align-items-center ps-16 position-relative justify-content-between gap-16">
                                        <div class="">
                                            <span
                                                class="fw-semibold text-primary-light text-sm d-block">Delivered</span>
                                            <span
                                                class="fw-normal text-secondary-light text-sm d-block">5/19/2025</span>
                                        </div>
                                        <span class="fw-normal text-secondary-light text-sm">12:15 PM</span>
                                    </div>
                                </div>
                                <div
                                    class="mt-8 mb-16 bg-neutral-100 p-10 radius-6 d-flex align-items-center justify-content-between gap-8">
                                    <div class="d-flex align-items-center gap-12">
                                        <span class="w-40-px h-40-px radius-4 overflow-hidden rounded-circle">
                                            <img src="assets/images/users/user1.png" alt="Image"
                                                class="w-40-px h-40-px rounded-circle flex-shrink-0 overflow-hidden">
                                        </span>
                                        <div class="">
                                            <span class="d-block text-primary-light fw-medium line-height-1">Joko
                                                Wiroso</span>
                                            <span class="d-block text-secondary-light text-sm">Courier</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-8">
                                        <button type="button"
                                            class="w-32-px h-32-px bg-base dark-bg-neutral-200 text-secondary-light text-lg radius-8 bg-hover-primary-600 text-hover-white d-flex justify-content-center align-items-center">
                                            <i class="ri-message-3-line"></i>
                                        </button>
                                        <button type="button"
                                            class="w-32-px h-32-px bg-base dark-bg-neutral-200 text-secondary-light text-lg radius-8 bg-hover-primary-600 text-hover-white d-flex justify-content-center align-items-center">
                                            <i class="ri-phone-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary-600 w-100 radius-6 py-10">Track</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-4 col-lg-6">
                    <div class="shadow-7 p-0 radius-12 bg-base overflow-hidden h-100">
                        <div
                            class="d-flex align-items-center flex-wrap gap-2 justify-content-between py-12 px-20 border-bottom border-neutral-200">
                            <h6 class="mb-0 fw-semibold text-lg">Shipment Success</h6>
                            <div class="">
                                <select
                                    class="form-select form-select-sm w-auto bg-base border text-secondary-light radius-8 h-32-px text-sm py-4">
                                    <option>This Monthly</option>
                                    <option>This Yearly</option>
                                    <option>This Weekly</option>
                                    <option>This Today</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body p-20">
                            <div class="d-flex align-items-center gap-20">
                                <h3 class="mb-0">65%</h3>
                                <span class="text-success-main fw-bold d-inline-flex align-items-center gap-1">
                                    <i class="ri-arrow-up-line"></i> 10%
                                </span>
                            </div>

                            <div class="mt-24">
                                <div class="d-flex gap-6">
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-600"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-100"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-100"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-100"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-100"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-100"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-100"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-100"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-100"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-100"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-100"> </span>
                                    <span class="flex-grow-1 h-100-px rounded-pill bg-success-100"> </span>
                                </div>
                            </div>

                            <ul class="d-flex flex-wrap align-items-center mt-24 gap-20">
                                <li class="d-flex align-items-center gap-2">
                                    <span class="w-10-px h-10-px flex-shrink-0 bg-success-600"></span>
                                    <span class="text-primary-light fw-medium text-md">Success</span>
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <span class="w-10-px h-10-px flex-shrink-0 bg-success-100"></span>
                                    <span class="text-neutral-400 fw-medium text-md">Not yet</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-4 col-lg-6">
                    <div class="shadow-7 p-0 radius-12 bg-base overflow-hidden h-100">
                        <div
                            class="d-flex align-items-center flex-wrap gap-2 justify-content-between py-16 px-20 border-bottom border-neutral-200">
                            <h6 class="mb-0 fw-semibold text-lg">Top Shipping Methods</h6>
                        </div>
                        <div class="card-body p-20 d-flex align-items-center flex-wrap">
                            <div id="multipleSeriesChart"
                                class="apexcharts-tooltip-z-none square-marker check-marker series-gap-24 d-flex justify-content-center">
                            </div>

                            <div class="d-flex flex-column gap-8 justify-content-center">
                                <div class="d-flex align-items-center gap-8">
                                    <span class="flex-shrink-0 w-8-px h-8-px radius-2 bg-success-600"></span>
                                    <span class="text-secondary-light fw-normal text-md">
                                        Air:
                                        <span class="fw-semibold text-primary-light">22%</span>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-8">
                                    <span class="flex-shrink-0 w-8-px h-8-px radius-2 bg-purple"></span>
                                    <span class="text-secondary-light fw-normal text-md">
                                        Road:
                                        <span class="fw-semibold text-primary-light">10%</span>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-8">
                                    <span class="flex-shrink-0 w-8-px h-8-px radius-2 bg-warning-600"></span>
                                    <span class="text-secondary-light fw-normal text-md">
                                        Sea:
                                        <span class="fw-semibold text-primary-light">35%</span>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-8">
                                    <span class="flex-shrink-0 w-8-px h-8-px radius-2 bg-danger-600"></span>
                                    <span class="text-secondary-light fw-normal text-md">
                                        Rail:
                                        <span class="fw-semibold text-primary-light">28%</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-4 col-lg-6">
                    <div class="shadow-7 p-0 radius-12 bg-base overflow-hidden h-100">
                        <div
                            class="d-flex align-items-center flex-wrap gap-2 justify-content-between py-16 px-20 border-bottom border-neutral-200">
                            <h6 class="mb-0 fw-semibold text-lg">This Month Orders</h6>
                        </div>
                        <div class="card-body p-20">
                            <div id="monthOrderChart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-4 col-lg-6">
                    <div class="card radius-8 border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                                <h6 class="mb-2 fw-bold text-lg">Countries Status</h6>
                                <div class="">
                                    <select
                                        class="form-select form-select-sm w-auto bg-base border text-secondary-light radius-8">
                                        <option>Yearly</option>
                                        <option>Monthly</option>
                                        <option>Weekly</option>
                                        <option>Today</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="world-map"></div>

                        <div class="card-body p-24 max-h-266-px scroll-sm overflow-y-auto">
                            <div class="d-flex flex-column gap-16">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center w-100">
                                        <img src="assets/images/flags/flag1.png" alt="Image"
                                            class="w-32-px h-32-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                        <div class="flex-grow-1">
                                            <h6 class="text-sm mb-0">USA</h6>
                                            <span class="text-xs text-secondary-light fw-medium">1,240 Users</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 w-100">
                                        <div class="w-100 max-w-66 ms-auto">
                                            <div class="progress progress-sm rounded-pill" role="progressbar"
                                                aria-label="Success example" aria-valuenow="25" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <div class="progress-bar bg-primary-600 rounded-pill"
                                                    style="width: 80%;"></div>
                                            </div>
                                        </div>
                                        <span class="text-secondary-light font-xs fw-semibold">80%</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center w-100">
                                        <img src="assets/images/flags/flag2.png" alt="Image"
                                            class="w-32-px h-32-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                        <div class="flex-grow-1">
                                            <h6 class="text-sm mb-0">Japan</h6>
                                            <span class="text-xs text-secondary-light fw-medium">1,240 Users</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 w-100">
                                        <div class="w-100 max-w-66 ms-auto">
                                            <div class="progress progress-sm rounded-pill" role="progressbar"
                                                aria-label="Success example" aria-valuenow="25" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <div class="progress-bar bg-orange rounded-pill" style="width: 60%;">
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-secondary-light font-xs fw-semibold">60%</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center w-100">
                                        <img src="assets/images/flags/flag3.png" alt="Image"
                                            class="w-32-px h-32-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                        <div class="flex-grow-1">
                                            <h6 class="text-sm mb-0">France</h6>
                                            <span class="text-xs text-secondary-light fw-medium">1,240 Users</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 w-100">
                                        <div class="w-100 max-w-66 ms-auto">
                                            <div class="progress progress-sm rounded-pill" role="progressbar"
                                                aria-label="Success example" aria-valuenow="25" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <div class="progress-bar bg-yellow rounded-pill" style="width: 49%;">
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-secondary-light font-xs fw-semibold">49%</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center w-100">
                                        <img src="assets/images/flags/flag4.png" alt="Image"
                                            class="w-32-px h-32-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                                        <div class="flex-grow-1">
                                            <h6 class="text-sm mb-0">Germany</h6>
                                            <span class="text-xs text-secondary-light fw-medium">1,240 Users</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 w-100">
                                        <div class="w-100 max-w-66 ms-auto">
                                            <div class="progress progress-sm rounded-pill" role="progressbar"
                                                aria-label="Success example" aria-valuenow="25" aria-valuemin="0"
                                                aria-valuemax="100">
                                                <div class="progress-bar bg-success-main rounded-pill"
                                                    style="width: 100%;"></div>
                                            </div>
                                        </div>
                                        <span class="text-secondary-light font-xs fw-semibold">100%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-8">
                    <div class="shadow-7 radius-12 bg-base h-100 overflow-hidden">
                        <div
                            class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
                            <h6 class="text-lg fw-semibold mb-0">Recent Orders</h6>
                            <a href="javascript:void(0)"
                                class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                                View All
                                <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive scroll-sm">
                                <table class="table bordered-table table-py-20 mb-0 rounded-0 border-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="rounded-0 text-secondary-light">Order No</th>
                                            <th scope="col" class="rounded-0 text-secondary-light">Category</th>
                                            <th scope="col" class="rounded-0 text-secondary-light">Route</th>
                                            <th scope="col" class="rounded-0 text-secondary-light">Date </th>
                                            <th scope="col" class="rounded-0 text-secondary-light text-center">Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">#6352148
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">Electronic
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">Surabaya -
                                                Bali </td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">2025-02-15
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-center">
                                                <div class="max-w-100-px mx-auto">
                                                    <span
                                                        class="bg-success-focus text-success-main px-16 py-2 rounded-pill fw-medium text-sm w-100">Delivered</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">#6352148
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">Clothes
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">Bail -
                                                Lombok </td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">2025-02-15
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-center">
                                                <div class="max-w-100-px mx-auto">
                                                    <span
                                                        class="bg-warning-focus text-warning-main px-16 py-2 rounded-pill fw-medium text-sm w-100">Pending</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">#6352148
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">Toy</td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">Semarang -
                                                Yogyakarta</td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">2025-02-15
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-center">
                                                <div class="max-w-100-px mx-auto">
                                                    <span
                                                        class="bg-info-focus text-info-main px-16 py-2 rounded-pill fw-medium text-sm w-100">In
                                                        transit</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">#6352148
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">Furniture
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">Malaysia -
                                                Thailand </td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">2025-02-15
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-center">
                                                <div class="max-w-100-px mx-auto">
                                                    <span
                                                        class="bg-danger-focus text-danger-main px-16 py-2 rounded-pill fw-medium text-sm w-100">Canceled</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">#6352148
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">Food</td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">Jakarta -
                                                Bandung </td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">2025-02-15
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-center">
                                                <div class="max-w-100-px mx-auto">
                                                    <span
                                                        class="bg-success-focus text-success-main px-16 py-2 rounded-pill fw-medium text-sm w-100">Delivered</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">#6352148
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">Toy</td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">Semarang -
                                                Yogyakarta </td>
                                            <td class="border-bottom border-neutral-200 text-secondary-light">2025-02-15
                                            </td>
                                            <td class="border-bottom border-neutral-200 text-center">
                                                <div class="max-w-100-px mx-auto">
                                                    <span
                                                        class="bg-info-focus text-info-main px-16 py-2 rounded-pill fw-medium text-sm w-100">In
                                                        transit</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <footer class="d-footer">
  <div class="row align-items-center justify-content-between">
    <div class="col-auto">
      <p class="mb-0">© 2024 WowDash. All Rights Reserved.</p>
    </div>
    <div class="col-auto">
      <p class="mb-0">Made by <span class="text-primary-600">wowtheme7</span></p>
    </div>
  </div>
</footer>
</main>
  <!-- jQuery library js -->
  <script src="assets/js/lib/jquery-3.7.1.min.js"></script>
  <!-- Bootstrap js -->
  <script src="assets/js/lib/bootstrap.bundle.min.js"></script>
  <!-- Apex Chart js -->
  <script src="assets/js/lib/apexcharts.min.js"></script>
  <!-- Data Table js -->
  <script src="assets/js/lib/dataTables.min.js"></script>
  <!-- Iconify Font js -->
  <script src="assets/js/lib/iconify-icon.min.js"></script>
  <!-- jQuery UI js -->
  <script src="assets/js/lib/jquery-ui.min.js"></script>
  <!-- Vector Map js -->
  <script src="assets/js/lib/jquery-jvectormap-2.0.5.min.js"></script>
  <script src="assets/js/lib/jquery-jvectormap-world-mill-en.js"></script>
  <!-- Popup js -->
  <script src="assets/js/lib/magnifc-popup.min.js"></script>
  <!-- Slick Slider js -->
  <script src="assets/js/lib/slick.min.js"></script>
  <!-- prism js -->
  <script src="assets/js/lib/prism.js"></script>
  <!-- file upload js -->
  <script src="assets/js/lib/file-upload.js"></script>
  <!-- audioplayer -->
  <script src="assets/js/lib/audioplayer.js"></script>
  
  <!-- main js -->
  <script src="assets/js/app.js"></script>

<script>
    // ============================ Sales figure Chart Start ==========================
    var options = {
        series: [{
            name: 'Truck Cargo',
            data: [44, 55, 41, 67, 22, 43, 21, 49, 44, 55, 41, 67]
        }, {
            name: 'Ship Cargo',
            data: [13, 23, 20, 8, 13, 27, 33, 12, 13, 23, 20, 8]
        }, {
            name: 'Car Box',
            data: [11, 17, 15, 15, 21, 14, 15, 13, 11, 17, 15, 15]
        }],
        chart: {
            type: 'bar',
            height: 350,
            stacked: true,
            stackType: '100%',
            toolbar: {
                show: false
            },
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                columnWidth: '23%',
                endingShape: 'rounded',
            }
        },
        dataLabels: {
            enabled: false
        },
        responsive: [{
            breakpoint: 480,
            options: {
                legend: {
                    position: 'bottom',
                    offsetX: -10,
                    offsetY: 0
                }
            }
        }],
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        },
        colors: ['#78D3F8', '#4F70FF', '#FF9F29'], 
        fill: {
            opacity: 1
        },
        legend: {
            show: false,
            position: 'right',
            offsetX: 0,
            offsetY: 50
        },
    };

    var chart = new ApexCharts(document.querySelector("#salesFigureChart"), options);
    chart.render();
    // ============================ Sales figure Chart End ==========================

    // ============================ Multiple series Chart Start ==========================
    var options = {
        series: [20, 22, 28, 10],
        chart: {
            type: 'polarArea',
            height: 250,
        },
        labels: ['Product 1', 'Product 2', 'Product 3', 'Product 4'],
        colors: ['#487FFF', '#FF9F29', '#9935FE', '#EF4A00'],
        stroke: {
            colors: ['#487FFF', '#FF9F29', '#9935FE', '#EF4A00'],
        },
        fill: {
            opacity: 0.8
        },
        legend: {
            show: false,
            position: 'bottom',
            horizontalAlign: 'center' // Align the legend horizontally
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#multipleSeriesChart"), options);
    chart.render();
    // ============================ Multiple series Chart End ==========================

    // ============================ Month Order Chart start ==========================
    var options = {
        series: [
            {
                name: 'Actual',
                data: [
                    {
                        x: 'Jan',
                        y: 100,
                        goals: [
                            {
                                name: 'Expected',
                                value: 103,
                                strokeHeight: 3,
                                strokeColor: '#487FFF'
                            }
                        ]
                    },
                    {
                        x: 'Feb',
                        y: 452,
                        goals: [
                            {
                                name: 'Expected',
                                value: 455,
                                strokeHeight: 3,
                                strokeColor: '#487FFF'
                            }
                        ]
                    },
                    {
                        x: 'Mar',
                        y: 303,
                        goals: [
                            {
                                name: 'Expected',
                                value: 306,
                                strokeHeight: 3,
                                strokeColor: '#487FFF'
                            }
                        ]
                    },
                    {
                        x: 'Apr',
                        y: 503,
                        goals: [
                            {
                                name: 'Expected',
                                value: 506,
                                strokeHeight: 3,
                                strokeColor: '#487FFF'
                            }
                        ]
                    },
                    {
                        x: 'May',
                        y: 93,
                        goals: [
                            {
                                name: 'Expected',
                                value: 96,
                                strokeHeight: 3,
                                strokeColor: '#487FFF'
                            }
                        ]
                    },
                    {
                        x: 'Jun',
                        y: 302,
                        goals: [
                            {
                                name: 'Expected',
                                value: 305,
                                strokeHeight: 3,
                                strokeColor: '#487FFF'
                            }
                        ]
                    },
                    {
                        x: 'Jul',
                        y: 452,
                        goals: [
                            {
                                name: 'Expected',
                                value: 455,
                                strokeHeight: 3,
                                strokeColor: '#487FFF'
                            }
                        ]
                    },
                    {
                        x: 'Aug',
                        y: 153,
                        goals: [
                            {
                                name: 'Expected',
                                value: 156,
                                strokeHeight: 3,
                                strokeColor: '#487FFF'
                            }
                        ]
                    },
                    {
                        x: 'Sep',
                        y: 453,
                        goals: [
                            {
                                name: 'Expected',
                                value: 456,
                                strokeHeight: 3,
                                strokeColor: '#487FFF'
                            }
                        ]
                    },
                    {
                        x: 'Oct',
                        y: 103,
                        goals: [
                            {
                                name: 'Expected',
                                value: 106,
                                strokeHeight: 3,
                                strokeColor: '#487FFF'
                            }
                        ]
                    },
                    {
                        x: 'Nov',
                        y: 253,
                        goals: [
                            {
                                name: 'Expected',
                                value: 256,
                                strokeHeight: 3,
                                strokeColor: '#487FFF'
                            }
                        ]
                    },
                    {
                        x: 'Dec',
                        y: 153,
                        goals: [
                            {
                                name: 'Expected',
                                value: 156,
                                strokeHeight: 3,
                                strokeColor: '#487FFF'
                            }
                        ]
                    },
                ]
            }
        ],
        chart: {
            height: 224,
            type: 'bar',
            toolbar: {
                show: false
            },
        },
        plotOptions: {
            bar: {
                columnWidth: '100%'
            }
        },
        colors: ['#C3D5FF'],
        dataLabels: {
            enabled: false
        },
        legend: {
            show: false,
            showForSingleSeries: true,
            customLegendItems: ['Actual', 'Expected'],
            markers: {
                fillColors: ['#C3D5FF', '#487FFF']
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#monthOrderChart"), options);
    chart.render();
    // ============================ Month Order Chart End ==========================

    // ================================ J Vector Map Start ================================ 
    $('#world-map').vectorMap(
        {
            map: 'world_mill_en',
            backgroundColor: 'transparent',
            borderColor: '#fff',
            borderOpacity: 0.25,
            borderWidth: 0,
            color: '#000000',
            regionStyle: {
                initial: {
                    fill: '#D1D5DB'
                }
            },
            markerStyle: {
                initial: {
                    r: 5,
                    'fill': '#fff',
                    'fill-opacity': 1,
                    'stroke': '#000',
                    'stroke-width': 1,
                    'stroke-opacity': 0.4
                },
            },
            markers: [{
                latLng: [35.8617, 104.1954],
                name: 'China : 250'
            },

            {
                latLng: [25.2744, 133.7751],
                name: 'AustrCalia : 250'
            },

            {
                latLng: [36.77, -119.41],
                name: 'USA : 82%'
            },

            {
                latLng: [55.37, -3.41],
                name: 'UK   : 250'
            },

            {
                latLng: [25.20, 55.27],
                name: 'UAE : 250'
            }],

            series: {
                regions: [{
                    values: {
                        "US": '#487FFF ',
                        "SA": '#487FFF',
                        "AU": '#487FFF',
                        "CN": '#487FFF',
                        "GB": '#487FFF',
                    },
                    attribute: 'fill'
                }]
            },
            hoverOpacity: null,
            normalizeFunction: 'linear',
            zoomOnScroll: false,
            scaleColors: ['#000000', '#000000'],
            selectedColor: '#000000',
            selectedRegions: [],
            enableZoom: false,
            hoverColor: '#fff',
        });
    // ================================ J Vector Map End ================================ 

    // ================================ Bars Up Down (Earning Statistics) chart Start ================================ 
    var options = {
        series: [
            {
                name: "Income",
                data: [44, 42, 57, 86, 58, 55, 70, 44, 42, 57, 86, 58, 55, 70],
            },
            {
                name: "Expenses",
                data: [-34, -22, -37, -56, -21, -35, -60, -34, -22, -37, -56, -21, -35, -60],
            },
        ],
        chart: {
            stacked: true,
            type: "bar",
            height: 64,
            fontFamily: "Poppins, sans-serif",
            toolbar: {
                show: false,
            },
            sparkline: {
                enabled: true // Remove whitespace
            },
        },
        colors: ["#9935fe26", "#9935FE"],
        plotOptions: {
            bar: {
                columnWidth: "8",
                borderRadius: [2],
                borderRadiusWhenStacked: "all",
            },
        },
        stroke: {
            width: [5, 5]
        },
        dataLabels: {
            enabled: false,
        },
        legend: {
            show: false,
            position: "top",
        },
        yaxis: {
            show: false,
            title: {
                text: undefined,
            },
            labels: {
                formatter: function (y) {
                    return y.toFixed(0) + "";
                },
            },
        },
        xaxis: {
            categories: [
                "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun",
                "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"
            ],
            show: false,
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
            labels: {
                show: false,
                style: {
                    colors: "#d4d7d9",
                    fontSize: "10px",
                    fontWeight: 500,
                },
            },
        },
        tooltip: {
            enabled: true,
            shared: true,
            intersect: false,
            theme: "dark",
            x: {
                show: false,
            },
        },
    };
    var chart = new ApexCharts(document.querySelector("#upDownBarchart"), options);
    chart.render();
    // ================================ Bars Up Down (Earning Statistics) chart End ================================ 
</script>

</body>

</html>