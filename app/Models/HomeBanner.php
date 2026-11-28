<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
    public const SECTIONS = [
        'sell_bulk' => 'Sell bulk scrap',
        'scrap_vehicle' => 'Scrap your vehicle',
        'offers' => 'Offers',
    ];

    /** What a tap on the banner does. */
    public const LINKS = [
        'none' => 'Nothing (image only)',
        'bulk_enquiry' => 'Open the enquiry form (bulk enquiry)',
        'pickup' => 'Open the pickup booking',
        'vehicle' => 'Open the vehicle form (scrap vehicle)',
        'url' => 'Open a web link',
    ];

    protected $fillable = ['section_key', 'title', 'subtitle', 'image', 'link_type', 'link_value', 'is_active', 'sort'];

    protected $casts = ['is_active' => 'boolean'];
}
