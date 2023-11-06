<?php
//mencoba membuka file wordlist
$keywords = [];
try {
    $file = fopen("wordlist.txt", "r");
    while (!feof($file)) {
        $line = fgets($file);
        $line = trim($line);
        if ($line !== '') {
            $keywords[] = $line;
        }
    }

    fclose($file);
} catch (Exception $e) {
    echo "File wordlist tidak ditemukan.\n";
    echo "Menggunakan wordlist default.\n";

    // Daftar kata kunci yang biasanya terkait dengan situs judi
    $keywords = ['judi', 'poker', 'kasino', 'slot', 'taruhan', 'togel', 'mahjong'];
}



// Fungsi untuk memindai file
function scan_file($file_path, $keywords)
{
    $content = file_get_contents($file_path);
    $found = [];
    foreach ($keywords as $keyword) {
        if (stripos(strtolower($content), strtolower($keyword)) !== false) {
            echo 'Kata kunci "' . $keyword . '" ditemukan dalam file ' . $file_path . "\n";
            $found[] = $keyword;
        }
    }
    return $found;
}

function scan_folder($folder_path, $keywords)
{
    $found = [];
    foreach ($keywords as $keyword) {
        if (stripos(strtolower($folder_path), strtolower($keyword)) !== false) {
            echo 'Kata kunci "' . $keyword . '" ditemukan dalam folder ' . $folder_path . "\n";
            $found[] = $keyword;
        }
    }
    return $found;
}


// Fungsi untuk memindai direktori
function scan_directory($directory_path, $keywords)
{
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory_path));
    $results = [];
    foreach ($files as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php', 'html'])) {
            $r = scan_file($file->getRealPath(), $keywords);
            if (count($r) > 0) {
                $results[] = [
                    "type" => "file",
                    "found" => implode(",", $r),
                    "path" => $file->getRealPath()
                ];
            }
        } else if ($file->isDir()) {
            $r = scan_folder($file->getRealPath(), $keywords);
            if (count($r) > 0) {
                $results[] = [
                    "type" => "folder",
                    "found" => implode(",", $r),
                    "path" => $file->getRealPath()
                ];
            }
        }
    }
    return $results;
}

// Mendapatkan argumen direktori dari command line
$directory_path = $argv[1] ?? '';

//cek argumen -d untuk delete file yang terdeteksi
$delete = false;
$log = false;
foreach ($argv as $arg) {
    if ($arg == '-d') {
        $delete = true;
    }
    if ($arg == '-l') {
        $log = true;
    }
}


if ($directory_path !== '' && is_dir($directory_path)) {
    echo "Memulai scan...";
    $rdir = scan_directory($directory_path, $keywords);
    if ($delete) {
        echo "Menghapus file dan folder yang terdeteksi...";
        for ($i = 0; $i < count($rdir); $i++) {
            if ($rdir[$i]['type'] == 'file') {
                unlink($rdir[$i]['path']);
            } else {
                rmdir($rdir[$i]['path']);
            }
        }
    }
    if ($log) {
        //membuat file log dengan new line
        $log = fopen("log-".date("Y-m-d-H-i-s").".txt", "w");
        foreach ($rdir as $r) {
            fwrite($log, $r['path'] . " | keyword : " . $r['found'] . "\n");
        }
        fclose($log);
    }
    echo "Selesai scan.";
} else {
    echo "Argumen direktori tidak valid atau tidak diberikan.\n";
}
?>