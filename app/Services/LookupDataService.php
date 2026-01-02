<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employer;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Cache;

/**
 * Service for caching frequently accessed lookup data
 * PERFORMANCE: Reduces database queries for static/rarely-changing data
 */
class LookupDataService
{
    /**
     * Cache duration in seconds (1 hour)
     */
    protected const CACHE_TTL = 3600;

    /**
     * Get all branches (cached)
     */
    public static function branches()
    {
        return Cache::remember('lookup_branches', self::CACHE_TTL, function () {
            return Branch::orderBy('name')->get();
        });
    }

    /**
     * Get active departments (cached)
     */
    public static function activeDepartments()
    {
        return Cache::remember('lookup_departments_active', self::CACHE_TTL, function () {
            return Department::active()->orderBy('name')->get();
        });
    }

    /**
     * Get all departments (cached)
     */
    public static function allDepartments()
    {
        return Cache::remember('lookup_departments_all', self::CACHE_TTL, function () {
            return Department::orderBy('name')->get();
        });
    }

    /**
     * Get active employers (cached)
     */
    public static function activeEmployers()
    {
        return Cache::remember('lookup_employers_active', self::CACHE_TTL, function () {
            return Employer::active()->orderBy('name')->get();
        });
    }

    /**
     * Get all employers (cached)
     */
    public static function allEmployers()
    {
        return Cache::remember('lookup_employers_all', self::CACHE_TTL, function () {
            return Employer::orderBy('name')->get();
        });
    }

    /**
     * Get active payment methods (cached)
     */
    public static function activePaymentMethods()
    {
        return Cache::remember('lookup_payment_methods_active', self::CACHE_TTL, function () {
            return PaymentMethod::active()->orderBy('name')->get();
        });
    }

    /**
     * Get all payment methods (cached)
     */
    public static function allPaymentMethods()
    {
        return Cache::remember('lookup_payment_methods_all', self::CACHE_TTL, function () {
            return PaymentMethod::orderBy('name')->get();
        });
    }

    /**
     * Clear all lookup caches (call when data changes)
     */
    public static function clearAll(): void
    {
        self::clearBranchCache();
        self::clearDepartmentCache();
        self::clearEmployerCache();
        self::clearPaymentMethodCache();
    }

    /**
     * Clear branch cache
     */
    public static function clearBranchCache(): void
    {
        Cache::forget('lookup_branches');
    }

    /**
     * Clear department cache
     */
    public static function clearDepartmentCache(): void
    {
        Cache::forget('lookup_departments_active');
        Cache::forget('lookup_departments_all');
    }

    /**
     * Clear employer cache
     */
    public static function clearEmployerCache(): void
    {
        Cache::forget('lookup_employers_active');
        Cache::forget('lookup_employers_all');
    }

    /**
     * Clear payment method cache
     */
    public static function clearPaymentMethodCache(): void
    {
        Cache::forget('lookup_payment_methods_active');
        Cache::forget('lookup_payment_methods_all');
    }
}
