$content = Get-Content 'D:\kerjaan\Proyek_absensi\absensi-app\resources\views\admin\users.blade.php' -Raw
$content = $content -replace [char]34 + "admin.users.update" + [char]34, 'admin.users.update'
$content = $content -replace [char]34 + "admin.users.delete" + [char]34, 'admin.users.delete'
$content = $content -replace [char]34 + "PUT" + [char]34, 'PUT'
$content = $content -replace [char]34 + "DELETE" + [char]34, 'DELETE'
$content = $content -replace [char]34 + "Hapus user ini?" + [char]34, 'Hapus user ini?'
$content | Set-Content 'D:\kerjaan\Proyek_absensi\absensi-app\resources\views\admin\users.blade.php' -NoNewline -Encoding UTF8
Write-Host 'Fixed!'
