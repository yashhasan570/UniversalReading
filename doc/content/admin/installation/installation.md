---
title: Installation
weight: 2
---

## On a dedicated web server (recommended way)

To install universalreading itself, you must run the following commands:

```bash
git clone https://github.com/universalreading/universalreading.git
cd universalreading && make install
```

If it's your first installation, you can safely answer "yes" when asking to reset the database.

Now, read the next step to create your virtual host, then
access your universalreading.

{{< callout type="info" >}}
universalreading is configured with environment variables. For local development, put
overrides in `.env.local`. For production, export the variables through your
web server, PHP-FPM, or container runtime. See
[Parameters]({{< relref "../parameters.md" >}}) for the full list.
{{< /callout >}}

{{< callout type="info" >}}
If you want to use SQLite to store your data, set
`DATABASE_URL=sqlite:///%kernel.project_dir%/data/db/universalreading.sqlite`.
{{< /callout >}}

{{< callout type="info" >}}
If you're installing universalreading behind Squid as a reverse proxy, make sure to update your `squid.conf` configuration to include `login=PASS` in the `cache_peer` line. This is necessary for API calls to work properly.
{{< /callout >}}

## On shared hosting

We provide a package with all dependencies inside. The default
configuration uses MySQL for the database. Configure the environment variables
documented on the [Parameters]({{< relref "../parameters.md" >}}) page before
running the installer. If you are upgrading an older deployment that still has
`app/config/parameters.yml`, universalreading will keep reading it with a deprecation
warning until universalreading 3.0.

We have already created a user: the login and password are `universalreading`.

With this package, universalreading doesn't check for mandatory extensions used
in the application (these checks are made during `composer install`
when you have a dedicated web server, see above).

Execute this command to download and extract the latest package:

```bash
wget https://wllbg.org/latest-v2-package && tar xvf latest-v2-package
```

You will find the [md5 hash of the latest package on our website](https://universalreading.org/#download).

The static package requires each command to be appended by `--env=prod` as the static package is only usable as a prod environment (dev environment is not supported and won't work at all).

Now, read the next step to create your virtual host.

You must create your first user by using the command `php bin/console universalreading:install --env=prod`
If an error occurs at this step due to bad settings, you must clear the cache with `php bin/console cache:clear --env=prod` before you try again the previous command.

Then you can access your universalreading.

## Usage of universalreading.it

[universalreading.it](https://universalreading.it) is a paid service to use universalreading without installing it on a web server.

This service always ships the latest release of universalreading. [You can create your account here](https://app.universalreading.it/). Try it for free: you'll get a 14-day free trial with no limitation (no credit card information required).

## Installation with Docker or Docker compose

### Command to launch container

This example starts universalreading at `http://localhost:8080` using SQLite backend and persists its data to Docker named volumes:

```bash
docker run \
  -v universalreading-data:/var/www/universalreading/data \
  -v universalreading-images:/var/www/universalreading/web/assets/images \
  -p 8080:80 \
  -e APP_SECRET=change-me \
  -e UNIVERSALREADING_BASE_URL=http://localhost:8080 \
  -e DATABASE_URL=sqlite:////var/www/universalreading/data/db/universalreading.sqlite \
  universalreading/universalreading
```

The default username and password are `universalreading:universalreading`. For more information, see [universalreading on Docker Hub](https://hub.docker.com/r/universalreading/universalreading/).

## Installation on Cloudron

Cloudron provides an easy way to install webapps on your server with a
focus on sysadmin automation and keeping apps updated. universalreading is
packaged as a Cloudron app and available to install directly from the
store.

[Install universalreading on your Cloudron](https://cloudron.io/store/org.universalreading.cloudronapp2.html)

## Installation on YunoHost

YunoHost provides an easy way to install webapps on your server with a
focus on sysadmin automation and keeping apps updated. universalreading is
packaged as an official YunoHost app and is available to install directly from the
official repository.

[![Install universalreading with YunoHost](https://install-app.yunohost.org/install-with-yunohost.png)](https://install-app.yunohost.org/?app=universalreading2)

## Installation on alwaysdata

alwaysdata's Marketplace allows to easily install universalreading (and many other
applications) on a Public or Private Cloud.

[Install universalreading on alwaysdata](https://www.alwaysdata.com/en/marketplace/universalreading/)

## Installation on Pethost

Pethost has a one-click template that runs the universalreading Docker image with
a persistent volume and HTTPS. It asks for a password and creates the
`universalreading` user with it, with sign-ups turned off.

[Install universalreading on Pethost](https://pethost.dev/templates/universalreading/)

## Installation on Hostim

Hostim has a one-click template that runs the universalreading Docker image with a
managed PostgreSQL database, a persistent volume for downloaded images, and
HTTPS. It generates the `universalreading` user's password on deploy and turns
sign-ups off.

[Install universalreading on Hostim](https://hostim.dev/docs/templates/universalreading/)

## Installation on ZopDay

ZopDay lets you deploy Docker-based applications to managed infrastructure or
to your own AWS/GCP account. The deploy link pre-fills the universalreading Docker
image (`universalreading/universalreading`) with port `80` already set.

[![Deploy to ZopDay](https://zop.dev/deploytozopday-inkhard.svg)](https://zop.dev/zopday/app/deploy?image=universalreading/universalreading&port=80&name=universalreading&utm_source=github&utm_medium=one_click&utm_content=universalreading)

## Installation on Simafri

Simafri has a step-by-step guide for running the official universalreading Docker
image on its managed VPS, with MariaDB, Redis and HTTPS through a reverse
proxy, from the first login to backups and upgrades. You keep root access to
the server, while Simafri handles its system updates, monitoring and
encrypted nightly backups kept off the machine.

[Install universalreading on Simafri](https://www.simafri.com/en/guides/self-host-universalreading/)

## Installation on Podway

Podway runs universalreading on its own small machine with a PostgreSQL 16
database, HTTPS, and an optional custom domain. It creates the
`universalreading` user with a generated password and turns sign-ups off.

[Install universalreading on Podway](https://podway.io/start?app=universalreading&ref=docs-universalreading)

## Installation on Synology

The SynoCommunity provides a package to install universalreading on your Synology NAS.

[Install universalreading with Synology](https://synocommunity.com/package/universalreading)
