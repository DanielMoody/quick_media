# Quick Media

Quick Media is a lightweight Drupal module that allows editors to embed media entities directly into content using simple tokens.

Instead of navigating the media browser or manually inserting embeds, editors can use:


`[media:123]`


…and have it automatically rendered as an `<img>` tag.

---
## Installation

```
composer require cms-alchemy/quick-media
drush en quick_media
```
## Features

- Token-based media embedding: `[media:ID]`
- Automatically resolves the correct source field for any media type
- Outputs fully formed `<img>` tags with:
  - `src`
  - `alt`
  - optional `title`
- Safe HTML escaping
- Cache tag integration for proper invalidation
- Views-based UI for browsing media and copying tokens
- Click-to-copy tokens in admin UI

---

## How It Works

The module provides a text filter:


quick_media_token_filter

This filter scans content for:

[media:123]


It then:

1. Loads the media entity
2. Resolves the source field dynamically (no hardcoding image fields)
3. Loads the referenced file
4. Generates a public URL
5. Outputs an `<img>` tag

Example output:

```html
<img src="/sites/default/files/example.jpg" alt="Example" style="max-width:100%;">

## Requirements
Drupal 9, 10, or 11
Media module
File module
Image module (for thumbnails in UI)
