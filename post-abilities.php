<?php
add_action( 'wp_abilities_api_categories_init', function() {
    if ( ! function_exists( 'wp_register_ability_category' ) ) {
        return;
    }

    wp_register_ability_category( 'content', [
        'label'       => 'Content',
        'description' => 'Abilities that create or modify site content.',
    ] );
} );

add_action( 'wp_abilities_api_init', function() {
    if ( ! function_exists( 'wp_register_ability' ) ) {
        return;
    }

    wp_register_ability( 'demo/create-post', [
        'label'       => 'Create Post',
        'description' => 'Creates a new WordPress post',
        'category'    => 'content',
        'input_schema' => [
            'type'       => 'object',
            'properties' => [
                'title'   => [
                    'type'        => 'string',
                    'description' => 'Post title',
                ],
                'content' => [
                    'type'        => 'string',
                    'description' => 'Post content',
                ],
                'status'  => [
                    'type'        => 'string',
                    'description' => 'Post status: draft or publish',
                    'default'     => 'draft',
                ],
            ],
            'required' => [ 'title' ],
        ],
        'permission_callback' => function() {
            return current_user_can( 'publish_posts' );
        },
        'execute_callback' => function( $params ) {
            $post_id = wp_insert_post( [
                'post_title'   => sanitize_text_field( $params['title'] ),
                'post_content' => wp_kses_post( $params['content'] ?? '' ),
                'post_status'  => $params['status'] ?? 'draft',
            ] );

            if ( is_wp_error( $post_id ) ) {
                return [ 'success' => false, 'error' => $post_id->get_error_message() ];
            }

            return [
                'success' => true,
                'post_id' => $post_id,
                'url'     => get_permalink( $post_id ),
                'message' => "Post created with ID: $post_id",
            ];
        },
        'meta' => [
            'mcp' => [ 'public' => true ],
        ],
    ] );
} );
