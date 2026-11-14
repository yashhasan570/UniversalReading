# Universal Reading

Self-hosted read-it-later web application: save articles, classify and tag them, read them anytime in a clean reader view, on the web or from any compatible client over the REST API.

## Features

- Save any web article for later reading, with automatic content extraction
- Tags, filters, search, starred entries
- Export: EPUB, PDF, CSV, JSON, XML, HTML
- Multi-user support with 2FA
- REST API for third-party clients
- Android, iOS and browser client compatibility

## Install

- `cp app/config/parameters.yml.dist app/config/parameters.yml` and edit
- `composer install` then `php bin/console universalreading:install`
- Or use the provided `compose.yaml` (builds the PHP container from source, redis included)

## Contact

Telegram: [@johnconstantine570](https://t.me/johnconstantine570)
