#!/bin/bash
# Blackbox HTTP test runner
set -e
BASE="https://absensippsu.myftp.biz"
TMPD=$(mktemp -d)
echo "=== UNAUTHENTICATED ==="
for path in "/" "/dashboard" "/admin/users" "/petugas/absensi" "/atasan/absensi"; do
  code=$(curl -k -s -o /dev/null -w '%{http_code}' "$BASE$path" 2>/dev/null || echo "ERR")
  echo "GET $path => $code"
done

get_token() { sed -n 's/.*name="_token" value="\([^"]*\)".*/\1/p' "$1" | head -1; }

do_login() {
  local label="$1" login="$2" pass="$3"
  shift 3
  local jar="$TMPD/${label}_jar"
  local html="$TMPD/${label}_login.html"
  curl -k -s -c "$jar" "$BASE/login" > "$html"
  local tok=$(get_token "$html")
  curl -k -s -o /dev/null -w "LOGIN_POST=>%{http_code}|%{redirect_url}" -b "$jar" -c "$jar" \
    -X POST "$BASE/login" --data-urlencode "_token=$tok" \
    --data-urlencode "login=$login" --data-urlencode "password=$pass"
  echo ""
  for path in "$@"; do
    code=$(curl -k -s -o /dev/null -w '%{http_code}' -b "$jar" "$BASE$path")
    echo "  GET $path => $code"
  done
}

echo "=== ADMIN ==="
do_login "admin" "admin@local.test" "Admin12345" "/dashboard" "/admin/users" "/admin/pengaturan" "/petugas/absensi" "/atasan/absensi"

echo "=== PETUGAS EMAIL ==="
do_login "petugas" "iwasya22@gmail.com" "iwasya22" "/dashboard" "/petugas/absensi" "/petugas/tugas/input" "/admin/users" "/atasan/absensi"

echo "=== PETUGAS USERNAME ==="
do_login "petugas_user" "iwasya22" "iwasya22" "/dashboard"

echo "=== ATASAN ==="
do_login "atasan" "atasan@local.test" "Atasan12345" "/dashboard" "/atasan/absensi" "/atasan/tugas" "/admin/users" "/petugas/absensi"

echo "=== INVALID ==="
do_login "invalid" "invalid@test.com" "wrongpass" "/dashboard"

rm -rf "$TMPD"
echo "=== DONE ==="
