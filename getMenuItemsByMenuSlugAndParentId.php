<?php
function getMenuItemsByMenuSlugAndParentId($menuSlug, $parentId) {
    $args = [
        'post_type' => 'nav_menu_item',
        'meta_key' => '_menu_item_menu_item_parent',
        'meta_value' => $parentId,
        'tax_query' => [
            [
                'taxonomy' => 'nav_menu',
                'field' => 'slug',
                'terms' => [ $menuSlug ]
            ]
        ],
        'order' => 'ASC',
        'orderby' => 'menu_order',
        'posts_per_page' => -1
    ];
    $tmpItems = query_posts($args);

    $menuitems = [];
    foreach ( $tmpItems as $tmpItem ) {
        $item = new stdClass;
        $type = get_post_meta($tmpItem->ID, '_menu_item_type', true);

        switch($type):
            // Add menu items types here when necessary
            case 'post_type':
                $postId = get_post_meta($tmpItem->ID, '_menu_item_object_id', true);
                $post = get_post($postId);
                $item->name = $post->post_title;
                $item->url = get_permalink($postId);
                break;
            case 'custom':
                $item->name = $tmpItem->post_title;
                $item->url = get_post_meta($tmpItem->ID, '_menu_item_url', true);
        endswitch;

        $item->children = getMenuItemsForParent($menuSlug, $tmpItem->ID);
        $menuItems[] = $item;
    }

    return $menuItems;
}


$menu_items = getMenuItemsByMenuSlugAndParentId('Primair', $post->post_parent);
