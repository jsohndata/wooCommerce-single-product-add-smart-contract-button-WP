add_action('woocommerce_single_product_summary', 'add_contact_button_with_product_info', 25);

function add_contact_button_with_product_info() {
    global $product;

    // Get product info
    $product_name = $product->get_name();
    $product_sku = $product->get_sku();
    $product_url  = get_permalink($product->get_id());

    // Build subject and body
    $subject = rawurlencode("Product Inquiry: $product_name (SKU: $product_sku)");
    $body = rawurlencode("Hi,\n\nI'm interested in the following product:\n\nProduct: $product_name\nSKU: $product_sku\nURL: $product_url\n\nThank you!");

    // Create mailto link
    $mailto_link = "mailto:youremail@example.com?subject={$subject}&body={$body}";

    // Output the button
    echo '<a href="' . esc_url($mailto_link) . '" class="button contact-button" style="margin-top: 15px; display: inline-block;">Contact Us</a>';
}
