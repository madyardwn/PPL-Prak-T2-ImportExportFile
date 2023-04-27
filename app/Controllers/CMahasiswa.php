<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class CMahasiswa extends BaseController
{

    protected $mahasiswa;

    public function __construct()
    {
        $this->mahasiswa = new \App\Models\MMahasiswa();
    }

    public function index()
    {
        $data = [
            'title' => 'Mahasiswa',
            'mahasiswa' => $this->mahasiswa->findAll(),
        ];
        return view('mahasiswa/index', $data);
    }

    public function upload()
    {
        $input = $this->validate(
            [
                'file' => 'uploaded[file]|max_size[file,2048]|ext_in[file,csv,xls,xlsx]',
            ]
        );

        if (!$input) {
            $data = [
                'title' => 'Mahasiswa',
                'mahasiswa' => $this->mahasiswa->findAll(),
                'validation' => $this->validator,
            ];
            return view('mahasiswa/index', $data);
        } else if ($this->request->getFile('file')->getExtension() == 'xls' || $this->request->getFile('file')->getExtension() == 'xlsx') {
            $file = $this->request->getFile('file');
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file);
            $data = $spreadsheet->getActiveSheet()->toArray();

            $count = 0;

            // $data remove first row
            array_shift($data);

            foreach ($data as $row) {
                // check if nim already exists
                $check = $this->mahasiswa->where('nim', $row[0])->first();

                if ($check) {
                    $this->mahasiswa->update(
                        $check['nim'],
                        [
                            'nama' => $row[1],
                            'ets' => number_format((float) $row[2], 2, '.', ''),
                            'eas' => number_format((float) $row[3], 2, '.', ''),
                            'final' => number_format((float) $row[2] * 0.45 + (float) $row[3] * 0.55, 2, '.', ''),
                        ]
                    );
                } else {
                    $this->mahasiswa->insert(
                        [
                            'nim' => $row[0],
                            'nama' => $row[1],
                            'ets' => number_format((float) $row[2], 2, '.', ''),
                            'eas' => number_format((float) $row[3], 2, '.', ''),
                            'final' => number_format((float) $row[2] * 0.45 + (float) $row[3] * 0.55, 2, '.', ''),
                        ]
                    );

                    $count++;
                }
            }

            session()->setFlashdata('message', $count . ' baris berahasil ditambahkan');
            session()->setFlashdata('alert-class', 'alert-success');
        } else {
            if ($file = $this->request->getFile('file')) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move('./uploads', $newName);
                    $file = fopen("./uploads/" . $newName, "r");
                    $i = 0;
                    $numberOfFields = 4;
                    $csvArr = array();

                    while (($column = fgetcsv($file, 10000, ",")) !== false) {
                        if ($i != 0) {
                            for ($j = 0; $j < $numberOfFields; $j++) {
                                $csvArr[$i - 1][$j] = $column[$j];
                            }
                        }
                        $i++;
                    }

                    fclose($file);

                    $count = 0;

                    foreach ($csvArr as $row) {
                        // check if nim already exists                        
                        $check = $this->mahasiswa->where('nim', $row[0])->first();

                        if ($check) {
                            $this->mahasiswa->update(
                                $check['nim'],
                                [
                                    'nama' => $row[1],
                                    'ets' => $row[2],
                                    'eas' => $row[3],
                                    'final' => $row[2] * 0.45 + $row[3] * 0.55,
                                ]
                            );
                        } else {
                            $this->mahasiswa->insert(
                                [
                                    'nim' => $row[0],
                                    'nama' => $row[1],
                                    'ets' => $row[2],
                                    'eas' => $row[3],
                                    'final' => $row[2] * 0.45 + $row[3] * 0.55,
                                ]
                            );

                            $count++;
                        }
                    }

                    session()->setFlashdata('message', $count . ' baris berhasil ditambahkan');
                    session()->setFlashdata('alert-class', 'alert-success');
                } else {
                    session()->setFlashdata('message', 'CSV file tidak bisa diimport.');
                    session()->setFlashdata('alert-class', 'alert-danger');
                }
            } else {
                session()->setFlashdata('message', 'CSV file tidak bisa diimport.');
                session()->setFlashdata('alert-class', 'alert-danger');
            }
        }
        return redirect()->to(base_url('mahasiswa'));
    }

    public function clear()
    {
        $this->mahasiswa->truncate();
        session()->setFlashdata('message', 'Data berhasil dihapus.');
        session()->setFlashdata('alert-class', 'alert-success');
        return redirect()->to(base_url('mahasiswa'));
    }

    public function exportExcel()
    {
        $mahasiswa = $this->mahasiswa->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NIM');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'ETS');
        $sheet->setCellValue('D1', 'EAS');
        $sheet->setCellValue('E1', 'Final');

        $i = 2;
        foreach ($mahasiswa as $row) {
            $sheet->setCellValue('A' . $i, $row['nim']);
            $sheet->setCellValue('B' . $i, $row['nama']);
            $sheet->setCellValue('C' . $i, $row['ets']);
            $sheet->setCellValue('D' . $i, $row['eas']);
            $sheet->setCellValue('E' . $i, $row['final']);
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'mahasiswa-data';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function exportPdf()
    {
        // create function export pdf
        $mahasiswa = $this->mahasiswa->findAll();

        // create html
        $html = '
                <div style="margin: auto; text-align: center;">
                    <img src="data:image/png;base64,' . base64_encode(file_get_contents('img/logo.png')) . '" style="width: 50px; height: 75px;">
                    <h1 style="text-align: center;">Data Mahasiswa</h1>
                </div>
            ';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="margin: auto;">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>NIM</th>';
        $html .= '<th>Nama</th>';
        $html .= '<th>ETS</th>';
        $html .= '<th>EAS</th>';
        $html .= '<th>Final</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($mahasiswa as $row) {
            $html .= '<tr>';
            $html .= '<td>' . $row['nim'] . '</td>';
            $html .= '<td>' . $row['nama'] . '</td>';
            $html .= '<td>' . $row['ets'] . '</td>';
            $html .= '<td>' . $row['eas'] . '</td>';
            $html .= '<td>' . $row['final'] . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);


        $dompdf->loadHtml(view('mahasiswa/print', ['mahasiswa' => $mahasiswa]));

        $dompdf->setPaper('A4', 'landscape');

        $dompdf->render();

        $dompdf->stream('mahasiswa-data.pdf', ['Attachment' => false]);
    }
}
