<?php

// Generated list of admin CRUD resources: [uri, controller].

use App\Http\Controllers\Api\V1\Admin;

return [
    ['slides', Admin\SlideController::class],
    ['features', Admin\FeatureController::class],
    ['partners', Admin\PartnerController::class],
    ['team-members', Admin\TeamMemberController::class],
    ['testimonials', Admin\TestimonialController::class],
    ['contracts', Admin\ContractController::class],
    ['factories', Admin\FactoryController::class],
    ['product-categories', Admin\ProductCategoryController::class],
    ['product-types', Admin\ProductTypeController::class],
    ['products', Admin\ProductController::class],
    ['service-types', Admin\ServiceTypeController::class],
    ['services', Admin\ServiceController::class],
    ['posts', Admin\PostController::class],
    ['gallery-categories', Admin\GalleryCategoryController::class],
    ['gallery-items', Admin\GalleryItemController::class],
    ['message-types', Admin\MessageTypeController::class],
];
