<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/**
 * Request and response shapes. Admin shapes carry both languages (`*_ar`, `*_en`);
 * public shapes are already localized.
 */
#[OA\Schema(schema: 'PageMeta', properties: [
    new OA\Property(property: 'total', type: 'integer'),
    new OA\Property(property: 'page', type: 'integer'),
    new OA\Property(property: 'last_page', type: 'integer'),
])]
#[OA\Schema(schema: 'AdminUser', properties: [
    new OA\Property(property: 'id', type: 'integer'),
    new OA\Property(property: 'name', type: 'string'),
    new OA\Property(property: 'email', type: 'string'),
    new OA\Property(property: 'role', type: 'string', enum: ['admin', 'editor']),
])]
#[OA\Schema(schema: 'Stat', properties: [
    new OA\Property(property: 'value', type: 'integer'),
    new OA\Property(property: 'suffix', type: 'string', example: '+'),
    new OA\Property(property: 'label', type: 'string'),
])]
#[OA\Schema(schema: 'SlideAdmin', properties: [
    new OA\Property(property: 'id', type: 'integer', readOnly: true),
    new OA\Property(property: 'title_ar', type: 'string'),
    new OA\Property(property: 'title_en', type: 'string', nullable: true),
    new OA\Property(property: 'subtitle_ar', type: 'string', nullable: true),
    new OA\Property(property: 'subtitle_en', type: 'string', nullable: true),
    new OA\Property(property: 'button_ar', type: 'string', nullable: true),
    new OA\Property(property: 'button_en', type: 'string', nullable: true),
    new OA\Property(property: 'button_url', type: 'string', nullable: true, example: '/products'),
    new OA\Property(property: 'image', description: 'Upload: JPG, PNG or WEBP up to 5 MB', type: 'string', format: 'binary', writeOnly: true),
    new OA\Property(property: 'image_url', type: 'string', format: 'uri', readOnly: true),
    new OA\Property(property: 'is_active', type: 'boolean'),
    new OA\Property(property: 'sort_order', type: 'integer', readOnly: true),
    new OA\Property(property: 'missing_en', type: 'boolean', readOnly: true),
])]
#[OA\Schema(schema: 'TestimonialAdmin', properties: [
    new OA\Property(property: 'id', type: 'integer', readOnly: true),
    new OA\Property(property: 'name_ar', type: 'string'),
    new OA\Property(property: 'name_en', type: 'string', nullable: true),
    new OA\Property(property: 'role_ar', type: 'string', nullable: true),
    new OA\Property(property: 'role_en', type: 'string', nullable: true),
    new OA\Property(property: 'quote_ar', type: 'string'),
    new OA\Property(property: 'quote_en', type: 'string', nullable: true),
    new OA\Property(property: 'color', type: 'string', enum: ['field', 'rust', 'sky', 'plum', 'deep', 'wheat', 'bloom']),
    new OA\Property(property: 'is_active', type: 'boolean'),
    new OA\Property(property: 'sort_order', type: 'integer', readOnly: true),
    new OA\Property(property: 'missing_en', type: 'boolean', readOnly: true),
])]
#[OA\Schema(schema: 'PartnerAdmin', properties: [
    new OA\Property(property: 'id', type: 'integer', readOnly: true),
    new OA\Property(property: 'name_ar', type: 'string'),
    new OA\Property(property: 'name_en', type: 'string', nullable: true),
    new OA\Property(property: 'field_ar', type: 'string', nullable: true),
    new OA\Property(property: 'field_en', type: 'string', nullable: true),
    new OA\Property(property: 'description_ar', type: 'string', nullable: true),
    new OA\Property(property: 'description_en', type: 'string', nullable: true),
    new OA\Property(property: 'website', type: 'string', format: 'uri', nullable: true),
    new OA\Property(property: 'logo', description: 'Upload: PNG or WEBP with a transparent background', type: 'string', format: 'binary', writeOnly: true),
    new OA\Property(property: 'logo_url', type: 'string', format: 'uri', readOnly: true),
    new OA\Property(property: 'is_active', type: 'boolean'),
    new OA\Property(property: 'sort_order', type: 'integer', readOnly: true),
    new OA\Property(property: 'missing_en', type: 'boolean', readOnly: true),
])]
#[OA\Schema(schema: 'StatInput', properties: [
    new OA\Property(property: 'value', type: 'integer'),
    new OA\Property(property: 'suffix', type: 'string', nullable: true),
    new OA\Property(property: 'label_ar', type: 'string'),
    new OA\Property(property: 'label_en', type: 'string', nullable: true),
])]
#[OA\Schema(schema: 'SettingsAdmin', properties: [
    new OA\Property(property: 'site_name_ar', type: 'string'),
    new OA\Property(property: 'site_name_en', type: 'string', nullable: true),
    new OA\Property(property: 'footer_about_ar', type: 'string', nullable: true),
    new OA\Property(property: 'footer_about_en', type: 'string', nullable: true),
    new OA\Property(property: 'phone1', type: 'string', nullable: true),
    new OA\Property(property: 'phone2', type: 'string', nullable: true),
    new OA\Property(property: 'whatsapp', type: 'string', nullable: true),
    new OA\Property(property: 'email', type: 'string', nullable: true),
    new OA\Property(property: 'inbox_email', description: 'Where contact-form messages are sent. Never shown on the site.', type: 'string', nullable: true),
    new OA\Property(property: 'address_ar', type: 'string', nullable: true),
    new OA\Property(property: 'address_en', type: 'string', nullable: true),
    new OA\Property(property: 'hours_ar', type: 'string', nullable: true),
    new OA\Property(property: 'hours_en', type: 'string', nullable: true),
    new OA\Property(property: 'map_url', type: 'string', nullable: true),
    new OA\Property(property: 'facebook', type: 'string', nullable: true),
    new OA\Property(property: 'youtube', type: 'string', nullable: true),
    new OA\Property(property: 'linkedin', type: 'string', nullable: true),
    new OA\Property(property: 'instagram', type: 'string', nullable: true),
    new OA\Property(property: 'stats', description: 'The third number is also shown on the hero photos', type: 'array', maxItems: 4, items: new OA\Items(ref: '#/components/schemas/StatInput')),
    new OA\Property(property: 'seo_title_ar', type: 'string', nullable: true),
    new OA\Property(property: 'seo_title_en', type: 'string', nullable: true),
    new OA\Property(property: 'seo_description_ar', type: 'string', nullable: true),
    new OA\Property(property: 'seo_description_en', type: 'string', nullable: true),
])]
#[OA\Schema(schema: 'Site', properties: [
    new OA\Property(property: 'site_name', type: 'string'),
    new OA\Property(property: 'footer_about', type: 'string', nullable: true),
    new OA\Property(property: 'contact', properties: [
        new OA\Property(property: 'phones', type: 'array', items: new OA\Items(type: 'string')),
        new OA\Property(property: 'whatsapp', type: 'string', nullable: true),
        new OA\Property(property: 'email', type: 'string', nullable: true),
        new OA\Property(property: 'address', type: 'string', nullable: true),
        new OA\Property(property: 'hours', type: 'string', nullable: true),
        new OA\Property(property: 'map_url', type: 'string', nullable: true),
    ], type: 'object'),
    new OA\Property(property: 'social', type: 'object', additionalProperties: new OA\AdditionalProperties(type: 'string')),
    new OA\Property(property: 'stats', type: 'array', items: new OA\Items(ref: '#/components/schemas/Stat')),
    new OA\Property(property: 'seo', properties: [
        new OA\Property(property: 'title', type: 'string', nullable: true),
        new OA\Property(property: 'description', type: 'string', nullable: true),
    ], type: 'object'),
    new OA\Property(property: 'categories', type: 'array', items: new OA\Items(properties: [
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'name', type: 'string'),
    ])),
])]
#[OA\Schema(schema: 'Home', properties: [
    new OA\Property(property: 'slides', type: 'array', items: new OA\Items(properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'subtitle', type: 'string', nullable: true),
        new OA\Property(property: 'button', type: 'string', nullable: true),
        new OA\Property(property: 'button_url', type: 'string', nullable: true),
        new OA\Property(property: 'image', type: 'string', format: 'uri'),
        new OA\Property(property: 'image_sm', type: 'string', format: 'uri'),
    ])),
    new OA\Property(property: 'categories', type: 'array', items: new OA\Items(properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'color', type: 'string'),
        new OA\Property(property: 'products_count', type: 'integer'),
        new OA\Property(property: 'types', type: 'array', items: new OA\Items(type: 'string')),
    ])),
    new OA\Property(property: 'services', type: 'array', items: new OA\Items(properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'title', type: 'string'),
        new OA\Property(property: 'excerpt', type: 'string', nullable: true),
        new OA\Property(property: 'type', type: 'string', nullable: true),
        new OA\Property(property: 'image', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'image_sm', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'read_minutes', type: 'integer'),
    ])),
    new OA\Property(property: 'partners', type: 'array', items: new OA\Items(properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'field', type: 'string', nullable: true),
        new OA\Property(property: 'logo', type: 'string', format: 'uri'),
        new OA\Property(property: 'website', type: 'string', nullable: true),
    ])),
    new OA\Property(property: 'testimonials', type: 'array', items: new OA\Items(properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'role', type: 'string', nullable: true),
        new OA\Property(property: 'quote', type: 'string'),
        new OA\Property(property: 'color', type: 'string'),
    ])),
])]
final class Schemas {}
