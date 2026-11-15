---
title: Parameters
weight: 7
---

## Environment-first configuration

universalreading is configured with environment variables.

- For local development, put overrides in `.env.local`.
- For tests, put overrides in `.env.test.local`.
- For Docker-based local development, copy `docker/php/env.example` to
  `docker/php/env` for Docker-specific overrides.
- For production, export the variables through your web server, PHP-FPM pool,
  systemd unit, or container runtime.

Keep `DATABASE_URL` in `.env.local` and `.env.test.local` so development and
test can choose different databases, even when you run universalreading in Docker.

{{< callout type="info" >}}
Fresh installs should not create `app/config/parameters.yml`. Upgraded installs
may keep it temporarily: universalreading still reads that file for backward
compatibility, but booting with it emits a deprecation notice and support will
be removed in universalreading 3.0.
{{< /callout >}}

{{< callout type="info" >}}
After changing configuration in production, clear the cache with
`bin/console cache:clear --env=prod`.
{{< /callout >}}

## Symfony runtime variables

| Name | Description | Default |
| -----|-------------|---------|
| APP_ENV | Symfony environment | `dev` |
| APP_DEBUG | Symfony debug flag | `1` in dev, `0` in prod |
| APP_SECRET | Secret used for security-related operations | `ch4n63m31fy0uc4n` in `.env`, set your own value in production |

## Application variables

| Name | Description | Default |
| -----|-------------|---------|
| DEFAULT_LOCALE | Default language of your universalreading instance | `en` |
| UNIVERSALREADING_BASE_URL | Full URL of your universalreading instance without a trailing slash | `http://127.0.0.1:8000` |
| UNIVERSALREADING_REGISTRATION_ENABLED | Enable public registration | `0` |
| UNIVERSALREADING_CAPTCHA_ENABLED | Require an image CAPTCHA for HTML user creation | `0` |
| UNIVERSALREADING_CONFIRMATION_ENABLED | Send a confirmation email for each registration | `1` |
| UNIVERSALREADING_COMPROMISED_PASSWORD_CHECK_ENABLED | Enable compromised-password checks on user forms | `0` |
| UNIVERSALREADING_FROM_EMAIL | Address used in the `From:` field for application emails | `universalreading@example.com` |
| UNIVERSALREADING_SERVER_NAME | User-friendly name of your instance for 2FA issuer strings | `Your universalreading instance` |
| UNIVERSALREADING_TWOFACTOR_SENDER | Sender address for emailed 2FA codes | `no-reply@universalreading.org` |
| UNIVERSALREADING_LOG_LEVEL | Minimum level written by the nested production log handler | `error` |
| UNIVERSALREADING_USER_AGENT | Default User-Agent used when universalreading fetches content | `Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.92 Safari/535.2` |
| UNIVERSALREADING_SESSION_COOKIE_NAME | Name of the session cookie. Change it if several services share the same host and log each other out | `PHPSESSID` |
| UNIVERSALREADING_REMEMBER_ME_COOKIE_NAME | Name of the "remember me" cookie | `REMEMBERME` |
| UNIVERSALREADING_TRUSTED_DEVICE_COOKIE_NAME | Name of the two-factor trusted device cookie | `wllbg_trusted_computer` |
| UNIVERSALREADING_OAUTH_ACCESS_TOKEN_LIFETIME | OAuth access-token lifetime in seconds | `3600` |
| UNIVERSALREADING_OAUTH_REFRESH_TOKEN_LIFETIME | OAuth refresh-token lifetime in seconds | `1209600` |
| UNIVERSALREADING_TABLE_PREFIX | Prefix added to universalreading database tables | `universalreading_` |
| UNIVERSALREADING_SITE_CONFIG_FOLDERS | Optional comma-separated list of extra graby site-config folders | empty |
| UNIVERSALREADING_ARTICLE_REPORTING_URL | Destination used to report display problems with an article | `mailto:siteconfig@universalreading.org?subject=Wrong%20display%20in%20universalreading` |
| SENTRY_DSN | Optional Sentry DSN used to report application errors | empty |

### Compromised-password protection

Compromised-password checks are disabled by default. Set
`UNIVERSALREADING_COMPROMISED_PASSWORD_CHECK_ENABLED=1` to enable checks against
[Have I Been Pwned](https://haveibeenpwned.com/). Only a prefix of the password
hash is sent. Temporary service failures do not block validation.

The check covers public registration, administrator and API user creation,
password changes, and password resets through forms. The installation command
and the `fos:user:create` and `fos:user:change-password` console commands are
not covered.

Clear the production cache after changing this setting if needed.

### CAPTCHA protection for user creation

Set `UNIVERSALREADING_CAPTCHA_ENABLED=1` to require a CAPTCHA on public registration
and administrator user-creation forms. The default value, `0`, leaves both
forms unchanged. Clear the production cache after changing the value:

```console
bin/console cache:clear --env=prod
```

Each challenge is stored in the visitor's session. Loading a new image or
submitting the form invalidates the previous answer, including after an
incorrect submission. The challenge has no clock-based expiry; its lifetime is
defined by those refresh and submission events.

The CAPTCHA is image-only and has no audio alternative. For users who cannot
complete it, an administrator should create the account through the protected
administrator form. API user registration remains CAPTCHA-free and is outside
this feature's scope.

A CAPTCHA is only one abuse-prevention layer. It does not replace rate limits,
reverse-proxy controls, or a web application firewall.

### Production log verbosity

`UNIVERSALREADING_LOG_LEVEL` controls the minimum level written by the nested Monolog
handler in production. Monolog accepts `debug`, `info`, `notice`, `warning`,
`error`, `critical`, `alert`, and `emergency`.

The main `fingers_crossed` handler continues to activate only at `error`.
Lowering `UNIVERSALREADING_LOG_LEVEL` therefore adds buffered context when a request
ends in an error without logging successful requests. For example:

```dotenv
UNIVERSALREADING_LOG_LEVEL=debug
```

After changing the value, clear the production cache:

```console
bin/console cache:clear --env=prod
```

## Service variables

| Name | Description | Default |
| -----|-------------|---------|
| DATABASE_URL | Doctrine connection string for SQLite, MySQL/MariaDB, or PostgreSQL | `sqlite:///%kernel.project_dir%/data/db/universalreading.sqlite` |
| MAILER_DSN | Symfony Mailer DSN | `smtp://127.0.0.1` |
| REDIS_URL | Redis connection string used by the async import worker | `redis://127.0.0.1:6379` |
| RABBITMQ_URL | RabbitMQ connection string used by the async import worker | `amqp://guest:guest@127.0.0.1:5672` |
| UNIVERSALREADING_RABBITMQ_PREFETCH_COUNT | RabbitMQ consumer prefetch value | `10` |

## Redis password

If your Redis password contains characters that need URL encoding, pass it in
the `password` query parameter of `REDIS_URL`. Predis decodes this parameter,
but not an encoded password in `redis://:password@host`. For example, the
password `p@ss:word+&%` can be configured as:

```dotenv
REDIS_URL="redis://localhost:6379/0?password=p%40ss%3Aword%2B%26%25"
```

`REDIS_URL` only configures the asynchronous imports. For Redis-backed PHP
sessions, use a `tcp://` save path, as in `docker/php/env.example`.

## Article problem-reporting destination

`UNIVERSALREADING_ARTICLE_REPORTING_URL` accepts an absolute `https:` URL or a
`mailto:` URI with one recipient. An unset or empty value uses the default
address shown above. Invalid values stop the Symfony container from compiling.

Query names and values in the configured URL must use RFC 3986 percent
encoding. universalreading preserves configured query parameters, including a custom
`subject`, but always replaces `body` with the URL of the article being
reported. Fragments are preserved.

```dotenv
UNIVERSALREADING_ARTICLE_REPORTING_URL="https://support.example.com/issues/new?template=article"
UNIVERSALREADING_ARTICLE_REPORTING_URL="mailto:support@example.com?subject=Article%20display%20problem"
```

Because the value is validated and stored while building the container, clear
the application cache after changing it.

## Legacy mapping for upgraded installs

If an upgraded installation still has `app/config/parameters.yml`, universalreading maps
the legacy keys below to the new environment-variable interface at boot time.

| Legacy key | Environment variable |
| ---------- | -------------------- |
| `secret` | `APP_SECRET` |
| `locale` | `DEFAULT_LOCALE` |
| `domain_name` | `UNIVERSALREADING_BASE_URL` |
| `mailer_dsn` | `MAILER_DSN` |
| `fosuser_registration` | `UNIVERSALREADING_REGISTRATION_ENABLED` |
| `fosuser_confirmation` | `UNIVERSALREADING_CONFIRMATION_ENABLED` |
| `from_email` | `UNIVERSALREADING_FROM_EMAIL` |
| `server_name` | `UNIVERSALREADING_SERVER_NAME` |
| `twofactor_sender` | `UNIVERSALREADING_TWOFACTOR_SENDER` |
| `rabbitmq_prefetch_count` | `UNIVERSALREADING_RABBITMQ_PREFETCH_COUNT` |
| `database_*` | `DATABASE_URL` |
| `redis_*` | `REDIS_URL` |
| `rabbitmq_*` | `RABBITMQ_URL` |
| `sentry_dsn` | `SENTRY_DSN` |
| `universalreading_user_agent` | `UNIVERSALREADING_USER_AGENT` |
| `fos_oauth_server_access_token_lifetime` | `UNIVERSALREADING_OAUTH_ACCESS_TOKEN_LIFETIME` |
| `fos_oauth_server_refresh_token_lifetime` | `UNIVERSALREADING_OAUTH_REFRESH_TOKEN_LIFETIME` |
| `database_table_prefix` | `UNIVERSALREADING_TABLE_PREFIX` |
