// create html for pdf for organization

<html>

<head>
  <title>Export PDF</title>
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
    }

    table,
    th,
    td {
      border: 1px solid black;
      padding: 5px;
    }
  </style>

</head>

<body>
  <img src="data:image/png;base64,<?php echo base64_encode(file_get_contents('img/logo.png')); ?>" alt="Logo" style="width: 50px; height: 75px;">
  <h1 style="text-align: center;">Politeknik Negeri Bandung</h1>
  <h2 style="text-align: center;">Fakultas Teknik Komputer dan Informatika</h2>
  <h3 style="text-align: center;">Data Mahasiswa</h3>
  <table border="1" cellpadding="5" cellspacing="0" style="margin: auto;">
    <thead>
      <tr>
        <th>NIM</th>
        <th>Nama</th>
        <th>ETS</th>
        <th>EAS</th>
        <th>Final</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($mahasiswa as $row) : ?>
        <tr>
          <td><?php echo $row['nim']; ?></td>
          <td><?php echo $row['nama']; ?></td>
          <td><?php echo $row['ets']; ?></td>
          <td><?php echo $row['eas']; ?></td>
          <td><?php echo $row['final']; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>

</html>
