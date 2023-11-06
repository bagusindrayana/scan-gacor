<?php
// Daftar kata kunci yang biasanya terkait dengan situs judi
$keywords = ['judi', 'poker', 'kasino', 'slot', 'taruhan', 'togel', 'mahjong'];
$directory_paths = ["./assets"];
$ignore_paths = ["./my-cron-job"];
$delete = true;
$log = false;


// Fungsi untuk memindai file
function scan_file($file_path, $keywords)
{
    $content = file_get_contents($file_path);
    $found = [];
    foreach ($keywords as $keyword) {
        if (stripos(strtolower($content), strtolower($keyword)) !== false) {
            echo 'Kata kunci "' . $keyword . '" ditemukan dalam file ' . $file_path . "<br>";
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
            echo 'Kata kunci "' . $keyword . '" ditemukan dalam folder ' . $folder_path . "<br>";
            $found[] = $keyword;
        }
    }
    return $found;
}


// Fungsi untuk memindai direktori
function scan_directory($directory_path, $keywords,$ignore_paths)
{
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory_path));
    //ignore path

    $results = [];
    foreach ($files as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php', 'html'])) {
            $r = scan_file($file->getRealPath(), $keywords);
            if (count($r) > 0 && (stripos($file->getRealPath(), $ignore_paths) == false)) {
                $results[] = [
                    "type" => "file",
                    "found" => implode(",", $r),
                    "path" => $file->getRealPath()
                ];
            }
        } else if ($file->isDir()) {
            $r = scan_folder($file->getRealPath(), $keywords);
            if (count($r) > 0 && (stripos($file->getRealPath(), $ignore_paths) == false)) {
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




if (count($directory_paths)) {
    echo "Memulai scan...<br>";
    $rdir = [];
    foreach ($directory_paths as $directory_path) {
        if(is_dir($directory_path)){
            $resultScan = scan_directory($directory_path, $keywords,$ignore_paths);
            $rdir = array_merge($rdir, $resultScan);
            
        }
    }
    if ($delete && count($rdir) > 0) {
        echo "Menghapus file dan folder yang terdeteksi...<br>";
        for ($i = 0; $i < count($rdir); $i++) {
            if ($rdir[$i]['type'] == 'file') {
                unlink($rdir[$i]['path']);
            } else {
                rmdir($rdir[$i]['path']);
            }
        }
    }
    if ($log && count($rdir) > 0) {
        //membuat file log dengan new line
        $log = fopen("log-".date("Y-m-d-H-i-s").".txt", "w");
        foreach ($rdir as $r) {
            fwrite($log, $r['path'] . " | keyword : " . $r['found'] . "<br>");
        }
        fclose($log);
    }
    echo "Selesai scan.<br>";
}
?>