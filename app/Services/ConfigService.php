<?php

namespace App\Services;

class ConfigService
{
    /**
     * Get business name from configuration
     */
    public function getBusinessName()
    {
        return config('custom.business.name');
    }

    /**
     * Get business email from configuration
     */
    public function getBusinessEmail()
    {
        return config('custom.business.email');
    }

    /**
     * Get business phone from configuration
     */
    public function getBusinessPhone()
    {
        return config('custom.business.phone');
    }

    /**
     * Get business timezone from configuration
     */
    public function getBusinessTimezone()
    {
        return config('custom.business.timezone');
    }

    /**
     * Get mail from address from configuration
     */
    public function getMailFromAddress()
    {
        return config('custom.mail.from_address');
    }

    /**
     * Get mail from name from configuration
     */
    public function getMailFromName()
    {
        return config('custom.mail.from_name');
    }

    /**
     * Get all business configuration
     */
    public function getBusinessConfig()
    {
        return config('custom.business');
    }

    /**
     * Get all mail configuration
     */
    public function getMailConfig()
    {
        return config('custom.mail');
    }
}
