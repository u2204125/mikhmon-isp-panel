
# Mikhmon-based ISP Management Panel (v3)

Lightweight web-based ISP/hotspot management interface built around the Mikhmon components included in this repository.

## Overview

- Purpose: Manage hotspot users, vouchers, DHCP leases, traffic, and basic router operations via a web UI.
- This repository bundles components developed by multiple authors; some parts are third-party open-source. See the `LICENSE` and `LICENSE.txt` files in the repo root and the `mikhmon/` folder for upstream licensing and attribution.

## Quick links

- Project folder: root contains `mikhmon/`, `dashboard/`, `hotspot/`, `include/`, `js/`, `css/`, and other modules.

## Suggested usage

- For development: run under a local webserver (Apache/Nginx + PHP). Place the project directory under your web server's document root or configure a virtual host.
- For production: secure configuration files, restrict access to admin endpoints, and follow best practices for PHP deployments.

## Requirements

- PHP 7.4+ (or PHP 8); common PHP extensions: `curl`, `mbstring`, `json`, `pdo` if DB usage is added.
- Web server: Apache or Nginx.
- (Optional) Composer / npm if you choose to add dependency management or build steps.

## Installation (local quick start)

1. Clone this repository:
```bash
	git clone https://github.com/<your-username>/<your-repo>.git
```

2. Place the files in your web root or configure a virtual host pointing to the cloned directory.
3. Copy or update configuration files (example: `include/config.php`, `include/readcfg.php`) with your environment settings (router credentials, base URL, etc.).
4. Ensure writable permissions on any directories that need uploads or runtime caches (if present).
5. Open the site in your browser and follow the UI to configure devices and users.

## Configuration

- The main configuration files are in `include/` — adapt them to your environment before first run.
- Keep all secret credentials out of version control. Use environment variables or a local-only config that is listed in `.gitignore`.

## Credits & Licenses

- This project includes third-party components. Keep the original `LICENSE` and `LICENSE.txt` files intact.
- Check `mikhmon/` and `lib/` folders for third-party code and attribution. If you redistribute, follow the upstream licenses.
