<?php
/*
tambah 'authorized' di middleware untuk otorisasi akses
*/
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DetailTransactionController;
use App\Http\Controllers\UndernameDetailController;
use App\Http\Controllers\DetailPurchaseController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\OrganizationStructureController;
use App\Http\Controllers\StructuralPositionController;
use App\Http\Controllers\WorkPositionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\BoronganController;
use App\Http\Controllers\HonorariumController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserPageMappingController;
use App\Http\Controllers\GoodController;
use App\Http\Controllers\UndernameController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\StockController;

use App\Models\Rekening; 
use App\Models\Company; 
use App\Models\Item; 
use App\Models\Transaction; 
use App\Models\Purchase; 
use App\Models\DetailPurchase; 
use App\Models\DetailTransaction; 
use App\Models\UndernameDetail; 
use App\Models\User; 
use App\Models\Presence; 
use App\Models\Undername; 

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


Route::get('/', function () {
    return view('welcome');
});
Route::get('unauthorized', function () {
    return view('partial.noAccess');
});

Route::get('home', [DashboardController::class, 'index'])->middleware(['auth','authorized']);
Route::get('home2',[DashboardController::class, 'indexHome2'])->middleware(['auth']);
Route::GET('employeeList2',[EmployeeController::class, 'index2'])->name('employeeList2')->middleware('auth', 'authorized');
Route::GET('infophp', [DashboardController::class, 'infophp'])->middleware('auth', 'authorized');

/*
*   Route Transaksi Penjualan
*
*/

Route::get('transactionList',[TransactionController::class, 'index'])->middleware(['auth', 'authorized']);
Route::get('transactionAdd',[TransactionController::class, 'create'])->middleware(['auth', 'authorized']);
Route::get('transactionView',[TransactionController::class, 'show'])->middleware(['auth', 'authorized']);
Route::get('transactionEdit/{transaction}',[TransactionController::class, 'edit'])->middleware(['auth', 'authorized']);

Route::get('transactionInvoice/{transaction}',[TransactionController::class, 'transactionInvoice'])->middleware(['auth', 'authorized']);
Route::get('transactionDocuments/{transaction}',[TransactionController::class, 'transactionDocuments'])->middleware(['auth', 'authorized']);
Route::get('transactionDocumentAdd/{transaction}',[TransactionController::class, 'createDocumentExport'])->middleware(['auth', 'authorized']);
Route::POST('transactionDocumentAddStore',[TransactionController::class, 'transactionDocumentAddStore'])->middleware(['auth']);
Route::GET('getAllExportDocuments', [TransactionController::class, 'getAllExportDocuments'])->middleware(['auth']);






Route::POST('transactionStore',[TransactionController::class, 'store'])->middleware(['auth']);
Route::POST('transactionUpdate',[TransactionController::class, 'update'])->middleware(['auth']);
Route::get('transactionRevoke',[TransactionController::class, 'revoke'])->middleware(['auth', 'authorized']);
Route::get('checkTransactionNum',[TransactionController::class, 'checkTransactionNum'])->middleware(['auth']);
Route::POST('setTransactionToBeRevoked',[TransactionController::class, 'setTransactionToBeRevoked'])->middleware(['auth']);
Route::get('detailtransactionList/{transaction}',[DetailTransactionController::class, 'index'])->middleware(['auth', 'authorized'])->name('detailtransactionList');
Route::get('detailtransactionAdd/{transaction}',[DetailTransactionController::class, 'create'])->middleware(['auth', 'authorized']);
Route::get('itemDetailTransactionAdd',[DetailTransactionController::class, 'store'])->middleware(['auth'])->name('itemDetailTransactionAdd');
Route::get('itemDetailTransactionDelete/{detail_transaction}',[DetailTransactionController::class, 'destroy'])->middleware(['auth'])->name('itemDetailTransactionDelete');
Route::GET('getAllExportTransaction', [TransactionController::class, 'getAllExportTransaction'])->middleware(['auth']);
Route::GET('getAllExportTransactionToRevoke', [TransactionController::class, 'getAllExportTransactionToRevoke'])->middleware(['auth']);
Route::GET('getAllDetail/{transactionId}', [DetailTransactionController::class, 'getAllDetail'])->middleware(['auth']);
Route::get('/transaction/pi/{transaction}', [InvoiceController::class, 'cetak_pi'])->middleware(['auth', 'authorized']);
Route::get('/transaction/ipl/{transaction}', [InvoiceController::class, 'cetak_ipl'])->middleware(['auth', 'authorized']);
Route::GET('getAllTransactionDocuments', [TransactionController::class, 'getAllTransactionDocuments'])->middleware(['auth']);
Route::GET('getFileDownload/{filepath}', [InvoiceController::class, 'getFileDownload'])->middleware(['auth']);
Route::POST('storePerubahanHargaDetailTransaksi',[DetailTransactionController::class, 'storePerubahanHargaDetailTransaksi'])->middleware(['auth']);

/*
*   Route Transaksi Penjualan Lokal
*
*/
Route::get('localTransactionList',[TransactionController::class, 'localIndex'])->middleware(['auth', 'authorized']);
Route::GET('getAllLocalTransaction', [TransactionController::class, 'getAllLocaltransaction'])->middleware(['auth']);
Route::get('localTransactionAdd',[TransactionController::class, 'localCreate'])->middleware(['auth', 'authorized']);
Route::POST('localTransactionStore',[TransactionController::class, 'localStore'])->middleware(['auth']);
Route::get('localTransactionEdit/{transaction}',[TransactionController::class, 'localEdit'])->middleware(['auth', 'authorized']);
Route::POST('localTransactionUpdate',[TransactionController::class, 'localUpdate'])->middleware(['auth']);
Route::get('/transaction/localIpl/{transaction}', [InvoiceController::class, 'cetak_local_ipl'])->middleware(['auth', 'authorized']);
Route::get('localTransactionDocument/{transaction}',[TransactionController::class, 'localTransactionDocument'])->middleware(['auth', 'authorized']);

/*
*   Route Transaksi Pembelian/Purchase
*
*/
Route::get('purchaseList',[PurchaseController::class, 'index'])->middleware(['auth', 'authorized']);
Route::GET('getPurchaseList/{negara}/{statusTransaksi}/{start}/{end}', [PurchaseController::class, 'getPurchaseList'])->middleware(['auth']);
Route::get('purchaseAdd',[PurchaseController::class, 'create'])->middleware(['auth', 'authorized']);
Route::POST('purchaseStore',[PurchaseController::class, 'store'])->middleware(['auth'])->name('purchaseStore');
Route::get('purchaseEdit/{purchase}',[PurchaseController::class, 'edit'])->middleware(['auth', 'authorized']);
Route::post('purchaseUpdate',[PurchaseController::class, 'update'])->middleware(['auth']);
Route::get('purchaseDocument/{purchase}',[PurchaseController::class, 'purchaseDocument'])->middleware(['auth', 'authorized']);
Route::GET('getAllPurchaseDocuments', [PurchaseController::class, 'getAllPurchaseDocuments'])->middleware(['auth']);
Route::get('purchaseItems/{purchase}',[DetailPurchaseController::class, 'index'])->middleware(['auth'])->name('purchaseItems', 'authorized');
Route::get('purchaseItemAdd/{purchase}',[DetailPurchaseController::class, 'create'])->middleware(['auth', 'authorized']);
Route::get('purchaseItemStore',[DetailPurchaseController::class, 'store'])->middleware(['auth']);
Route::POST('itemDetailPurchaseDelete',[DetailPurchaseController::class, 'destroy'])->middleware(['auth']);
Route::GET('getAllPurchaseItems/{purchase}', [DetailPurchaseController::class, 'getAllPurchaseItems'])->middleware(['auth'])->name('getAllPurchaseItems');
Route::get('/purchase/notaPembelian/{purchase}', [InvoiceController::class, 'cetakNotaPembelian'])->middleware(['auth', 'authorized']);



//ITEM STOCKS
Route::get('itemStockList',[ItemController::class, 'index'])->middleware(['auth', 'authorized'])->name('itemStockList');
Route::GET('getAllStockItem', [ItemController::class, 'getAllStockItem'])->middleware(['auth']);
Route::get('itemStockView/{itemId}',[ItemController::class, 'show'])->middleware(['auth', 'authorized']);
Route::get('itemStockViewUnpacked/{itemId}',[ItemController::class, 'showUnpacked'])->middleware(['auth', 'authorized']);
Route::get('itemStockSubtractView/{itemId}',[ItemController::class, 'showKurangi'])->middleware(['auth', 'authorized']);
Route::GET('getItemHistory/{speciesId}/{start}/{end}/{opsi}', [StoreController::class, 'getItemStoreHistory'])->middleware(['auth']);
Route::GET('getItemSubtractHistory/{speciesId}/{start}/{end}/{opsi}', [StoreController::class, 'getItemSubtractHistory'])->middleware(['auth']);
Route::GET('getUnpackedHistory/{speciesId}', [ItemController::class, 'getUnpackedItemHistory'])->middleware(['auth']);
Route::get('editUnpacked/{itemId}',[StoreController::class, 'editUnpacked'])->middleware(['auth', 'authorized'])->name('editUnpacked');
Route::POST('unpackedUpdate',[StoreController::class, 'unpackedUpdate'])->middleware(['auth'])->name('unpackedUpdate');
Route::get('itemStockAdd/{itemId}',[StoreController::class, 'create'])->middleware(['auth', 'authorized'])->name('itemStockAdd');
Route::get('itemStockSubtract/{itemId}',[StoreController::class, 'subtract'])->middleware(['auth', 'authorized']);
Route::get('itemStockEdit/{store}',[StoreController::class, 'edit'])->middleware(['auth', 'authorized']);
Route::get('itemStoreDetail/{storeId}',[StoreController::class, 'itemStoreDetail'])->middleware(['auth', 'authorized']);
Route::get('storeAdd',[StoreController::class, 'store'])->middleware(['auth'])->name('storeAdd');
Route::post('storeSubtract',[StoreController::class, 'storeSubtract'])->middleware(['auth'])->name('storeSubtract');
Route::get('storeUpdate',[StoreController::class, 'update'])->middleware(['auth'])->name('storeUpdate');

//ITEM STOCKS
Route::get('speciesStockList',[ItemController::class, 'indexStockSpecies'])->middleware(['auth', 'authorized']);
Route::get('getAllSpeciesStock/{isChecked}',[ItemController::class, 'getSpeciesStock'])->middleware(['auth']);
Route::get('getSizesForSpecies/{speciesId}',[ItemController::class, 'getSizeForSpecies'])->middleware(['auth']);
Route::get('getGradesForSize/{sizeId}',[ItemController::class, 'getGradeForSize'])->middleware(['auth']);
Route::get('getWeightbaseForSize/{sizeId}/{gradeId}',[ItemController::class, 'getWeightbaseForSize'])->middleware(['auth']);
Route::get('getShapesForWeightbase/{sizeId}/{gradeId}/{weightbase}',[ItemController::class, 'getShapesForWeightbase'])->middleware(['auth']);
Route::get('getPackingsForShape/{sizeId}/{gradeId}/{weightbase}/{packingId}',[ItemController::class, 'getPackingsForShape'])->middleware(['auth']);
Route::get('getFreezingsForPacking/{sizeId}/{gradeId}/{weightbase}/{packingId}/{shapeId}',[ItemController::class, 'getFreezingsForPacking'])->middleware(['auth']);

//Approval
Route::get('itemStockApprovalPenambahan',[StoreController::class, 'indexApprovalPenambahan'])->middleware(['auth', 'authorized']);
Route::get('itemStockApprovalPengurangan',[StoreController::class, 'indexApprovalPengurangan'])->middleware(['auth', 'authorized']);
Route::post('getStoresRecord',[StoreController::class, 'getStoresRecord'])->middleware(['auth']);
Route::post('approveStockChange',[StoreController::class, 'stockChange'])->middleware(['auth']);
Route::post('deleteStockChange',[StoreController::class, 'stockChangeDelete'])->middleware(['auth']);
Route::post('deleteStockSubtractChange',[StoreController::class, 'deleteStockSubtractChange'])->middleware(['auth']);
Route::get('itemStockSubtractEdit/{stockSubtract}',[StoreController::class, 'subtractEdit'])->middleware(['auth', 'authorized']);
Route::post('stockSubtractUpdate',[StoreController::class, 'subtractUpdate'])->middleware(['auth']);
Route::post('getStorekSubtractRecord',[StoreController::class, 'getStorekSubtractRecord'])->middleware(['auth']);
Route::post('approveStockSubtractChange',[StoreController::class, 'stockSubtractChange'])->middleware(['auth']);

//SPECIES
Route::get('speciesList',[SpeciesController::class, 'index'])->middleware(['auth', 'authorized'])->name('speciesList');
Route::get('speciesCreate',[SpeciesController::class, 'createSpecies'])->middleware(['auth', 'authorized']);
Route::post('speciesStore',[SpeciesController::class, 'storeSpecies'])->middleware(['auth']);
Route::GET('getAllSpecies/{familyId}', [SpeciesController::class, 'getAllSpecies'])->middleware(['auth']);
Route::GET('getAllSpeciesSize/{speciesId}', [SpeciesController::class, 'getAllSpeciesSize'])->middleware(['auth']);
Route::GET('getAllSpeciesItem/{speciesId}', [SpeciesController::class, 'getAllSpeciesItem'])->middleware(['auth']);
Route::get('itemList/{speciesId}',[SpeciesController::class, 'itemList'])->middleware(['auth', 'authorized'])->name('itemList');
Route::get('sizeList/{speciesId}',[SpeciesController::class, 'sizeList'])->middleware(['auth', 'authorized']);
Route::GET('getAllItem/{speciesId}', [SpeciesController::class, 'getAllItem'])->middleware(['auth']);
Route::get('editSpecies/{speciesId}',[SpeciesController::class, 'editSpecies'])->middleware(['auth', 'authorized']);
Route::get('editSpeciesSize/{sizeId}',[SpeciesController::class, 'editSpeciesSize'])->middleware(['auth', 'authorized']);
Route::get('editSpeciesItem/{itemId}',[SpeciesController::class, 'editSpeciesItem'])->middleware(['auth', 'authorized']);
Route::get('addSpeciesSize/{speciesId}',[SpeciesController::class, 'createSize'])->middleware(['auth', 'authorized']);
Route::get('addSpeciesItem/{speciesId}',[SpeciesController::class, 'createItem'])->middleware(['auth', 'authorized']);
Route::get('sizeCreateStore',[SpeciesController::class, 'storeSize'])->middleware(['auth']);
Route::post('itemCreateStore',[SpeciesController::class, 'storeItem'])->middleware(['auth']);
Route::POST('getIsItemAlreadyExist', [SpeciesController::class, 'getIsItemAlreadyExist'])->middleware(['auth']);
Route::get('sizeEditStore',[SpeciesController::class, 'updateSize'])->middleware(['auth']);
Route::get('itemEditStore',[SpeciesController::class, 'updateItem'])->middleware(['auth']);

//COMPANIES
Route::get('companyList',[CompanyController::class, 'index'])->middleware(['auth', 'authorized']);
Route::get('companyAdd',[CompanyController::class, 'create'])->middleware(['auth', 'authorized']);
Route::get('companyEdit/{company}',[CompanyController::class, 'edit'])->middleware(['auth', 'authorized']);
Route::post('companyStore',[CompanyController::class, 'store'])->middleware(['auth'])->name('companyStore');
Route::post('companyUpdate',[CompanyController::class, 'update'])->middleware(['auth'])->name('companyUpdate');
Route::GET('getAllCompany', [CompanyController::class, 'getAllCompany'])->middleware(['auth']);

Route::GET('getAllCompanyProducts', [CompanyController::class, 'getAllCompanyProducts'])->middleware(['auth']);



//to get size for all species
Route::GET('getItemsForSelectOption/{tid}/{pid}/{speciesId}', [ItemController::class, 'getItemForSelectOption'])->middleware(['auth']);
Route::GET('getOneStore/{storeId}', [StoreController::class, 'getOneStore'])->middleware(['auth']);

//to get one full Rekening record with current rekening id
Route::get('/getOneRekening/{rekening}', function (Rekening $rekening) { 
    return $rekening; 
});

//to get item stock amount
Route::get('/getItemAmount/{item}', function (Item $item) { 
    return $item; 
});


//to get one full Company record with current company id
Route::get('/getOneCompany/{company}', function (Company $company) { 
    return $company; 
});


//Presensi Harian
Route::GET('presenceHarianList',[PresenceController::class, 'index'])->middleware('auth', 'authorized');
Route::GET('presenceHarianScanMasuk',[PresenceController::class, 'indexScanMasuk'])->middleware('auth', 'authorized');
Route::GET('presenceHarianScanKeluar',[PresenceController::class, 'indexScanKeluar'])->middleware('auth', 'authorized');
Route::GET('presenceHarianHistory',[PresenceController::class, 'presenceHarianHistory'])->middleware('auth', 'authorized');
Route::get('getPresenceHarianHistory/{start}/{end}', [PresenceController::class, 'getPresenceHarianHistory'])->middleware('auth');

//Presensi Harian Import
Route::GET('presenceHarianImport',[PresenceController::class, 'createImport'])->middleware('auth', 'authorized');
Route::get('getPresenceHarianImportList/{presenceDate}', [PresenceController::class, 'excelPresenceHarianFileGenerator']);
Route::post('presenceHarianImportStore',[PresenceController::class, 'presenceHarianImportStore'])->middleware(['auth']);

//Presensi Harian Satuan
Route::get('getPresenceHarianEmployees',[PresenceController::class, 'getPresenceHarianEmployees'])->middleware('auth');
Route::post('storePresenceHarianEmployee',[PresenceController::class, 'storePresenceHarianEmployee'])->middleware(['auth']);
Route::GET('employeePresenceHarianHistory/{employee}',[PresenceController::class, 'employeePresenceHarianHistory'])->middleware('auth', 'authorized');
Route::get('getEmployeePresenceHarianHistory/{employeeId}/{start}/{end}', [PresenceController::class, 'getEmployeePresenceHarianHistory'])->middleware('auth');
Route::get('presenceHarianEdit/{presence}',[PresenceController::class, 'presenceHarianEdit'])->middleware('auth', 'authorized');
Route::POST('presenceHarianUpdate',[PresenceController::class, 'presenceHarianUpdate'])->middleware(['auth']);
Route::GET('submitScanPresensiMasuk',[PresenceController::class, 'submitPresensiMasukKartuPegawai'])->middleware(['auth']);
Route::GET('submitScanPresensiKeluar',[PresenceController::class, 'submitPresensiKeluarKartuPegawai'])->middleware(['auth']);

//Presensi borongan
Route::GET('boronganList',[BoronganController::class, 'index'])->middleware('auth', 'authorized');
Route::GET('boronganCreate',[BoronganController::class, 'create'])->middleware('auth', 'authorized');
Route::POST('boronganStore',[BoronganController::class, 'storeBorongan'])->middleware('auth');
Route::GET('boronganWorkerAdd/{borongan}',[BoronganController::class, 'tambahDetailPekerjaBorongan'])->middleware('auth', 'authorized');
Route::GET('boronganWorkerList/{borongan}',[BoronganController::class, 'show'])->middleware('auth', 'authorized');
Route::GET('boronganDeleteRecord/{borongan}',[BoronganController::class, 'destroy'])->middleware('auth', 'authorized');
Route::get('getBorongans',[BoronganController::class, 'getBorongans'])->middleware('auth');
Route::POST('storePekerjaBorongan/{borongan}',[BoronganController::class, 'storePekerja'])->name('storePekerjaBorongan')->middleware('auth');
Route::GET('presenceBoronganHistoryPerorangan',[BoronganController::class, 'boronganHistorySingleEmployee'])->middleware('auth', 'authorized');
Route::get('getPresenceBoronganHistory/{start}/{end}',[BoronganController::class, 'getPresenceBoronganHistory'])->middleware('auth');

//Presensi Honorarium
Route::GET('honorariumList',[HonorariumController::class, 'index'])->middleware('auth', 'authorized');
Route::get('getPresenceHonorariumEmployees',[HonorariumController::class, 'getPresenceHonorariumEmployees'])->middleware('auth');
Route::post('storePresenceHonorariumEmployee',[HonorariumController::class, 'storePresenceHonorariumEmployee'])->middleware(['auth']);
Route::GET('presenceHonorariumHistory',[HonorariumController::class, 'presenceHonorariumHistory'])->middleware('auth', 'authorized');
Route::get('getPresenceHonorariumHistory/{start}/{end}/{isGenerated}', [HonorariumController::class, 'getPresenceHonorariumHistory'])->middleware('auth');
Route::GET('presenceHonorariumImport',[HonorariumController::class, 'createImportHonorarium'])->middleware('auth', 'authorized');
Route::get('getHonorariumImportList/{presenceDate}', [HonorariumController::class, 'excelHonorariumFileGenerator']);
Route::post('honorariumImportStore',[HonorariumController::class, 'honorariumImport'])->middleware(['auth']);
Route::GET('honorariumDeleteRecord/{id}',[HonorariumController::class, 'destroy'])->middleware('auth');

//Penggajian
Route::GET('generateGaji',[SalaryController::class, 'indexGenerate'])->name('generateGaji')->middleware('auth', 'authorized');
Route::GET('generateGajiBulanan',[SalaryController::class, 'indexGenerateGajiBulanan'])->name('generateGajiBulanan')->middleware('auth', 'authorized');
Route::GET('payrollList',[SalaryController::class, 'indexPayroll'])->middleware('auth', 'authorized');
Route::GET('payrollListBulanan',[SalaryController::class, 'indexPayrollBulanan'])->middleware('auth', 'authorized');
Route::GET('salariesList/{salaryId}',[SalaryController::class, 'index'])->middleware('auth', 'authorized');
Route::GET('getSalariesList/{salaryId}',[SalaryController::class, 'getSalariesList'])->middleware('auth');
Route::GET('getPayrollList/{start}/{end}',[SalaryController::class, 'getPayrollList'])->middleware('auth');
Route::GET('getPayrollListBulanan/{start}/{end}',[SalaryController::class, 'getPayrollListBulanan'])->middleware('auth');
Route::GET('printPayrollList/{payrollId}',[SalaryController::class, 'printPayrollList'])->middleware('auth', 'authorized');
Route::GET('printPayrollListBulanan/{payrollId}',[SalaryController::class, 'printPayrollListBulanan'])->middleware('auth', 'authorized');
Route::GET('getEmployeeDetailSalaries/{jenis}/{payrollId}',[SalaryController::class, 'getEmployeeDetailSalaries'])->middleware('auth');
Route::post('generateGajiBulananStore',[SalaryController::class, 'generateGajiBulananStore'])->middleware('auth');
Route::get('getServerDate',[DashboardController::class, 'getServerDate'])->middleware('auth');

/*
Route::GET('getEmployeesBulanan',[EmployeeController::class, 'getEmployeesBulanan'])->middleware('auth');
*/

Route::POST('generateGajiStore',[SalaryController::class, 'store'])->middleware('auth');
Route::POST('ubahStatusPenggajianHarian',[SalaryController::class, 'hapusPenggajian'])->middleware('auth');
Route::POST('ubahStatusPenggajianBulanan',[SalaryController::class, 'hapusPenggajian'])->middleware('auth');
Route::POST('slipGajiKaryawan',[SalaryController::class, 'viewSlipGaji'])->middleware('auth', 'authorized');
Route::get('/slipGaji/slipGajiPerPayroll/{dpid}', [InvoiceController::class, 'slipGajiPerPayroll'])->middleware(['auth', 'authorized']);
Route::get('/slipGaji/slipGajiPerPayrollBulanan/{dpid}', [InvoiceController::class, 'slipGajiPerPayrollBulanan'])->middleware(['auth', 'authorized']);

//Penggajian harian
Route::GET('salaryHarianList',[SalaryController::class, 'indexHarian'])->middleware('auth', 'authorized');
Route::GET('getSalariesHarian',[SalaryController::class, 'getSalariesHarian'])->middleware('auth');
Route::GET('checkCetakGajiPegawaiHarian/{salary}',[SalaryController::class, 'checkCetakGajiPegawaiHarian'])->middleware('auth', 'authorized');
Route::GET('printSalaryHarianList/{salary}',[SalaryController::class, 'printSalaryHarianList'])->middleware('auth');
Route::GET('getSalariesHarianForCheck/{salary}',[SalaryController::class, 'getSalariesHarianForCheck'])->middleware('auth');
Route::GET('harianMarkedPaid',[SalaryController::class, 'harianMarkedPaid'])->middleware('auth');

//Penggajian Lembur
Route::GET('lemburBulananList',[SalaryController::class, 'indexLemburBulanan'])->middleware('auth', 'authorized');
Route::GET('getLemburBulanan',[SalaryController::class, 'getLemburBulanan'])->middleware('auth');
Route::GET('getLemburPegawaiBulanan/{salary}',[SalaryController::class, 'getLemburPegawaiBulanan'])->middleware('auth');
Route::GET('checkCetakLemburPegawaiBulanan/{salary}',[SalaryController::class, 'checkCetakLemburPegawaiBulanan'])->middleware('auth', 'authorized');
Route::POST('markLemburIsPaid',[SalaryController::class, 'markLemburIsPaid'])->middleware('auth');

//hapus generate
Route::POST('hapusGenerateGajiHarian',[SalaryController::class, 'hapusGenerateGajiHarian'])->middleware('auth');
Route::POST('hapusGenerateLemburBulanan',[SalaryController::class, 'hapusGenerateLemburBulanan'])->middleware('auth');
Route::POST('hapusGenerateHonorarium',[SalaryController::class, 'hapusGenerateHonorarium'])->middleware('auth');
Route::POST('hapusGenerateBorongan',[SalaryController::class, 'hapusGenerateBorongan'])->middleware('auth');

//Penggajian Borongan
Route::GET('salaryBoronganList',[SalaryController::class, 'indexBorongan'])->middleware('auth', 'authorized');
Route::GET('getSalariesBorongan',[SalaryController::class, 'getSalariesBorongan'])->middleware('auth');
Route::GET('getBoronganSalariesForPrint/{borongan}',[SalaryController::class, 'getBoronganSalariesForPrint'])->middleware('auth');
Route::GET('checkCetakGajiPegawaiBorongan/{borongan}',[SalaryController::class, 'checkCetakGajiPegawaiBorongan'])->middleware('auth', 'authorized');
Route::POST('markBoronganIsPaid',[SalaryController::class, 'markBoronganIsPaid'])->middleware('auth');
Route::GET('printSalaryBoronganList/{borongan}',[SalaryController::class, 'printSalaryBoronganList'])->middleware('auth', 'authorized');
Route::GET('standarBorongan',[BoronganController::class, 'indexStandarBorongan'])->middleware('auth', 'authorized');
Route::GET('standarBoronganTambah',[BoronganController::class, 'standarBoronganAdd'])->middleware('auth', 'authorized');
Route::GET('standarBoronganEdit/{id}',[BoronganController::class, 'standarBoronganEdit'])->middleware('auth', 'authorized');
Route::GET('standarBoronganApproval',[BoronganController::class, 'standarBoronganApproval'])->middleware('auth', 'authorized');
Route::POST('standarBoronganStore',[BoronganController::class, 'standarBoronganStore'])->middleware('auth');
Route::POST('standarBoronganUpdate',[BoronganController::class, 'standarBoronganUpdate'])->middleware('auth');
Route::GET('getBoronganStandardList/{jenis}',[BoronganController::class, 'getBoronganStandardList'])->middleware('auth');
Route::GET('getStandarBoronganPrice/{jenisId}',[BoronganController::class, 'getStandarBoronganPrice'])->middleware('auth');
Route::GET('getStandarBoronganApproval',[BoronganController::class, 'getStandarBoronganApproval'])->middleware('auth');
Route::post('approveStandarChange',[BoronganController::class, 'approveStandarChange'])->middleware(['auth']);

//Penggajian Honorarium
Route::GET('salaryHonorariumList',[SalaryController::class, 'indexHonorarium'])->middleware('auth', 'authorized');
Route::GET('getSalariesHonorarium',[SalaryController::class, 'getSalariesHonorarium'])->middleware('auth');
Route::GET('checkCetakHonorariumPegawai/{salary}',[SalaryController::class, 'checkCetakHonorariumPegawai'])->middleware('auth', 'authorized');
Route::GET('getSalariesHonorariumForCheck/{salary}',[SalaryController::class, 'getSalariesHonorariumForCheck'])->middleware('auth');
Route::GET('printSalaryHonorariumList/{salary}',[SalaryController::class, 'printSalaryHonorariumList'])->middleware('auth', 'authorized');
Route::GET('honorariumMarkedPaid',[SalaryController::class, 'honorariumMarkedPaid'])->middleware('auth');

/*
Route::POST('markStatusBorongan',[SalaryController::class, 'markStatusBorongan'])->middleware('auth');
Route::GET('presenceAddForm',[PresenceController::class, 'createForm'])->middleware('auth');
Route::get('getAllEmployeesForPresenceForm/{presenceDate}',[PresenceController::class, 'getAllEmployeesForPresenceForm'])->middleware('auth');
Route::GET('getDailySalariesDetail',[SalaryController::class, 'getDailySalariesDetail'])->middleware('auth');
*/

Route::GET('employeeList',[EmployeeController::class, 'index'])->name('employeeList')->middleware('auth', 'authorized');
Route::GET('employeeBarcodeList',[EmployeeController::class, 'indexBarcodeList'])->middleware('auth', 'authorized');
Route::GET('employeeAdd',[EmployeeController::class, 'create'])->middleware('auth', 'authorized');
Route::GET('employeeEdit/{employee}',[EmployeeController::class, 'edit'])->middleware('auth', 'authorized');
Route::GET('profileEdit/{employee}',[EmployeeController::class, 'employeePersonalDataEdit'])->middleware('auth', 'authorized');
Route::GET('passedit/{employee}',[EmployeeController::class, 'editPassword'])->middleware('auth', 'authorized');
Route::POST('passUpdate',[EmployeeController::class, 'storePassword'])->name('passUpdate')->middleware('auth');
Route::POST('employeeStore',[EmployeeController::class, 'store'])->name('employeeStore')->middleware('auth');
Route::POST('employeeUpdate',[EmployeeController::class, 'update'])->name('employeeUpdate')->middleware('auth');
Route::POST('employeeMappingUpdate',[EmployeeController::class, 'updateMapping'])->name('employeeMappingUpdate')->middleware('auth');
Route::get('getAllEmployees/{isChecked}/{empType}',[EmployeeController::class, 'getAllEmployees'])->middleware('auth');
Route::get('getEmployeesBarcode',[EmployeeController::class, 'getEmployeesBarcode'])->middleware('auth');
Route::get('getAllActiveEmployees',[EmployeeController::class, 'getAllActiveEmployees'])->middleware('auth');
Route::GET('employeeMappingEdit/{employee}',[EmployeeController::class, 'editMapping'])->middleware('auth', 'authorized');
Route::GET('employeeMappingHistory/{employee}',[EmployeeController::class, 'historyMapping'])->middleware('auth', 'authorized');
Route::GET('organizationStructureList',[OrganizationStructureController::class, 'index'])->middleware('auth', 'authorized');
Route::GET('organizationStructureAdd',[OrganizationStructureController::class, 'create'])->middleware('auth', 'authorized');
Route::POST('organizationStructureStore',[OrganizationStructureController::class, 'store'])->middleware('auth');
Route::POST('organizationStructureUpdate',[OrganizationStructureController::class, 'update'])->middleware('auth');
Route::GET('organizationStructureEdit/{organization_structure}',[OrganizationStructureController::class, 'edit'])->middleware('auth', 'authorized');
Route::GET('structuralPositionList',[StructuralPositionController::class, 'index'])->middleware('auth', 'authorized');
Route::GET('structuralPositionAdd',[StructuralPositionController::class, 'create'])->middleware('auth', 'authorized');
Route::POST('structuralPositionStore',[StructuralPositionController::class, 'store'])->middleware('auth');
Route::POST('structuralPositionUpdate',[StructuralPositionController::class, 'update'])->middleware('auth', 'authorized');
Route::GET('structuralPositionEdit/{structural_position}',[StructuralPositionController::class, 'edit'])->middleware('auth', 'authorized');
Route::GET('workPositionList',[WorkPositionController::class, 'index'])->middleware('auth', 'authorized');
Route::GET('workPositionAdd',[WorkPositionController::class, 'create'])->middleware('auth', 'authorized');
Route::POST('workPositionStore',[WorkPositionController::class, 'store'])->middleware('auth');
Route::POST('workPositionUpdate',[WorkPositionController::class, 'update'])->middleware('auth');
Route::GET('workPositionEdit/{work_position}',[WorkPositionController::class, 'edit'])->middleware('auth', 'authorized');
Route::GET('getAllOrgStructure',[OrganizationStructureController::class, 'list'])->middleware('auth');
Route::GET('getAllStructuralPosition',[StructuralPositionController::class, 'getAllStructuralPosition'])->middleware('auth');
Route::GET('getAllWorkPosition',[WorkPositionController::class, 'getAllWorkPosition'])->middleware('auth');
Route::post('orgStructureList', [EmployeeController::class, 'orgStructureList'])->name('orgStructureList');

/*
*
*
Reporting
*
*/
Route::get('priceList',[DashboardController::class, 'indexHarga'])->middleware(['auth', 'authorized']);
Route::get('getPriceList/{species}/{start}/{end}', [DashboardController::class, 'getPriceList'])->middleware(['auth']);
Route::get('hppList',[DashboardController::class, 'indexHpp'])->name('hppList')->middleware(['auth', 'authorized']);
Route::post('getHpp', [DashboardController::class, 'getHpp'])->middleware(['auth']);
Route::get('rekapitulasiGaji',[DashboardController::class, 'rekapitulasiGaji'])->name('rekapitulasiGaji')->middleware(['auth', 'authorized']);
Route::post('getRekapitulasiGaji', [DashboardController::class, 'getRekapitulasiGaji'])->middleware(['auth']);
Route::get('rekapitulasiGajiPerBulan',[DashboardController::class, 'rekapitulasiGajiPerBulan'])->middleware(['auth', 'authorized']);
Route::get('getRekapitulasiGajiPerBulan', [DashboardController::class, 'getRekapitulasiGajiPerBulan'])->middleware(['auth']);
Route::POST('cetakRekapGajiBulanan', [DashboardController::class, 'cetakRekapGajiBulanan'])->middleware(['auth', 'authorized']);
Route::get('rekapitulasiPembelianPerBulan',[DashboardController::class, 'rekapitulasiPembelianPerBulan'])->middleware(['auth', 'authorized']);
Route::post('getRekapitulasiPembelianPerBulan', [DashboardController::class, 'getRekapitulasiPembelianPerBulan'])->middleware(['auth']);
Route::post('cetakRekapPembelianPerBulan', [DashboardController::class, 'cetakRekapPembelianPerBulan'])->middleware(['auth', 'authorized']);
Route::get('checkPayrollByDateRange',[DashboardController::class, 'checkPayrollByDateRange'])->middleware(['auth', 'authorized']);
Route::get('salaryByDateRange',[DashboardController::class, 'salaryByDateRange'])->middleware(['auth', 'authorized']);
Route::get('getSalaryByDateRange/{opsi}/{start}/{end}', [DashboardController::class, 'getSalaryByDateRange'])->middleware(['auth']);
Route::get('cetakSalaryByDateRange/{opsi}/{start}/{end}', [DashboardController::class, 'cetakSalaryByDateRange'])->middleware(['auth', 'authorized']);
Route::post('getPayrollByDateRange', [DashboardController::class, 'getPayrollByDateRange'])->middleware(['auth']);
Route::get('rekapitulasiPresensi',[DashboardController::class, 'rekapitulasiPresensi'])->name('rekapitulasiPresensi')->middleware(['auth', 'authorized']);
Route::get('getRekapitulasiPresensi/{start}/{end}/{opsi}', [DashboardController::class, 'getRekapitulasiPresensi'])->middleware(['auth']);
Route::get('historyDetailPenjualan',[DashboardController::class, 'historyDetailPenjualan'])->middleware(['auth', 'authorized']);
Route::get('getDetailTransactionListHistory/{species}/{start}/{end}', [DashboardController::class, 'getDetailTransactionListHistory'])->middleware(['auth']);

//jurnal keuangan
Route::get('jurnal',[DashboardController::class, 'jurnalPembelian'])->name('rekapitulasiGaji')->middleware(['auth', 'authorized']);

/*
USER PAGE MAPPING
*/
Route::get('applicationList', [UserPageMappingController::class, 'applicationIndex'])->middleware('auth', 'authorized');
Route::get('pageList/{applicationId}', [UserPageMappingController::class, 'pageIndex'])->middleware('auth', 'authorized');
Route::get('pageAdd/{applicationId}', [UserPageMappingController::class, 'pageAdd'])->middleware('auth', 'authorized');
Route::get('userMappingList', [UserPageMappingController::class, 'userMappingIndex'])->middleware('auth', 'authorized');
Route::post('userMapping', [UserPageMappingController::class, 'mapping'])->middleware('auth', 'authorized');
Route::get('pageMapping/{page}', [UserPageMappingController::class, 'pageMapping'])->middleware('auth', 'authorized');
Route::get('getEmployeesMappingList', [UserPageMappingController::class, 'getEmployeesMappingList'])->middleware('auth');
Route::get('getApplicationList', [UserPageMappingController::class, 'getApplicationList'])->middleware('auth');
Route::get('getPageList/{applicationId}', [UserPageMappingController::class, 'getPageList'])->middleware('auth');
Route::post('pageStore', [UserPageMappingController::class, 'pageStore'])->middleware('auth');
Route::post('applicationMappingStore', [UserPageMappingController::class, 'store'])->middleware('auth');
Route::post('pageMappingStore', [UserPageMappingController::class, 'pageMappingStore'])->middleware('auth');

/*
GOODS
*/
Route::get('goodList',[GoodController::class, 'index'])->middleware(['auth', 'authorized']);
Route::GET('goodAdd',[GoodController::class, 'create'])->middleware('auth', 'authorized');
Route::GET('goodEdit/{good}',[GoodController::class, 'edit'])->middleware('auth', 'authorized');
Route::GET('goodUbahTambah/{good}',[GoodController::class, 'ubahTambah'])->middleware('auth', 'authorized');
Route::GET('goodUbahKurang/{good}',[GoodController::class, 'ubahKurang'])->middleware('auth', 'authorized');
Route::post('goodStore',[GoodController::class, 'store'])->middleware('auth');
Route::post('goodUpdate',[GoodController::class, 'update'])->middleware('auth');
Route::post('goodUbahTambah',[GoodController::class, 'storeTambah'])->middleware('auth');
Route::post('goodUbahKurang',[GoodController::class, 'storeKurang'])->middleware('auth');
Route::get('getGoods/{jenis}', [GoodController::class, 'getGoods'])->middleware('auth');


//Kategori BPP
Route::get('goodCategories',[GoodController::class, 'goodCategoriesIndex'])->middleware(['auth', 'authorized']);
Route::get('getGoodCategories', [GoodController::class, 'getGoodCategories'])->middleware('auth');
 
Route::get('goodCategoriesAdd',[GoodController::class, 'goodCategoriesAdd'])->middleware(['auth', 'authorized']);
Route::post('goodCategoryStore',[GoodController::class, 'goodCategoryStore'])->middleware('auth');

//Kategori BPP
Route::get('goodUnits',[GoodController::class, 'goodUnitsIndex'])->middleware(['auth', 'authorized']);
Route::get('getGoodUnits', [GoodController::class, 'getGoodUnits'])->middleware('auth');
 
Route::get('goodUnitsAdd',[GoodController::class, 'goodUnitsAdd'])->middleware(['auth', 'authorized']);
Route::post('goodUnitsStore',[GoodController::class, 'goodUnitsStore'])->middleware('auth');


/*
*   Route Transaksi Undername
*
*/
Route::get('undernameList',[UndernameController::class, 'index'])->middleware(['auth', 'authorized']);
Route::GET('getAllUndernameTransaction', [UndernameController::class, 'getAllUndernameTransaction'])->middleware(['auth']);
Route::get('undernameAdd',[UndernameController::class, 'createUndername'])->middleware(['auth', 'authorized']);
Route::POST('undernameStore',[UndernameController::class, 'undernameStore'])->middleware(['auth']);
Route::get('undernameEdit/{undername}',[UndernameController::class, 'undernameEdit'])->middleware(['auth', 'authorized']);
Route::POST('undernameUpdate',[UndernameController::class, 'undernameUpdate'])->middleware(['auth'])->name('undernameUpdate');
Route::get('undernameDocument/{undername}',[UndernameController::class, 'undernameDocument'])->middleware(['auth', 'authorized']);
Route::get('detailundernameList/{undername}',[UndernameDetailController::class, 'index'])->middleware(['auth', 'authorized'])->name('detailundernameList');
Route::get('detailundernameAdd/{undername}',[UndernameDetailController::class, 'create'])->middleware(['auth', 'authorized']);
Route::POST('itemDetailUndernameAdd',[UndernameDetailController::class, 'store'])->middleware(['auth']);
Route::GET('getUndernameDetails/{transactionId}', [UndernameDetailController::class, 'view'])->middleware(['auth']);
Route::get('itemDetailUndernameDelete/{undernameDetail}',[UndernameDetailController::class, 'destroy'])->middleware(['auth'])->name('itemDetailUndernameDelete');
Route::get('/undername/pi/{undername}', [UndernameController::class, 'cetak_pi'])->middleware(['auth', 'authorized']);
Route::get('/undername/ipl/{undername}', [UndernameController::class, 'cetak_ipl'])->middleware(['auth', 'authorized']);
Route::GET('getAllUndernameDocuments', [UndernameController::class, 'getAllUndernameDocuments'])->middleware(['auth']);

/*
*   Barcode Generator
*
*
*/
Route::get('barcodeGenerator',[BarcodeController::class, 'create'])->middleware(['auth', 'authorized']);
Route::POST('barcodeImageGenerate',[BarcodeController::class, 'generate'])->middleware(['auth']);
Route::get('barcodeItemList/{speciesId}',[BarcodeController::class, 'itemList'])->middleware(['auth']);
Route::get('barcodeList',[BarcodeController::class, 'barcodeList'])->middleware(['auth', 'authorized']);
Route::get('getAllBarcodes/{speciesId}/{itemId}',[BarcodeController::class, 'getAllBarcodes'])->middleware(['auth']);
Route::GET('getBarcodeFileDownload/{filepath}', [BarcodeController::class, 'getBarcodeFileDownload'])->middleware(['auth']);
Route::GET('deleteBarcode/{id}', [BarcodeController::class, 'deleteBarcode'])->middleware(['auth']);
Route::GET('productChecking/{id}', [BarcodeController::class, 'productChecking']);

/*
*   Surat Menyurat
*
*
*/
Route::get('administrasi',[AdministrationController::class, 'index'])->middleware(['auth', 'authorized']);
Route::get('employeePaperList/{employeeId}',[AdministrationController::class, 'paperList'])->middleware(['auth', 'authorized']);
Route::get('administrasiFormPilihSurat/{employeeId}',[AdministrationController::class, 'formPilih'])->middleware(['auth', 'authorized']);
Route::post('administrasiSuratStore',[AdministrationController::class, 'store'])->middleware(['auth', 'authorized']);
Route::get('administrasiFormSuratPeringatan',[AdministrationController::class, 'formSuratPeringatan'])->middleware(['auth', 'authorized'])->name('administrasiFormSuratPeringatan');
Route::get('administrasiFormSuratPHK',[AdministrationController::class, 'formSuratPHK'])->middleware(['auth', 'authorized'])->name('administrasiFormSuratPHK');
Route::get('administrasiFormSuratPerjalanan',[AdministrationController::class, 'formSuratPerjalananDinas'])->middleware(['auth', 'authorized'])->name('administrasiFormSuratPerjalanan');
Route::get('administrasiAllSurat',[AdministrationController::class, 'indexAllSurat'])->middleware(['auth', 'authorized']);
Route::post('administrasiSuratPerjalananDinasStore',[AdministrationController::class, 'suratPerjalanan'])->middleware(['auth']);
Route::post('administrasiSuratPeringatanStore',[AdministrationController::class, 'suratPeringatan'])->middleware(['auth']);
Route::post('administrasiSuratPHKStore',[AdministrationController::class, 'suratPHK'])->middleware(['auth']);
Route::get('getAllEmployeePaper/{employeeId}',[AdministrationController::class, 'getAllEmployeePaper'])->middleware(['auth']);
Route::get('getAllPapers',[AdministrationController::class, 'getAllPapers']);
Route::GET('getAdministrationFileDownload/{filepath}', [AdministrationController::class, 'getAdministrationFileDownload'])->middleware(['auth']);


Route::get('documentRepository',[AdministrationController::class, 'documentRepo'])->middleware(['auth', 'authorized']);
Route::get('getAllDocuments',[AdministrationController::class, 'getAllDocuments']);
Route::GET('getDocumentFileDownload/{filepath}', [AdministrationController::class, 'getDocumentFileDownload'])->middleware(['auth']);
Route::get('documentRepositoryAdd',[AdministrationController::class, 'documentRepoAdd'])->middleware(['auth', 'authorized']);
Route::post('documentStore',[AdministrationController::class, 'documentStore'])->middleware(['auth']);


/*
*   Stock Opname
*
*/
Route::get('opname',[StoreController::class, 'opname'])->middleware(['auth', 'authorized']);
Route::get('getOpnameData/{isChecked}',[StoreController::class, 'getOpnameData'])->middleware(['auth']);
Route::get('opnameImport',[StoreController::class, 'opnameImport'])->middleware(['auth', 'authorized']);
Route::get('getStockOpnameImportList', [StoreController::class, 'excelStockOpnameFileGenerator'])->middleware(['auth']);
Route::post('stockOpnameStore',[StoreController::class, 'stockOpnameStore'])->middleware(['auth', 'authorized']);
Route::get('historyPerubahanStock', [StoreController::class, 'historyPerubahanStock'])->middleware(['auth', 'authorized']);
Route::get('getPerubahanStock/{species}/{start}/{end}', [StoreController::class, 'getHistoryPerubahanStock'])->middleware(['auth']);


/*
*   Cuti
*
*
*/

Route::get('cuti',[LeaveController::class, 'index'])->middleware(['auth', 'authorized']);
Route::get('cutiAjukan/{empid}',[LeaveController::class, 'ajukanCuti'])->middleware(['auth', 'authorized']);
Route::get('cutiHistory/{empid}',[LeaveController::class, 'view'])->middleware(['auth', 'authorized']);
Route::get('cutiHolidayList',[LeaveController::class, 'indexHoliday'])->middleware(['auth', 'authorized']);
Route::get('getAllActiveEmployeesForLeave',[LeaveController::class, 'getAllActiveEmployeesForLeave'])->middleware('auth');
Route::post('dateCounterChecker',[LeaveController::class, 'dateCounterChecker'])->middleware('auth');
Route::post('cutiStore',[LeaveController::class, 'store'])->middleware('auth');
Route::post('cutiUpdate',[LeaveController::class, 'update'])->middleware('auth');
Route::post('dateOverlapExist',[LeaveController::class, 'dateOverlapExist'])->middleware('auth');
Route::get('getAllEmployeeLeaveHistory/{id}',[LeaveController::class, 'viewEmployee'])->middleware('auth');
Route::post('cutiHolidayDateAdddayDateStore',[LeaveController::class, 'cutiHolidayDateAdddayDateStore'])->middleware('auth');
Route::post('cutiHolidayDateDestroy',[LeaveController::class, 'destroy'])->middleware('auth');
Route::get('getAllHolidays',[LeaveController::class, 'getAllHolidays'])->middleware('auth');

/*
*   Stock masuk gudang
*   Tally masuk barang
*
*/
Route::get('scanList',[StockController::class, 'index'])->middleware(['auth', 'authorized']);
Route::get('scanDetailBarcodeTransactionList/{transactionId}',[StockController::class, 'indexTransactionBarcode'])->middleware(['auth', 'authorized']);
Route::get('transactionBarcodeList/{transactionId}',[StockController::class, 'indexTransactionBarcodeList'])->middleware(['auth', 'authorized']);
Route::get('scanRekapMasuk',[StockController::class, 'indexScanMasuk'])->middleware(['auth', 'authorized']);
Route::get('scanRekapMasukHari/{storageDate}',[StockController::class, 'indexScanMasukHari'])->middleware(['auth', 'authorized']);
Route::get('scanRekapMasukHariBarcodeList/{storageDate}/{itemId}',[StockController::class, 'indexScanMasukHariBarcodeList'])->middleware(['auth', 'authorized']);
Route::get('scanRekapKeluar',[StockController::class, 'indexScanKeluar'])->middleware(['auth', 'authorized']);
Route::get('scanRekapKeluarHari/{transactionId}/{loadingDate}',[StockController::class, 'indexScanKeluarHari'])->middleware(['auth', 'authorized']);
Route::get('scanRekapKeluarBarcodeList/{transactionId}/{loadingDate}/{itemId}',[StockController::class, 'indexScanKeluarBarcodeList'])->middleware(['auth', 'authorized']);
Route::get('scanTransactionList',[StockController::class, 'indexTransaction'])->middleware(['auth', 'authorized']);
Route::get('scanMasuk',[StockController::class, 'create'])->middleware(['auth', 'authorized']);
Route::get('scanKeluar/{transactionId}',[StockController::class, 'createKeluar'])->middleware(['auth', 'authorized']);
Route::get('scanKeluarV2/{transactionId}',[StockController::class, 'createKeluarV2'])->middleware(['auth', 'authorized']);
Route::get('scanEditBarcode/{barcodeId}',[StockController::class, 'show'])->middleware(['auth', 'authorized']);
Route::post('scanStoreMasuk',[StockController::class, 'storeMasuk'])->middleware(['auth']);
Route::post('scanStoreKeluar',[StockController::class, 'storeKeluar'])->middleware(['auth']);
Route::post('scanStoreKeluarV2',[StockController::class, 'storeKeluarV2'])->middleware(['auth']);
Route::get('scanStoreBarcodeKeluar',[StockController::class, 'scanStoreBarcodeKeluar'])->middleware(['auth']);
Route::get('checkStatusBarcodeBarang',[StockController::class, 'checkStatusBarcodeBarang'])->middleware(['auth']);
Route::GET('getItemsForScanPage/{speciesId}', [StockController::class, 'getItemsForScanPage'])->middleware(['auth']);
Route::GET('getAllBarcodeData', [StockController::class, 'getAllBarcodeData'])->middleware(['auth']);
Route::GET('getAllBarcodeTransactionData', [StockController::class, 'getAllBarcodeTransactionData'])->middleware(['auth']);
Route::GET('getAllBarcodeExportTransaction', [StockController::class, 'getAllBarcodeExportTransaction'])->middleware(['auth']);
Route::GET('getAllBarcodeItemDetail/{transactionId}', [StockController::class, 'getAllBarcodeItemDetail'])->middleware(['auth']);
Route::GET('getScanMasukHarian', [StockController::class, 'getScanMasukHarian'])->middleware(['auth']);
Route::GET('getScanMasukHarianTanggal', [StockController::class, 'getScanMasukHarianTanggal'])->middleware(['auth']);
Route::GET('getBarcodeListTanggalItem', [StockController::class, 'getBarcodeListTanggalItem'])->middleware(['auth']);
Route::GET('getScanKeluarBarcodeList', [StockController::class, 'getScanKeluarBarcodeList'])->middleware(['auth']);
Route::POST('updateHapusBarcode',[StockController::class, 'updateHapusBarcode'])->middleware(['auth']);
Route::GET('getScanKeluarData', [StockController::class, 'getScanKeluarData'])->middleware(['auth']);
Route::GET('getScannedKeluarTransaksiHari', [StockController::class, 'getScannedKeluarTransaksiHari'])->middleware(['auth']);



require __DIR__.'/auth.php';
