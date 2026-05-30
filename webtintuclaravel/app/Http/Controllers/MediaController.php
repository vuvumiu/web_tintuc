<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MediaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $baseDir = public_path('responsive_filemanager/source');
        $currentDir = $request->input('folder', '');

        $safeDir = ltrim(str_replace(['..', "\0"], '', $currentDir), '/');
        $fullPath = $baseDir . ($safeDir ? '/' . $safeDir : '');

        if (!is_dir($fullPath)) {
            $fullPath = $baseDir;
            $safeDir = '';
        }

        $items = [];
        if (is_dir($fullPath)) {
            $entries = File::directories($fullPath);
            foreach ($entries as $dir) {
                $items[] = [
                    'type'    => 'folder',
                    'name'    => basename($dir),
                    'path'    => ($safeDir ? $safeDir . '/' : '') . basename($dir),
                    'updated' => date('d/m/Y H:i', filemtime($dir)),
                ];
            }

            $files = File::files($fullPath);
            foreach ($files as $file) {
                $ext = strtolower($file->getExtension());
                $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
                $items[] = [
                    'type'    => in_array($ext, $imageExts) ? 'image' : 'file',
                    'name'    => $file->getFilename(),
                    'path'    => ($safeDir ? $safeDir . '/' : '') . $file->getFilename(),
                    'size'    => $this->formatBytes($file->getSize()),
                    'updated' => date('d/m/Y H:i', filemtime($file)),
                    'url'     => asset('responsive_filemanager/source/' . ($safeDir ? $safeDir . '/' : '') . $file->getFilename()),
                ];
            }
        }

        $parentFolder = $safeDir ? dirname($safeDir) : null;

        return view('back.media.index', [
            'items'        => $items,
            'currentDir'  => $safeDir,
            'parentFolder'=> $parentFolder !== '.' ? $parentFolder : null,
        ]);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
