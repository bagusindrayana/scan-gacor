## File untuk scan file php dan html dengan wordlist tertentu

### Penggunaan
- pilih `scan-gacor.php` atau `scan-gacor.py` sesuai bahasa yang tersedia di komputer/server kamu
- tambahkan/ubah wordlist yang akan digunakan di file `wordlist.txt`
- jalankan perintah `php scan-gacor.php ./direktori-yang-akan-discan` atau `python3 scan-gacor.py ./direktori-yang-akan-discan`
- tambahkan argument `-l` untuk membuat log file hasil scan
- ⚠️tambahkan argument `-d` untuk hapus otomatis file dan folder jika terdeteksi ⚠️
  
    ```
    💀 Scan tidak memperdulikan besar kecil huruf, pastikan untuk mengecek hasil scan terlebih dahulu sebelum menggunakan perintah `-d` untuk menghapus file dan folder yang terdeteksi
    ```
- gunakan cronjob untuk menjalankan script secara otomatis dan menghapus file dan folder yang terdeteksi, jika tidak mempunyai akses terminal bisa menggunakan `cronjob/web-cronjob.php` dengan mengubah direktori didalam kodenya
### Limitasi
- hanya mengecek berdasarkan string dari wordlist dan hanya 2 ektesnsi file yaitu php dan html (silahkan tambahkan sendiri di kodenya)
- kemungkinan tidak bisa mendeteksi web yang di Obfuscate