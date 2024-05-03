<?php

declare(strict_types=1);

namespace Tests\Console;

use App\Groups\MusicProviders\MusicProviderFactory;
use App\Groups\Tracks\TrackFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

class TapeGenerateCommandTest extends TestCase
{
    use RefreshDatabase;

    private MusicProviderFactory $providerFactory;
    private TrackFactory $trackFactory;
    private Carbon $carbon;
    private Filesystem $file;
    private string $tapePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->providerFactory = new MusicProviderFactory();
        $this->trackFactory = new TrackFactory();
        $this->carbon = new Carbon();
        $this->file = $this->app->make(Filesystem::class);
        $this->tapePath = $this->app->storagePath('app/tapes/test-tape.json');
    }

    protected function tearDown(): void
    {
        if ($this->file->exists($this->tapePath)) {
            $this->file->delete($this->tapePath);
        }

        parent::tearDown();
    }

    public function testTapeGenerateCommand(): void
    {
        $this->providerFactory
            ->count(4)
            ->state(new Sequence(
                ['name' => 'Bandcamp'],
                ['name' => 'SoundCloud'],
                ['name' => 'Vimeo'],
                ['name' => 'YouTube'],
            ))
            ->create();

        $this->trackFactory
            ->createOne([
                'provider_id' => 1,
                'provider_key' => 'key1',
                'title' => 'Foo1 Bar1',
                'image' => 'image1',
                'urge' => true,
            ]);

        $this->trackFactory
            ->createOne([
                'provider_id' => 1,
                'provider_key' => 'key2',
                'title' => 'Foo2 Bar2',
                'image' => 'image2',
                'urge' => false,
            ]);

        $this->trackFactory
            ->createOne([
                'provider_id' => 2,
                'provider_key' => 'key3',
                'title' => 'Foo3 Bar3',
                'image' => 'image3',
                'urge' => true,
            ]);

        $this->trackFactory
            ->createOne([
                'provider_id' => 2,
                'provider_key' => 'key4',
                'title' => 'Foo4 Bar4',
                'image' => 'image4',
                'urge' => false,
            ]);

        $this->trackFactory
            ->createOne([
                'provider_id' => 3,
                'provider_key' => 'key5',
                'title' => 'Foo5 Bar5',
                'image' => 'image5',
                'urge' => true,
            ]);

        $this->trackFactory
            ->createOne([
                'provider_id' => 4,
                'provider_key' => 'key6',
                'title' => 'Foo6 Bar6',
                'image' => 'image6',
                'urge' => true,
            ]);

        /** @var PendingCommand $command */
        $command = $this->artisan('tape:generate 777 "Test / Tape"');

        $command->expectsOutput('Generated: '.$this->tapePath)
            ->assertSuccessful()
            ->execute();

        $this->assertFileExists($this->tapePath);

        /** @var string $tape */
        $tape = file_get_contents($this->tapePath);

        $tape = json_decode($tape);
        $date = $this->carbon::now();

        $this->assertCount(5, get_object_vars($tape));
        $this->assertSame(777, $tape->id);
        $this->assertSame('Test / Tape', $tape->title);
        $this->assertSame('/'.$date->format('Y').'/'.$date->format('m').'/test-tape', $tape->path);
        $this->assertSame($date->format('M d, Y'), $tape->date);
        $this->assertCount(4, $tape->items);

        $item1 = $tape->items[0];
        $this->assertCount(7, get_object_vars($item1));
        $this->assertSame('Foo1 Bar1', $item1->title);
        $this->assertSame('Bandcamp', $item1->provider);
        $this->assertSame('key1', $item1->provider_key);
        $this->assertSame('image1', $item1->image);
        $this->assertSame('1/1', $item1->image_aspect_ratio);
        $this->assertSame('1/1', $item1->embed_aspect_ratio);
        $this->assertSame('foo1-bar1', $item1->slug);

        $item2 = $tape->items[1];
        $this->assertCount(7, get_object_vars($item2));
        $this->assertSame('Foo3 Bar3', $item2->title);
        $this->assertSame('SoundCloud', $item2->provider);
        $this->assertSame('key3', $item2->provider_key);
        $this->assertSame('image3', $item2->image);
        $this->assertSame('1/1', $item1->image_aspect_ratio);
        $this->assertSame('1/1', $item1->embed_aspect_ratio);
        $this->assertSame('foo3-bar3', $item2->slug);

        $item3 = $tape->items[2];
        $this->assertCount(7, get_object_vars($item3));
        $this->assertSame('Foo5 Bar5', $item3->title);
        $this->assertSame('Vimeo', $item3->provider);
        $this->assertSame('key5', $item3->provider_key);
        $this->assertSame('image5', $item3->image);
        $this->assertSame('16/9', $item3->image_aspect_ratio);
        $this->assertSame('16/9', $item3->embed_aspect_ratio);
        $this->assertSame('foo5-bar5', $item3->slug);

        $item4 = $tape->items[3];
        $this->assertCount(7, get_object_vars($item4));
        $this->assertSame('Foo6 Bar6', $item4->title);
        $this->assertSame('YouTube', $item4->provider);
        $this->assertSame('key6', $item4->provider_key);
        $this->assertSame('image6', $item4->image);
        $this->assertSame('16/9', $item4->image_aspect_ratio);
        $this->assertSame('16/9', $item4->embed_aspect_ratio);
        $this->assertSame('foo6-bar6', $item4->slug);
    }
}
