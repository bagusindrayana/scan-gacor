import os
import sys
from datetime import datetime

# Mencoba membuka file wordlist
keywords = None
try:
    with open("wordlist.txt", "r") as wordlist:
        # Ubah menjadi array
        keywords = wordlist.read().splitlines()
        print("Menggunakan file wordlist.")
except FileNotFoundError:
    print("File wordlist tidak ditemukan.")
    print("Menggunakan wordlist default.")

    # Daftar kata kunci yang biasanya terkait dengan situs judi
    keywords = ['judi', 'poker', 'kasino', 'slot', 'taruhan', 'togel', 'mahjong']

# Fungsi untuk memindai file
def scan_file(file_path, keywords):
    with open(file_path, "r", encoding="utf-8", errors="ignore") as file:
        content = file.read()
        found = []
        for keyword in keywords:
            if keyword.lower() in content.lower():
                print(f'Kata kunci "{keyword}" ditemukan dalam file {file_path}')
                found.append(keyword)
        return found

# Fungsi untuk memindai folder
def scan_folder(folder_path, keywords):
    found = []
    for keyword in keywords:
        if keyword.lower() in folder_path.lower():
            print(f'Kata kunci "{keyword}" ditemukan dalam folder {folder_path}')
            found.append(keyword)
    return found

# Fungsi untuk memindai direktori
def scan_directory(directory_path, keywords):
    results = []
    for root, dirs, files in os.walk(directory_path):
        for file in files:
            if file.lower().endswith(('.php', '.html')):
                file_path = os.path.join(root, file)
                r = scan_file(file_path, keywords)
                if r:
                    results.append({
                        "type": "file",
                        "found": ",".join(r),
                        "path": file_path
                    })
        for folder in dirs:
            folder_path = os.path.join(root, folder)
            r = scan_folder(folder_path, keywords)
            if r:
                results.append({
                    "type": "folder",
                    "found": ",".join(r),
                    "path": folder_path
                })
    return results

# Mendapatkan argumen direktori dari command line
directory_path = sys.argv[1] if len(sys.argv) > 1 else ''

# Cek argumen -d untuk delete file yang terdeteksi
delete = '-d' in sys.argv

# Cek argumen -l untuk membuat file log
log = '-l' in sys.argv

if directory_path and os.path.isdir(directory_path):
    print("Memulai scan...")
    rdir = scan_directory(directory_path, keywords)
    if delete:
        print("Menghapus file dan folder yang terdeteksi...")
        for r in rdir:
            if r['type'] == 'file':
                os.remove(r['path'])
            else:
                os.rmdir(r['path'])
    if log:
        # Membuat file log dengan new line
        # filename dengan format log-<datetime>.txt
        filename = f"log-{datetime.now().strftime('%Y-%m-%d-%H-%M-%S')}.txt"
        with open(filename, "w") as log_file:
            for r in rdir:
                log_file.write(f"{r['path']} | keyword: {r['found']}\n")
    print("Selesai scan.")
else:
    print("Argumen direktori tidak valid atau tidak diberikan.")
