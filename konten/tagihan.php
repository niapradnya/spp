<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Tagihan</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
              <li class="breadcrumb-item active">Tagihan</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      <div class="card">
       <div class="card-header">
         <h5>Data Tagihan</h5>
      </div>
      <div class="card-body">
        <button class="btn bg-purple mb-2" data-toggle="modal" data-target="#modalRecycleBin">  <i class="fas fa-recycle"></i> Recycle Bin </button>
        <table id="example1" class="table table-hover table-sm">
          <thead class="bg-purple">
            <th>ID</th>
            <th>Periode</th>
            <th>NIS Siswa</th>
            <th>Nama Siswa</th>
            <th>Kelas</th>
            <th>Deskripsi Biaya</th>
            <th>Tanggal Jatuh Tempo</th>
            <th>Jumlah Biaya</th>
            <th>Terbayar</th>
            <th>Aksi</th>
          </thead>
          <?php
            $sql="SELECT tagihan.*,siswa.nis,siswa.nama,biaya.deskripsi_biaya,biaya.jumlah_biaya,biaya.tanggal_jatuh_tempo,periode.periode FROM tagihan,siswa,biaya,periode WHERE tagihan.dihapus_pada IS NULL AND tagihan.id_siswa=siswa.id_siswa AND tagihan.id_biaya=biaya.id_biaya AND biaya.id_periode=periode.id_periode";
            $query=mysqli_query($koneksi,$sql);
            while($kolom=mysqli_fetch_array($query)){
              ?>
              <tr>
                <td><?= $kolom['id_tagihan']; ?></td>
                <td><?= $kolom['periode']; ?></td>
                <td><?= $kolom['nis']; ?></td>
                <td><?= $kolom['nama']; ?></td>
                <td><?= $kolom['kelas']; ?></td>
                <td><?= $kolom['deskripsi_biaya']; ?></td>
                <td><?= $kolom['tanggal_jatuh_tempo']; ?></td>
                <td><?= number_format($kolom['jumlah_biaya']); ?></td>
                <td><?= number_format($kolom['total_terbayar']); ?></td>
                
                <td>
                                       <!-- Tombol Informasi-->
                                      <a
                                        title="Informasi Riwayat Tagihan"
                                        href="index.php?p=tagihan-info&id_tagihan=<?= $kolom['id_tagihan']; ?>">
                                        <i class="fas fa-search"></i>
                                    </a>
                                    &nbsp;
                                    <!-- Tombol Edit-->

                                    <a
                                        title="Ubah Tagihan"
                                        href="index.php?p=tagihan-edit&id_tagihan=<?= $kolom['id_tagihan']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                </td>
              </tr>
              
              <?php
            } //Akhir While
              ?>
        </table>
        <button type="button" class="btn bg-purple btn-block mt-3" data-toggle="modal" data-target="#modalTambah"> <i class="fas fa-plus">Tambah Tagihan</i></button>
      </div>
      </div>
       
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Modal Tambah Tagihan -->
  <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Tagihan Per Jurusan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="aksi/tagihan.php" method="post">
        <input type="hidden" name="aksi" value="tambah-berdasarkan-kelas">
         
          <label for="id_periode">ID Periode</label>
          <?php 
                   $query2 = "SELECT * FROM periode";
                   $sql2 = mysqli_query($koneksi,$query2);
                   ?>
        <select name="id_periode" id="id_periode" class="form-control">
            <option value="">-- Pilih Periode --</option>
            <?php while ($kolom2 = mysqli_fetch_array($sql2)) : ?>
            <option name="periode" value="<?= $kolom2['id_periode'] ?>"><?= $kolom2['periode'] ?></option>
            <?php endwhile ?>
        </select>

          <label for="tingkat">Tingkat</label>
          <input type="number" name="tingkat" class="form-control" required>

          <label for="id_jurusan">Jurusan</label>
          <select name="id_jurusan" class="form-control" required>
           <option value="">-- Pilih Jurusan --</option>
           <?php 
            $sql_jurusan="SELECT * FROM jurusan WHERE dihapus_pada IS NULL ORDER BY jurusan ASC";
            $query_jurusan=mysqli_query($koneksi,$sql_jurusan);
            while($jurusan=mysqli_fetch_array($query_jurusan)){
              echo "<option value='$jurusan[id_jurusan]'>$jurusan[jurusan]</option>";
            }
           ?>
         </select>
            &nbsp;
          <button type="submit" class="btn btn-block bg-purple mt-3"> <i class="fas fa-save"
          >Simpan</i></button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Recycle Bin -->
<div class="modal fade" id="modalRecycleBin" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Data Pengapusan Sementara</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <table class="table table-hover">
          <thead class="bg-purple">
            <th>ID Biaya</th>
            <th>Deskripsi Biaya</th>
            <th>Dihapus Pada</th>
            <th>Aksi</th>
          </thead>
          <?php
            $sql="SELECT * FROM biaya WHERE dihapus_pada IS NOT NULL";
            $query=mysqli_query($koneksi,$sql);
            while($kolom=mysqli_fetch_array($query)){
              ?>
              <tr>
                <td><?= $kolom['id_biaya']; ?></td>
                <td><?= $kolom['deskripsi_biaya']; ?></td>
                <td><?= $kolom['dihapus_pada']; ?></td>
                <td>  <a onclick="return confirm('Yakin akan mengembalikan data ini?')" href="aksi/biaya.php?aksi=restore&id_biaya=<?= $kolom['id_biaya']; ?>"> <i class="fas fa-trash-restore"></i> </a>
                  &nbsp;
                <a onclick="return confirm('Yakin akan menghapus data ini secara permanen?')" href="aksi/biaya.php?aksi=hapus-permanen&id_biaya=<?= $kolom['id_biaya']; ?>"> <i class="fas fa-eraser"></i> </a>
              </td>
              </tr>
              <?php
            } //Akhir While
              ?>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
       
      </div>
    </div>
  </div>
</div>