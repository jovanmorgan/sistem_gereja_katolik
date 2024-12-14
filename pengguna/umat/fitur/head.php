<?php include 'nama_halaman.php'; ?>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title><?= $page_title ?> | Gereja Katolik Santa Maria Asumta </title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="./.../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />
    <link href="../../assets/img/gereja/logo_gereja.png" rel="icon" />
    <!-- Fonts and icons -->
    <script src="../../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["../../assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css?v=<?= time(); ?>" />
    <link rel="stylesheet" href="../../assets/css/plugins.min.css?v=<?= time(); ?>" />
    <link rel="stylesheet" href="../../assets/css/kaiadmin.min.css?v=<?= time(); ?>" />

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="../../assets/css/demo.css?v=<?= time(); ?>" />
</head>