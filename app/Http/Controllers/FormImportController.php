<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dossier;
use App\Models\FormsData;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class FormImportController extends Controller
{
    public function show()
    {
        return view('forms.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('excel');
        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();

        $headers = $worksheet->rangeToArray('A1:' . $worksheet->getHighestColumn() . '1')[0];

        // Lire les données formatées pour conserver les dates au format FR (15/05/2025)
        $rows = [];

        foreach ($worksheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
        
            $formattedRow = [];
        
            foreach ($cellIterator as $cell) {
                $column = $cell->getColumn();
                $value = $cell->getValue();
        
                // Vérifie si la cellule est une date Excel
                if (is_numeric($value) && \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                    $date = ExcelDate::excelToDateTimeObject($value);
                    $formattedRow[$column] = $date->format('d/m/Y'); // 🇫🇷 format français
                } else {
                    $formattedRow[$column] = $this->cleanValue(trim($cell->getFormattedValue()));
                }
            }
        
            $rows[$row->getRowIndex()] = $formattedRow;
        }
        unset($rows[1]); // Supprimer l'en-tête

        foreach ($rows as $row) {
            $referenceUnique = $row['A'] ?? null;

            if (!$referenceUnique) {
                continue;
            }

            $dossier = Dossier::where('reference_unique', $referenceUnique)->first();

            if (!$dossier) {
                Log::warning("Dossier non trouvé pour la référence : {$referenceUnique}");
                continue;
            }

            foreach ($headers as $index => $metaKey) {
                if ($index === 0 || $metaKey === 'reference_unique') {
                    continue;
                }

                $columnLetter = chr(65 + $index);
                $metaValue = $row[$columnLetter] ?? null;

                if (!is_null($metaValue)) {
                    FormsData::updateOrCreate(
                        [
                            'dossier_id' => $dossier->id,
                            'form_id'    => 80,
                            'meta_key'   => $metaKey,
                        ],
                        [
                            'meta_value' => $metaValue,
                        ]
                    );
                }
            }
        }

        return redirect()
            ->route('forms.import.form')
            ->with('success', 'Importation terminée avec succès.');
    }


    private function cleanValue(?string $value): ?string
{
    if (is_null($value)) {
        return null;
    }

    // Supprimer les espaces, € et autres symboles non numériques (sauf , et .)
    $value = str_replace(['€', ' ', "\u{00A0}"], '', $value); // supprime espaces normaux et insécables

    // Supprimer les séparateurs de milliers (points ou virgules) s’ils sont mal utilisés
    // Ex : "42,182.12" devient "42182.12"
    $value = preg_replace('/(?<=\d)[,.](?=\d{3}\b)/', '', $value);

    // Si le séparateur décimal est un point, remplace-le par une virgule
    if (preg_match('/\.\d{1,2}$/', $value)) {
        $value = str_replace('.', ',', $value);
    }

    return $value;
}

}
