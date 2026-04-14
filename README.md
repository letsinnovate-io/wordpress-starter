# WordPress Blocks — Starter Project

A ready-to-go WordPress project configured with the [WordPress Blocks](https://github.com/letsinnovate-io/wordpress-blocks) plugin for animated page blocks.

## Requirements

- [DDEV](https://ddev.readthedocs.io/) (handles PHP, MySQL, Composer, and WordPress for you)
- [Git Tower](https://www.git-tower.com/) (or any Git client)
- Access to the `letsinnovate-io` GitHub organization

## Starting a New Client Site

### Step 1 — One-time setup (only do this once per machine)

Get the GitHub PAT from **1Password** → **Shared Vault** → look for **"GitHub Composer PAT"**.

Then open a terminal and run:

```bash
ddev config global
ddev exec composer config --global github-oauth.github.com PASTE_TOKEN_HERE
```

> **Note:** This sets the token inside DDEV's global Composer config, not your host machine. It persists across all DDEV projects on this machine.

If you haven't used DDEV before, you can set this on any existing DDEV project. Just `cd` into any running DDEV project and run the `ddev exec composer config ...` command.

### Step 2 — Create the repo

1. Go to [github.com/organizations/letsinnovate-io/repositories/new](https://github.com/organizations/letsinnovate-io/repositories/new)
2. Name it for the client (e.g. `acme-website`)
3. Set it to **Private**
4. **Do not** initialize with a README, .gitignore, or license
5. Click **Create repository**

### Step 3 — Clone it

Open **Git Tower** → **Clone** → paste the repo URL → clone to your machine.

Then open a terminal and `cd` into the cloned directory:

```bash
cd ~/path/to/acme-website
```

### Step 4 — Scaffold the project

Run these commands one at a time:

```bash
# 1. Tell DDEV this is a WordPress project
ddev config --project-type=wordpress --docroot=web --project-name=acme-website

# 2. Start the containers
ddev start

# 3. Scaffold from the starter template (this downloads everything)
ddev composer create letsinnovate-io/wordpress-starter \
  --stability=dev --no-interaction \
  --repository='{"type":"vcs","url":"https://github.com/letsinnovate-io/wordpress-starter.git"}'
```

When this finishes you'll see `ddev composer create-project was successful.`

### Step 5 — Restart to finish setup

The starter includes its own `.ddev/config.yaml` with setup hooks. You need to restart so those hooks run:

```bash
# The project name changed (the starter has its own name), so unlist the old one
ddev stop --unlist acme-website

# Restart — this downloads WordPress, installs the database,
# activates the plugin, and installs + activates the theme
ddev start
```

You'll see output like:

```
Success: WordPress downloaded.
Success: WordPress installed successfully.
Plugin 'wordpress-blocks' activated.
Switched to 'Craft Blocks' theme.
```

### Step 6 — Rename for the client

Edit two files:

1. **`.ddev/config.yaml`** — change `name: my-wp-blocks-site` to `name: acme-website`
2. **`composer.json`** — change `"name": "letsinnovate-io/wordpress-starter"` to `"name": "letsinnovate-io/acme-website"`

Then restart to pick up the new name:

```bash
ddev stop --unlist my-wp-blocks-site
ddev start
```

### Step 7 — Set up project auth

```bash
cp auth.json.example auth.json
```

Open `auth.json` and paste the same GitHub PAT from 1Password (replacing `YOUR_FINE_GRAINED_PAT_HERE`).

### Step 8 — Commit and push

In **Git Tower**: stage all files, commit with message `"Initial project from wordpress-starter"`, push to origin.

### Step 9 — Start building

```
https://acme-website.ddev.site          ← frontend
https://acme-website.ddev.site/wp-admin ← admin (admin / admin)
```

Add blocks in the WordPress editor, edit Twig layouts in `web/wp-content/themes/craft-blocks/twig/layouts/`, and build the site.

---

## Quick Start (testing the starter without a client repo)

```bash
mkdir my-test && cd my-test
git init
ddev config --project-type=wordpress --docroot=web --project-name=my-test
ddev start
ddev composer create letsinnovate-io/wordpress-starter \
  --stability=dev --no-interaction \
  --repository='{"type":"vcs","url":"https://github.com/letsinnovate-io/wordpress-starter.git"}'

# Restart so the setup hooks run
ddev stop --unlist my-test
ddev start

# Visit: check the URL in the ddev start output
# Admin: add /wp-admin (admin / admin)
```

---

## Private Repo Authentication

This project depends on private GitHub packages (`wordpress-blocks` and `block-templates`). Composer needs a fine-grained GitHub Personal Access Token to download them.

### How it works

- **Global DDEV auth** (Step 1 above) — lets `ddev composer create` and `ddev composer install` access private repos from inside the DDEV container
- **Project `auth.json`** (Step 7 above) — lets `composer install` work in production or CI environments outside DDEV

### Creating a new PAT

If the token in 1Password has expired, generate a new one:

1. Go to [github.com/settings/tokens](https://github.com/settings/tokens?type=beta)
2. Create a **fine-grained** token scoped to the `letsinnovate-io` organization
3. Grant **Repository access** to:
   - `letsinnovate-io/block-templates`
   - `letsinnovate-io/wordpress-blocks`
   - `letsinnovate-io/staticsite-php-blocks`
4. Under **Permissions → Repository permissions**, set **Contents** to **Read and write**
5. Save the token and update it in the **1Password shared vault** so the team has it

> **Why "Read and write"?** Composer uses `git clone --mirror` to fetch private packages, which GitHub considers a write operation even though no data is actually written to the repo.

---

## What Happens Automatically

On `ddev start`, the DDEV hooks will:

1. Download WordPress core into `web/` (if not already present)
2. Run `wp core install` to set up the database (if not already installed)
3. Activate the WordPress Blocks plugin
4. Install the Craft Blocks theme (copied from the plugin's bundled `theme/` directory)
5. Activate the Craft Blocks theme

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
├── auth.json               # Composer GitHub auth (gitignored)
├── auth.json.example       # Auth template (committed)
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
4. Copy `auth.json.example` to `auth.json` and add the GitHub PAT
5. Run `composer install --no-dev --optimize-autoloader`
6. Point your web server's docroot to `web/`

## Troubleshooting

### "Could not find package" during `ddev composer create`

You forgot the `--repository` flag. Copy the full command from Step 4 above.

### "Write access to repository not granted" during `composer install`

The GitHub PAT only has **read** access on Contents. It needs **Read and write**. See "Creating a new PAT" above.

### Site shows "Error establishing a database connection"

The `.env` file has the wrong `DB_PREFIX`. Check what prefix your tables use:

```bash
ddev exec mysql -u db -pdb db -e "SHOW TABLES" | head -5
```

Update `DB_PREFIX` in `.env` to match (usually `wp_`).

### Plugin or theme not active after `ddev start`

Run the hooks manually:

```bash
ddev exec wp plugin activate wordpress-blocks --path=/var/www/html/web
ddev exec wp theme activate craft-blocks --path=/var/www/html/web
```

## Related Packages

| Package | Description |
|---|---|
| [wordpress-blocks](https://github.com/letsinnovate-io/wordpress-blocks) | The WordPress plugin |
| [block-templates](https://github.com/letsinnovate-io/block-templates) | Shared Twig templates, assets, and block definitions |
| [craft-blocks](https://github.com/letsinnovate-io/craft-blocks) | Craft CMS version of the same plugin |
