<?php
// store.php
require_once 'functions.php';

$template_id = isset($_GET['template_id']) ? (int)$_GET['template_id'] : null;
$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : $_SESSION['guest_id'];

if (!$template_id) {
    die("Template ID is required.");
}

$template_fields = getTemplateFields($template_id);
$field_values = getTemplateFieldValues($session_id, $template_id);
$html_path = getTemplateHtmlPath($template_id);

if (!$html_path || !file_exists($html_path)) {
    die("Template file not found.");
}

// Get HTML content
$html_content = file_get_contents($html_path);

// Replace placeholders with actual values
foreach ($template_fields as $field) {
    $field_value = $field_values[$field['field_name']]['field_value'] ?? $field['default_value'];
    $placeholder = '{{' . $field['field_name'] . '}}';
    $html_content = str_replace($placeholder, htmlspecialchars($field_value), $html_content);
}

// Output the customized template
header('Content-Type: text/html; charset=utf-8');
echo $html_content;
?>