<?php

namespace App\Model;

class FlashMessage
{
    const REGISTER_OK = 'User Registered, its getting Activated';
    const REGISTER_FAIL = 'Can not register that user';
    const REGISTER_SPAM = 'User rejected';
    const REGISTER_FAIL_SPAM_CHECKER = 'LogIn, the user will be checked son';

    const LOGIN_FAIL = 'User not found or unactivated';

    const ITEM_OK = 'Item Added';
    const ITEM_UPDATED = 'Item Updated';
    const ITEM_DELETED = 'Item Deleted';
    const ITEM_FAIL = 'Error in this item operation';
}