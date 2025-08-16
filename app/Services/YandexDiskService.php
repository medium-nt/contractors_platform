<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;
use ZipArchive;

class YandexDiskService {
    public function __construct(string $token)
    {
    }

    private static function getToken(): string
    {
        $token = config('services.yandex.token');

        if (!$token) {
            throw new \RuntimeException('Yandex token is not set in config/services.php or .env');
        }

        return $token;
    }

    private static function getCertPath(): string
    {
        return storage_path('certs/cacert.pem');
    }

    public static function ensureDirectoryExists(string $path): void
    {
        $directory = dirname($path);
        if ($directory === '.' || $directory === '/') {
            return;
        }

        $parts = explode('/', trim($directory, '/'));
        $currentPath = '';

        foreach ($parts as $part) {
            $currentPath .= '/' . $part;
            $currentPath = ltrim($currentPath, '/');

            $response = Http::withHeaders(['Authorization' => 'OAuth ' . self::getToken()])
                ->withOptions([
                    'verify' => self::getCertPath(),
                ])
                ->send('PUT', 'https://cloud-api.yandex.net/v1/disk/resources', [
                    'query' => [
                        'path' => $currentPath,
                    ],
                ]);

            // Игнорируем 409 — папка уже существует
            if (!in_array($response->status(), [201, 409])) {
                throw new \Exception("Не удалось создать папку: $currentPath");
            }
        }
    }

    public static function listFiles($path)
    {
        try {
            $url = 'https://cloud-api.yandex.net/v1/disk/resources?' . http_build_query([
                'fields' => '_embedded.items.name,_embedded.items.type,_embedded.items.media_type,_embedded.items.modified,_embedded.items.size',
                'sort' => 'name',
                'limit' => 1000,
                'path' => $path,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'OAuth ' . self::getToken(),
            ])
                ->withOptions([
                    'verify' => self::getCertPath(),
                ])
                ->get($url)
                ->throw();

            $data = $response->json();

            $res = $data['_embedded']['items'] ?? [];

        } catch (Throwable $e) {
            Log::channel('yandex_disk')
                ->error('Ошибка при обращении к Yandex Disk API', [
                    'message' => $e->getMessage(),
                    'url' => $path,
                ]);

            $res = [];
        }

        return $res;
    }

    public static function write(string $path, string $contents): void
    {
        self::ensureDirectoryExists($path);

        $uploadResponse = Http::withHeaders(['Authorization' => 'OAuth ' . self::getToken()])
            ->withOptions([
                'verify' => self::getCertPath(),
            ])
            ->get('https://cloud-api.yandex.net/v1/disk/resources/upload?path=' . urlencode(ltrim($path, '/')) . '&overwrite=true');

        $uploadUrl = $uploadResponse->json()['href'] ?? null;

        if (!$uploadUrl) {
            throw new \Exception("Не удалось получить ссылку для загрузки файла: $path");
        }

        Http::withOptions([
            'verify' => self::getCertPath(),
        ])->withBody($contents, 'application/octet-stream')
            ->put($uploadUrl);
    }

    public static function read(string $path): string
    {
        $response = Http::withHeaders(['Authorization' => 'OAuth ' . self::getToken()])
            ->withOptions(['verify' => self::getCertPath()])
            ->get('https://cloud-api.yandex.net/v1/disk/resources/download', [
                'path' => $path,
            ]);

        $downloadUrl = $response->json()['href'] ?? null;

        if (!$downloadUrl) {
            throw new \Exception("Не удалось получить ссылку на скачивание файла: $path");
        }

        $fileResponse = Http::withOptions([
            'verify' => self::getCertPath(),
        ])->get($downloadUrl);

        return $fileResponse->body();
    }

    public static function deleteFile($path): bool
    {
        $response = Http::withHeaders(['Authorization' => 'OAuth ' . self::getToken()])
            ->withOptions([
                'verify' => self::getCertPath(),
            ])
            ->delete('https://cloud-api.yandex.net/v1/disk/resources?path=' . urlencode($path) . '&permanently=true');

        $ans = false;

        $http_code = $response->status();
        if ($http_code == 204 || $http_code == 202) {
            $ans = true;
        }

        return $ans;
    }

    public static function downloadArchive($order, $folder): BinaryFileResponse|false
    {
        $folderPath = '/alexstud/orders/' . $order->id . '/' . $folder;
        $listFiles = self::listFiles($folderPath);

        $filesToAdd = array_filter($listFiles, fn($f) => $f['type'] === 'file');
        if (empty($filesToAdd)) {
            return false;
        }

        $zip = new ZipArchive;
        $zipPath = storage_path("app/temp/order_{$order->id}.zip");

        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0777, true);
        }

        $result = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($result !== true) {
            return false;
        }

        foreach ($listFiles as $file) {
            if ($file['type'] !== 'file') continue;

            $filePath = $folderPath . '/' . $file['name'];
            $content = YandexDiskService::read($filePath);
            $zip->addFromString($file['name'], $content);
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public static function createFolder(string $path): bool
    {
        self::ensureDirectoryExists($path);

        $response = Http::withHeaders([
            'Authorization' => 'OAuth ' . self::getToken(),
        ])->withOptions([
            'verify' => self::getCertPath(),
        ])->send('PUT', 'https://cloud-api.yandex.net/v1/disk/resources?path=' . urlencode(ltrim($path, '/')));

        if ($response->status() == 201) {
            return true;
        }

        Log::channel('yandex_disk')
            ->info('Папка не создалась', [
                'path' => $path,
                'response' => $response->json(),
            ]);

        return false;
    }

    public static function publishYandexDiskResource(string $path): bool
    {
        $response = Http::withHeaders([
            'Authorization' => 'OAuth ' . self::getToken(),
        ])->withOptions([
            'verify' => self::getCertPath(),
        ])->send('PUT', 'https://cloud-api.yandex.net/v1/disk/resources/publish?path=' . urlencode(ltrim($path, '/')));

        return $response->successful();
    }

    public static function getYandexDiskPublicUrl(string $path): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => 'OAuth ' . self::getToken(),
        ])->withOptions([
            'verify' => self::getCertPath(),
        ])->get('https://cloud-api.yandex.net/v1/disk/resources?path=' . urlencode(ltrim($path, '/')));

        if ($response->successful()) {
            return $response->json('public_url');
        }

        return null;
    }

}
