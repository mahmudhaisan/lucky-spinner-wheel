<?php
// Add a custom dashboard page to the admin menu
function carpet_dashboard_menu_page()
{
    add_menu_page(
        'Carpet Dashboard',
        'Carpet Dashboard',
        'manage_options',
        'carpet_dashboard_page',
        'render_carpet_dashboard_page'
    );
}
add_action('admin_menu', 'carpet_dashboard_menu_page');

// Render the custom dashboard page
function render_carpet_dashboard_page()
{
?>
    <div class="wrap">
        <h2>Custom Dashboard</h2>
    </div>
<?php
}
