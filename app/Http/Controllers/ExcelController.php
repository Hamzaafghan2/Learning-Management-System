<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Permission;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class ExcelController extends Controller
{
    // Export permissions to Excel
    public function exportPermissions()
    {
        $permissions = Permission::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Group Name');

        // Fill data
        $row = 2;
        foreach ($permissions as $permission) {
            $sheet->setCellValue('A'.$row, $permission->id);
            $sheet->setCellValue('B'.$row, $permission->name);
            $sheet->setCellValue('C'.$row, $permission->group_name);
            $row++;
        }

        $fileName = 'permissions.xlsx';
        $writer = new Xlsx($spreadsheet);

        // Download directly
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $fileName .'"');
        $writer->save('php://output');
        exit;
    }

    // Import permissions from Excel
    public function importPermissions(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        // Skip header row
        foreach ($sheetData as $index => $row) {
            if ($index === 0) continue;

            Permission::updateOrCreate(
                ['id' => $row[0]], // use id if you want to update existing
                [
                    'name' => $row[1],
                    'group_name' => $row[2],
                ]
            );
        }

        return redirect()->back()->with([
            'message' => 'Permissions imported successfully!',
            'alert-type' => 'success'
        ]);
    }
}
