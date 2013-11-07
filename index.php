<?php
require('../pocket-knife/start.php');
set_include_path('./classes');

$patients = new Patients();
$patients->GET();
$tests = new Tests();
$tests->GET();

// build page
ob_start();
?>

<h2>Patients</h2>
<?php echo Formatter::getResourceList($patients); ?>
<p><a href="<?php echo WebUrl::create('api.php/patient'); ?>">New Patient</a></p>

<h2>Latest Tests</h2>
<?php echo Formatter::getLatestTests($tests); ?>
<p><a href="<?php echo WebUrl::create('api.php/test'); ?>">New Test</a></p>

<?php
$page = ob_get_clean();
$template = new WebTemplate('template.php', WebTemplate::PHP_FILE);
$template->replace('main_view', $page);
$template->display();
