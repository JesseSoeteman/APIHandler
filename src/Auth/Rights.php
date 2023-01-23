<?php

namespace APIHandler\Auth;

/**
 * Class to define the rights of a user.
 */
abstract class Rights {
    const NoPermission = 'NoPermission';
    const ReadOnly = 'ReadOnly';
    const Admin = 'Admin';
    const AdminSudo = 'AdminSudo';
}