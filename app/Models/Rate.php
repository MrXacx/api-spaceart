<?php

namespace App\Models;

use App\Exceptions\CheckDBOperationException;
use App\Models\Traits\HasHiddenTimestamps;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;
use Thiagoprz\CompositeKey\HasCompositeKey;

class Rate extends Model
{
    use HasCompositeKey, HasFactory, HasHiddenTimestamps {
        HasHiddenTimestamps::__construct as hideTimestamps;
    }

    protected $primaryKey = ['author_id', 'agreement_id'];

    public $incrementing = false;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->hideTimestamps();
    }

    protected $fillable = [
        'author_id',
        'rated_id',
        'agreement_id',
        'score',
        'note',
    ];

    protected $hidden = [
        'author_id',
        'rated_id',
        'agreement_id',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function rated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_id');
    }

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(Agreement::class, 'agreement_id');
    }

    public function withAllRelations(): Rate
    {
        return $this->load('author', 'rated', 'agreement');
    }

    /**
     * @throws ValidationException
     * @throws CheckDBOperationException
     */
    public function save(array $options = []): bool
    {
        throw_unless($this->author->active, new CheckDBOperationException("The author's account $this->author_id is disabled"));

        throw_unless(
            Carbon::now()
                ->isBefore($this->agreement->getActiveInterval()['end_moment']),
            new CheckDBOperationException("The agreement $this->agreement_id is not finished"));

        return parent::save($options);
    }
}
