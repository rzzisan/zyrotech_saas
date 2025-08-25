<?php

namespace App\Traits;

trait PhoneNumberFormatter
{
    /**
     * Formats a Bangladeshi phone number to the 880xxxxxxxxxx format.
     */
    protected function formatPhoneNumberBd(string $number): string
    {
        // Remove all non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', $number);

        // If the number starts with 880 and is 13 digits long, it's already correct
        if (strlen($cleaned) === 13 && strpos($cleaned, '880') === 0) {
            return $cleaned;
        }

        // If the number starts with 0 and is 11 digits long (e.g., 017...), remove the 0 and add 880
        if (strlen($cleaned) === 11 && strpos($cleaned, '0') === 0) {
            return '880' . substr($cleaned, 1);
        }

        // If the number starts with 1 and is 10 digits long (e.g., 17...), add 880
        if (strlen($cleaned) === 10 && strpos($cleaned, '1') === 0) {
            return '880' . $cleaned;
        }

        // Return the cleaned number if it doesn't match known formats
        return $cleaned;
    }
}
