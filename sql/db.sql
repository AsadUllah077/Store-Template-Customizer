-- Database: store_templates
CREATE DATABASE IF NOT EXISTS store_templates;
USE store_templates;

-- Table for available templates
CREATE TABLE IF NOT EXISTS store_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    thumbnail_path VARCHAR(255),
    html_file_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for template fields (customizable elements)
CREATE TABLE IF NOT EXISTS store_template_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    field_label VARCHAR(100) NOT NULL,
    field_type ENUM('text', 'color', 'image', 'textarea', 'number') NOT NULL,
    default_value VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES store_templates(id) ON DELETE CASCADE
);

-- Table for stored customizations
CREATE TABLE IF NOT EXISTS store_template_field_values (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    template_id INT NOT NULL,
    field_id INT NOT NULL,
    field_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES store_templates(id) ON DELETE CASCADE,
    FOREIGN KEY (field_id) REFERENCES store_template_fields(id) ON DELETE CASCADE
);

-- Insert sample templates
INSERT INTO store_templates (name, description, thumbnail_path, html_file_path) VALUES
('Minimal', 'Clean and simple design', 'templates/minimal/thumbnail.jpg', 'templates/minimal/template.html'),
('Bold', 'Vibrant colors and strong typography', 'templates/bold/thumbnail.jpg', 'templates/bold/template.html'),
('Elegant', 'Sophisticated design with subtle animations', 'templates/elegant/thumbnail.jpg', 'templates/elegant/template.html'),
('Modern', 'Contemporary layout with card-based design', 'templates/modern/thumbnail.jpg', 'templates/modern/template.html');

-- Insert fields for template 1 (Minimal)
INSERT INTO store_template_fields (template_id, field_name, field_label, field_type, default_value) VALUES
(1, 'primary_color', 'Primary Color', 'color', '#3498db'),
(1, 'secondary_color', 'Secondary Color', 'color', '#2c3e50'),
(1, 'header_text', 'Header Text', 'text', 'Welcome to My Store'),
(1, 'banner_image', 'Banner Image', 'image', 'default_banner.jpg'),
(1, 'font_size', 'Base Font Size', 'number', '16'),
(1, 'contact_email', 'Contact Email', 'text', 'contact@example.com');

-- Insert fields for template 2 (Bold)
INSERT INTO store_template_fields (template_id, field_name, field_label, field_type, default_value) VALUES
(2, 'primary_color', 'Primary Color', 'color', '#e74c3c'),
(2, 'background_color', 'Background Color', 'color', '#ecf0f1'),
(2, 'header_text', 'Store Name', 'text', 'BOLD STORE'),
(2, 'tagline', 'Tagline', 'text', 'Shop with confidence'),
(2, 'main_banner', 'Main Banner', 'image', 'bold_banner.jpg'),
(2, 'font_family', 'Font Family', 'text', 'Impact, sans-serif');

-- Insert fields for template 3 (Elegant)
INSERT INTO store_template_fields (template_id, field_name, field_label, field_type, default_value) VALUES
(3, 'primary_color', 'Primary Color', 'color', '#8e44ad'),
(3, 'secondary_color', 'Secondary Color', 'color', '#34495e'),
(3, 'header_text', 'Store Title', 'text', 'Elegant Boutique'),
(3, 'welcome_message', 'Welcome Message', 'textarea', 'Welcome to our elegant store. Enjoy your shopping experience.'),
(3, 'logo_image', 'Logo Image', 'image', 'elegant_logo.png'),
(3, 'contact_phone', 'Contact Phone', 'text', '+1 234 567 890');

-- Insert fields for template 4 (Modern)
INSERT INTO store_template_fields (template_id, field_name, field_label, field_type, default_value) VALUES
(4, 'primary_color', 'Primary Color', 'color', '#27ae60'),
(4, 'card_color', 'Card Background', 'color', '#ffffff'),
(4, 'header_text', 'Store Header', 'text', 'Modern Shop'),
(4, 'featured_banner', 'Featured Banner', 'image', 'modern_banner.jpg'),
(4, 'font_size', 'Base Font Size', 'number', '14'),
(4, 'social_links', 'Social Media Links', 'textarea', 'facebook.com\ntwitter.com\ninstagram.com');