<?php
session_start();
include "../koneksi.php";
include "../function.php";

if($_POST){
    if($_POST['aksi']=='tambah'){ // Untuk Manajemen
    $id_siswa=$_POST['id_siswa'];
    $id_bayar_metode=$_POST['id_bayar_metode'];
    $keterangan=$_POST['keterangan'];
    $status_verifikasi= "Belum Verifikasi";
    $nominal_bayar=$_POST['nominal_bayar'];
    $tanggal_bayar=$_POST['tanggal_bayar'];
    // Perintah untuk upload file
    $date = date('Y_m_d_H_i_s');
    $date = str_replace(".","", $date);
    $nama_file_bukti=$date."_".$_FILES['bukti']['name'];
    $posisi_file_bukti=$_FILES['bukti']['tmp_name'];
    $folder_file_bukti="../file/buktibayar/";
    // Penempatan File Ke Folder Bukti
    move_uploaded_file($posisi_file_bukti,$folder_file_bukti.$nama_file_bukti);
    $bukti=$nama_file_bukti;

    $sql="INSERT INTO bayar (id_bayar,id_siswa,id_bayar_metode,keterangan,bukti,status_verifikasi,nominal_bayar,tanggal_bayar,dibuat_pada,diubah_pada,dihapus_pada) VALUES(DEFAULT,$id_siswa,$id_bayar_metode,'$keterangan','$bukti','$status_verifikasi','$nominal_bayar','$tanggal_bayar',DEFAULT,DEFAULT,DEFAULT)";
     // echo $sql; // Cek perintah
    mysqli_query($koneksi,$sql);
    notifikasi($koneksi);

      // Perintah Alokasi Pembayaran Otomatis
      $sql_cari_id_bayar="SELECT id_bayar FROM bayar WHERE id_siswa=$id_siswa ORDER BY id_bayar DESC LIMIT 1";
      $query_cari_id_bayar=mysqli_query($koneksi,$sql_cari_id_bayar);
      $bayar=mysqli_fetch_array($query_cari_id_bayar);
      $id_bayar=$bayar['id_bayar'];
    //   echo $id_bayar;
    $sql_tagihan="SELECT tagihan.*,biaya.deskripsi_biaya,biaya.jumlah_biaya,biaya.tanggal_jatuh_tempo FROM tagihan,biaya WHERE tagihan.id_biaya=biaya.id_biaya AND tagihan.total_terbayar<(biaya.jumlah_biaya-tagihan.potongan) AND tagihan.id_siswa=$id_siswa ORDER BY biaya.tanggal_jatuh_tempo ASC";
    echo $sql_tagihan;
    $query_tagihan=mysqli_query($koneksi,$sql_tagihan);
    $alokasi_dana=$nominal_bayar;
    while($tagihan=mysqli_fetch_array($query_tagihan)){
        $id_tagihan=$tagihan['id_tagihan'];
        $jumlah_biaya=$tagihan['jumlah_biaya'];
        $potongan=$tagihan['potongan'];
        $total_terbayar=$tagihan['total_terbayar'];
        if($alokasi_dana>
        ($jumlah_biaya-$total_terbayar-$potongan)){
            $dibayarkan=$jumlah_biaya-$total_terbayar-$potongan;
        } else {
            $dibayarkan=$alokasi_dana;
        }
        // Insert Ke Tabel bayar_alokasi
        $sql_alokasi="INSERT INTO bayar_alokasi(id_bayar_alokasi,id_bayar,id_tagihan,total_alokasi,dialokasikan_oleh,dibuat_pada,diubah_pada,dihapus_pada) VALUES(DEFAULT,$id_bayar,$id_tagihan,$dibayarkan,'Otomatis Oleh Sistem',DEFAULT,DEFAULT,DEFAULT)";
        mysqli_query($koneksi,$sql_alokasi);
        // Update Tagihan
        $sql_update_tagihan="UPDATE tagihan SET total_terbayar=total_terbayar+$dibayarkan WHERE id_tagihan=$id_tagihan";
        mysqli_query($koneksi,$sql_update_tagihan);
        // echo $sql_alokasi;
        $alokasi_dana=$alokasi_dana-$dibayarkan;
        // echo "<br>Dibayarkan Untuk ".$tagihan['deskripsi_biaya']." Sebesar ".number_format($dibayarkan);
        if($alokasi_dana<=0){
            break;
        }
    }
    // Ubah Status Verivikasi Pembayaran (Jika yang membayar adalah petugas maka status sudah verifikasi namun jika yang membayar adalah siswa/ortu siswa maka status belum verifikasi)
    $sql_ver="UPDATE bayar SET status_verifikasi='Sudah Verifikasi' WHERE id_bayar=$id_bayar";
    mysqli_query($koneksi,$sql_ver);

    header('location:../index.php?p=bayar');
    }

    else if ($_POST['aksi'] == 'konfirmasi-bayar'){ // Konfirmasi Bayar
        $id_bayar=$_POST['id_bayar'];
        $id_siswa=$_POST['id_siswa'];
        $nominal_bayar=str_replace(",","",$_POST['nominal_bayar']);

        $sql_tagihan="SELECT tagihan.*,biaya.deskripsi_biaya,biaya.jumlah_biaya,biaya.tanggal_jatuh_tempo FROM tagihan,biaya WHERE tagihan.id_biaya=biaya.id_biaya AND tagihan.total_terbayar<(biaya.jumlah_biaya-tagihan.potongan) AND tagihan.id_siswa=$id_siswa ORDER BY biaya.tanggal_jatuh_tempo ASC";
    // echo $sql_tagihan;
    $query_tagihan=mysqli_query($koneksi,$sql_tagihan);
    $alokasi_dana=$nominal_bayar;
    while($tagihan=mysqli_fetch_array($query_tagihan)){
        $id_tagihan=$tagihan['id_tagihan'];
        $jumlah_biaya=$tagihan['jumlah_biaya'];
        $potongan=$tagihan['potongan'];
        $total_terbayar=$tagihan['total_terbayar'];
        if($alokasi_dana>
        ($jumlah_biaya-$total_terbayar-$potongan)){
            $dibayarkan=$jumlah_biaya-$total_terbayar-$potongan;
        } else {
            $dibayarkan=$alokasi_dana;
        }
        // Insert Ke Tabel bayar_alokasi
        $sql_alokasi="INSERT INTO bayar_alokasi(id_bayar_alokasi,id_bayar,id_tagihan,total_alokasi,dialokasikan_oleh,dibuat_pada,diubah_pada,dihapus_pada) VALUES(DEFAULT,$id_bayar,$id_tagihan,$dibayarkan,'Otomatis Oleh Sistem',DEFAULT,DEFAULT,DEFAULT)";
        mysqli_query($koneksi,$sql_alokasi);
        // Update Tagihan
        $sql_update_tagihan="UPDATE tagihan SET total_terbayar=total_terbayar+$dibayarkan WHERE id_tagihan=$id_tagihan";
        mysqli_query($koneksi,$sql_update_tagihan);
        // echo $sql_update_tagihan;
        $alokasi_dana=$alokasi_dana-$dibayarkan;
        // echo "<br>Dibayarkan Untuk ".$tagihan['deskripsi_biaya']." Sebesar ".number_format($dibayarkan);
        if($alokasi_dana<=0){
            break;
        }
    }
    // Ubah Status Verivikasi Pembayaran (Jika yang membayar adalah petugas maka status sudah verifikasi namun jika yang membayar adalah siswa/ortu siswa maka status belum verifikasi)
    $sql_ver="UPDATE bayar SET status_verifikasi='Sudah Verifikasi' WHERE id_bayar=$id_bayar";
    mysqli_query($koneksi,$sql_ver);
    notifikasi($koneksi);


    header('location:../index.php?p=bayar');

        // echo "<br>ID Siswa : ".$id_siswa;
        // echo "<br>ID Bayar : ".$id_bayar;


    }

    else if ($_POST['aksi'] == 'tambah-siswa'){ // untuk siswa
        $id_siswa = $_POST['id_siswa'];
        $id_bayar_metode = $_POST['id_bayar_metode'];
        $keterangan = $_POST['keterangan'];
        $status_verifikasi = "Belum Verifikasi";
        $nominal_bayar = $_POST['nominal_bayar'];
        $tanggal_bayar = $_POST['tanggal_bayar'];
        // perintah untuk upload file
        $date = date('Y_m_d_H_i_s');
        $date = str_replace(".","", $date);
        $nama_file_bukti=$date."_".$_FILES['bukti']['name'];
        $posisi_file_bukti=$_FILES['bukti']['tmp_name']; //asal file
        $folder_file_bukti="../file/buktibayar/"; //tujuan penaruhan file
        // Penempatan file ke folder bukti
        move_uploaded_file($posisi_file_bukti,$folder_file_bukti.$nama_file_bukti);
        $bukti=$nama_file_bukti;

        $sql = "INSERT INTO bayar (id_bayar,id_siswa,id_bayar_metode,keterangan,bukti,status_verifikasi,nominal_bayar,tanggal_bayar,dibuat_pada,diubah_pada,dihapus_pada) VALUES (DEFAULT,$id_siswa,$id_bayar_metode,'$keterangan','$bukti','$status_verifikasi',$nominal_bayar,'$tanggal_bayar',DEFAULT,DEFAULT,DEFAULT)";
        // echo $sql; //cek perintah
        mysqli_query($koneksi, $sql);
        notifikasi($koneksi);

        header("location:../index.php?p=riwayat");
    }
    else if($_POST['aksi']=='ubah'){
        $id_biaya=$_POST['id_biaya'];
        $id_periode=$_POST['id_periode'];
        $tingkat=$_POST['tingkat'];
        $id_jurusan=$_POST['id_jurusan'];
        $deskripsi_biaya=$_POST['deskripsi_biaya'];
        $jumlah_biaya=$_POST['jumlah_biaya'];
        $tanggal_jatuh_tempo=$_POST['tanggal_jatuh_tempo'];
        
       $sql="UPDATE bayar SET id_periode=$id_periode,deskripsi_biaya='$deskripsi_biaya',tingkat='$tingkat',id_jurusan='$id_jurusan',jumlah_biaya='$jumlah_biaya',tanggal_jatuh_tempo='$tanggal_jatuh_tempo' WHERE id_biaya=$id_biaya";
        echo $sql; // Cek perintah
        mysqli_query($koneksi,$sql);
        notifikasi($koneksi);
        // header('location:../index.php?p=biaya');
    }
}

if($_GET){
    if($_GET['aksi']=='hapus'){
        $id_bayar=$_GET['id_bayar'];
        // $sql="DELETE FROM bayar WHERE id_bayar=$id_bayar";  // Hard Delete
        $sql="UPDATE bayar SET dihapus_pada=now() WHERE id_bayar=$id_bayar"; // soft delete
        mysqli_query($koneksi,$sql);
        notifikasi($koneksi);
        $sql2="UPDATE bayar_alokasi SET dihapus_pada=now() WHERE id_bayar=$id_bayar";
        mysqli_query($koneksi,$sql2);

        // Update Saldo Tagihan 
        $sql3="SELECT * FROM bayar_alokasi WHERE id_bayar=$id_bayar";
        $query3=mysqli_query($koneksi,$sql3);
        while($row3=mysqli_fetch_array($query3)){
            $id_tagihan=$row3['id_tagihan'];
            $total_alokasi=$row3['total_alokasi'];
            $sql4="UPDATE tagihan SET total_terbayar=total_terbayar-$total_alokasi WHERE id_tagihan=$id_tagihan";
            mysqli_query($koneksi,$sql4);

        }

        
        header('location:../index.php?p=bayar');
    }
    else if ($_GET['aksi']=='restore'){
        $id_bayar=$_GET['id_bayar'];
        $sql="UPDATE bayar SET dihapus_pada=NULL WHERE id_bayar=$id_bayar";
        mysqli_query($koneksi,$sql);
        notifikasi($koneksi);
        header('location:../index.php?p=bayar');

    }
    else if ($_GET['aksi']=='hapus-permanen'){
            $id_bayar=$_GET['id_bayar'];
            $sql="DELETE FROM bayar WHERE id_bayar=$id_bayar";  

            mysqli_query($koneksi,$sql);
            notifikasi($koneksi);
            header('location:../index.php?p=bayar');
    }
}