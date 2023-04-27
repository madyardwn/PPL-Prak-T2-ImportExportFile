<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?php echo $title; ?></title>
  <meta name="description" content="The tiny framework with powerful features">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .container {
      max-width: 500px;
    }
  </style>
</head>

<body>
  <div class="container mt-5">
    <div class="card">
      <div class="card-header text-center">
        <img src="/img/logo.png" alt="Logo" style="width: 50px; height: 75px;">
        <strong>Upload CSV/EXCEL File</strong>
        <div class="mt-2">
          <?php if (session()->has('message')) { ?>
            <div class="alert <?php echo session()->getFlashdata('alert-class') ?>">
                <?php echo session()->getFlashdata('message') ?>
            </div>
          <?php } ?>
          <?php $validation = \Config\Services::validation(); ?>
        </div>
        <form action="<?php echo base_url('mahasiswa/upload'); ?>" method="post" enctype="multipart/form-data">
          <div class="form-group mb-3">
            <div class="mb-3">
              <input type="file" name="file" class="form-control" id="file">
            </div>
          </div>
          <div class="d-grid">
            <input type="submit" name="submit" value="Upload" class="btn btn-dark" />
          </div>
        </form>
        <!-- <div class="d-grid mt-3"> -->
        <!--   <a href="<?php echo base_url('mahasiswa/export-excel'); ?>" class="btn btn-dark">Export Excel</a><br> -->
        <!--   <a href="<?php echo base_url('mahasiswa/export-pdf'); ?>" class="btn btn-dark">Export PDF</a> -->
        <!-- </div> -->
        <div class="row mt-3">
          <div class="col-md-6 mb-2">
            <a href="<?php echo base_url('mahasiswa/export-excel'); ?>" class="btn btn-dark">Export Excel</a><br>
          </div>
          <div class="col-md-6 mb-2">
            <a href="<?php echo base_url('mahasiswa/export-pdf'); ?>" class="btn btn-dark">Export PDF</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th scope="col">NIM</th>
              <th scope="col">Nama</th>
              <th scope="col">ETS</th>
              <th scope="col">EAS</th>
              <th scope="col">Final</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($mahasiswa as $mhs) { ?>
              <tr>
                <td><?php echo $mhs['nim']; ?></td>
                <td><?php echo $mhs['nama']; ?></td>
                <td><?php echo $mhs['ets']; ?></td>
                <td><?php echo $mhs['eas']; ?></td>
                <td><?php echo $mhs['final']; ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="card-footer text-center">
        <a href="<?php echo base_url('mahasiswa/clear'); ?>" class="btn btn-dark">Bersihkan</a>
      </div>
    </div>
  </div>
</body>

</html>
