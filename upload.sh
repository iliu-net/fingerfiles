#!/bin/sh
#
# Sample upload script
#
m=()
for k in POST_KEY POST_OBJ POST_FILE POST_URL
do
  eval 'v="$'"$k"'"'
  [ -n "$v" ] && continue
  m+=( $k )
done
if [ ${#m[@]} -gt 0 ] ; then
  echo "Missing ${m[*]}"
  exit 1
fi

[ -z "$algo" ] && algo='sha256sum'

echo "Signing..."
sig="$((
  echo "$POST_KEY"
  cat "$POST_FILE"
) | $algo | cut -d' ' -f1)"

echo "Uploading..."
curl \
	--upload-file "$POST_FILE" \
	"$POST_URL/repo/$POST_OBJ/$sig"
