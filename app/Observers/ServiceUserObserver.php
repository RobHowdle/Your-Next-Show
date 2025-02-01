<?php

namespace App\Observers;

use App\Models\ServiceUser;

class ServiceUserObserver
{
    public function created(ServiceUser $serviceUser)
    {
        $serviceUser->serviceable->verify();
    }

    public function deleted(ServiceUser $serviceUser)
    {
        $serviceUser->serviceable->unverify();
    }
}