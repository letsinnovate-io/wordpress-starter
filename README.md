# WordPress Blocks — Starter Project

A ready-to-go WordPress project configured with the [WordPress Blocks](https://github.com/letsinnovate-io/wordpress-blocks) plugin for animated page blocks.

## Requirements

- PHP 8.2+
- [Composer](https://getcomposer.org/)
- [DDEV](https://ddev.readthedocs.io/) (for local development)
- A [GitHub Personal Access Token](https://github.com/settings/tokens) with access to the private repos

## Quick Start (DDEV)

```bash
# 1. Clone this repo
git clone https://github.com/letsinnovate-io/wordpress-starter.git my-site
cd my-site

# 2. Configure Composer authentication for private repos
composer config --global github-oauth.github.com YOUR_GITHUB_PAT

# 3. Install dependencies
ddev start
ddev composer install

# 4. That's it — DDEV auto-installs WordPress + activates the plugin
#    Visit: https://my-wp-blocks-site.ddev.site
#    Admin: https://my-wp-blocks-site.ddev.site/wp-admin  (admin / admin)
```

## What Happens Automatically

On `ddev start`, the DDEV hooks will:

1. Download WordPress core into `web/` (if not already present)
2. Run `wp core install` to set up the database (if not already installed)
3. Activate the WordPress Blocks plugin (which auto-installs the Craft Blocks theme)

## Configuration

All configuration lives in `.env` (copied from `.env.example` on first `composer install`).

| Variable | Description | Default |
|---|---|---|
| `DB_NAME` | Database name | `db` (DDEV) |
| `DB_USER` | Database user | `db` (DDEV) |
| `DB_PASSWORD` | Database password | `db` (DDEV) |
| `DB_HOST` | Database host | `localhost` |
| `WP_HOME` | Site URL | auto (DDEV) |
| `WP_DEBUG` | Enable debug mode | `false` |
| `AUTH_KEY`, etc. | Salts | Generate at [roots.io/salts.html](https://roots.io/salts.html) |

In DDEV, database and URL settings are provided automatically — you only need `.env` for production.

## Project Structure

```
├── .ddev/                  # DDEV local dev config
│   └── config.yaml
├── .env.example            # Environment template (committed)
├── .env                    # Your environment values (gitignored)
├── composer.json           # Manages plugin + dependencies
├── vendor/                 # Composer dependencies (gitignored)
└── web/                    # WordPress docroot
    ├── wp-config.php       # Dotenv-powered config (committed)
    ├── wp-admin/           # WP core (gitignored, downloaded by DDEV)
    ├── wp-includes/        # WP core (gitignored)
    └── wp-content/
        ├── plugins/
        │   └── wordpress-blocks/  # Installed via Composer
        ├── themes/
        │   └── craft-blocks/      # Auto-installed by plugin on activation
        └── uploads/               # Media uploads (gitignored)
```

## Twig Layout Templates

The Craft Blocks theme uses Twig for page layouts, giving you full control over the HTML shell — just like Craft CMS entry templates.

Edit the layout files in `web/wp-content/themes/craft-blocks/twig/layouts/`:

- `default.twig` — base layout (nav, footer, asset loading)
- `page.twig` — page-specific layout (includes default)
- Create `post.twig`, `landing.twig`, etc. for other layouts

Available template variables:

| Variable | Description |
|---|---|
| `page.title` | Post/page title |
| `page.url` | Permalink |
| `site.name` | Site name from Settings |
| `site.language` | Site language |
| `blocks` | Rendered block HTML |
| `criticalCss` | Inline critical CSS |
| `stylesheets` | CSS file URLs |
| `scripts` | JS file URLs |
| `googleFonts` | Google Fonts URLs |
| `fontCss` | Inline font preset rules |
| `wpHead` | Captured `wp_head()` output |
| `wpFooter` | Captured `wp_footer()` output |

## Production Deployment

1. Copy `.env.example` to `.env` on your server
2. Fill in production database credentials, site URL, and fresh salts
3. Set `WP_DEBUG=false`
4. Run `composer install --no-dev --optimize-autoloader`
5. Point your web server's docroot to `web/`

## Renaming Your Project

1. Edit `.ddev/config.yaml` — change `name:` to your project name
2. Edit `composer.json` — change `name:` to your package name
3. Run `ddev restart`

## Related Packages

| Package | Description |
|---|---|
| [wordpress-blocks](https://github.com/letsinnovate-io/wordpress-blocks) | The WordPress plugin |
| [block-templates](https://github.com/letsinnovate-io/block-templates) | Shared Twig templates, assets, and block definitions |
| [craft-blocks](https://github.com/letsinnovate-io/craft-blocks) | Craft CMS version of the same plugin |
