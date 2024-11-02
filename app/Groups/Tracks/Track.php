<?php

declare(strict_types=1);

namespace App\Groups\Tracks;

use App\Groups\MusicProviders\MusicProvider;
use App\Groups\TrackGenres\TrackGenre;
use App\Scopes\SortableScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $url
 * @property int $provider_id
 * @property string $provider_key
 * @property string $title
 * @property string $image
 * @property bool $urge
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read MusicProvider $provider
 * @property-read array<int, TrackGenre> $genres
 *
 * @mixin Builder<self>
 */
class Track extends Model
{
    use SortableScope;
    use TrackScope;

    protected $casts = [
        'urge' => 'boolean',
    ];

    private const URGE_LIMIT = 6;

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

    /**
     * @return BelongsToMany<TrackGenre, $this>
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(
            related: TrackGenre::class,
            table: 'genre_track',
            relatedPivotKey: 'genre_id',
        );
    }

    /**
     * @return array<int, string>
     */
    public function getMinimalGenres(int $limit): array
    {
        $pivot = $this->genres();

        $pivotTable = $pivot->getTable();
        $relatedTable = $pivot->getRelated()->getTable();

        $genres = $this::query()
            ->select($relatedTable.'.name')
            ->selectRaw('count(*) as aggregate')
            ->from($pivotTable)
            ->join(
                $relatedTable,
                $pivotTable.'.'.$pivot->getRelatedPivotKeyName(),
                '=',
                $relatedTable.'.'.$pivot->getRelatedKeyName()
            )
            ->groupBy($pivotTable.'.'.$pivot->getRelatedPivotKeyName())
            ->orderBy('aggregate', 'desc')
            ->limit($limit)
            ->pluck($relatedTable.'.name')
            ->toArray();

        sort($genres, SORT_STRING);

        return $genres;
    }

    public function toggleUrge(Track $track): bool
    {
        if (
            false === $track->urge &&
            self::URGE_LIMIT <= $this->favorites()->count()
        ) {
            return false;
        }

        $track->urge = !$track->urge;

        return $track->save();
    }

    public function stopUrges(): void
    {
        $this->favorites()->update(['urge' => false]);
    }
}
