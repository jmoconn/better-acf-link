# Better ACF Link

### Requires ACF and ACFE

Includes the following helper functions, which accept the ACF field array (from get_field):

-   tm_get_custom_link( $field )
-   tm_print_custom_link( $field )
-   tm_print_link_open( $field ) // useful if link should be used as wrapper around linked element
-   tm_print_link_close()

Includes admin controls for adding/remove subfields and restricting post type filter options.

Allows further filtering of available post types via the following filter (expects associative array of custom post type slugs => names ):

-   tm-link-post-types

Allows further filtering of subfields via the following filter (can further specify field name or key):

-   tm/fields/tm_link/sub_fields
