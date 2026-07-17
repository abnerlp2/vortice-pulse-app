<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('modules.dashboard', function () {
    return true;
});
