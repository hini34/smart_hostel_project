<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' | Smart Hostel' : 'Smart Hostel'; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.2/css/all.css">

<!-- sidebar.php -->
<link rel="stylesheet" href="../../assets/css/student/student_sidebar.css">

<?php if (!empty($extra_css)): ?>
    <link rel="stylesheet" href="<?php echo $extra_css; ?>">
<?php endif; ?>