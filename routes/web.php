<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\BudgetUploadController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DimensionController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RemarkController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Middleware\CheckSession;
use Illuminate\Support\Facades\Route;
use Mews\Captcha\Facades\Captcha;

Route::get('/refresh-captcha', function () {
    return response()->json(['captcha' => captcha_img()]);
});
Route::post('/login', [LoginController::class, 'login'])->name('login');
// Route::post('/otp', [LoginController::class, 'login'])->middleware('auth')->name('login');
Route::get('/otp', [OtpController::class, 'otpVerif'])
    ->name('otp.otp-verification');
Route::post('/verify-otp', [OtpController::class, 'verify'])
    ->name('otp.verify');
Route::post('/resendOtp-otp', [OtpController::class, 'resendOtp'])
    ->middleware('auth')
    ->name('otp.resendOtp');
Route::group(['middleware' => ['auth', CheckSession::class]], function () {
    Route::get('/', [MainController::class, 'index'])->name('index');
    Route::get('/all', [MainController::class, 'indexAll'])->name('index-all');
});
Route::get('/reports/workcenter-by-account/{acc_id}/{year}', [ReportController::class, 'reportByWorkcenterAndAccount'])
    ->name('reports.workcenterReport');
Route::get('/reports/workcenter/{wct_id}/{year}', [MainController::class, 'reportByWorkcenter'])->name('reports.workcenterReport');
Route::get('sumarries/department/{dpt_id}/year/{year}', [MainController::class, 'reportByDepartmentAndYear'])->name('sumarries.byDepartmentAndYear')->middleware('auth');
Route::get('sumarries/{dpt_id}/{year}', [MainController::class, 'reportByDepartmentAndYear'])->name('sumarries.sum-acc')->middleware('auth');
Route::get('sumarries/report-acc/{acc_id}/{dpt_id}/{year?}', [MainController::class, 'reportByAccount'])->name('sumarries.report-acc')->middleware('auth');

Route::get('/reports/detail/{acc_id}/{wct_id}/{year}', [MainController::class, 'detailReport'])->name('reports.detailReport');//Department
Route::get('departments', [DepartmentController::class, 'index'])->middleware('auth')->name('departments.index')->middleware('auth');
Route::get('department', [MainController::class, 'department'])->name('department')->middleware('auth');
Route::get('departments/{dpt_id}/accounts', [DepartmentController::class, 'showAccounts'])->name('departments.accounts')->middleware('auth');
Route::get('departments/{dpt_id}/accounts/{acc_id}/approvals', [DepartmentController::class, 'index'])->name('approvals.index')->middleware('auth');
Route::get('departments/detail/{dpt_id}', [DepartmentController::class, 'detail'])->name('departments.detail')->middleware('auth');

//Submission
// Route::get('/all', [MainController::class, 'indexKadept'])->name('index-all');
// Route::get('/all', [MainController::class, 'indexKadiv'])->name('index-all');
// Route::get('/all', [MainController::class, 'indexDIC'])->name('index-all');
// Route::get('/sub-kadept', [SubmissionController::class, 'indexKadept'])->name('sub-kadept');
// Route::get('/sub-kadiv', [SubmissionController::class, 'indexKadiv'])->name('sub-kadiv');
// Route::get('/sub-dic', [SubmissionController::class, 'indexDIC'])->name('sub-dic');
Route::get('submissions', [SubmissionController::class, 'index'])->name('submissions.index')->middleware('auth');
Route::get('submissions/detail/{acc_id}', [SubmissionController::class, 'detail'])->name('submissions.detail')->middleware('auth');
Route::post('submissions/{sub_id}/submit', [SubmissionController::class, 'submit'])->name('submissions.submit')->middleware('auth');
Route::delete('submissions/{sub_id}', [SubmissionController::class, 'destroy'])->name('submissions.destroy')->middleware('auth');
Route::delete('submissions/{sub_id}/{id}', [SubmissionController::class, 'delete'])->name('submissions.delete')->middleware('auth');
Route::get('submissions/report/{sub_id}', [SubmissionController::class, 'report'])->name('submissions.report')->middleware('auth');
Route::get('submissions/reportKadept/{sub_id}', [SubmissionController::class, 'reportKadept'])->name('submissions.reportKadept')->middleware('auth');
Route::get('submissions/{sub_id}/id/{id}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit')->middleware('auth');
Route::put('submissions/{sub_id}/id/{id}', [SubmissionController::class, 'update'])->name('submissions.update')->middleware('auth');
Route::post('submissions/{sub_id}/disapprove', [SubmissionController::class, 'disapprove'])->name('submissions.disapprove')->middleware('auth');
Route::get('template/download', [SubmissionController::class, 'downloadTemplate'])->name('template.download')->middleware('auth');
Route::get('template/downloadExpend', [SubmissionController::class, 'downloadTemplateExpend'])->name('template.downloadExpend')->middleware('auth');
Route::post('upload-template', [SubmissionController::class, 'upload'])->name('upload.template')->middleware('auth');
Route::post('upload-templateExpend', [SubmissionController::class, 'uploadExpend'])->name('upload.templateExpend')->middleware('auth');
Route::post('submissions/{sub_id}/add-item', [SubmissionController::class, 'addItem'])->name('submissions.add-item')->middleware('auth');
Route::post('submissions/get-item-name', [SubmissionController::class, 'getItemName'])->name('submissions.getItemName')->middleware('auth');
Route::get('/submissions/{sub_id}/download-documents', [AccountController::class, 'downloadDocuments'])->name('submissions.downloadDocuments');

//Account
Route::middleware(['auth'])->group(function () {
    Route::get('accounts/{acc_id}/create', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('accounts/addTempData', [AccountController::class, 'addTempData'])->name('accounts.addTempData');
});
Route::delete('accounts/removeTempData/{index}', [AccountController::class, 'removeTempData'])->name('accounts.removeTempData')->middleware('auth');
Route::post('accounts/editTempData/{index}', [AccountController::class, 'editTempData'])->name('accounts.editTempData')->middleware('auth');
Route::post('accounts', [AccountController::class, 'store'])->name('accounts.store')->middleware('auth');
Route::post('accounts/cancel', [AccountController::class, 'cancel'])->name('accounts.cancel')->middleware('auth');
Route::get('accounts/{sub_id}/create-item-data', [AccountController::class, 'getCreateItemData'])->name('accounts.getCreateItemData')->middleware('auth');
Route::post('accounts/store-item', [AccountController::class, 'storeItem'])->name('accounts.storeItem')->middleware('auth');
Route::post('/accounts/get-item-name', [AccountController::class, 'getItemName'])->name('accounts.getItemName')->middleware('auth');
Route::post('/accounts/get-currencies', [AccountController::class, 'getCurrencies'])->name('accounts.getCurrencies');
Route::post('/accounts/upload-pdf', [AccountController::class, 'uploadPdf'])->name('accounts.uploadPdf');
Route::post('/accounts/remove-pdf', [AccountController::class, 'removePdf'])->name('accounts.removePdf');

Route::get('drafts/detail/{acc_id}', [DraftController::class, 'index'])->name('drafts.detail');
Route::get('drafts/repair', [DraftController::class, 'indexRepair'])->name('drafts.repair');
Route::post('drafts/{sub_id}/submit', [DraftController::class, 'submit'])->name('drafts.submit');
Route::delete('drafts/{sub_id}', [DraftController::class, 'destroy'])->name('drafts.destroy');

Route::get('approvals/detail', [ApprovalController::class, 'approvalDetail'])->name('approvals.detail')->middleware('auth');
Route::get('approvals/history/{sub_id}', [ApprovalController::class, 'history'])->name('approvals.history')->middleware('auth');
Route::get('approvals/pending', [ApprovalController::class, 'pendingApprovals'])->name('approvals.pending')->middleware('auth');
Route::post('/approvals/approve-account/{acc_id}', [ApprovalController::class, 'approveAccount'])->name('approvals.approve_account');
Route::post('/approvals/reject-account/{acc_id}', [ApprovalController::class, 'rejectAccount'])->name('approvals.reject_account');
Route::get('/approvals/account-detail/{acc_id}/{dpt_id}', [ApprovalController::class, 'accountDetail'])->name('approvals.account-detail');
Route::post('approvals/{sub_id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
Route::post('approvals/{sub_id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
Route::post('/approvals/approve-by-account/{acc_id}/{dpt_id}', [ApprovalController::class, 'approveByAccount'])->name('approvals.approveByAccount');
Route::post('/approvals/reject-by-account/{acc_id}/{dpt_id}', [ApprovalController::class, 'rejectByAccount'])->name('approvals.rejectByAccount');
Route::post('approvals/approve-department/{dpt_id}', [ApprovalController::class, 'approveByDepartment'])->name('approvals.approve-department');
Route::post('approvals/reject-department/{dpt_id}', [ApprovalController::class, 'rejectByDepartment'])->name('approvals.reject-department');

Route::get('remarks/remark/{sub_id}', [RemarkController::class, 'history'])->name('remarks.remark')->middleware('auth');
Route::get('remarks/{sub_id}', [RemarkController::class, 'index'])->name('remarks.index')->middleware('auth');
Route::post('remarks', [RemarkController::class, 'store'])->name('remarks.store')->middleware('auth');
Route::post('remarks/reply', [RemarkController::class, 'reply'])->name('remarks.reply')->middleware('auth');
Route::get('/remarks/get-remarks/{sub_id}', [RemarkController::class, 'getRemarks'])->middleware('auth');

Route::get('reports/index/{acc_id}', [ReportController::class, 'index'])->name('reports.index')->middleware('auth');
Route::get('reports/{acc_id}/{dpt_id}/printAccount', [ReportController::class, 'printAccount'])->name('reports.printAccount')->middleware('auth');
Route::get('reports/downloadAllReport', [ReportController::class, 'downloadAllReport'])->name('reports.downloadAllReport')->middleware('auth');
Route::get('reports/downloadAll/{dpt_id}', [ReportController::class, 'downloadAll'])->name('reports.downloadAll')->middleware('auth');
Route::get('report-all', [ReportController::class, 'reportAllSect'])->middleware('auth')->name('reports.report-all');
Route::get('report-all/downloadReportSect', [ReportController::class, 'downloadReportSect'])->middleware('auth')->name('reports.downloadReportSect');
Route::get('reports', [ReportController::class, 'reportAll'])->middleware('auth')->name('reports.report');
Route::get('reports/print-monthly-account/{acc_id}/{dpt_id}/{month}', [ReportController::class, 'printMonthlyAccount'])->name('reports.printMonthlyAccount')->middleware('auth');
Route::get('/reports/report-dept', [ReportController::class, 'departmentList'])->name('reports.report-dept');

Route::get('dimensions', [DimensionController::class, 'index'])->middleware('auth')->name('dimensions.index');
Route::get('dimensions/detail/{dim_id}', [DimensionController::class, 'detail'])->name('dimensions.detail')->middleware('auth');
Route::get('dimensions/{dim_id}/create', [DimensionController::class, 'create'])->name('dimensions.create')->middleware('auth');
Route::post('dimensions/{dim_id}', [DimensionController::class, 'store'])->name('dimensions.store')->middleware('auth');
Route::put('dimensions/{dim_id}/status/{id}', [DimensionController::class, 'updateStatus'])->middleware('auth');
Route::get('dimensions/{dim_id}/edit/{id}', [DimensionController::class, 'edit'])->name('dimensions.edit')->middleware('auth');
Route::put('dimensions/{dim_id}/{id}', [DimensionController::class, 'update'])->name('dimensions.update')->middleware('auth');

Route::get('suppliers', [SupplierController::class, 'index'])->middleware('auth')->name('suppliers.index');
Route::put('suppliers/status/{id}', [SupplierController::class, 'updateStatus'])->middleware('auth');
Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create')->middleware('auth');
Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store')->middleware('auth');
Route::get('suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit')->middleware('auth');
Route::put('suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update')->middleware('auth');

Route::get('currencies', [CurrencyController::class, 'index'])->name('currencies.index')->middleware('auth');
Route::put('currencies/status/{id}', [CurrencyController::class, 'updateStatus'])->middleware('auth');
Route::get('currencies/create', [CurrencyController::class, 'create'])->name('currencies.create')->middleware('auth');
Route::post('currencies', [CurrencyController::class, 'store'])->name('currencies.store')->middleware('auth');
Route::get('currencies/{id}/edit', [CurrencyController::class, 'edit'])->name('currencies.edit')->middleware('auth');
Route::put('currencies/{id}', [CurrencyController::class, 'update'])->name('currencies.update')->middleware('auth');

Route::get('notifications', [NotificationController::class, 'getNotifications'])->name('notifications')->middleware('auth');
Route::post('notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read')->middleware('auth');
Route::post('/notifications/delete-all', [NotificationController::class, 'deleteAll'])->middleware('auth');

Route::post('/budget-upload', [BudgetUploadController::class, 'upload'])->name('budget.upload');
Route::post('/budget/upload-fy-lo', [BudgetUploadController::class, 'uploadFyLo'])
    ->name('budget.upload-fy-lo');
// Route::get('submissions/{acc_id}', [ReportController::class, 'ReportAccount'])->name('reports.ReportAccount');
// Route::get('reports/account/{id}', [ReportController::class, 'reportAccount'])->name('reports.account');
// Route::get('reports/{acc_id}', [ReportController::class, 'viewReportDept'])->name('reports.viewReportDept');

require __DIR__ . '/auth.php';

//Route::get('accounts/ads', [AccountController::class, 'index'])->name('accounts.index');
//Route::get('accounts/comm', [AccountController::class, 'index'])->name('accounts.index');
//Route::get('drafts/detail', [DraftController::class, 'index'])->name('drafts.detail');
//Route::get('departments/create', [TemplateController::class, 'index'])->name('templates.index');
//Route::get('departments/{dpt_id}/create', [TemplateController::class, 'index'])->name('templates.index');
//Route::get('departments/{dpt_id}/create', [DepartmentController::class, 'create'])->name('departments.create');
//Route::get('departments/template/{id}', [DepartmentController::class, 'template'])->name('departments.template');

Route::post('submissions/clear-session', [AccountController::class, 'clearSession'])->name('submissions.clear-session');
Route::get('/index/accounts', [MainController::class, 'indexAccounts'])->name('index.accounts');

Route::post('/approvals/approve-division/{div_id}', [MainController::class, 'approveDivision'])->name('approvals.approve-division');
Route::post('/approvals/reject-division/{div_id}', [MainController::class, 'rejectDivision'])->name('approvals.reject-division');

Route::get('/purposes/list/{acc_id}/{dept_id}/{year?}/{submission_type?}', [MainController::class, 'listPurposes'])->name('purposes.list');

Route::put('/submissions/{sub_id}/id/{id}/month/{month}', [SubmissionController::class, 'updateMonthly'])->name('submissions.updateMonthly');
Route::delete('/submissions/{sub_id}/id/{id}/month/{month}', [SubmissionController::class, 'destroyMonthly'])->name('submissions.destroyMonthly');