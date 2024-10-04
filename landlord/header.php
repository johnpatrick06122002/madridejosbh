<?php include('../connection.php'); ?>
<?php include('session.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LANDLORD</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Rich Text Editor CSS -->
    <link rel="stylesheet" href="../src/richtext.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <!-- Custom CSS -->
    <style type="text/css">
        body, html {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        .sidebar {
            margin: 0;
            padding: 0;
            width: 200px;
            background-color: #444444;
            position: fixed;
            height: 100%;
            overflow: auto;
        }
        .sidebar a {
            display: block;
            color: #fff;
            padding: 16px;
            text-decoration: none;
        }
        .sidebar a.active {
            background-color: #04AA6D;
            color: white;
        }
        .sidebar a:hover:not(.active) {
            background-color: #555;
            color: white;
        }
        div.content {
            margin-left: 200px;
            padding: 1px 16px;
            height: 1000px;
        }
        .page-wrapper.box-content {
            border: thin solid silver;
        }
        .card {
            width: 100%;
            display: flex;
            flex-direction: column;
            border: 1px red solid;
        }
        .header {
            height: 30%;
            background: red;
            color: white;
            text-align: center;
        }
        .container {
            padding: 2px 16px;
        }
        ul.pagination li {
            background: #04AA6D;
            padding: 10px;
            margin: 5px;
            border: thin solid silver;
        }
        ul.pagination li a {
            color: #fff;
        }
        ul.pagination li.disabled {
            background: #adadad;
        }
        h1.pending {
            text-align: center;
            margin-top: 30vh;
        }
        @media screen and (max-width: 700px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .sidebar a {
                float: left;
            }
            div.content {
                margin-left: 0;
            }
        }
        @media screen and (max-width: 400px) {
            .sidebar a {
                text-align: center;
                float: none;
            }
        }
    </style>
    <!-- Chart.js and SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>
    