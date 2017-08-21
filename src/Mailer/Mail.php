<?php

namespace App\Mailer;

final class Mail
{
    const TRANSLATOR_DOMAIN = 'mails';

    const SENDER = 'contact@travelbook.com';
    const REPLY_TO = 'contact@travelbook.com';

    const CONTENT_TYPE = 'text/html';

    const SUBJECT_REGISTRATION = 'registration.subject';
    const SUBJECT_PASSWORD_LOST = 'password_lost.subject';
    const SUBJECT_CHANGE_PASSWORD = 'change_password.subject';
    const SUBJECT_UPDATE_ACCOUNT = 'update_account.subject';
    const SUBJECT_ENABLE_ACCOUNT = 'enable_account.subject';
    const SUBJECT_DISABLE_ACCOUNT = 'disable_account.subject';
    const SUBJECT_PICTURE_VIRUS = 'picture_virus.subject';
    const SUBJECT_PICTURE_INVALID = 'picture_invalid.subject';
}
