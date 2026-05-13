path = r"D:\kerjaan\Proyek_absensi\absensi-app\app\Http\Controllers\Admin\AdminController.php"
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix users()
content = content.replace(
    "'items' => \$query->orderBy('created_at', 'asc')->paginate(25)->withQueryString()",
    "'items' => \$query->orderBy('id_user', 'asc')->paginate(\$request->get('per_page', 25))->withQueryString()"
)

# Fix tempat()
content = content.replace(
    "TempatTugas::orderBy('id_tempat', 'asc')->paginate(15)",
    "TempatTugas::orderBy('id_tempat', 'asc')->paginate(\$request->get('per_page', 15))"
)

# Fix periode()
content = content.replace(
    "Periode::orderBy('id_periode', 'asc')->paginate(15)",
    "Periode::orderBy('id_periode', 'asc')->paginate(\$request->get('per_page', 15))"
)

# Fix kalender()
content = content.replace(
    "Kalender::orderBy('id_kalender', 'asc')->paginate(20)",
    "Kalender::orderBy('id_kalender', 'asc')->paginate(\$request->get('per_page', 20))"
)

# Fix sanksi()
content = content.replace(
    "Sanksi::with('user')->orderBy('id_sanksi', 'asc')->paginate(20)",
    "Sanksi::with('user')->orderBy('id_sanksi', 'asc')->paginate(\$request->get('per_page', 20))"
)

# Fix activity logs
content = content.replace(
    "ActivityLog::with('user')->orderBy('id_log', 'asc')->paginate(15)",
    "ActivityLog::with('user')->orderBy('id_log', 'asc')->paginate(\$request->get('per_page', 15))"
)

# Fix dataSensitif
content = content.replace(
    "'users' => \$query->orderBy('id_user', 'asc')->paginate(50)",
    "'users' => \$query->orderBy('id_user', 'asc')->paginate(\$request->get('per_page', 50))"
)

# Fix bukaAksesAbsen
content = content.replace(
    "->where('status', 'akses_dibuka')\n                ->orderBy('created_at', 'asc')\n                ->paginate(15)",
    "->where('status', 'akses_dibuka')\n                ->orderBy('id_absensi', 'asc')\n                ->paginate(\$request->get('per_page', 15))"
)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)
print("Done!")
