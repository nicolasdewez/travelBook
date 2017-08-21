<?php

namespace App\Serializer;

final class Group
{
    const EVENT_REGISTRATION = 'event_registration';
    const EVENT_PASSWORD_LOST = 'event_password_lost';
    const EVENT_CHANGE_PASSWORD = 'event_change_password';
    const EVENT_UPDATE_ACCOUNT = 'event_update_account';
    const EVENT_ENABLE_ACCOUNT = 'event_enable_account';
    const EVENT_DISABLE_ACCOUNT = 'event_disable_account';
    const EVENT_ANALYZE_PICTURE = 'event_analyze_picture';
    const EVENT_INVALID_PICTURE = 'event_invalid_picture';
}
