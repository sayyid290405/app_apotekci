<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'MyApotek' ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Custom Style -->
    <style>
        body {
            background-color: #f5f7fb;
        }

        .navbar-custom {
            background: #0f766e;
            color: white;
        }

        .sidebar {
            width: 230px;
            height: 100vh;
            position: fixed;
            background: white;
            border-right: 1px solid #ddd;
        }

        .sidebar a {
            display: block;
            padding: 12px;
            color: #333;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #f1f1f1;
        }

        .content {
            margin-left: 230px;
            padding: 20px;
        }

        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-custom px-3">
    <span class="navbar-brand text-white fw-bold">MyApotek</span>

    <div class="ms-auto text-white">
        <i class="fa fa-bell me-3"></i>
        <?= $this->session->userdata('nama') ?? 'Admin' ?>
    </div>
</nav>