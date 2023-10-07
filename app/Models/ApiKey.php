<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Random\Randomizer;

class ApiKey extends Model
{
    use HasFactory;

    protected $table = 'api_keys';

    private static Randomizer $rnd;

    protected $fillable = [
        'user_id',
        'key',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::$rnd = new Randomizer();
    }

    /**
     * @return string|null
     */
    public static function createApiKey(): ?string
    {
        $result = ApiKey::updateOrCreate(
            ['user_id' => Auth::user()->id],
            ['key'     => self::$rnd->shuffleBytes(bin2hex(self::$rnd->getBytes(32)))],
        );

        return $result->key;
    }

    public static function getApiKey()
    {
        return ApiKey::where('user_id', Auth::user()->id)->first();
    }
}
