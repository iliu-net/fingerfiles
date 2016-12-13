#!/bin/bash
#
# Helper Script
#

phar="fingerfile.phar"
target="fingerfile.php"

mk() {
	[ -f "$phar" ] && rm -f "$phar"
	php mkphar.php "$phar" src
}

deploy() {
	m=()
	for k in FTP_USER FTP_PASSWD FTP_SITE FTP_DIR
	do
		eval 'v="$'"$k"'"'
		[ -n "$v" ] && continue
		m+=( $k )
	done
	if [ ${#m[@]} -gt 0 ] ; then
		echo "Missing ${m[*]}"
		exit 1
	fi
	mk
	curl -T "$phar" -u "$FTP_USER:$FTP_PASSWD" "ftp://$FTP_SITE/$FTP_DIR/$target"
}

check() {
	find src -name '*.php' -type f | (
		rv=0
		while read php
		do
			if ! ret="$(php -l "$php" 2>&1)" ; then
				echo "$php: $ret"
				rv=1
			fi
		done
		exit $rv
	)
	return $?
}

chkdpl() {
	check && deploy
}


"$@"
