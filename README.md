Generic Object Storage Volume for Craft CMS
==============================================

This plugin provides a S3 (Object) storage integration for [Craft CMS](https://craftcms.com/).

- Supports path style S3 and subdomain style S3.
- Visibility can also be set independently per filesystem.

## Requirements
- PHP 8.0
- Craft 4.0

## Installation

To install the plugin.


### Install the plugin via composer

```
composer require behinddesign/craft-object-storage
```

### Update your local .env file (Optional, set these during configuration of Filesystem)
```
OBJECT_STORAGE_BUCKET="(YOUR_APP_NAME)"
OBJECT_STORAGE_HOST="(YOUR_APP_NAME_IF_USING_SUBDOMAIN).my.s3.host.com"
OBJECT_STORAGE_HOST_TYPE="subdomain"
OBJECT_STORAGE_MAKE_UPLOADS_PUBLIC="true"
OBJECT_STORAGE_KEY="(YOUR_APP_NAME)"
OBJECT_STORAGE_REGION="eu-west-1"
OBJECT_STORAGE_SECRET="(OBJECT_STORAGE_SECRET)"
OBJECT_STORAGE_SERVER="Your S3 Host"
```

### Install the plugin
```
php craft plugin/install behinddesign-cract-object-storage
```

Or browse to  CP > Settings > Plugins to enable the plugin.


### Configure

Within Settings -> Filesystems -> Add / Edit a filesystem

The dropdown for 'Filesystem Type' will now contain 'Generic S3' as an option.

## Thanks!
[fortrabbit](https://github.com/fortrabbit/craft-object-storage) for providing the base
code for this repository. Changes based off of their code made setting this up very easy.
