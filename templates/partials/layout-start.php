<?php
/**
 * Gemeinsamer Seitenkopf (Head + Header).
 *
 * Erwartet optional $pageTitle; der Seitenname "Fahrgast-Info" wird
 * immer angehängt bzw. allein gezeigt.
 */
$pageTitle = $pageTitle ?? '';
$siteName = 'Fahrgast-Info';
?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle !== '' ? $pageTitle . ' – ' . $siteName : $siteName, ENT_QUOTES) ?></title>

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>

<body>

<header class="py-4 px-4 px-md-5">
    <h1 class="h3 mb-0"><a href="/" class="text-reset text-decoration-none"><?= $siteName ?></a></h1>
</header>
