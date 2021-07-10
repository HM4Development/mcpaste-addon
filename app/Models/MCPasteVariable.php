<?php

namespace Pterodactyl\Models;

class MCPasteVariable extends Model
{
    /**
     * The resource name for this model when it is transformed into an
     * API representation using fractal.
     */
    public const RESOURCE_NAME = 'mcpaste_variables';

    /** @var string */
    protected $table = 'mcpaste_variables';

    protected $primaryKey = 'name';

    protected $fillable = ['value'];

    public $timestamps = false;

    /** @var string[] */
    public static $validationRules = [
        'name' => 'required|string',
        'value' => 'nullable|string',
    ];
}
