#!/usr/bin/env python3
"""Blackbox HTTP testing for absensi PPSU staging."""
import requests
import re
import sys
import tempfile
import os

BASE = "https://absensippsu.myftp.biz"
CREDS = {
    "admin":    ("admin@local.test", "Admin12345"),
    "petugas_email": ("iwasya22@gmail.com", "iwasya22"),
    "petugas_user":  ("iwasya22", "iwasya22"),
    "atasan":   ("atasan@local.test", "Atasan12345"),
    "invalid":  ("invalid@example.test", "WrongPassword123"),
}

results = []

def log(area, case_, path, actual, status):
    results.append((area, case_, path, actual, status))
    print(f"{area:20s} | {case_:30s} | {path:30s} | {actual:30s} | {status}")

s = requests.Session()
s.verify = False  # self-signed cert OK
s.headers.update({"User-Agent": "BlackboxTest/1.0"})

# --- helpers ---
def get_csrf(html):
    m = re.search(r'name="_token" value="([^"]+)"', html)
    return m.group(1) if m else None

# --- UNAUTHENTICATED ---
print("=== UNAUTHENTICATED ===")
for path in ["/", "/dashboard", "/admin/users", "/petugas/absensi", "/atasan/absensi"]:
    r = s.get(f"{BASE}{path}", allow_redirects=False)
    loc = r.headers.get("Location", "")
    actual = f"{r.status_code}"
    if r.status_code == 302 and "/login" in loc:
        actual += f" -> {loc}"
    expected_redirect = r.status_code == 302 and "/login" in loc
    log("unauthenticated", "REDIRECT", path, actual, "PASS" if expected_redirect else "FAIL")

def login_test(label, login, password):
    """Perform login + role access checks."""
    s2 = requests.Session()
    s2.verify = False
    s2.headers.update({"User-Agent": "BlackboxTest/1.0"})

    # get CSRF
    r = s2.get(f"{BASE}/login")
    token = get_csrf(r.text)
    if not token:
        log(label, "LOGIN", "/login", "CSRF missing", "FAIL")
        return s2, False

    # POST login
    r = s2.post(f"{BASE}/login", data={"_token": token, "login": login, "password": password}, allow_redirects=False)
    loc = r.headers.get("Location", "")
    actual = f"POST=>{r.status_code} redirect={loc}"
    post_ok = r.status_code == 302 and "/dashboard" in loc

    # GET dashboard
    r = s2.get(f"{BASE}/dashboard", allow_redirects=False)
    dash_ok = r.status_code == 200
    actual += f" dash={r.status_code}"

    status = "PASS" if (post_ok and dash_ok) else "FAIL"
    log(label, "LOGIN", "/login", actual, status)
    if not dash_ok:
        return s2, False
    return s2, True

def check_paths(session, label, paths, expected_code):
    for path in paths:
        r = session.get(f"{BASE}{path}", allow_redirects=False)
        actual = str(r.status_code)
        status = "PASS" if r.status_code == expected_code else "FAIL"
        case = f"ALLOWED" if expected_code == 200 else "FORBIDDEN"
        log(label, case, path, actual, status)

# --- ADMIN ---
print("=== ADMIN ===")
sess, ok = login_test("admin", *CREDS["admin"])
if ok:
    check_paths(sess, "admin", ["/dashboard", "/admin/users", "/admin/pengaturan"], 200)
    check_paths(sess, "admin", ["/petugas/absensi", "/atasan/absensi"], 403)

# --- PETUGAS (email) ---
print("=== PETUGAS (email) ===")
sess, ok = login_test("petugas_email", *CREDS["petugas_email"])
if ok:
    check_paths(sess, "petugas_email", ["/dashboard", "/petugas/absensi", "/petugas/tugas/input"], 200)
    check_paths(sess, "petugas_email", ["/admin/users", "/atasan/absensi"], 403)

# --- PETUGAS (username) ---
print("=== PETUGAS (username) ===")
sess, _ = login_test("petugas_username", *CREDS["petugas_user"])

# --- ATASAN ---
print("=== ATASAN ===")
sess, ok = login_test("atasan", *CREDS["atasan"])
if ok:
    check_paths(sess, "atasan", ["/dashboard", "/atasan/absensi", "/atasan/tugas"], 200)
    check_paths(sess, "atasan", ["/admin/users", "/petugas/absensi"], 403)

# --- INVALID LOGIN ---
print("=== INVALID LOGIN ===")
sess, _ = login_test("invalid", *CREDS["invalid"])

# --- SUMMARY ---
print("\n=== SUMMARY ===")
passed = sum(1 for r in results if r[4] == "PASS")
failed = sum(1 for r in results if r[4] == "FAIL")
print(f"Total: {len(results)} | PASS: {passed} | FAIL: {failed}")
for r in results:
    print(f"  {r[4]:5s} | {r[0]:20s} | {r[1]:30s} | {r[2]:30s} | {r[3]}")

# Output JSON for report automation
import json
print("\n=== JSON_RESULTS ===")
print(json.dumps([{"area": r[0], "case": r[1], "path": r[2], "actual": r[3], "status": r[4]} for r in results], indent=2, ensure_ascii=False))
