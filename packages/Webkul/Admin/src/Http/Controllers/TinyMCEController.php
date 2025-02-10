<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class TinyMCEController extends Controller
{
    /**
     * Storage folder path.
     *
     * @var string
     */
    private $storagePath = 'tinymce';

    /**
     * Upload file from tinymce.
     *
     * @return void
     */
    public function upload()
    {
        $media = $this->storeMedia();

        if (! empty($media)) {
            return response()->json([
                'location' => $media['file_url'],
            ]);
        }

        return response()->json([]);
    }

    /**
     * Store media.
     *
     * @return array
     */
    public function storeMedia()
    {
        if (!request()->hasFile('file')) {
            return [];
        }

        $file = request()->file('file');

        // Obtendo o ID do projeto do banco monitorado e formatando com zeros à esquerda
        $projectId = str_pad(optional($GLOBALS['dbMonitor'])->id ?? 0, 7, '0', STR_PAD_LEFT);

        // Definindo o caminho com base no ID do projeto
        $path = $file->store("projects/{$projectId}/uploads");

        return [
            'file'      => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_url'  => Storage::url($path),
        ];
    }
}
