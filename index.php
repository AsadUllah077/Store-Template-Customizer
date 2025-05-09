<?php
// index.php
require_once 'functions.php';

$templates = getTemplates();
// echo "<pre>";
// print_r($templates);
// die;
$selected_template_id = isset($_GET['template_id']) ? (int)$_GET['template_id'] : null;
$fields = [];
$field_values = [];

if ($selected_template_id) {
    $fields = getTemplateFields($selected_template_id);
   
    $field_values = getTemplateFieldValues($_SESSION['guest_id'], $selected_template_id);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['template_id'])) {
    $template_id = (int)$_POST['template_id'];
    $session_id = $_SESSION['guest_id'];
    
    foreach ($_POST as $field_name => $value) {
        if (strpos($field_name, 'field_') === 0) {
            $field_id = (int)substr($field_name, 6);
            saveTemplateFieldValue($session_id, $template_id, $field_id, $value);
        }
    }
    
    // Handle file uploads
    foreach ($_FILES as $field_name => $file) {
        if (strpos($field_name, 'field_') === 0 && $file['error'] === UPLOAD_ERR_OK) {
            $field_id = (int)substr($field_name, 6);
            $file_path = handleFileUpload($field_name);
            if ($file_path) {
                saveTemplateFieldValue($session_id, $template_id, $field_id, $file_path);
            }
        }
    }
    
    // Redirect to prevent form resubmission
    header("Location: index.php?template_id=$template_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Template Customizer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .template-card {
            cursor: pointer;
            transition: transform 0.3s;
        }
        .template-card:hover {
            transform: scale(1.03);
        }
        .template-card.selected {
            border: 3px solid #0d6efd;
        }
        .color-preview {
            width: 30px;
            height: 30px;
            display: inline-block;
            border: 1px solid #ddd;
            margin-left: 10px;
        }
        .preview-container {
            border: 1px solid #ddd;
            padding: 20px;
            margin-top: 20px;
            min-height: 300px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-5">Store Template Customizer</h1>
        
        <div class="row mb-5">
            <div class="col-12">
                <h2>Choose a Template</h2>
                <div class="row">
                    <?php foreach ($templates as $template): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card template-card <?= $selected_template_id == $template['id'] ? 'selected' : '' ?>"
                                 onclick="window.location.href='?template_id=<?= $template['id'] ?>'">
                                <img src="<?= htmlspecialchars($template['thumbnail_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($template['name']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($template['name']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($template['description']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <?php if ($selected_template_id): ?>
        <div class="row">
            <div class="col-md-6">
                <h2>Customize Your Store</h2>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="template_id" value="<?= $selected_template_id ?>">
                    
                    <?php foreach ($fields as $field): 
                        $field_value = $field_values[$field['field_name']]['field_value'] ?? $field['default_value'];
                    ?>
                        <div class="mb-3">
                            <label for="field_<?= $field['id'] ?>" class="form-label">
                                <?= htmlspecialchars($field['field_label']) ?>
                            </label>
                            
                            <?php if ($field['field_type'] === 'text'): ?>
                                <input type="text" class="form-control" id="field_<?= $field['id'] ?>" 
                                       name="field_<?= $field['id'] ?>" value="<?= htmlspecialchars($field_value) ?>">
                            
                            <?php elseif ($field['field_type'] === 'color'): ?>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" id="field_<?= $field['id'] ?>" 
                                           name="field_<?= $field['id'] ?>" value="<?= htmlspecialchars($field_value) ?>">
                                    <span class="input-group-text color-preview" style="background-color: <?= htmlspecialchars($field_value) ?>"></span>
                                </div>
                            
                            <?php elseif ($field['field_type'] === 'image'): ?>
                                <div>
                                    <?php if ($field_value && file_exists($field_value)): ?>
                                        <img src="<?= htmlspecialchars($field_value) ?>" class="img-thumbnail mb-2" style="max-height: 100px;">
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="field_<?= $field['id'] ?>" 
                                           name="field_<?= $field['id'] ?>" accept="image/*">
                                </div>
                            
                            <?php elseif ($field['field_type'] === 'textarea'): ?>
                                <textarea class="form-control" id="field_<?= $field['id'] ?>" 
                                          name="field_<?= $field['id'] ?>" rows="3"><?= htmlspecialchars($field_value) ?></textarea>
                            
                            <?php elseif ($field['field_type'] === 'number'): ?>
                                <input type="number" class="form-control" id="field_<?= $field['id'] ?>" 
                                       name="field_<?= $field['id'] ?>" value="<?= htmlspecialchars($field_value) ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <button type="submit" class="btn btn-primary">Save Customizations</button>
                    <a href="store.php?template_id=<?= $selected_template_id ?>" class="btn btn-success">Preview Store</a>
                </form>
            </div>
            
            <div class="col-md-6">
                <h2>Live Preview</h2>
                <div class="preview-container">
                    <?php 
                    $html_path = getTemplateHtmlPath($selected_template_id);
                    if ($html_path && file_exists($html_path)) {
                        $html_content = file_get_contents($html_path);
                        
                        // Replace placeholders with actual values
                        foreach ($fields as $field) {

                            // echo "<pre>";
                            // print_r($fields);
                            // die;
                            $field_value = $field_values[$field['field_name']]['field_value'] ?? $field['default_value'];
                            $placeholder = '{{' . $field['field_name'] . '}}';
                            $html_content = str_replace($placeholder, htmlspecialchars($field_value), $html_content);
                        }
                        
                        echo $html_content;
                    } else {
                        echo '<div class="alert alert-warning">Preview not available for this template.</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update color preview when color input changes
        $(document).on('input', 'input[type="color"]', function() {
            $(this).next('.color-preview').css('background-color', $(this).val());
        });
    </script>
</body>
</html>