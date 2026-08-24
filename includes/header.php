<?php
/**
 * PENNEN Footwear — Reusable Header Component
 *
 * @var string $pageTitle
 * @var string $metaDescription
 * @var string $rootPath
 * @var array  $extraCss
 */
$rootPath = $rootPath ?? './';
$pageTitle = $pageTitle ?? 'PENNEN — A Growing Step';
$metaDescription = $metaDescription ?? 'PENNEN — engineered footwear, a growing step. Shop across every major Indian marketplace.';
$extraCss = $extraCss ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>" />
  <link rel="icon" type="image/png" href="<?php echo $rootPath; ?>favicon.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Italiana&family=JetBrains+Mono:wght@400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/variables.css" />
  <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/layout.css" />
  <?php foreach ($extraCss as $css): ?>
    <link rel="stylesheet" href="<?php echo $rootPath . $css; ?>" />
  <?php endforeach; ?>
  <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/responsive.css" />
</head>
<body>
