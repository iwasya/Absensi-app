#!/usr/bin/env bash
set -u

BASE_URL="${STAGING_BASE_URL:-https://absensippsu.myftp.biz}"
TMP_DIR="$(mktemp -d)"

cleanup() {
  rm -rf "$TMP_DIR"
}
trap cleanup EXIT

require_env() {
  local missing=0
  for key in "$@"; do
    if [ -z "${!key:-}" ]; then
      echo "MISSING|$key"
      missing=1
    fi
  done
  return "$missing"
}

extract_token() {
  sed -n 's/.*name="_token" value="\([^"]*\)".*/\1/p' "$1" | head -n 1
}

http_code() {
  local jar="$1"
  local path="$2"
  curl -k -s -o /dev/null -w '%{http_code}' -b "$jar" "$BASE_URL$path"
}

login_role() {
  local role="$1"
  local login="$2"
  local password="$3"
  local jar="$TMP_DIR/$role.cookies"
  local login_html="$TMP_DIR/$role-login.html"
  local after_html="$TMP_DIR/$role-after.html"

  curl -k -s -c "$jar" "$BASE_URL/login" > "$login_html"
  local token
  token="$(extract_token "$login_html")"

  if [ -z "$token" ]; then
    echo "$role|LOGIN|/login|token missing|FAIL"
    return 1
  fi

  local post_result
  post_result="$(curl -k -s -o "$after_html" -w '%{http_code}|%{redirect_url}' -b "$jar" -c "$jar" \
    -X POST "$BASE_URL/login" \
    --data-urlencode "_token=$token" \
    --data-urlencode "login=$login" \
    --data-urlencode "password=$password")"

  local dashboard_code
  dashboard_code="$(http_code "$jar" "/dashboard")"

  if [ "$dashboard_code" != "200" ]; then
    echo "$role|LOGIN|/login|$post_result dashboard=$dashboard_code|FAIL"
    return 1
  fi

  echo "$role|LOGIN|/login|$post_result dashboard=$dashboard_code|PASS"

  case "$role" in
    petugas)
      check_allowed "$role" "$jar" "/dashboard" "/petugas/absensi" "/petugas/tugas/input"
      check_forbidden "$role" "$jar" "/admin/users" "/atasan/absensi"
      ;;
    atasan)
      check_allowed "$role" "$jar" "/dashboard" "/atasan/absensi" "/atasan/tugas"
      check_forbidden "$role" "$jar" "/admin/users" "/petugas/absensi"
      ;;
    admin)
      check_allowed "$role" "$jar" "/dashboard" "/admin/users" "/admin/pengaturan"
      check_forbidden "$role" "$jar" "/petugas/absensi" "/atasan/absensi"
      ;;
  esac
}

check_allowed() {
  local role="$1"
  local jar="$2"
  shift 2

  for path in "$@"; do
    local code
    code="$(http_code "$jar" "$path")"
    if [ "$code" = "200" ]; then
      echo "$role|ALLOWED|$path|$code|PASS"
    else
      echo "$role|ALLOWED|$path|$code|FAIL"
    fi
  done
}

check_forbidden() {
  local role="$1"
  local jar="$2"
  shift 2

  for path in "$@"; do
    local code
    code="$(http_code "$jar" "$path")"
    if [ "$code" = "403" ]; then
      echo "$role|FORBIDDEN|$path|$code|PASS"
    else
      echo "$role|FORBIDDEN|$path|$code|FAIL"
    fi
  done
}

if ! require_env \
  STAGING_PETUGAS_LOGIN STAGING_PETUGAS_PASSWORD \
  STAGING_ATASAN_LOGIN STAGING_ATASAN_PASSWORD \
  STAGING_ADMIN_LOGIN STAGING_ADMIN_PASSWORD; then
  exit 2
fi

echo "area|case|path|actual|status"

anon_jar="$TMP_DIR/anon.cookies"
curl -k -s -c "$anon_jar" "$BASE_URL/login" > /dev/null
for path in "/" "/dashboard" "/admin/users" "/petugas/absensi" "/atasan/absensi"; do
  code="$(curl -k -s -o /dev/null -w '%{http_code}|%{redirect_url}' -b "$anon_jar" "$BASE_URL$path")"
  if [[ "$code" == 302*"/login"* ]]; then
    echo "unauthenticated|REDIRECT|$path|$code|PASS"
  else
    echo "unauthenticated|REDIRECT|$path|$code|FAIL"
  fi
done

if [ "${SKIP_INVALID_LOGIN:-0}" != "1" ]; then
  bad_jar="$TMP_DIR/bad.cookies"
  bad_login="$TMP_DIR/bad-login.html"
  bad_after="$TMP_DIR/bad-after.html"
  curl -k -s -c "$bad_jar" "$BASE_URL/login" > "$bad_login"
  bad_token="$(extract_token "$bad_login")"
  bad_result="$(curl -k -s -o "$bad_after" -w '%{http_code}|%{redirect_url}' -b "$bad_jar" -c "$bad_jar" \
    -X POST "$BASE_URL/login" \
    --data-urlencode "_token=$bad_token" \
    --data-urlencode "login=invalid@example.test" \
    --data-urlencode "password=WrongPassword123")"
  bad_dashboard_code="$(http_code "$bad_jar" "/dashboard")"
  if [ "$bad_dashboard_code" = "302" ]; then
    echo "auth|INVALID_LOGIN|/login|$bad_result dashboard=$bad_dashboard_code|PASS"
  else
    echo "auth|INVALID_LOGIN|/login|$bad_result dashboard=$bad_dashboard_code|FAIL"
  fi
fi

if [ -z "${ONLY_ROLE:-}" ] || [ "$ONLY_ROLE" = "petugas" ]; then
  login_role petugas "$STAGING_PETUGAS_LOGIN" "$STAGING_PETUGAS_PASSWORD"
fi

if [ -z "${ONLY_ROLE:-}" ] || [ "$ONLY_ROLE" = "atasan" ]; then
  login_role atasan "$STAGING_ATASAN_LOGIN" "$STAGING_ATASAN_PASSWORD"
fi

if [ -z "${ONLY_ROLE:-}" ] || [ "$ONLY_ROLE" = "admin" ]; then
  login_role admin "$STAGING_ADMIN_LOGIN" "$STAGING_ADMIN_PASSWORD"
fi
