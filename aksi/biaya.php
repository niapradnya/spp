<?php
session_start();
include "../koneksi.php";
include "../function.php";

if($_POST){
    if($_POST['aksi']=='tambah'){
    $id_periode=$_POST['id_periode'];
    $tingkat=$_POST['tingkat'];
    $id_jurusan=$_POST['id_jurusan'];
    $deskripsi_biaya=$_POST['deskripsi_biaya'];
    $jumlah_biaya=$_POST['jumlah_biaya'];
    $tanggal_jatuh_tempo=$_POST['tanggal_jatuh_tempo'];
    
    $sql="INSERT INTO biaya (id_biaya,id_periode,tingkat,id_jurusan,deskripsi_biaya,jumlah_biaya,tanggal_jatuh_tempo,dibuat_pada,diubah_pada,dihapus_pada) VALUES(DEFAULT,'$id_periode','$tingkat','$id_jurusan','$deskripsi_biaya','$jumlah_biaya','$tanggal_jatuh_tempo',DEFAULT,DEFAULT,DEFAULT)";
    mysqli_query($koneksi,$sql);
    notifikasi($koneksi);

    // echo $sql; // Cek perintah
   
    header('location:../index.php?p=biaya');
    }
    else if($_POST['aksi']=='ubah'){
        $id_biaya=$_POST['id_biaya'];
        $id_periode=$_POST['id_periode'];
        $tingkat=$_POST['tingkat'];
        $id_jurusan=$_POST['id_jurusan'];
        $deskripsi_biaya=$_POST['deskripsi_biaya'];
        $jumlah_biaya=$_POST['jumlah_biaya'];
        $tanggal_jatuh_tempo=$_POST['tanggal_jatuh_tempo'];
        
       $sql="UPDATE biaya SET id_periode=$id_periode,deskripsi_biaya='$deskripsi_biaya',tingkat='$tingkat',id_jurusan='$id_jurusan',jumlah_biaya='$jumlah_biaya',tanggal_jatuh_tempo='$tanggal_jatuh_tempo' WHERE id_biaya=$id_biaya";
        // echo $sql; // Cek perintah
        mysqli_query($koneksi,$sql);
        notifikasi($koneksi);
        header('location:../index.php?p=biaya');
    }
}

if($_GET){
    if($_GET['aksi']=='hapus'){
        $id_biaya=$_GET['id_biaya'];
        // $sql="DELETE FROM biaya WHERE id_biaya=$id_biaya";  // Hard Delete
        $sql="UPDATE biaya SET dihapus_pada=now() WHERE id_biaya=$id_biaya"; // soft delete

        mysqli_query($koneksi,$sql);
        notifikasi($koneksi);
        header('location:../index.php?p=biaya');
    }
    else if ($_GET['aksi']=='restore'){
        $id_biaya=$_GET['id_biaya'];
        $sql="UPDATE biaya SET dihapus_pada=NULL WHERE id_biaya=$id_biaya";
        mysqli_query($koneksi,$sql);
        notifikasi($koneksi);
        header('location:../index.php?p=biaya');

    }
    else if ($_GET['aksi']=='hapus-permanen'){
            $id_biaya=$_GET['id_biaya'];
            $sql="DELETE FROM biaya WHERE id_biaya=$id_biaya";  

            mysqli_query($koneksi,$sql);
            notifikasi($koneksi);
            header('location:../index.php?p=biaya');
    }
}