<?php

namespace App\Services\Helper;

use App\Services\ModalIndexQuey;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class PdfGenerator
{
    /**
     * Generate pdf from view and save it to public disk and sent the path
     *
     * @return string
     */
    public static function generate(Builder $query, string $view_template, string $filename, array $options = [])
    {

        $query = ModalIndexQuey::globalQuery($query);

        $data = $query->get();

        $file_path = "reports/$filename";

        $pdf = Pdf::loadView($view_template, [
            ...$options,
            'data' => $data,
        ]);

        Storage::disk('report')->put($file_path, $pdf->output());

        return "/$file_path";
    }

    /**
     * Generate pdf from view and save it to public disk and sent the path
     *
     * @return string
     */
    public static function makeInvoice(string $view_template, array $data, string $filename)
    {
        $pdf = Pdf::loadView($view_template, $data);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 120,
            'defaultMediaType' => 'screen',
            'isFontSubsettingEnabled' => true,
            'debugKeepTemp' => true,
            'chroot' => public_path(),
        ]);

        $storage = Storage::disk('invoices');

        if ($storage->exists($filename)) {
            $storage->makeDirectory($filename);
        }

        $storage->put($filename, $pdf->output());

        return "$filename";
    }
}
