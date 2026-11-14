---
title: Console Commands
weight: 5
---

universalreading has a number of CLI commands to manage various tasks. You
can list all the commands by executing `bin/console` in the universalreading
folder.

Each command has help accessible through `bin/console help %command%`.

> If you're in a production environment, remember to add `--env=prod` to each
command.

Notable commands
----------------

From Symfony:

 - `assets:install`: May be helpful if assets are missing.
 - `cache:clear`: Should be run after each update (included in make update).
 - `doctrine:migrations:status`: Outputs the status of your database migrations.
 - `fos:user:activate`: Manually activates a user.
 - `fos:user:change-password`: Changes a password for a user.
 - `fos:user:create`: Creates a user.
 - `fos:user:deactivate`: Deactivates a user (not deleted).
 - `fos:user:demote`: Removes a role from a user, typically admin rights.
 - `fos:user:promote`: Adds a role to a user, typically admin rights.
 - `rabbitmq:*`: May be useful if you're using RabbitMQ.

Custom to universalreading:

 - `universalreading:api-client:create`: Creates an OAuth API client for an existing user.
 - `universalreading:clean-downloaded-images`: Cleans downloaded images which are no longer associated with an entry.
 - `universalreading:clean-duplicates`: Removes all entry duplicates for one user or all users.
 - `universalreading:entry:reload`: Reloads entries.
 - `universalreading:export`: Exports all entries for a user. You can choose the output path of the file.
 - `universalreading:generate-hashed-urls`: Generates hashed URLs for each entry.
 - `universalreading:import`: Imports entries in different formats to a user account.
 - `universalreading:import:redis-worker`: Useful if you use Redis.
 - `universalreading:install`: (re)Installs universalreading.
 - `universalreading:tag:all`: Tags all entries for a user using their tagging rules.
 - `universalreading:user:show`: Shows the details for a user.
 - `universalreading:user:list`: Lists all existing users.

universalreading:api-client:create
--------------------------

This command creates an OAuth API client for an existing user. Useful for IaC
pipelines, credential rotation, or provisioning a second client without
recreating the user.

Usage:

```
universalreading:api-client:create [--display-name DISPLAY-NAME] [--grant-types GRANT-TYPES] [--format FORMAT] <username>
```

Arguments:

 - username: Owner of the client

Options:

 - `--display-name=DISPLAY-NAME`: Display name shown in /developer [default: "Default client"]
 - `--grant-types=GRANT-TYPES`: Comma-separated list of allowed grant types: token, authorization_code, password, refresh_token [default: all four]
 - `--format=FORMAT`: Output format: `text`, `env`, or `json` [default: "text"]

Examples:

```
# Human-readable output
bin/console universalreading:api-client:create alice

# Machine-readable for CI / provisioning
bin/console universalreading:api-client:create alice --display-name="ansible-bootstrap" --format=env

# JSON output
bin/console universalreading:api-client:create alice --format=json

# Restrict grant types
bin/console universalreading:api-client:create alice --grant-types=password,refresh_token
```


universalreading:clean-downloaded-images
-------------------------

This command cleans downloaded images which are no more associated to an entry. This is useful if you enabled "Download images locally" before 2.4.0 because there were a bug in removing images from an entry when you removed that entry.

Usage:

```
universalreading:clean-downloaded-images
```

Options:
 - `--dry-run`: Don't remove images, just dump number of images which might be removed


universalreading:clean-duplicates
-------------------------

This command helps you clean your articles list in case of duplicates.

Usage:

```
universalreading:clean-duplicates [<username>]
```

Arguments:

 - username: User to clean


universalreading:entry:reload
---------------------

This command reloads entries.

Usage:

```
universalreading:entry:reload [<username>]
```

Arguments:
 - username: Reload entries only for the given user.


universalreading:export
---------------

This command helps you export all entries for a user.

Usage:

```
universalreading:export <username> [<filepath>]
```

Arguments:

 - username: User from which to export entries
 - filepath: Path of the exported file


universalreading:generate-hashed-urls
---------------

This command helps you generate hashes of the URL of each entry to check through the API if a URL is already saved. Only available since 2.4.0.

Usage:

```
universalreading:generate-hashed-urls <username>
```

Arguments:

 - username: User to process entries


universalreading:import
---------------

This command helps you import entries from a JSON export.

Usage:

```
universalreading:import [--] <username> <filepath>
```

Arguments:

 - username: User to populate
 - filepath: Path to the JSON file

Options:

 - `--importer=IMPORTER`: The importer to use: v1, v2, instapaper, pinboard, readability, firefox or chrome [default: "v1"]
 - `--markAsRead=MARKASREAD`: Mark all entries as read [default: false]
 - `--useUserId`: Use user id instead of username to find account
 - `--disableContentUpdate`: Disable fetching updated content from URL


universalreading:import:redis-worker
----------------------------

This command helps you launch Redis worker.

Usage:

```
universalreading:import:redis-worker [--] <serviceName>
```

Arguments:

 - serviceName: Service to use: universalreading_v1, universalreading_v2, pocket, readability, pinboard, firefox, chrome or instapaper

Options:

 - `--maxIterations[=MAXITERATIONS]`: Number of iterations before stopping [default: false]


universalreading:install
----------------

This command helps you install or re-install universalreading.

Usage:

```
universalreading:install
```


universalreading:tag:all
----------------

This command helps you tag all entries using tagging rules.

Usage:

```
universalreading:tag:all <username>
```

Arguments:
 - username: User to tag entries for.


universalreading:user:show
------------------

This command shows the details for a user.

Usage:

```
universalreading:user:show <username>
```

Arguments:
 - username: User to show details for.


universalreading:user:list
------------------

This command lists all existing users.

Usage:

```
universalreading:user:list [<search>]
```

Arguments:
 - search: Filter the list with the given search term. The search is done on users' username, name, and email

Options:
 - `--limit=LIMIT`: Max number of users displayed in the list
