<?php
// Start session if not already started - MUST be before any output
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'nedmin/netting/baglan.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Ticaret</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-purple: #9D7FC7;
            --dark-purple: #8B6FC7;
            --light-purple: #B8A5D9;
            --lighter-purple: #D9CEE8;
            --accent-purple: #7A5FB8;
            --purple-gradient: linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%);
        }
        
        body {
            background-color: #FAF8FC;
            color: #333;
        }
        
        .top-bar {
            background: linear-gradient(135deg, #D9CEE8 0%, #B8A5D9 100%);
            padding: 6px 0;
            font-size: 0.8rem;
            border-bottom: 1px solid rgba(157, 127, 199, 0.3);
            animation: slideDown 0.5s ease-out;
        }
        
        .top-bar .row {
            align-items: center;
            margin: 0;
        }
        
        .top-bar .container {
            padding: 0 15px;
        }
        
        .top-bar .col-md-6 {
            display: flex;
            align-items: center;
            padding: 0;
        }
        
        .top-bar .col-md-6:first-child {
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        
        .top-bar .col-md-6.text-end {
            justify-content: flex-end;
            gap: 0.5rem;
        }
        
        .top-bar .contact-info {
            color: #555;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            white-space: nowrap;
        }
        
        .top-bar .contact-info i {
            color: #7A5FB8;
            margin-right: 0.35rem;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        
        .top-bar .contact-info:hover {
            color: #7A5FB8;
            background: rgba(255, 255, 255, 0.25);
        }
        
        .top-bar .contact-info:hover i {
            color: #9D7FC7;
            transform: scale(1.1);
        }
        
        .top-bar a {
            color: #555;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            white-space: nowrap;
        }
        
        .top-bar a:hover {
            color: #7A5FB8;
            background: rgba(255, 255, 255, 0.25);
        }
        
        .top-bar a i {
            margin-right: 0.35rem;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        
        .top-bar a:hover i {
            transform: scale(1.1);
        }
        
        @media (max-width: 768px) {
            .top-bar {
                padding: 5px 0;
                font-size: 0.75rem;
            }
            
            .top-bar .col-md-6:first-child {
                gap: 0.5rem;
                justify-content: center;
                margin-bottom: 0.25rem;
            }
            
            .top-bar .col-md-6.text-end {
                justify-content: center;
                gap: 0.75rem;
            }
            
            .top-bar .contact-info {
                padding: 0.2rem 0.4rem;
                font-size: 0.7rem;
            }
            
            .top-bar .contact-info i {
                font-size: 0.75rem;
                margin-right: 0.25rem;
            }
            
            .top-bar a {
                padding: 0.2rem 0.4rem;
                font-size: 0.7rem;
            }
            
            .top-bar a i {
                font-size: 0.75rem;
                margin-right: 0.25rem;
            }
            
            .user-dropdown-toggle {
                padding: 0.3rem 0.6rem !important;
                font-size: 0.75rem;
            }
            
            .user-dropdown-toggle i {
                font-size: 0.95rem !important;
                margin-right: 0.4rem !important;
            }
        }
        
        /* User Dropdown Styling */
        .user-dropdown-toggle {
            color: #555 !important;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            padding: 0.4rem 0.75rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(157, 127, 199, 0.2);
            font-weight: 500;
        }
        
        .user-dropdown-toggle:hover {
            color: #7A5FB8 !important;
            background: rgba(255, 255, 255, 0.35);
            border-color: rgba(157, 127, 199, 0.4);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(157, 127, 199, 0.2);
        }
        
        .user-dropdown-toggle i {
            color: #9D7FC7;
            font-size: 1.1rem;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .user-dropdown-toggle:hover i {
            transform: scale(1.15);
            color: #8B6FC7;
        }
        
        .user-dropdown {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            margin-top: 0.5rem !important;
            padding: 0.5rem 0;
            background: white;
            min-width: 220px;
            position: absolute !important;
            top: 100% !important;
            left: auto !important;
            right: 0 !important;
            transform: translateY(0) !important;
            will-change: transform;
        }
        
        .top-bar .dropdown {
            position: relative;
        }
        
        .user-dropdown.show {
            display: block !important;
            transform: translateY(0) !important;
            opacity: 1 !important;
        }
        
        .user-dropdown .dropdown-item {
            padding: 0.75rem 1.25rem;
            color: #555;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-radius: 0;
        }
        
        .user-dropdown .dropdown-item i {
            color: #9D7FC7;
            width: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .user-dropdown .dropdown-item:hover {
            background: linear-gradient(135deg, #F5F0FA 0%, #E8DFF0 100%);
            color: #7A5FB8;
            padding-left: 1.5rem;
        }
        
        .user-dropdown .dropdown-item:hover i {
            color: #8B6FC7;
            transform: scale(1.2);
        }
        
        .user-dropdown .dropdown-divider {
            margin: 0.5rem 0;
            border-color: rgba(157, 127, 199, 0.2);
        }
        
        .user-dropdown .dropdown-item.text-danger {
            color: #FF6B6B !important;
        }
        
        .user-dropdown .dropdown-item.text-danger:hover {
            background: linear-gradient(135deg, #FFF0F0 0%, #FFE8E8 100%);
            color: #FF6B6B !important;
        }
        
        .user-dropdown .dropdown-item.text-danger i {
            color: #FF6B6B;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .navbar-custom {
            background-color: #fff;
            box-shadow: 0 4px 20px rgba(157, 127, 199, 0.15);
            border-bottom: 2px solid var(--light-purple);
            padding: 1rem 0;
            transition: all 0.3s ease;
            animation: fadeInDown 0.6s ease-out;
        }
        
        .navbar-custom.scrolled {
            padding: 0.5rem 0;
            box-shadow: 0 2px 15px rgba(157, 127, 199, 0.25);
        }
        
        @keyframes fadeInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .navbar-brand {
            color: var(--dark-purple) !important;
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: -0.5px;
            position: relative;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .navbar-brand::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-purple), var(--accent-purple));
            transition: width 0.3s ease;
            border-radius: 2px;
        }
        
        .navbar-brand:hover {
            color: var(--accent-purple) !important;
            transform: translateY(-2px);
        }
        
        .navbar-brand:hover::before {
            width: 100%;
        }
        
        .search-form {
            width: 100%;
            max-width: 600px;
            position: relative;
            animation: fadeInUp 0.8s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .search-form .input-group {
            position: relative;
        }
        
        .search-form .input-group::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 50px;
            padding: 2px;
            background: linear-gradient(135deg, var(--primary-purple), var(--accent-purple));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }
        
        .search-form .input-group:focus-within::before {
            opacity: 1;
        }
        
        .form-control:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 0.2rem rgba(157, 127, 199, 0.3);
            transform: scale(1.02);
        }
        
        .search-form .btn {
            border-radius: 0 50px 50px 0;
            transition: all 0.3s ease;
        }
        
        .search-form .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(157, 127, 199, 0.4);
        }
        
        .btn-primary, .btn-outline-primary {
            background-color: var(--primary-purple);
            border-color: var(--primary-purple);
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover, .btn-outline-primary:hover {
            background-color: var(--dark-purple);
            border-color: var(--dark-purple);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(157, 127, 199, 0.5);
        }
        
        .btn-outline-primary {
            background-color: transparent;
            color: var(--primary-purple);
        }
        
        .btn-outline-primary:hover {
            color: white;
        }
        
        .categories-nav {
            background: linear-gradient(135deg, #E8DFF0 0%, #D9CEE8 100%);
            padding: 12px 0;
            border-bottom: 1px solid rgba(157, 127, 199, 0.3);
            animation: fadeIn 0.8s ease-out;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        .category-menu .dropdown-menu {
            width: 100%;
            padding: 15px;
            border-radius: 8px;
            margin-top: 5px;
            box-shadow: 0 8px 20px rgba(157, 127, 199, 0.3);
            border: 1px solid var(--light-purple);
        }
        
        .category-menu .dropdown-toggle::after {
            display: none;
        }
        
        .category-menu .nav-link {
            color: #555;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 10px 18px;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
        }
        
        .category-menu .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(157, 127, 199, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .category-menu .nav-link:hover::before {
            left: 100%;
        }
        
        .category-menu .nav-link:hover {
            color: var(--dark-purple);
            background: linear-gradient(135deg, var(--lighter-purple) 0%, #E8DFF0 100%);
            transform: translateX(8px) scale(1.05);
            box-shadow: 0 4px 12px rgba(157, 127, 199, 0.2);
        }
        
        .category-submenu {
            display: none;
            position: absolute;
            left: 100%;
            top: 0;
            width: 200px;
            background: white;
            box-shadow: 0 8px 20px rgba(177, 156, 217, 0.2);
            z-index: 1000;
            padding: 10px 0;
            border-radius: 8px;
            border: 1px solid var(--light-purple);
        }
        
        .category-item:hover .category-submenu {
            display: block;
        }
        
        .cart-icon, .notification-icon {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .cart-icon:hover, .notification-icon:hover {
            transform: scale(1.1) translateY(-2px);
        }
        
        .cart-badge, .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--purple-gradient);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(157, 127, 199, 0.5);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        .nav-link {
            color: #555 !important;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 1rem !important;
            font-weight: 500;
            margin: 0 0.25rem;
            border-radius: 8px;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-purple), var(--accent-purple));
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .nav-link:hover {
            color: var(--dark-purple) !important;
            background-color: var(--lighter-purple);
            transform: translateY(-2px);
        }
        
        .nav-link:hover::after {
            width: 80%;
        }
        
        .dropdown-menu {
            box-shadow: 0 8px 20px rgba(157, 127, 199, 0.3);
            border: 1px solid var(--light-purple);
            border-radius: 12px;
            padding: 0.5rem 0;
            margin-top: 0.5rem !important;
        }
        
        .dropdown-menu.show {
            animation: slideDownMenu 0.3s ease-out;
        }
        
        @keyframes slideDownMenu {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Prevent dropdown from overlapping toggle button */
        .user-dropdown[data-bs-popper] {
            margin-top: 0.5rem !important;
            transform: translateY(0) !important;
        }
        
        .dropdown-item {
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .dropdown-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(135deg, var(--primary-purple), var(--accent-purple));
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }
        
        .dropdown-item:hover {
            background: linear-gradient(90deg, var(--lighter-purple) 0%, transparent 100%);
            color: var(--dark-purple);
            padding-left: 2rem;
            transform: translateX(5px);
        }
        
        .dropdown-item:hover::before {
            transform: scaleY(1);
        }
        
        .user-dropdown .dropdown-item {
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .user-dropdown .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 8px;
            color: var(--primary-purple);
        }
        
        /* Search results styling */
        #search-results {
            border: 1px solid var(--light-purple);
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(157, 127, 199, 0.3);
        }
        
        #search-results a:hover {
            text-decoration: none;
        }
        
        #search-results .hover-bg-light:hover {
            background-color: var(--lighter-purple);
        }
        
        #search-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(157, 127, 199, 0.3);
            border-color: var(--primary-purple);
        }
        
        /* Card enhancements */
        .card {
            border: 1px solid rgba(157, 127, 199, 0.3);
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(157, 127, 199, 0.15);
        }
        
        .card:hover {
            box-shadow: 0 8px 24px rgba(157, 127, 199, 0.3);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: linear-gradient(135deg, #E8DFF0 0%, #D9CEE8 100%);
            border-bottom: 1px solid rgba(157, 127, 199, 0.3);
            font-weight: 600;
            color: var(--dark-purple);
        }
        
        /* Product card enhancements */
        .product-card {
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(157, 127, 199, 0.35);
        }
        
        .product-card img {
            transition: transform 0.3s ease;
        }
        
        .product-card:hover img {
            transform: scale(1.05);
        }
        
        /* Badge styling */
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .bg-primary {
            background-color: var(--primary-purple) !important;
        }
        
        .text-primary {
            color: var(--primary-purple) !important;
        }
        
        /* Spinner color */
        .spinner-border.text-primary {
            color: var(--primary-purple) !important;
        }
        
        /* List group styling */
        .list-group-item-action {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .list-group-item-action:hover {
            background-color: var(--lighter-purple);
            border-left-color: var(--primary-purple);
            color: var(--dark-purple);
            transform: translateX(5px);
        }
        
        .list-group-item-action.active {
            background: linear-gradient(135deg, #F5F0FA 0%, #E8DFF0 100%);
            border-left-color: var(--primary-purple);
            color: var(--dark-purple);
            font-weight: 600;
        }
        
        /* Badge enhancements */
        .badge.bg-success {
            background-color: #8B6FC7 !important;
        }
        
        .badge.bg-primary {
            background-color: var(--primary-purple) !important;
        }
        
        /* Table hover effects */
        .table-hover tbody tr:hover {
            background-color: var(--lighter-purple);
        }
        
        /* Alert styling */
        .alert-success {
            background-color: #E8DFF0;
            border-color: var(--light-purple);
            color: var(--dark-purple);
        }
        
        .alert-danger {
            background-color: #FFE8E8;
            border-color: #FFB3B3;
            color: #8B0000;
        }
        
        .alert-info {
            background-color: var(--lighter-purple);
            border-color: var(--light-purple);
            color: var(--dark-purple);
        }
        
        /* Carousel styling */
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: var(--primary-purple);
            border-radius: 50%;
            padding: 15px;
        }
        
        .carousel-indicators [data-bs-target] {
            background-color: var(--primary-purple);
        }
        
        .carousel-indicators .active {
            background-color: var(--dark-purple);
        }
        
        /* Button enhancements */
        .btn {
            transition: all 0.3s ease;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        /* Input focus enhancements */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid rgba(177, 156, 217, 0.3);
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 0.2rem rgba(157, 127, 199, 0.3);
        }
        
        /* Pagination styling */
        .pagination .page-link {
            color: var(--primary-purple);
            border-color: rgba(177, 156, 217, 0.3);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary-purple);
            border-color: var(--primary-purple);
        }
        
        .pagination .page-link:hover {
            color: var(--dark-purple);
            background-color: var(--lighter-purple);
            border-color: var(--light-purple);
        }
        
        /* Modal enhancements */
        .modal-header {
            background: linear-gradient(135deg, #F5F0FA 0%, #E8DFF0 100%);
            border-bottom: 1px solid rgba(177, 156, 217, 0.2);
        }
        
        .modal-title {
            color: var(--dark-purple);
            font-weight: 600;
        }
        
        /* Link styling */
        a {
            color: var(--primary-purple);
            transition: all 0.3s ease;
        }
        
        a:hover {
            color: var(--dark-purple);
        }
        
        /* Star rating color */
        .fa-star.text-warning {
            color: #FFD700 !important;
        }
        
        /* Smooth transitions */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        
        
        
        /* Glow effect on hover */
        .navbar-brand, .nav-link, .cart-icon, .notification-icon {
            position: relative;
        }
        
        .navbar-brand:hover, .nav-link:hover, .cart-icon:hover, .notification-icon:hover {
            filter: drop-shadow(0 0 8px rgba(157, 127, 199, 0.5));
        }
        
        /* Search input animation */
        .search-form .form-control {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .search-form .form-control:focus {
            animation: searchPulse 2s infinite;
        }
        
        @keyframes searchPulse {
            0%, 100% {
                box-shadow: 0 0 0 0.2rem rgba(157, 127, 199, 0.3);
            }
            50% {
                box-shadow: 0 0 0 0.3rem rgba(157, 127, 199, 0.2);
            }
        }
        
        /* User dropdown animation */
        .user-dropdown .dropdown-toggle::after {
            transition: transform 0.3s ease;
        }
        
        .user-dropdown.show .dropdown-toggle::after {
            transform: rotate(180deg);
        }
        
        /* Responsive animations */
        @media (max-width: 768px) {
            .navbar-custom {
                padding: 0.75rem 0;
            }
            
            .navbar-brand {
                font-size: 1.5rem;
            }
            
            /* Mobil görünümde arama barını aşağı al */
            .navbar-collapse {
                margin-top: 1rem;
            }
            
            .search-form {
                margin-top: 0.5rem !important;
                margin-bottom: 0.5rem;
            }
            
            /* Mobil bildirim butonu stillendirme */
            .notification-icon.d-lg-none {
                position: relative;
                color: var(--dark-purple);
                padding: 0.5rem !important;
                border-radius: 8px;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .notification-icon.d-lg-none:hover {
                background: rgba(157, 127, 199, 0.1);
                color: var(--primary-purple);
            }
            
            .notification-icon.d-lg-none i {
                font-size: 1.2rem;
            }
            
            .notification-icon.d-lg-none .notification-badge {
                position: absolute;
                top: 0;
                right: 0;
                background: #FF6B6B;
                color: white;
                border-radius: 50%;
                width: 18px;
                height: 18px;
                font-size: 0.7rem;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                border: 2px solid white;
            }
        }
    </style>
</head>
<body>
    <?php 
    // Check if user is logged in
    $user_logged_in = isset($_SESSION['kullanici_id']);
    ?>
    
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <span class="contact-info"><i class="fas fa-phone-alt"></i> +90 555 123 45 67</span>
                    <span class="contact-info"><i class="fas fa-envelope"></i> info@eticaret.com</span>
                </div>
                <div class="col-md-6 text-end">
                    <?php if ($user_logged_in): ?>
                        <div class="dropdown d-inline-block">
                            <a href="#" class="user-dropdown-toggle dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> 
                                <span><?php echo $_SESSION['kullanici_ad'] . ' ' . $_SESSION['kullanici_soyad']; ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Profilim</a></li>
                                <li><a class="dropdown-item" href="orders.php"><i class="fas fa-shopping-bag"></i> Siparişlerim</a></li>
                                <li><a class="dropdown-item" href="address.php"><i class="fas fa-map-marker-alt"></i> Adreslerim</a></li>
                                <li><a class="dropdown-item" href="wishlist.php"><i class="fas fa-heart"></i> Favori Ürünlerim</a></li>
                                <li><a class="dropdown-item" href="notifications.php"><i class="fas fa-bell"></i> Bildirimlerim</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger logout-link" href="#"><i class="fas fa-sign-out-alt"></i> Çıkış Yap</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="me-3"><i class="fas fa-user"></i> Giriş Yap</a>
                        <a href="register.php"><i class="fas fa-user-plus"></i> Kayıt Ol</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">E-Ticaret</a>
            <div class="d-flex align-items-center gap-2">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <?php if ($user_logged_in): ?>
                <a class="nav-link notification-icon d-lg-none" href="notifications.php" style="padding: 0.5rem;">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notification-badge-mobile" style="display: none;"></span>
                </a>
                <?php endif; ?>
            </div>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <form class="d-flex search-form mx-auto position-relative" role="search" id="search-form" action="urunler.php" method="GET">
                    <div class="input-group">
                        <input class="form-control" type="search" id="search-input" name="search" placeholder="Ürün, kategori veya marka ara" aria-label="Search" autocomplete="off">
                        <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                    <!-- Live search results dropdown -->
                    <div id="search-results" class="position-absolute w-100 mt-1 bg-white shadow-sm rounded" style="top: 100%; left: 0; z-index: 1050; display: none; max-height: 400px; overflow-y: auto;"></div>
                </form>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home"></i> Anasayfa
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="wishlist.php"><i class="fas fa-heart"></i> Favoriler</a>
                    </li>
                    <?php if ($user_logged_in): ?>
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link notification-icon" href="notifications.php">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge" id="notification-badge" style="display: none;"></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link cart-icon" href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Sepet
                            <?php if(isset($_SESSION['kullanici_id'])): ?>
                                <?php 
                                $sepet_adet = $db->prepare("SELECT SUM(adet) as adet FROM sepet WHERE kullanici_id = :kullanici_id");
                                $sepet_adet->execute(['kullanici_id' => $_SESSION['kullanici_id']]);
                                $sepet_adet = $sepet_adet->fetch(PDO::FETCH_ASSOC)['adet'] ?? 0;
                                ?>
                                <span class="cart-badge"><?php echo $sepet_adet; ?></span>
                            <?php else: ?>
                                <?php
                                $sepet_adet = 0;
                                if(isset($_SESSION['sepet'])){
                                    foreach($_SESSION['sepet'] as $item){
                                        $sepet_adet += $item['adet'];
                                    }
                                }
                                ?>
                                <span class="cart-badge"><?php echo $sepet_adet; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Categories Navigation -->
    <div class="categories-nav">
        <div class="container">
            <ul class="nav category-menu" id="main-categories">
                <!-- Categories will be loaded dynamically -->
                <li class="nav-item">
                    <a class="nav-link" href="urunler.php">
                        <i class="fas fa-bars me-2"></i> Tüm Ürünler
                    </a>
                </li>
                <!-- Additional category items will be added here -->
            </ul>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="container mt-4">
    
    <!-- AJAX Logout Script -->
    <script>
        $(document).ready(function() {
            // Load main categories
            loadMainCategories();
            saveVisit();
            // Load notifications if user is logged in
            <?php if ($user_logged_in): ?>
            loadNotificationsCount();
            <?php endif; ?>

            // Live search functionality
            let searchTimeout;
            const searchInput = $('#search-input');
            const searchResults = $('#search-results');

            searchInput.on('input', function() {
                const query = $(this).val();
                clearTimeout(searchTimeout);

                if (query.length >= 2) {
                    searchTimeout = setTimeout(function() {
                        $.ajax({
                            url: 'nedmin/netting/islem.php',
                            type: 'GET',
                            data: { 
                                islem: 'live_search', 
                                query: query 
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.durum === 'success') {
                                    displayLiveSearchResults(response.results);
                                } else {
                                    searchResults.html('<div class="p-3 text-muted">Sonuç bulunamadı</div>').show();
                                }
                            },
                            error: function() {
                                searchResults.html('<div class="p-3 text-danger">Bir hata oluştu</div>').show();
                            }
                        });
                    }, 300);
                } else {
                    searchResults.hide();
                }
            });

            // Hide search results when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#search-form').length) {
                    searchResults.hide();
                }
            });

            // Handle form submission
            $('#search-form').on('submit', function(e) {
                const query = searchInput.val();
                if (query.trim() === '') {
                    e.preventDefault();
                }
            });

            // Function to display live search results
            function displayLiveSearchResults(results) {
                if (results.length === 0) {
                    searchResults.html('<div class="p-3 text-muted">Sonuç bulunamadı</div>').show();
                    return;
                }

                let html = '';
                results.forEach(function(item) {
                    html += `
                    <a href="urun.php?id=${item.id}" class="text-decoration-none text-dark">
                        <div class="d-flex align-items-center p-2 border-bottom hover-bg-light">
                            <div class="flex-shrink-0">
                                <img src="${item.foto_path || 'uploads/no-image.png'}" alt="${item.urun_isim}" width="50" height="50" style="object-fit: contain;">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-semibold">${item.urun_isim}</div>
                                <div class="small text-muted">${item.kategori_isim || ''} ${item.marka_isim ? '- ' + item.marka_isim : ''}</div>
                                <div class="text-primary fw-bold">₺${parseFloat(item.urun_fiyat).toFixed(2)}</div>
                            </div>
                        </div>
                    </a>`;
                });

                html += `
                <div class="p-2 bg-light text-center">
                    <a href="urunler.php?search=${encodeURIComponent(searchInput.val())}" class="text-primary">
                        Tüm sonuçları görüntüle <i class="fas fa-arrow-right"></i>
                    </a>
                </div>`;

                searchResults.html(html).show();
            }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            const logoutLinks = document.querySelectorAll('.logout-link');
            
            logoutLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // AJAX request for logout
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'logout.php', true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.durum === 'success') {
                                window.location.href = response.redirect;
                            }
                        }
                    };
                    
                    xhr.send();
                });
            });
        });

        function loadMainCategories() {
            $.ajax({
                url: 'nedmin/netting/islem.php',
                type: 'GET',
                data: { islem: 'kategori_getir' },
                dataType: 'json',
                success: function(response) {
                    if (response.durum === 'success') {
                        displayMainCategories(response.kategoriler);
                    }
                }
            });
        }
        
        // Function to display main categories
        function displayMainCategories(categories) {
            const mainCategoriesContainer = $('#main-categories');
            // Keep the "Tüm Kategoriler" item
            const allCategoriesItem = mainCategoriesContainer.html();
            mainCategoriesContainer.html(allCategoriesItem);
            
            // Get only main categories (no parent)
            const mainCategories = categories.filter(category => !category.parent_kategori_id);
            
            // Add main categories to the menu
            mainCategories.forEach(category => {
                const hasChildren = categories.some(cat => cat.parent_kategori_id == category.id);
                const categoryItem = `
                    <li class="nav-item">
                        <a class="nav-link" href="urunler.php?kategori=${category.id}">
                            ${category.kategori_isim}
                        </a>
                    </li>
                `;
                mainCategoriesContainer.append(categoryItem);
            });
        }
        
        // Function to display toast notifications
        function showToast(message, type = 'success') {
            // Create toast container if it doesn't exist
            let toastContainer = $('.toast-container');
            if (toastContainer.length === 0) {
                toastContainer = $('<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1050;"></div>');
                $('body').append(toastContainer);
            }
            
            // Define colors based on type (pastel purple theme)
            const colors = {
                'success': 'linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%)',
                'error': 'linear-gradient(135deg, #FF6B6B 0%, #FF8E8E 100%)',
                'danger': 'linear-gradient(135deg, #FF6B6B 0%, #FF8E8E 100%)',
                'warning': 'linear-gradient(135deg, #FFA500 0%, #FFB84D 100%)',
                'info': 'linear-gradient(135deg, #9D7FC7 0%, #8B6FC7 100%)'
            };
            
            const icons = {
                'success': 'fa-check-circle',
                'error': 'fa-exclamation-circle',
                'danger': 'fa-times-circle',
                'warning': 'fa-exclamation-triangle',
                'info': 'fa-info-circle'
            };
            
            const bgColor = colors[type] || colors['success'];
            const icon = icons[type] || icons['success'];
            
            // Create toast
            const toastId = 'toast-' + Date.now();
            const toast = $(`
                <div id="${toastId}" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true" style="background: ${bgColor}; border-radius: 12px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); min-width: 320px; animation: slideInRight 0.3s ease-out;">
                    <div class="d-flex align-items-center p-3">
                        <div class="toast-icon me-3" style="font-size: 1.5rem; animation: pulse 2s ease-in-out infinite;">
                            <i class="fas ${icon}"></i>
                        </div>
                        <div class="toast-body flex-grow-1" style="font-weight: 500; font-size: 0.95rem;">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close" style="opacity: 0.8;"></button>
                    </div>
                </div>
            `);
            
            // Add CSS animations if not already added
            if (!$('#toast-animations').length) {
                $('<style id="toast-animations">')
                    .text(`
                        @keyframes slideInRight {
                            from {
                                transform: translateX(100%);
                                opacity: 0;
                            }
                            to {
                                transform: translateX(0);
                                opacity: 1;
                            }
                        }
                        @keyframes pulse {
                            0%, 100% {
                                transform: scale(1);
                            }
                            50% {
                                transform: scale(1.1);
                            }
                        }
                    `)
                    .appendTo('head');
            }
            
            toastContainer.append(toast);
            
            // Initialize and show toast
            const bsToast = new bootstrap.Toast(toast[0], {
                autohide: true,
                delay: 3000
            });
            
            bsToast.show();
            
            // Remove toast element after it's hidden
            toast.on('hidden.bs.toast', function() {
                $(this).fadeOut(300, function() {
                    $(this).remove();
                });
            });
        }
        
        // Function to load notification count
        function loadNotificationsCount() {
            $.ajax({
                url: 'nedmin/netting/islem.php',
                type: 'POST',
                data: { islem: 'bildirim_sayisi' },
                dataType: 'json',
                success: function(response) {
                    if (response.durum === 'success') {
                        const count = response.bildirim_sayisi;
                        if (count > 0) {
                            $('#notification-badge').text(count).show();
                            $('#notification-badge-mobile').text(count).show();
                        } else {
                            $('#notification-badge').hide();
                            $('#notification-badge-mobile').hide();
                        }
                    }
                }
            });
        }
        function saveVisit(){
            $.ajax({
                url: 'nedmin/netting/islem.php',
                type: 'POST',
                data: { islem: 'ziyaret_kayit' },
                success: function(response){
                    console.log(response);
                },
                error: function(xhr, status, error){
                    console.log(xhr.responseText);
                }
            });
            
        }
    </script>
</body>
</html> 