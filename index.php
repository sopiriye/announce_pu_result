<?php
$page = $_GET['page'] ?? 'home';

include 'includes/header.php';

switch ($page) {
    case 'polling_unit_result':
        include 'views/polling_unit_result.php';
        break;
    case 'lga_total_result':
        include 'views/lga_total_result.php';
        break;
    case 'add_result':
        include 'views/add_result.php';
        break;
    default:
        include 'views/home.php';
}

include 'includes/footer.php';
?>