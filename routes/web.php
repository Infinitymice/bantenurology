<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\EventTypeController as AdminEventTypeController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\AccommodationController;
use App\Http\Controllers\Admin\SettingAbsensiController;
use App\Http\Controllers\Admin\DownloadController;
use App\Http\Controllers\Admin\SettingWheelController;
use App\Http\Controllers\Admin\GroupCodeController;
use App\Http\Controllers\Admin\DownloadInvoiceController;
use App\Http\Middleware\Auth;
use App\Http\Middleware\Admin;
use App\Http\Controllers\Admin\VoucherController;
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

// Route halaman registrasi

Route::get('/register/step1', [RegisterController::class, 'showStep1'])->name('register.step1');
Route::post('/register/step1', [RegisterController::class, 'storeStep1']);

//check group code
Route::get('/check-group-code/{code}', [RegisterController::class, 'checkGroupCode'])->name('check-group-code');

Route::get('/register/step2', [RegisterController::class, 'showStep2'])->name('register.step2');
Route::post('/register/step2', [RegisterController::class, 'storeStep2']);


Route::get('/register/accommodation', [RegisterController::class, 'showAccommodation'])->name('register.accommodation');
Route::post('/register/accommodation', [RegisterController::class, 'storeAccommodation']);
Route::post('/register/check-availability', [RegisterController::class, 'checkAvailability']);


Route::get('/register/step3', [RegisterController::class, 'showStep3'])->name('register.step3');
Route::post('/register/step3', [RegisterController::class, 'submitRegistration']);

Route::get('/register/step4', [RegisterController::class, 'showStep4'])->name('register.step4');


// Route dropdown

Route::get('/events-by-type/{type}', [EventController::class, 'getEventsByType'])->name('events.by.type');

Route::get('/event-detail/{eventId}', function ($eventId) {
    $event = \App\Models\Event::find($eventId);

    if ($event) {
        return response()->json([
            'name' => $event->name,
            'price' => $event->price
        ]);
    }

    return response()->json(['error' => 'Event not found'], 404);
});

//Route Finish Registration
Route::post('/finish-registration/{payment}', [PaymentController::class, 'uploadProof'])->name('finish.registration');
Route::get('/transaction-success', [PaymentController::class, 'transactionSuccess'])->name('register.transactionSuccess');

//Route Paylater
//Route::get('/register/pay-later', [RegisterController::class, 'showPayLaterForm'])->name('register.pay-laterForm');
Route::post('/register/pay-later', [RegisterController::class, 'processPayLaterPayment'])->name('register.pay-laterUpload');
// Route::post('/pay-later/invoice', [RegisterController::class, 'processInvoice'])->name('register.pay-laterInvoice'); // cek invoice


Route::get('/pay-later/{payment_id}', [RegisterController::class, 'payLater'])->name('register.pay-later');
Route::get('/transaction-later', [RegisterController::class, 'showTransactionLater'])->name('register.transactionLater');


//Route Paylater Baru
Route::get('/pay-later', [RegisterController::class, 'showInvoiceForm'])->name('register.pay-laterInvoice');
Route::post('/pay-later/details', [RegisterController::class, 'showPayLaterForm'])->name('register.pay-later.details');

//Route Home
Route::get('/', [HomeController::class, 'index'])->name('user-home');
Route::get('/tt', [HomeController::class, 'tt']);

//Route auth
Route::prefix('admin/auth')->name('admin.auth.')->group(function () {
    // Menampilkan form login
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login');

    //Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});

//Route Middleware Admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('admin/participants', [ParticipantController::class, 'index'])->name('admin.participants');
    Route::get('admin/participants/data', [ParticipantController::class, 'getData'])->name('admin.participants.data');
    Route::get('admin/payments', [ParticipantController::class, 'getPaymentIndex'])->name('admin.payments');
    Route::get('admin/payments/data', [ParticipantController::class, 'getDataPayments'])->name('admin.participant.dataPayments');
    Route::post('admin/payment-status/update', [ParticipantController::class, 'updatePaymentStatus'])->name('admin.update.payment.status');
    // Route untuk crud peserta
    Route::get('admin/participants/{participant}/edit', [ParticipantController::class, 'edit'])->name('admin.participants.edit');
    Route::put('admin/participants/{participant}', [ParticipantController::class, 'update'])->name('admin.participants.update');
    Route::delete('admin/participants/{participant}', [ParticipantController::class, 'destroy'])->name('admin.participants.delete');
    Route::post('/admin/participant/register', [ParticipantController::class, 'registerAdd'])->name('admin.participant.register');

    //route import peserta
    Route::post('/admin/import-participants', [ParticipantController::class, 'import'])->name('admin.import-participants');

    //route admin upload bukti pembayaran
    Route::post('/admin/upload-payment-proof', [ParticipantController::class, 'uploadPaymentProof'])->name('admin.upload.payment.proof');

    //route export alldata
    Route::get('/payments/export', [ParticipantController::class, 'exportAllPayments'])->name('admin.export.all');


    //route download qr
    Route::post('/print-qr-code', [DownloadController::class, 'printQRCode'])->name('print.qr-code');
    Route::get('admin/download', [DownloadController::class, 'index'])->name('admin.download.index');
    Route::get('admin/download/qr-code/{id}', [DownloadController::class, 'downloadQRCode'])->name('admin.download.qr-code');
    Route::get('/qr-codes/data', [DownloadController::class, 'getData'])->name('admin.qr-codes.data');



    //Route untuk absensi
    Route::get('admin/absensi', [AbsensiController::class, 'index'])->name('admin.absensi.index');
    Route::get('admin/absensi/data', [AbsensiController::class, 'getData'])->name('admin.absensi.data');
    Route::post('admin/reset-absensi', [AdminController::class, 'resetAbsensi'])->name('admin.reset.absensi');
    Route::delete('admin/absensi/hapus', [AbsensiController::class, 'deleteAll'])->name('admin.absensi.deleteAll');
    Route::delete('/admin/absensi/delete-by-date', [AbsensiController::class, 'deleteByDate'])->name('admin.absensi.deleteByDate');

    //Route wheels of name
    Route::get('/admin/wheel', [SettingWheelController::class, 'showWheel'])->name('admin.wheel.index');
    Route::get('/admin/setting-wheel', [SettingWheelController::class, 'showSettingWheel'])->name('admin.setting-wheel');
    Route::post('/admin/setting-wheel', [SettingWheelController::class, 'processSettingWheel']);

    //Route Invoice
    Route::get('/admin/invoices', [DownloadInvoiceController::class, 'index'])->name('admin.invoices');
    Route::get('admin/invoices/data', [DownloadInvoiceController::class, 'getData'])->name('admin.invoices.data');
    // Route::get('invoices/download/{invoiceNumber}', [DownloadInvoiceController::class, 'download'])->name('admin.invoices.download');

    // Ganti menjadi
    Route::get('admin/invoices/download/{invoiceNumber}', [DownloadInvoiceController::class, 'download'])->name('admin.invoices.download');

});

//Route Kelola User
Route::prefix('admin/users')->name('admin.users.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/data', [UserController::class, 'getData'])->name('data'); 
    Route::get('/create', [UserController::class, 'create'])->name('create'); 
    Route::post('/store', [UserController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit'); 
    Route::post('/update/{id}', [UserController::class, 'update'])->name('update'); 
    Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete'); 
});

// Rute Event Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Rute untuk Event Types
    Route::get('event-types', [AdminEventTypeController::class, 'index'])->name('event-types.index');
    Route::get('event-types/data', [AdminEventTypeController::class, 'getEventTypesData'])->name('event-types.data');
    Route::get('event-types/create', [AdminEventTypeController::class, 'create'])->name('event-types.create');
    Route::post('event-types', [AdminEventTypeController::class, 'store'])->name('event-types.store');
    Route::get('event-types/edit/{id}', [AdminEventTypeController::class, 'edit'])->name('event-types.edit');
    Route::put('event-types/{id}', [AdminEventTypeController::class, 'update'])->name('event-types.update');
    Route::delete('event-types/delete/{id}', [AdminEventTypeController::class, 'destroy'])->name('event-types.delete');

    // Rute untuk Events
    Route::get('/', [AdminEventController::class, 'index'])->name('index');
    Route::get('data', [AdminEventController::class, 'getEventsData'])->name('events.data');
    Route::get('events/create', [AdminEventController::class, 'create'])->name('events.create');
    Route::post('events/store', [AdminEventController::class, 'store'])->name('events.store');
    Route::get('events/edit/{id}', [AdminEventController::class, 'edit'])->name('events.edit');
    Route::put('events/update/{id}', [AdminEventController::class, 'update'])->name('events.update');
    Route::delete('events/delete/{id}', [AdminEventController::class, 'destroy'])->name('events.delete');

    //route untuk akomodasi
    Route::get('accommodation', [AccommodationController::class, 'index'])->name('accommodation.index');
    Route::get('accommodation/create', [AccommodationController::class, 'create'])->name('accommodation.create');
    Route::post('accommodation/store', [AccommodationController::class, 'store'])->name('accommodation.store');
    Route::get('accommodation/{accommodation}/edit', [AccommodationController::class, 'edit'])->name('accommodation.edit');
    Route::put('accommodation/{accommodation}', [AccommodationController::class, 'update'])->name('accommodation.update');
    Route::delete('accommodation/{accommodation}', [AccommodationController::class, 'destroy'])->name('accommodation.destroy');
    Route::get('accommodation/data', [AccommodationController::class, 'getData'])->name('accommodation.data');

    // //Route untuk code group    
    // Route::get('group-codes', [GroupCodeController::class, 'index'])->name('group-codes.index');
    // Route::post('group-codes', [GroupCodeController::class, 'store'])->name('group-codes.store'); // Changed from GET to POST
    // Route::post('group-codes/generate', [GroupCodeController::class, 'generate'])->name('group-codes.generate');
    // Route::get('group-codes/data', [GroupCodeController::class, 'getData'])->name('group-codes.data');
    // Route::get('group-codes/{id}/edit', [GroupCodeController::class, 'edit'])->name('group-codes.edit');
    // Route::put('group-codes/{id}', [GroupCodeController::class, 'update'])->name('group-codes.update');
    // Route::post('group-codes/{id}/toggle', [GroupCodeController::class, 'toggleStatus'])->name('group-codes.toggle');
    // Route::delete('group-codes/{id}', [GroupCodeController::class, 'destroy'])->name('group-codes.destroy');

    // Add these new routes
    Route::get('vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
    Route::get('vouchers/data', [VoucherController::class, 'getData'])->name('vouchers.data');
    Route::get('vouchers/{voucher}', [VoucherController::class, 'show'])->name('vouchers.show');
    Route::post('vouchers', [VoucherController::class, 'store'])->name('vouchers.store');
    Route::put('vouchers/{voucher}', [VoucherController::class, 'update'])->name('vouchers.update');
    Route::delete('vouchers/{voucher}', [VoucherController::class, 'destroy'])->name('vouchers.destroy');
});


//route untuk print qr
Route::middleware(['auth', 'non-admin'])->get('/home', function () {
    return view('admin.home');
})->name('home');


// QR Search & Print
// Route::get('admin/searchqr', [QrCodeController::class, 'search'])->name('qr-search');
// Route::post('admin/print', [QrCodeController::class, 'print'])->name('qr-print');

// // QR Absensi Scan
// Route::get('admin/scan', [QrCodeController::class, 'scan'])->name('qr-absence');
// Route::post('admin/scan', [QrCodeController::class, 'processScan'])->name('qr-scan');


Route::middleware(['auth', 'non-admin'])->prefix('absensi')->name('qr.')->group(function () {
    Route::get('index', [QrCodeController::class, 'index'])->name('index');
    Route::get('searchqr', [QrCodeController::class, 'search'])->name('search');
    Route::get('showqr/{paymentId}', [QrCodeController::class, 'ShowQR'])->name('showQr');
    Route::get('print/{id}', [QrCodeController::class, 'print'])->name('print');
    Route::get('scanqr', [QrCodeController::class, 'indexScan'])->name('indexScan');
    Route::get('scanqr/scan', [QrCodeController::class, 'scan'])->name('scan');
    Route::get('absensi', [QrCodeController::class, 'getAbsensi'])->name('absensi.get');
    Route::get('filter-absensi', [QrCodeController::class, 'filterAbsensi'])->name('filter.absensi');
    Route::post('/auto-absensi', [QrCodeController::class, 'autoAbsensi'])->name('auto.absensi');
    Route::post('/qr/check-absensi', [QrCodeController::class, 'checkAbsensi'])->name('check.absensi');


});

Route::get('/check-availability', [RegisterController::class, 'checkAvailability'])->name('check.availability');
// web.php
Route::post('/qr/direct-print', [QrCodeController::class, 'directPrint'])->name('qr.direct-print');

//Route setting absensi
Route::prefix('admin')->group(function () {
    Route::get('/settings', [SettingAbsensiController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [SettingAbsensiController::class, 'update'])->name('admin.settings.update');
});

Route::post('/check-voucher-code', [RegisterController::class, 'checkVoucherCode'])->name('check.voucher.code');
