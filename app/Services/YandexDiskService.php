<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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

    private static function ensureDirectoryExists(string $path): void
    {
        $directory = dirname($path);
        if ($directory === '.' || $directory === '/') {
            return;
        }

        $parts = explode('/', trim($directory, '/'));
        $currentPath = 'alexstud';

        foreach ($parts as $part) {
            $currentPath .= '/' . $part;

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
            ->get($url);

        $data = $response->json();

        return $data['_embedded']['items'] ?? [];
    }

    public static function write(string $path, string $contents): void
    {
        self::ensureDirectoryExists($path);

        $uploadResponse = Http::withHeaders(['Authorization' => 'OAuth ' . self::getToken()])
            ->withOptions([
                'verify' => self::getCertPath(),
            ])
            ->get('https://cloud-api.yandex.net/v1/disk/resources/upload?path=' . urlencode('alexstud/' . ltrim($path, '/')) . '&overwrite=true');

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

}
