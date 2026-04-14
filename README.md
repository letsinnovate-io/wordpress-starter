# WordPress Blocks — Starter Project

A ready-to-go WordPress project configured with the [WordPress Blocks](https://github.com/letsinnovate-io/wordpress-blocks) plugin for animated page blocks.

## Requirements

- PHP 8.2+
- [Composer](https://getcomposer.org/)
- [DDEV](https://ddev.readthedocs.io/) (for local development)
- A [GitHub Personal Access Token](https://github.com/settings/tokens) with access to the private repos

## Starting a New Client Site

### 1. One-time setup (only once per machine)

Configure Composer with the GitHub PAT so it can access our private repos. Get the token from **1Password** → **Shared Vault** → **"GitHub Composer PAT"**.

```bash
composer config --global github-oauth.github.com PASTE_TOKEN_HERE
```

This stores the token in `~/.composer/auth.json` and DDEV passes it through automatically.

### 2. Create the repo and clone it

1. Go to [github.com/organizations/letsinnovate-io/repositories/new](https://github.com/organizations/letsinnovate-io/repositories/new)
2. Create a new **private** repo (e.g. `acme-website`) — **do not** initialize with a README
3. Open **Git Tower** → Clone the new repo to your local machine
4. `cd` into the cloned directory in your terminal

### 3. Scaffold from the starter

```bash
# Configure DDEV for this project
ddev config --project-type=wordpress --docroot=web --project-name=acme-website

# Scaffold the project from the starter (downloads all files + dependencies)
ddev start
ddev composer create letsinnovate-io/wordpress-starter --stability=dev --no-interaction
```

This pulls the entire starter project into your repo: `composer.json`, `wp-config.php`, `.env.example`, `.ddev/` config, `.gitignore`, and all dependencies.

### 4. Customize

```bash
# Copy the auth template for this project's future composer installs
cp auth.json.example auth.json
# Paste the same GitHub PAT from 1Password into auth.json
```

Edit these files for the client:
- **`.ddev/config.yaml`** → `name:` is already set from step 3
- **`composer.json`** → change `"name"` to `"letsinnovate-io/acme-website"`

### 5. Commit and push

In **Git Tower**: stage all files, commit with message `"Initial project from wordpress-starter"`, push to origin.

### 6. Start building

```
https://acme-website.ddev.site          ← frontend
https://acme-website.ddev.site/wp-admin ← admin (admin / admin)
```

Add blocks in the WordPress editor, edit Twig layouts in `web/wp-content/themes/craft-blocks/twig/layouts/`, and build the site.

---

## Quick Start (testing the starter directly)

If you just want to try the starter without creating a client repo:

```bash
mkdir my-test && cd my-test
ddev config --project-type=wordpress --docroot=web --project-name=my-test
ddev start
ddev composer create letsinnovate-io/wordpress-starter --stability=dev --no-interaction
# Visit: https://my-test.ddev.site
# Admin: https://my-test.ddev.site/wp-admin (admin / admin)
```

## Private Repo Authentication

This project depends on private GitHub packages. Composer needs a fine-grained GitHub Personal Access Token to download them.

### Setup

1. Open **1Password** → **Shared Vault** → look for **"GitHub Composer PAT"**
2. Copy the token value
3. In your project root:
   ```bash
   cp auth.json.example auth.json
   ```
4. Open `auth.json` and replace `YOUR_FINE_GRAINED_PAT_HERE` with the token from 1Password

> **Never commit `auth.json`** — it is gitignored. The token in 1Password is a shared fine-grained PAT scoped to the `letsinnovate-io` org repos. If you need to generate a new one, go to [github.com/settings/tokens](https://github.com/settings/tokens?type=beta) and create a fine-grained token with **read access** to:
> - `letsinnovate-io/block-templates`
> - `letsinnovate-io/wordpress-blocks`
> - `letsinnovate-io/staticsite-php-blocks`
>
> Then update the token in the 1Password shared vault so the rest of the team has it.

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
