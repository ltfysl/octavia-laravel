## Summary

Escape LIKE wildcards in search queries

## Changes

- Escape % _ \\ in user search terms before wrapping with %...%
- Applies to SearchController and MarketplaceController LIKE filters

Fixes #1
