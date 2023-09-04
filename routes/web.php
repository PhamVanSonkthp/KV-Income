<?php

use App\Models\BadStarCalendar;
use App\Models\Calendar;
use App\Models\Category;
use App\Models\DayZodiacCalendar;
use App\Models\FiveElementCalendar;
use App\Models\Formatter;
use App\Models\GioLyThuanPhongCalendar;
use App\Models\GoodStarCalendar;
use App\Models\Helper;
use App\Models\Image;
use App\Models\LunaCalendar;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SunCalendar;
use App\Models\ThapNhiBatTuDayCalendar;
use App\Models\TimeCalendar;
use App\Models\TimeZodiacCalendar;
use App\Models\TongHopBangKeCalendar;
use App\Models\TrucDayCalendar;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpWord\IOFactory;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/assets/{type}/{user_id}/{id}/{size}/{slug}', [
    'uses' => 'App\Http\Controllers\ImagesController@show',
]);
