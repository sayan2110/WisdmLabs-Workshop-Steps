<?php
add_filter(
    'wp_register_ability_args',
    static function ( array $args, string $ability_id ): array {
        if (
            wp_get_environment_type() === 'local'
            && ! str_starts_with( $ability_id, 'mcp-adapter/' )
        ) {
            $args['meta']['mcp']['public'] = true;
        }
        return $args;
    },
    10,
    2
);