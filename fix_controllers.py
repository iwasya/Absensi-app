import os
import re

# List of controllers to fix
controllers = [
    r"D:\kerjaan\Proyek_absensi\absensi-app\app\Http\Controllers\Admin\AdminController.php",
    r"D:\kerjaan\Proyek_absensi\absensi-app\app\Http\Controllers\Atasan\ApprovalController.php",
    r"D:\kerjaan\Proyek_absensi\absensi-app\app\Http\Controllers\Atasan\SanksiController.php",
    r"D:\kerjaan\Proyek_absensi\absensi-app\app\Http\Controllers\Petugas\AbsensiController.php",
    r"D:\kerjaan\Proyek_absensi\absensi-app\app\Http\Controllers\Petugas\CutiController.php",
    r"D:\kerjaan\Proyek_absensi\absensi-app\app\Http\Controllers\Petugas\SanksiController.php",
    r"D:\kerjaan\Proyek_absensi\absensi-app\app\Http\Controllers\Petugas\TugasController.php",
]

for path in controllers:
    if not os.path.exists(path):
        print(f"Skipping: {path} not found")
        continue
    
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Fix paginate() calls to use $request->get("per_page", default)
    # Pattern: paginate(NUMBER) -> paginate($request->get("per_page", NUMBER))
    content = re.sub(
        r"paginate\((\d+)\)",
        lambda m: f'paginate($request->get("per_page", {m.group(1)}))',
        content
    )
    
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"Fixed: {os.path.basename(path)}")

print("All done!")
