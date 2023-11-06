<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan File</title>
</head>
<body>
    <form action="web-ui.php" method="post">
        <label for="directory_path">Direktori</label>
        <input type="text" name="directory_path" id="directory_path" value="<?=$_POST['directory_path'] ?? './'?>">
        <br>
        <label for="keywords">Kata Kunci</label>
        <input type="text" name="keywords" id="keywords" value="<?=$_POST['keywords'] ?? 'judi, poker, kasino, slot, taruhan, togel, mahjong'?>">
        <br>
        <input type="submit" name="scan" value="Scan">
    </form>
    <?php
    
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

    if(isset($_POST['scan'])) {
        $directory_path = $_POST['directory_path'];
        $keywords = explode(",", $_POST['keywords']);
        //trim all keywords
        $keywords = array_map('trim', $keywords);
        $results = scan_directory($directory_path, $keywords);
        echo "<pre>";
        print_r($results);
        echo "</pre>";
    }

    ?>
</body>
</html>