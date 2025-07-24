<?php

namespace App\Providers;

use Faker\Provider\Base;

class FarsiFakerProvider extends Base
{
    protected static $names = [
        'علی', 'محمد', 'حسین', 'رضا', 'مهدی', 'نیما', 'امیر', 'سارا', 'فاطمه', 'زهرا', 'مریم', 'نرگس'
    ];

    protected static $categories = [
        'ایرانی', 'فست‌فود', 'کافه', 'شیرینی‌فروشی', 'سوپرمارکت', 'بین‌المللی', 'دریایی', 'گیاهی'
    ];

    protected static $foodNames = [
        'کباب کوبیده', 'جوجه کباب', 'پیتزا مارگاریتا', 'برگر کلاسیک', 'سالاد سزار', 'کیک شکلاتی', 'لاته', 'چای ایرانی'
    ];

    public function farsiName()
    {
        return static::randomElement(static::$names);
    }

    public function farsiCategory()
    {
        return static::randomElement(static::$categories);
    }

    public function farsiFoodName()
    {
        return static::randomElement(static::$foodNames);
    }

    public function farsiAddress()
    {
        return 'تهران، ' . $this->generator->streetAddress();
    }

    public function farsiPhone()
    {
        return '09' . mt_rand(100000000, 999999999);
    }

    public function farsiDescription()
    {
        return $this->generator->sentence(10);
    }
}