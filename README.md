# fingerfiles

Simple filetransfer web app.

This web is meant to be used by scripts to upload files to a
trusted repository.

Allows for uploaded files to be authenticated.

## Usage

Use the `helper.sh` script to create a `phar` file:

    bash helper.sh mk

Copy the resulting `fingerfile.phar` to your web server.  You may
need to rename the extension from `phar` to `php`.

Browse to where the `phar` file was located.  The first run you
will be asked to create an `admin` user.

To use, follow the `manage` link and create a new _object_.  This will
create an URL a random string to be used for authenticating uploads.

Feed this to the `upload.sh` script.

## Upload script

Example:

    env \
			POST_KEY=....random...bytes.... \
			POST_OBJ='sample.apk' \
			POST_FILE='local-sample-file.apk' \
			POST_URL='https://localhost/path-to/filefinger.php' \
			upload.sh

## Notes

This script was inspired by [transfer.sh](https://transfer.sh/), an
easy file sharing from the command line utility.  Which functionality
is great, unfortunately it is flagged by [google](https://google.com/)
as a malicious software site.


