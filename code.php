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
