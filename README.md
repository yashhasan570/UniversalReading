# universalreading

> [!IMPORTANT]
> The name of this project is **universalreading** — all lowercase, always. Not "Universal Reading", not "WallaBag", not any other variation. Just plain, humble, all-lowercase **universalreading** 😉

[![CI](https://github.com/universalreading/universalreading/actions/workflows/continuous-integration.yml/badge.svg?branch=master)](https://github.com/universalreading/universalreading/actions/workflows/continuous-integration.yml?query=branch%3Amaster)
[![Matrix](https://matrix.to/img/matrix-badge.svg)](https://matrix.to/#/#universalreading:matrix.org)
[![Translation status](https://hosted.weblate.org/widgets/universalreading/-/svg-badge.svg)](https://hosted.weblate.org/engage/universalreading/?utm_source=widget)
![License](https://img.shields.io/github/license/universalreading/universalreading)

universalreading is a web application allowing you to save web pages for later reading.
Click, save and read it when you want. It extracts content so that you won't be distracted by pop-ups and cie.

You can install it on your own server, or you can create an account on [universalreading.it](https://universalreading.it).

![universalreading](./.github/images/screenshot.png)

* Website: [universalreading.org](https://universalreading.org)
* Android app: [universalreading/android-app](https://github.com/universalreading/android-app)
* iOS app: [universalreading/ios-app](https://github.com/universalreading/ios-app)
* Browser extension: [universalreading/universalreadingger](https://github.com/universalreading/universalreadingger)
* GNOME (Linux) app: [read-it-later](https://gitlab.gnome.org/World/read-it-later) (not maintained by this project)
* All resources about universalreading ecosystem are listed here: https://github.com/universalreading/universalreading/wiki/universalreading-ecosystem

## Documentation

The documentation is available at https://doc.universalreading.org.

The source files live in the [`doc/`](doc/) directory of this repository.

## Installation

Please read [the documentation to see the universalreading requirements](https://doc.universalreading.org/en/admin/installation/requirements.html).

Then you can install universalreading by executing the following commands:

```bash
git clone https://github.com/universalreading/universalreading.git
cd universalreading && make install
```

If your system's default `make` is not GNU make, use `gmake install` instead.

Now, [configure a virtual host](https://doc.universalreading.org/en/admin/installation/virtualhosts.html) to use your universalreading.

### Other methods

Refer to the [installation documentation](https://doc.universalreading.org/en/admin/installation/installation.html) for other installation methods.

## Translation

This project uses [Weblate](https://weblate.org/) for translation.

Feel free to help us [translating universalreading](https://hosted.weblate.org/projects/universalreading/).

## Contributing

To learn more about developing universalreading, please refer to the [contribution guide](./.github/CONTRIBUTING.md).

Content extraction relies on [Graby](https://github.com/j0k3r/graby), [php-readability](https://github.com/j0k3r/php-readability) and [ftr-site-config](https://github.com/fivefilters/ftr-site-config).

## License
Copyright © 2013-current Nicolas Lœuillet <nicolas@loeuillet.org>

This work is free. You can redistribute it and/or modify it under the
terms of the MIT License. See the [COPYING.md](./COPYING.md) file for more details.

## Contact

Questions, problem reports or feedback? Reach us on Telegram: [@johnconstantine570](https://t.me/johnconstantine570)
