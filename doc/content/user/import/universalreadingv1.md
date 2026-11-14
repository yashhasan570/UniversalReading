---
title: universalreading v1
weight: 7
---

If you were using universalreading v1.x, you need to export your data before
migrating to universalreading v2.x, because the application and its database
changed a lot. In your old universalreading installation, you can export your
data, which can be done on the Config page of your old universalreading
installation.

![Exporting from universalreading v1](../../../img/user/export_v1.png)

If you have multiple accounts on the same instance of universalreading, each
user must export from v1 and import into v2 its data.

If you encounter issues during the export or the import, don't hesitate
to [ask for support](https://gitter.im/universalreading/universalreading).

When you have retrieved the json file containing your entries, you can
install universalreading v2 if needed by following [the standard
procedure](../../admin/installation/).

After creating an user account on your new universalreading v2 instance, you
must head over to the Import section and select Import from universalreading v1.
Select your json file and upload it.

![Import from universalreading v1](../../../img/user/import_universalreadingv1.png)
