<?php

namespace App\Logger;

final class Log
{
    const SUBJECT_REGISTRATION = 'Registration';
    const SUBJECT_RESEND_REGISTRATION = 'Resend registration';
    const SUBJECT_ACTIVE = 'Active';
    const SUBJECT_PASSWORD_LOST = 'Password Lost';
    const SUBJECT_CHANGE_PASSWORD = 'Change Password';
    const SUBJECT_UPDATE_ACCOUNT = 'Update Account';
    const SUBJECT_ENABLE_ACCOUNT = 'Enable Account';
    const SUBJECT_DISABLE_ACCOUNT = 'Disable Account';
    const SUBJECT_SAVE_USER = 'User saved';
}
