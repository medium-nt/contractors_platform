<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\YandexDiskService;
use Illuminate\Http\Request;

class DiskController extends Controller
{
    public function index()
    {
        $folder = rtrim($_GET['folder'] ?? '', '/');
        $path = self::getPath($folder) . $folder;
        $dirname = dirname($folder);

        if (!self::hasAccessFolder($folder)){
            return redirect()
                ->route('disk.index')
                ->with('error', 'У вас нет доступа к этой папке.');
        }

        if (isset($_GET['is_file']) && $_GET['is_file'] == 1){
            return OrderService::downloadFile($path);
        }

        return view('disk.index', [
            'title' => 'Персональный диск',
            'link' => ($folder == '') ? '' : $folder . '/',
            'backLink' => ($dirname == '/' || $dirname == '.' ) ? '' : $dirname,
            'resources' => YandexDiskService::listFiles($path),
            'allFolders' => 'personal_disk',
            'myFolderLink' => ($folder == '') ? 'Мой диск/' . auth()->user()->id : false,
        ]);
    }

    public function uploadFile(Request $request)
    {
        $folder = $_GET['folder'] ?? '';
        $path = self::getPath($_GET['folder']);

        if(!self::hasAccessToUpload($path . $folder)){
            return redirect()
                ->route('disk.index')
                ->with('error', 'У вас нет доступа к загрузке в эту папку.');
        }

        foreach ($request->file('files') as $file) {
            $filePath = $folder . $file->getClientOriginalName();
            YandexDiskService::write($path . $filePath, file_get_contents($file));
        }

        return redirect()
            ->route('disk.index', ['folder' => $folder])
            ->with('success', 'Файлы загружены.');
    }

    function hasAccessFolder($folder): bool
    {
        if (auth()->user()->role->name === 'admin' || !str_contains($folder, 'personal_disk')) {
            return true;
        }

        $parts = explode('/', trim($folder, '/'));

        return (isset($parts[1]) && $parts[1] == auth()->user()->id);
    }

    function hasAccessToUpload($folder): bool
    {
        if (auth()->user()->role->name === 'admin') {
            return true;
        }

        if (str_contains($folder, 'alexstud/personal_disk/'.auth()->user()->id . '/')) {
            $parts = explode('/', trim($folder, '/'));

            return (isset($parts[2]) && $parts[2] == auth()->user()->id);
        }

        return false;
    }

    public function deleteFile()
    {
        $folder = $_GET['folder'] ?? '';
        $path = self::getPath($folder);

        if(!self::hasAccessToUpload($path . $folder)){
            return redirect()
                ->route('disk.index')
                ->with('error', 'У вас нет доступа к удалению из этой папки.');
        }

        YandexDiskService::deleteFile($path . $folder);
        sleep(1);

        if (dirname($folder) == '/' || dirname($folder) == '.') {
            $redirectFolder = '';
        } else {
            $redirectFolder = dirname($folder);
        }

        return redirect()
            ->route('disk.index', ['folder' => $redirectFolder])
            ->with('success', 'Удалено.');
    }

    public function createFolder()
    {
        $path = self::getPath($_GET['folder']);

        $result = YandexDiskService::createFolder($path . $_GET['folder'] . $_POST['folder_name']);

        if (!$result) {
            return redirect()
                ->route('disk.index')
                ->with('error', 'Папка не создана.');
        }

        sleep(1);

        return redirect()
            ->route('disk.index', ['folder' => $_GET['folder']])
            ->with('success', 'Папка создана.');
    }

    private static function getPath($folder)
    {
        $path = config('app.personal_disk.folder') . auth()->user()->id . '/';

        if (auth()->user()->role->name === 'admin') {
            $parts = explode('/', trim($folder, '/'));

            if (isset($parts[0]) && $parts[0] == 'personal_disk') {
                $path = '/alexstud/';
            } else {
                $path = config('app.personal_disk.folder') . auth()->user()->id . '/';
            }
        }

        return $path;
    }

    public function getYandexDiskPublicUrl()
    {
        $folder = $_GET['folder'] ?? '';
        $path = self::getPath($folder) . $folder;

        $result = YandexDiskService::publishYandexDiskResource($path);

        if(!$result) {
            return redirect()
                ->route('disk.index')
                ->with('error', 'ошибка в публикации ссылки');
        }

        $publicUrl = YandexDiskService::getYandexDiskPublicUrl($path);

        if (dirname($folder) == '/' || dirname($folder) == '.') {
            $redirectFolder = '';
        } else {
            $redirectFolder = dirname($folder);
        }

        return redirect()
            ->route('disk.index', ['folder' => $redirectFolder])
            ->with('success', 'Ссылка опубликована.')
            ->with('publicUrl', $publicUrl);
    }

}
