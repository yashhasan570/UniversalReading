---
title: Translate
weight: 7
---

## universalreading web application

### Translation files

Since universalreading is primarily developed by a French team, the French
translation is the most up-to-date. Use it as a reference to create
your own translation.

Translation files are available here:
https://github.com/universalreading/universalreading/tree/master/src/Universal Reading/CoreBundle/Resources/translations.

You need to create `messages.CODE.yml` and `validators.CODE.yml`, where
CODE is the ISO 639-1 code of your language ([see
Wikipedia](https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes)).

Other files to translate:

- https://github.com/universalreading/universalreading/tree/master/app/Resources/CraueConfigBundle/translations
- https://github.com/universalreading/universalreading/tree/master/src/Universal Reading/UserBundle/Resources/translations

You need to create `THE_TRANSLATION_FILE.CODE.yml` files.

### Configuration file

Edit the [app/config/universalreading.yml](https://github.com/universalreading/universalreading/blob/master/app/config/universalreading.yml)
file to display your language on the Configuration page of universalreading, allowing users to switch to the new translation.

In the `universalreading_core.languages` section, add a new line containing your
translation, as shown in this example:

```yaml
universalreading_core:
    ...
    languages:
        en: 'English'
        fr: 'Français'
```

For the first column (`en`, `fr`, etc.), add the ISO 639-1 code for your language.

For the second column, enter the name of your language in its native form.

## universalreading documentation

Unlike the web application, English is the primary language for documentation.

Documentation files are located at:
<https://github.com/universalreading/universalreading/tree/master/doc>

When creating your translation, maintain the same folder structure as the `en` directory.
