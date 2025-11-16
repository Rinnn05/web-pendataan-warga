<?php require_once __DIR__ . '/../app/helpers.php'; require_login();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=warga_export.csv');
$out = fopen('php://output','w');
fputcsv($out, ['NIK','Nama','Jenis Kelamin','TTL','Alamat','No HP','KK']);
$stmt=$pdo->query('SELECT w.*,k.no_kk FROM warga w LEFT JOIN keluarga k ON w.keluarga_id=k.id ORDER BY w.id DESC');
while($r=$stmt->fetch(PDO::FETCH_ASSOC)){ fputcsv($out, [$r['nik'],$r['nama'],$r['jenis_kelamin'],($r['tempat_lahir'].' '.$r['tanggal_lahir']),$r['alamat_text'],$r['nomor_hp'],$r['no_kk']]); }
fclose($out); exit; ?>