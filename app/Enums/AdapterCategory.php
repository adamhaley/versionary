<?php

namespace App\Enums;

enum AdapterCategory: string
{
    case Coding = 'coding';
    case Chat = 'chat';
    case Image = 'image';
    case Video = 'video';

    public function label(): string
    {
        return match ($this) {
            self::Coding => 'Coding',
            self::Chat => 'Chat',
            self::Image => 'Image',
            self::Video => 'Video',
        };
    }
}
