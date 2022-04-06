<?php

/**
 * Subtitle Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'campus-message-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'campus-message';
if ( ! empty( $block['className'] ) ) {
	$className .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$className .= ' align' . $block['align'];
}

$cm_to = get_field_object( 'cm-to' );
$cm_from = get_field_object( 'cm-from' );
$cm_to_label = $cm_to['label'];
$cm_from_label = $cm_from['label'];
$cm_to_value = $cm_to['value'] ?: ' Message audience';
$cm_from_value = $cm_from['value'] ?: ' Message sender';

?>
<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $className ); ?>">
	<p class="campus-message-text cm-to"><span class="campus-message-label"><?php echo $cm_to_label; ?> </span><?php echo $cm_to_value; ?></p>
	<p class="campus-message-text cm-from"><span class="campus-message-label"><?php echo $cm_from_label; ?> </span><?php echo $cm_from_value; ?></p>
</div>
