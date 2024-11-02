<?php

declare(strict_types=1);

namespace App\Groups\Playlists;

use App\Groups\MusicProviders\MusicProvider;
use App\Scopes\SortableScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $url
 * @property int $provider_id
 * @property string $provider_key
 * @property string $title
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read MusicProvider $provider
 *
 * @mixin Builder<self>
 */
class Playlist extends Model
{
    use PlaylistScope;
    use SortableScope;

    /**
     * @return BelongsTo<MusicProvider, $this>
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(
            related: MusicProvider::class,
            foreignKey: 'provider_id',
        );
    }
}
