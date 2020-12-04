<?php

namespace App\Model;

class FlashMessage
{
    const REGISTER_OK = 'User Registered, its getting Activated';
    const REGISTER_FAIL = 'Can not register that user';
    const REGISTER_ACTIVATE_ACTIVATED = 'User activated';
    const REGISTER_ACTIVATE_REJECTED = 'User rejected';
    const REGISTER_ACTIVATE_NOT_FOUND = 'User not found';

    const ITEM_OK = 'Item Added';
    const ITEM_UPDATED = 'Item Updated';
    const ITEM_DELETED = 'Item Deleted';
    const ITEM_FAIL = 'Error in this item operation';
}