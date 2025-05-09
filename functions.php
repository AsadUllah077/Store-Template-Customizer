<?php
// functions.php
require_once 'config.php';

function getTemplates() {
    $sql = "SELECT * FROM store_templates";
    $result = executeQuery($sql)->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getTemplateFields($template_id) {
    $sql = "SELECT * FROM store_template_fields WHERE template_id = ?";
    $stmt = executeQuery($sql, [$template_id], 'i');
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getTemplateFieldValues($session_id, $template_id) {
    $sql = "SELECT v.field_id, v.field_value, f.field_name, f.field_type 
            FROM store_template_field_values v
            JOIN store_template_fields f ON v.field_id = f.id
            WHERE v.session_id = ? AND v.template_id = ?";
    $stmt = executeQuery($sql, [$session_id, $template_id], 'si');
    $result = $stmt->get_result();
    
    $values = [];
    while ($row = $result->fetch_assoc()) {
        $values[$row['field_name']] = $row;
    }
    return $values;
}

function saveTemplateFieldValue($session_id, $template_id, $field_id, $value) {
    // Check if value already exists
    $check_sql = "SELECT id FROM store_template_field_values 
                  WHERE session_id = ? AND template_id = ? AND field_id = ?";
    $check_stmt = executeQuery($check_sql, [$session_id, $template_id, $field_id], 'sii');
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Update existing value
        $update_sql = "UPDATE store_template_field_values 
                      SET field_value = ?, updated_at = NOW() 
                      WHERE session_id = ? AND template_id = ? AND field_id = ?";
        return executeQuery($update_sql, [$value, $session_id, $template_id, $field_id], 'ssii');
    } else {
        // Insert new value
        $insert_sql = "INSERT INTO store_template_field_values 
                      (session_id, template_id, field_id, field_value) 
                      VALUES (?, ?, ?, ?)";
        return executeQuery($insert_sql, [$session_id, $template_id, $field_id, $value], 'siis');
    }
}

function handleFileUpload($field_name) {
    if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file_ext = pathinfo($_FILES[$field_name]['name'], PATHINFO_EXTENSION);
    $file_name = uniqid('img_', true) . '.' . $file_ext;
    $file_path = $upload_dir . $file_name;
    
    if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $file_path)) {
        return $file_path;
    }
    
    return null;
}

function getTemplateHtmlPath($template_id) {
    $sql = "SELECT html_file_path FROM store_templates WHERE id = ?";
    $stmt = executeQuery($sql, [$template_id], 'i');
    $result = $stmt->get_result();
    $template = $result->fetch_assoc();
    return $template ? $template['html_file_path'] : null;
}
?>