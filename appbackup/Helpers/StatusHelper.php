<?php

namespace App\Helpers;

class StatusHelper
{
    /**
     * Get the Bootstrap color class corresponding to a book status.
     *
     * @param string $status
     * @return string
     */
    public static function bookStatusColor(string $status): string
    {
        return match (strtolower($status)) {
            'published' => 'success',
            'pending' => 'warning',
            'draft' => 'secondary',
            'archived' => 'dark',
            'rejected' => 'danger',
            default => 'primary',
        };
    }

    /**
     * Get the Bootstrap color class corresponding to a revenue status.
     *
     * @param string $status
     * @return string
     */
    public static function revenueStatusColor(string $status): string
    {
        return match (strtolower($status)) {
            'paid' => 'success',
            'approved' => 'primary',
            'pending' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get the Bootstrap color class corresponding to a payment status.
     *
     * @param string $status
     * @return string
     */
    public static function paymentStatusColor(string $status): string
    {
        return match (strtolower($status)) {
            'completed' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'refunded' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Get the Bootstrap color class corresponding to a user role.
     *
     * @param string $role
     * @return string
     */
    public static function userRoleColor(string $role): string
    {
        return match (strtolower($role)) {
            'admin' => 'danger',
            'author' => 'info',
            'school' => 'primary',
            'teacher' => 'success',
            'student' => 'secondary',
            'parent' => 'dark',
            'reader' => 'light',
            'adult_reader' => 'warning',
            default => 'light',
        };
    }
}
