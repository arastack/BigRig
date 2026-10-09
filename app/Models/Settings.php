<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_name',
        'business_slogan',
        'commence_year',
        'business_description',
        'keywords',
        'business_email',
        'secondary_email',
        'business_contact',
        'secondary_contact',
        'whatsapp_support',
        'telegram_support',
        'dark_theme_logo',
        'light_theme_logo',
        'instagram_handle',
        'facebook_handle',
        'youtube_handle',
        'tiktok_handle',
        'linkedin_handle',
        'show_whatsapp_support',
        'show_telegram_support',
        'show_preloader',
        'preferred_landing_page',
        'base_currency',
        'vat_rate',
    ];
}
