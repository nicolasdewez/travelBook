<?php

namespace App\Logger;

final class Log
{
    const SUBJECT_REGISTRATION = 'Registration';
    const SUBJECT_RESEND_REGISTRATION = 'Resend registration';
    const SUBJECT_ACTIVE = 'Active';
    const SUBJECT_PASSWORD_LOST = 'Password lost';
    const SUBJECT_CHANGE_PASSWORD = 'Change password';
    const SUBJECT_UPDATE_ACCOUNT = 'Update account';
    const SUBJECT_ENABLE_ACCOUNT = 'Enable account';
    const SUBJECT_DISABLE_ACCOUNT = 'Disable account';
    const SUBJECT_PROCESS_FEEDBACK = 'Process feedback';
    const SUBJECT_SAVE_USER = 'User saved';
    const SUBJECT_SAVE_PICTURE = 'Picture saved';
    const SUBJECT_SAVE_PLACE = 'Place saved';
    const SUBJECT_SAVE_TRAVEL = 'Travel saved';
    const SUBJECT_SAVE_FEEDBACK = 'Feedback saved';
    const SUBJECT_ANALYZE_PICTURE = 'Analyze picture';
    const SUBJECT_PICTURE_VIRUS = 'Picture is virus';
    const SUBJECT_PICTURE_VALIDATION = 'Picture validation';
    const SUBJECT_PICTURE_INVALIDATION = 'Picture invalidation';
}
