# WooCommerce: Conditional “Contact Us” Email Button with Product Details — WPCode-Compatible

🤖 **AI-Enhanced (GPT-5)** WooCommerce snippet that adds a smart **"Contact Us"** button to the single product page — **only when the product is available**.  
The button opens a pre-filled email including the product **name**, **SKU**, and **page URL** in the subject and body.  
If the product is **out of stock** or tagged/categorized as **sold**, the button will not appear.  
Clean, lightweight, and compatible with WPCode, Code Snippets, or a child theme’s `functions.php`.

---

## Working Sample
[Sansefuria.com](https://sansefuria.com/plants/cereus-forbesii-monstrose-ming-thing-medium-succulent-lfmng00/)

---

## Features

- Adds a visually styled **“Contact Us”** button to the product summary
- Opens a pre-filled email with:
  - Product **name**
  - Product **SKU**
  - Product **URL**
- **Conditional logic**:
  - Hidden automatically if product is **sold out** or marked **sold** (tag or category)
- Uses `mailto:` (no forms or plugins needed)
- Works with any theme using WooCommerce hooks
- Clean, functional-style code — no templates modified
- All text/email easily configurable at the top of the snippet

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
5. Name it: `WooCommerce Conditional Contact Us Button`
6. Paste the code from the **Code** section below
7. Set **Location** to `Run Everywhere`
8. Save and **Activate**

### Option 2: Add to `functions.php`

1. Open your child theme’s `functions.php` file
2. Paste the PHP code at the end
3. Save the file

---

## Example Email Output
When a customer clicks the Contact Us button, their email app opens with the following:

Subject:
```
Product Inquiry: $product_name (SKU: $product_sku)
```

Body:
```
Hi,

I'm interested in the following product:

Product: $product_name
SKU: $product_sku  
URL: $product_url

Thank you!
```

## Code

```php
<?php
/**
 * WooCommerce: Conditional “Contact Us” Button (functional style, configurable)
 * - Renders only when product is purchasable, in stock, and not “sold”
 * - All texts + email configurable at the top
 * - Functional helpers, filterable rules
 */

/** ======================
 *  CONFIGURATION
 *  ====================== */
$sansefuria_contact_config = [
    'email'        => 'email@gmail.com',
    'heading'      => '❤️ Bring This Plant Home',
    'lead'         => 'Ready to give this plant a home?',
    'button_label' => 'Let’s Get In Touch!',
    'subject_tpl'  => 'Product Inquiry: %1$s (SKU: %2$s)',
    'body_tpl'     => "Hi,\n\nI'm interested in the following product:\n\nProduct: %s\nSKU: %s\nURL: %s\n\nThank you!",
];

/**
 * Main hook – render contact section conditionally
 */
add_action('woocommerce_single_product_summary', function () use ($sansefuria_contact_config): void {
    $product = wc_get_product(get_the_ID());
    if (! $product instanceof WC_Product) {
        return;
    }

    if (! sansefuria_is_product_contactable($product)) {
        return;
    }

    $mailto = sansefuria_build_mailto_link(
        $sansefuria_contact_config['email'],
        $product->get_name(),
        $product->get_sku() ?: '',
        get_permalink($product->get_id()),
        $sansefuria_contact_config['subject_tpl'],
        $sansefuria_contact_config['body_tpl']
    );

    echo sansefuria_render_contact_section(
        $mailto,
        $sansefuria_contact_config['heading'],
        $sansefuria_contact_config['lead'],
        $sansefuria_contact_config['button_label']
    ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}, 25);


/** ======================
 *  HELPERS
 *  ====================== */

/** Predicate: should we show the contact section? */
function sansefuria_is_product_contactable(WC_Product $product): bool {
    return sansefuria_is_purchasable_in_stock($product)
        && ! sansefuria_is_marked_sold($product);
}

/** Product must be purchasable AND in stock */
function sansefuria_is_purchasable_in_stock(WC_Product $product): bool {
    return $product->is_purchasable() && $product->is_in_stock();
}

/** Treat items as “sold” if tagged/categorized that way (filterable) */
function sansefuria_is_marked_sold(WC_Product $product): bool {
    $is_sold = has_term('sold', 'product_tag', $product->get_id())
            || has_term('sold', 'product_cat', $product->get_id());

    return (bool) apply_filters('sansefuria_is_marked_sold', $is_sold, $product);
}

/** Build a safe mailto link */
function sansefuria_build_mailto_link(
    string $email,
    string $name,
    string $sku,
    string $url,
    string $subject_tpl,
    string $body_tpl
): string {
    $subject = rawurlencode(sprintf($subject_tpl, $name, $sku !== '' ? $sku : 'N/A'));
    $body    = rawurlencode(sprintf(
        $body_tpl,
        $name,
        $sku !== '' ? $sku : 'N/A',
        $url
    ));

    return "mailto:{$email}?subject={$subject}&body={$body}";
}

/** Pure renderer: returns HTML */
function sansefuria_render_contact_section(
    string $mailto_href,
    string $heading,
    string $lead,
    string $button_label
): string {
    $title   = esc_html($heading);
    $lead    = esc_html($lead);
    $button  = esc_html($button_label);
    $mailto  = esc_url($mailto_href);

    return <<<HTML
<div class="plant-contact-section" style="margin-top:2em;">
  <h3>{$title}</h3>
  <p>{$lead}</p>
  <a href="{$mailto}" class="button contact-button" style="margin-top:10px;display:inline-block;">{$button}</a>
  <style>
    @media (max-width: 768px) {
      .plant-contact-section .contact-button {
        display: block !important;
        width: 100% !important;
        text-align: center;
      }
    }
  </style>
</div>
HTML;
}


---
