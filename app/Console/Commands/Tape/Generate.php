<?php

declare(strict_types=1);

namespace App\Console\Commands\Tape;

use App\Groups\Tracks\Track;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;

class Generate extends Command
{
    protected $signature = 'tape:generate {id} {title}';

    protected $description = 'Generate tapes from the favorite tracks';

    public function handle(Track $track, Carbon $carbon, Filesystem $file): int
    {
        /** @var Application $app */
        $app = $this->laravel;

        $id = $this->argument('id');
        $title = $this->argument('title');

        $tracks = $track->favorites()
            ->with('provider')
            ->latest()
            ->get()
            ->toArray();

        $aspectRatio = fn (string $provider) =>
            'Bandcamp' === $provider || 'SoundCloud' === $provider
                ? '1/1'
                : '16/9';

        $items = [];
        foreach ($tracks as $i => $item) {
            $items[$i]['title'] = $item['title'];
            $items[$i]['provider'] = $item['provider']['name'];
            $items[$i]['provider_key'] = $item['provider_key'];
            $items[$i]['image'] = $item['image'];
            $items[$i]['image_aspect_ratio'] = $aspectRatio($item['provider']['name']);
            $items[$i]['embed_aspect_ratio'] = $aspectRatio($item['provider']['name']);
            $items[$i]['slug'] = Str::slug($item['title']);
        }

        $data['id'] = (int)$id;
        $data['title'] = $title;
        $data['path'] = '/'.$carbon->format('Y').'/'.$carbon->format('m').'/'.Str::slug($title);
        $data['date'] = $carbon->format('M d, Y');
        $data['items'] = $items;

        $data = json_encode(
            $data,
            JSON_PRETTY_PRINT |
            JSON_UNESCAPED_SLASHES |
            JSON_UNESCAPED_UNICODE
        );

        if (false !== $data) {
            $data = preg_replace_callback('/^\s+/m', function ($matches) {
                return str_repeat(' ', (int)(strlen($matches[0]) / 2));
            }, $data);

            assert(is_string($data));

            $tapesPath = $app->storagePath('app/tapes');
            if (!file_exists($tapesPath)) {
                $file->makeDirectory($tapesPath);
            }

            $filename = $tapesPath.'/'.Str::slug($title).'.json';
            $file->put($filename, $data);

            $this->info('Generated: '.$filename);

            return self::SUCCESS;
        }

        $this->error('Failed to generate tape.');

        return self::FAILURE;
    }
}
