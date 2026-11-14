---
title: Right Access
weight: 4
---

### Test environment

When we just want to test universalreading, we just run the command `make run`
to start our universalreading instance and everything will go smoothly because
the user who started the project can access the current folder
naturally, without any problem.

### Production environment

As soon as we use Apache or Nginx to access our universalreading instance,
and not the command `make run` to start it, we should take care to
grant the correct rights on the correct folders to keep all the folders
of the project safe.

To do so, the folder name, known as `DocumentRoot` (for Apache) or
`root` (for Nginx), has to be absolutely accessible by the Apache/Nginx
user. Its name is generally `www-data`, `apache`, or `nobody` (depending
on the Linux system used).

So the folder `/var/www/universalreading/web` has to be accessible by this last
one. But this may not be enough if we just care about this folder,
because we could meet a blank page or get an error 500 when trying to
access the homepage of the project.

This is due to the fact that we will need to grant the same rights
access on the folder `/var/www/universalreading/var` like those we gave on the
folder `/var/www/universalreading/web`. Thus, we fix this problem with the
following command:

```bash
chown -R www-data:www-data /var/www/universalreading/var
```

It has to be the same for the following folders

-   /var/www/universalreading/bin/
-   /var/www/universalreading/vendor/
-   /var/www/universalreading/data/
-   /var/www/universalreading/web/

by entering

```bash
chown -R www-data:www-data /var/www/universalreading/bin
chown -R www-data:www-data /var/www/universalreading/vendor
chown -R www-data:www-data /var/www/universalreading/data/
chown -R www-data:www-data /var/www/universalreading/web/
```

otherwise, sooner or later you will see these error messages:

```
Unable to write to the "bin" directory.
file_put_contents(/.../universalreading/vendor/autoload.php): failed to open stream: Permission denied
```

### Additional rules for SELinux

If SELinux is enabled on your system, you will need to configure
additional contexts in order for universalreading to function properly. To check
if SELinux is enabled, simply enter the following:

`getenforce`

This will return `Enforcing` if SELinux is enabled. Creating a new
context involves the following syntax:

`semanage fcontext -a -t <context type> <full path>`

For example:

`semanage fcontext -a -t httpd_sys_content_t "/var/www/universalreading(/.*)?"`

This will recursively apply the httpd_sys_content_t context to the
universalreading directory and all underlying files and folders. The following
rules are needed:

| Full path  | Context |
| ------------- | ------------- |
| /var/www/universalreading(/.\*)?  | `httpd_sys_content_t`  |
| /var/www/universalreading/data(/.\*)?  | `httpd_sys_rw_content_t`  |
| /var/www/universalreading/var/logs(/.\*)?  | `httpd_log_t`  |
| /var/www/universalreading/var/cache(/.\*)?  | `httpd_cache_t`  |

After creating these contexts, enter the following in order to apply
your rules:

`restorecon -R -v /var/www/universalreading`

You can check contexts in a directory by typing `ls -lZ` and you can see
all of your current rules with `semanage fcontext -l -C`.

If you're installing the preconfigured latest-v2-package, then an
additional rule is needed during the initial setup:

`semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/universalreading/var"`

After you successfully access your universalreading and complete the initial
setup, this context can be removed:

```bash
    semanage fcontext -d -t httpd_sys_rw_content_t "/var/www/universalreading/var"
    retorecon -R -v /var/www/universalreading/var
```
