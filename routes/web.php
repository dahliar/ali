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

Route::get('home',[DashboardController::class, 'index'])->middleware(['auth','authorized']);
Route::get('home2',[DashboardController::class, 'indexHome2'])->middleware(['auth']);
Route::GET('employeeList2',[EmployeeController::class, 'index2'])->name('employeeList')->middleware('auth', 'authorized');


Route::get('unauthorized', function () {
    return view('partial.noAccess');
});

/*
*   Route Transaksi Penjualan
*
*/
Route::get('transactionList',[TransactionController::class, 'index'])->middleware(['auth', 'authorized']);
Route::get('transactionAdd',[TransactionController::class, 'create'])->middleware(['auth', 'authorized']);
Route::get('transactionView',[TransactionController::class, 'show'])->middleware(['auth', 'authorized']);
Route::get('transactionEdit/{transaction}',[TransactionController::class, 'edit'])->middleware(['auth', 'authorized']);
Route::POST('transactionStore',[TransactionController::class, 'store'])->middleware(['auth']);
Route::POST('transactionUpdate',[TransactionController::class, 'update'])->middleware(['auth'])->name('transactionUpdate');


Route::get('detailtransactionList/{transaction}',[DetailTransactionController::class, 'index'])->middleware(['auth', 'authorized'])->name('detailtransactionList');
Route::get('detailtransactionAdd/{transaction}',[DetailTransactionController::class, 'create'])->middleware(['auth', 'authorized']);
Route::get('itemDetailTransactionAdd',[DetailTransactionController::class, 'store'])->middleware(['auth'])->name('itemDetailTransactionAdd');

Route::get('itemDetailTransactionDelete/{detail_transaction}',[DetailTransactionController::class, 'destroy'])->middleware(['auth'])->name('itemDetailTransactionDelete');
Route::GET('getAllExportTransaction', [TransactionController::class, 'getAllExportTransaction'])->middleware(['auth']);
Route::GET('getAllDetail/{transactionId}', [DetailTransactionController::class, 'getAllDetail'])->middleware(['auth']);
Route::get('/transaction/pi/{transaction}', [InvoiceController::class, 'cetak_pi'])->middleware(['auth', 'authorized']);
Route::get('/transaction/ipl/{transaction}', [InvoiceController::class, 'cetak_ipl'])->middleware(['auth', 'authorized']);


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




/*
*   Route Transaksi Pembelian/Purchase
*
*/
Route::get('purchaseList',[PurchaseController::class, 'index'])->middleware(['auth', 'authorized']);
Route::GET('getPurchaseList/{negara}/{statusTransaksi}/{start}/{end}', [PurchaseController::class, 'getPurchaseList'])->middleware(['auth']);
Route::get('purchaseAdd',[PurchaseController::class, 'create'])->middleware(['auth', 'authorized']);
Route::get('purchaseStore',[PurchaseController::class, 'store'])->middleware(['auth'])->name('purchaseStore');
Route::get('purchaseEdit/{purchase}',[PurchaseController::class, 'edit'])->middleware(['auth', 'authorized']);
Route::post('purchaseUpdate',[PurchaseController::class, 'update'])->middleware(['auth']);



/*
Route::GET('getAllPurchases', [PurchaseController::class, 'getAllPurchases'])->middleware(['auth'])->name('getAllPurchases');
*/


Route::get('purchaseItems/{purchase}',[DetailPurchaseController::class, 'index'])->middleware(['auth'])->name('purchaseItems', 'authorized');
Route::get('purchaseItemAdd/{purchase}',[DetailPurchaseController::class, 'create'])->middleware(['auth', 'authorized']);
Route::get('purchaseItemStore',[DetailPurchaseController::class, 'store'])->middleware(['auth']);
Route::POST('itemDetailPurchaseDelete',[DetailPurchaseController::class, 'destroy'])->middleware(['auth']);



Route::GET('getAllPurchaseItems/{purchase}', [DetailPurchaseController::class, 'getAllPurchaseItems'])->middleware(['auth'])->name('getAllPurchaseItems');


Route::get('/purchase/notaPembelian/{purchase}', [InvoiceController::class, 'cetakNotaPembelian'])->middleware(['auth', 'authorized']);

//ITEM STOCKS
Route::get('itemStockList',[ItemController::class, 'index'])->middleware(['auth', 'authorized'])->name('itemStockList');
Route::GET('getAllStockItem/{speciesId}', [ItemController::class, 'getAllStockItem'])->middleware(['auth']);
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


Route::get('itemStockEdit/{store}',[StoreController::class, 'edit'])->middleware(['auth', 'authorized'])->name('itemStockAdd');
Route::get('itemStoreDetail/{storeId}',[StoreController::class, 'itemStoreDetail'])->middleware(['auth', 'authorized']);
Route::get('storeAdd',[StoreController::class, 'store'])->middleware(['auth'])->name('storeAdd');
Route::post('storeSubtract',[StoreController::class, 'storeSubtract'])->middleware(['auth'])->name('storeSubtract');
Route::get('storeUpdate',[StoreController::class, 'update'])->middleware(['auth'])->name('storeUpdate');

//ITEM STOCKS
Route::get('speciesStockList',[ItemController::class, 'indexStockSpecies'])->middleware(['auth', 'authorized']);
Route::get('getAllSpeciesStock',[ItemController::class, 'getSpeciesStock'])->middleware(['auth']);


//Approval
Route::get('itemStockApprovalPenambahan',[StoreController::class, 'indexApprovalPenambahan'])->middleware(['auth', 'authorized']);
Route::get('itemStockApprovalPengurangan',[StoreController::class, 'indexApprovalPengurangan'])->middleware(['auth', 'authorized']);
Route::post('getStoresRecord',[StoreController::class, 'getStoresRecord'])->middleware(['auth']);
Route::post('approveStockChange',[StoreController::class, 'stockChange'])->middleware(['auth']);
Route::post('deleteStockChange',[StoreController::class, 'stockChangeDelete'])->middleware(['auth']);
Route::post('deleteStockSubtractChange',[StoreController::class, 'deleteStockSubtractChange'])->middleware(['auth']);

Route::get('itemStockSubtractEdit/{stockSubtract}',[StoreController::class, 'subtractEdit'])->middleware(['auth', 'authorized'])->name('itemStockAdd');
Route::post('stockSubtractUpdate',[StoreController::class, 'subtractUpdate'])->middleware(['auth'])->name('storeUpdate');
Route::post('getStorekSubtractRecord',[StoreController::class, 'getStorekSubtractRecord'])->middleware(['auth']);
Route::post('approveStockSubtractChange',[StoreController::class, 'stockSubtractChange'])->middleware(['auth']);

//SPECIES
Route::get('speciesList',[SpeciesController::class, 'index'])->middleware(['auth', 'authorized'])->name('speciesList');
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
//Route::get('companyView',[CompanyController::class, 'show'])->middleware(['auth']);
Route::get('companyEdit/{company}',[CompanyController::class, 'edit'])->middleware(['auth', 'authorized']);
Route::get('companyStore',[CompanyController::class, 'store'])->middleware(['auth'])->name('companyStore');
Route::get('companyUpdate',[CompanyController::class, 'update'])->middleware(['auth'])->name('companyUpdate');
Route::GET('getAllCompany', [CompanyController::class, 'getAllCompany'])->middleware(['auth']);

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




//Presensi borongan
Route::GET('boronganList',[BoronganController::class, 'index'])->middleware('auth', 'authorized');
Route::GET('boronganCreate',[BoronganController::class, 'create'])->middleware('auth', 'authorized');
Route::POST('boronganStore',[BoronganController::class, 'storeBorongan'])->middleware('auth');
Route::GET('boronganWorkerAdd/{borongan}',[BoronganController::class, 'tambahDetailPekerjaBorongan'])->middleware('auth', 'authorized');
Route::GET('boronganWorkerList/{borongan}',[BoronganController::class, 'show'])->middleware('auth', 'authorized');
Route::GET('boronganDeleteRecord/{borongan}',[BoronganController::class, 'destroy'])->middleware('auth', 'authorized');
Route::get('getBorongans',[BoronganController::class, 'getBorongans'])->middleware('auth');
Route::POST('storePekerjaBorongan/{borongan}',[BoronganController::class, 'storePekerja'])->name('storePekerjaBorongan')->middleware('auth');

//Presensi Honorarium
Route::GET('honorariumList',[HonorariumController::class, 'index'])->middleware('auth', 'authorized');
Route::get('getPresenceHonorariumEmployees',[HonorariumController::class, 'getPresenceHonorariumEmployees'])->middleware('auth');
Route::post('storePresenceHonorariumEmployee',[HonorariumController::class, 'storePresenceHonorariumEmployee'])->middleware(['auth']);
Route::GET('presenceHonorariumHistory',[HonorariumController::class, 'presenceHonorariumHistory'])->middleware('auth', 'authorized');
Route::get('getPresenceHonorariumHistory/{start}/{end}', [HonorariumController::class, 'getPresenceHonorariumHistory'])->middleware('auth');

Route::GET('presenceHonorariumImport',[HonorariumController::class, 'createImportHonorarium'])->middleware('auth', 'authorized');
Route::get('getHonorariumImportList/{presenceDate}', [HonorariumController::class, 'excelHonorariumFileGenerator']);
Route::post('honorariumImportStore',[HonorariumController::class, 'honorariumImport'])->middleware(['auth']);





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
Route::GET('employeeAdd',[EmployeeController::class, 'create'])->middleware('auth', 'authorized');
Route::GET('employeeEdit/{employee}',[EmployeeController::class, 'edit'])->middleware('auth', 'authorized');
Route::GET('profileEdit/{employee}',[EmployeeController::class, 'employeePersonalDataEdit'])->middleware('auth', 'authorized');
Route::GET('passedit/{employee}',[EmployeeController::class, 'editPassword'])->middleware('auth', 'authorized');
Route::POST('passUpdate',[EmployeeController::class, 'storePassword'])->name('passUpdate')->middleware('auth');
Route::POST('employeeStore',[EmployeeController::class, 'store'])->name('employeeStore')->middleware('auth');
Route::POST('employeeUpdate',[EmployeeController::class, 'update'])->name('employeeUpdate')->middleware('auth');
Route::POST('employeeMappingUpdate',[EmployeeController::class, 'updateMapping'])->name('employeeMappingUpdate')->middleware('auth');
Route::get('getAllEmployees',[EmployeeController::class, 'getAllEmployees'])->middleware('auth');
Route::GET('employeeMappingEdit/{employee}',[EmployeeController::class, 'editMapping'])->middleware('auth', 'authorized');

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
Route::post('getRekapitulasiGajiPerBulan', [DashboardController::class, 'getRekapitulasiGajiPerBulan'])->middleware(['auth']);
Route::post('cetakRekapGajiBulanan', [DashboardController::class, 'cetakRekapGajiBulanan'])->middleware(['auth', 'authorized']);


Route::get('rekapitulasiPembelianPerBulan',[DashboardController::class, 'rekapitulasiPembelianPerBulan'])->middleware(['auth', 'authorized']);
Route::post('getRekapitulasiPembelianPerBulan', [DashboardController::class, 'getRekapitulasiPembelianPerBulan'])->middleware(['auth']);
Route::post('cetakRekapPembelianPerBulan', [DashboardController::class, 'cetakRekapPembelianPerBulan'])->middleware(['auth', 'authorized']);

Route::get('checkPayrollByDateRange',[DashboardController::class, 'checkPayrollByDateRange'])->middleware(['auth', 'authorized']);
Route::post('getPayrollByDateRange', [DashboardController::class, 'getPayrollByDateRange'])->middleware(['auth']);

Route::get('rekapitulasiPresensi',[DashboardController::class, 'rekapitulasiPresensi'])->name('rekapitulasiPresensi')->middleware(['auth', 'authorized']);
Route::get('getRekapitulasiPresensi/{start}/{end}/{opsi}', [DashboardController::class, 'getRekapitulasiPresensi'])->middleware(['auth']);

Route::get('historyDetailPenjualan',[DashboardController::class, 'historyDetailPenjualan'])->middleware(['auth', 'authorized']);
Route::get('getDetailTransactionListHistory/{species}/{start}/{end}', [DashboardController::class, 'getDetailTransactionListHistory'])->middleware(['auth']);





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



Route::get('getGoods', [GoodController::class, 'getGoods'])->middleware('auth');




/*
*   Route Transaksi Undername
*
*/
Route::get('undernameList',[TransactionController::class, 'indexUndername'])->middleware(['auth', 'authorized']);
Route::GET('getAllUndernameTransaction', [TransactionController::class, 'getAllUndernameTransaction'])->middleware(['auth']);
Route::get('undernameAdd',[TransactionController::class, 'createUndername'])->middleware(['auth', 'authorized']);
Route::POST('undernameStore',[TransactionController::class, 'undernameStore'])->middleware(['auth']);
Route::get('undernameEdit/{undername}',[TransactionController::class, 'undernameEdit'])->middleware(['auth', 'authorized']);
Route::POST('undernameUpdate',[TransactionController::class, 'undernameUpdate'])->middleware(['auth'])->name('undernameUpdate');
Route::get('detailundernameList/{undername}',[UndernameDetailController::class, 'index'])->middleware(['auth', 'authorized'])->name('detailundernameList');
Route::get('detailundernameAdd/{undername}',[UndernameDetailController::class, 'create'])->middleware(['auth', 'authorized']);
Route::POST('itemDetailUndernameAdd',[UndernameDetailController::class, 'store'])->middleware(['auth']);
Route::GET('getUndernameDetails/{transactionId}', [UndernameDetailController::class, 'view'])->middleware(['auth']);
Route::get('itemDetailUndernameDelete/{undernameDetail}',[UndernameDetailController::class, 'destroy'])->middleware(['auth'])->name('itemDetailUndernameDelete');
Route::get('/undername/pi/{undername}', [UndernameController::class, 'cetak_pi'])->middleware(['auth', 'authorized']);
Route::get('/undername/ipl/{undername}', [UndernameController::class, 'cetak_ipl'])->middleware(['auth', 'authorized']);



/*
Route::get('transactionView',[TransactionController::class, 'show'])->middleware(['auth', 'authorized']);
Route::get('detailtransactionList/{transaction}',[DetailTransactionController::class, 'index'])->middleware(['auth', 'authorized'])->name('detailtransactionList');
Route::get('detailtransactionAdd/{transaction}',[DetailTransactionController::class, 'create'])->middleware(['auth', 'authorized']);
Route::get('itemDetailTransactionAdd',[DetailTransactionController::class, 'store'])->middleware(['auth'])->name('itemDetailTransactionAdd');
*/

require __DIR__.'/auth.php';
