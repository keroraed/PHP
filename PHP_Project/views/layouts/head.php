<?php
/**
 * Shared HTML head partial
 * Variables:
 *   $pageTitle  (string) - page title, defaults to 'Premium Cafeteria'
 *   $bodyClass  (string) - optional extra class(es) for <body>
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Premium Cafeteria') ?> - Premium Cafeteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/css/modern.css" rel="stylesheet">
</head>
<body<?= isset($bodyClass) ? ' class="' . htmlspecialchars($bodyClass) . '"' : '' ?>>
