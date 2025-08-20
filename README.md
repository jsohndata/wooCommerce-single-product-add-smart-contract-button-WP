# WooCommerce: Add Dynamic “Contact Us” Email Button with Product Details — WPCode-Compatible

🤖 **AI-Enhanced (GPT-5)** WooCommerce snippet that adds a smart **"Contact Us"** button to the single product page.  
The button opens a pre-filled email including the product **name**, **SKU**, and **page URL** in the subject and body.  
Clean, lightweight, and compatible with WPCode, Code Snippets, or a child theme’s `functions.php`.

---

## Features

- Adds a visually styled **“Contact Us”** button to the product summary
- Opens a pre-filled email with:
  - Product **name**
  - Product **SKU**
  - Product **URL**
- Uses `mailto:` (no forms or plugins needed)
- Works with any theme using WooCommerce hooks
- Clean code, no templates modified

---

## Requirements

- WordPress with WooCommerce active
- One of the following:
  - [WPCode plugin](https://wordpress.org/plugins/wpcode/) (recommended)
  - Code Snippets plugin
  - A child theme with access to `functions.php`

---

## Installation

### Option 1: WPCode (Recommended)

1. Install and activate the **WPCode** plugin
2. Go to **Code Snippets → Add New**
3. Choose **“Add Your Custom Code (New Snippet)”**
4. Select **PHP Snippet**
5. Name it: `WooCommerce Contact Us Button`
6. Paste the code from the **Code** section below
7. Set **Location** to `Run Everywhere`
8. Save and **Activate**

### Option 2: Add to `functions.php`

1. Open your child theme’s `functions.php` file
2. Paste the PHP code at the end
3. Save the file

---

## Code

```php
/**
 * WooCommerce: Add "Contact Us" email button with product name, SKU, and URL
 */
add_action('woocommerce_single_product_summary', 'add_contact_button_with_product_info', 25);

function add_contact_button_with_product_info() {
    global $product;

    // Get product details
    $product_name = $product->get_name();
    $product_sku  = $product->get_sku();
    $product_url  = get_permalink($product->get_id());

    // Build subject and body
    $subject = rawurlencode("Product Inquiry: $product_name (SKU: $product_sku)");
    $body    = rawurlencode("Hi,\n\nI'm interested in the following product:\n\nProduct: $product_name\nSKU: $product_sku\nURL: $product_url\n\nThank you!");

    // Create mailto link
    $mailto_link = "mailto:youremail@example.com?subject={$subject}&body={$body}";

    // Output the button
    echo '<a href="' . esc_url($mailto_link) . '" class="button contact-button" style="margin-top: 15px; display: inline-block;">Contact Us</a>';
}

---

## Code
Example Email Output

When a customer clicks the Contact Us button, their email app opens with the following:

Subject
## Code
Product Inquiry: $productName (SKU: $productSKU)

---

Body 
## Code
Hi,

I'm interested in the following product:

Product: $productName
SKU: $productSKU  
URL: $productURL

Thank you!


---






