<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DetailTransactionController;
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

use App\Models\Rekening; 
use App\Models\Company; 
use App\Models\Item; 
use App\Models\Transaction; 
use App\Models\Purchase; 
use App\Models\DetailPurchase; 
use App\Models\DetailTransaction; 
use App\Models\User; 
use App\Models\Presence; 

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

Route::get('testUser',[User::class, 'testUser'])->middleware(['auth']);

Route::get('/', function () {
    return view('welcome');
});
/*
Route::get('/show', function () {
    return view('show');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');
*/

Route::get('/home', function () {
    return view('home');
})->middleware(['auth']);

/*
Route::get('/homeTwo', function () {
    return view('home2');
})->middleware(['auth']);
*/

Route::get('unauthenticated', function () {
    return view('partial.footer');
})->name('unauthenticated');

/*
*   Route Transaksi Penjualan
*
*/
Route::get('transactionList',[TransactionController::class, 'index'])->middleware(['auth']);
Route::get('transactionAdd',[TransactionController::class, 'create'])->middleware(['auth']);
Route::get('transactionView',[TransactionController::class, 'show'])->middleware(['auth']);
Route::get('transactionEdit/{transaction}',[TransactionController::class, 'edit'])->middleware(['auth']);
Route::get('transactionStore',[TransactionController::class, 'store'])->middleware(['auth'])->name('transactionStore');
Route::get('transactionUpdate',[TransactionController::class, 'update'])->middleware(['auth'])->name('transactionUpdate');


Route::get('detailtransactionList/{transaction}',[DetailTransactionController::class, 'index'])->middleware(['auth'])->name('detailtransactionList');
Route::get('detailtransactionAdd/{transaction}',[DetailTransactionController::class, 'create'])->middleware(['auth']);
Route::get('itemDetailTransactionAdd',[DetailTransactionController::class, 'store'])->middleware(['auth'])->name('itemDetailTransactionAdd');

Route::get('itemDetailTransactionDelete/{detail_transaction}',[DetailTransactionController::class, 'destroy'])->middleware(['auth'])->name('itemDetailTransactionDelete');

/*
*   Route Transaksi Pembelian/Purchase
*
*/
Route::get('purchaseList',[PurchaseController::class, 'index'])->middleware(['auth']);
Route::GET('getPurchaseList', [PurchaseController::class, 'getPurchaseList'])->middleware(['auth']);
Route::get('purchaseAdd',[PurchaseController::class, 'create'])->middleware(['auth']);
Route::get('purchaseStore',[PurchaseController::class, 'store'])->middleware(['auth'])->name('purchaseStore');

Route::GET('getAllPurchases', [PurchaseController::class, 'getAllPurchases'])->middleware(['auth'])->name('getAllPurchases');


Route::get('purchaseItems/{purchase}',[DetailPurchaseController::class, 'index'])->middleware(['auth'])->name('purchaseItems');
Route::get('purchaseItemAdd/{purchase}',[DetailPurchaseController::class, 'create'])->middleware(['auth']);
Route::get('purchaseItemStore',[DetailPurchaseController::class, 'store'])->middleware(['auth']);

Route::GET('getAllPurchaseItems/{purchase}', [DetailPurchaseController::class, 'getAllPurchaseItems'])->middleware(['auth'])->name('getAllPurchaseItems');


Route::get('/purchase/notaPembelian/{purchase}', [InvoiceController::class, 'cetakNotaPembelian'])->middleware(['auth']);

//ITEM STOCKS
Route::get('itemStockList',[ItemController::class, 'index'])->middleware(['auth'])->name('itemStockList');
Route::GET('getAllStockItem/{speciesId}', [ItemController::class, 'getAllStockItem'])->middleware(['auth']);
Route::get('itemStockView/{itemId}',[ItemController::class, 'show'])->middleware(['auth']);
Route::get('itemStockViewUnpacked/{itemId}',[ItemController::class, 'showUnpacked'])->middleware(['auth']);
Route::GET('getItemHistory/{speciesId}', [ItemController::class, 'getItemHistory'])->middleware(['auth']);
Route::GET('getUnpackedHistory/{speciesId}', [ItemController::class, 'getUnpackedItemHistory'])->middleware(['auth']);
Route::get('editUnpacked/{itemId}',[StoreController::class, 'editUnpacked'])->middleware(['auth'])->name('editUnpacked');
Route::get('unpackedUpdate',[StoreController::class, 'unpackedUpdate'])->middleware(['auth'])->name('unpackedUpdate');
Route::get('itemStockAdd/{itemId}',[StoreController::class, 'create'])->middleware(['auth'])->name('itemStockAdd');
Route::get('itemStockEdit/{store}',[StoreController::class, 'edit'])->middleware(['auth'])->name('itemStockAdd');
Route::get('itemStoreDetail/{storeId}',[StoreController::class, 'itemStoreDetail'])->middleware(['auth']);
Route::get('storeAdd',[StoreController::class, 'store'])->middleware(['auth'])->name('storeAdd');
Route::get('storeUpdate',[StoreController::class, 'update'])->middleware(['auth'])->name('storeUpdate');

//SPECIES
Route::get('speciesList',[SpeciesController::class, 'index'])->middleware(['auth'])->name('speciesList');
Route::GET('getAllSpecies/{familyId}', [SpeciesController::class, 'getAllSpecies'])->middleware(['auth']);
Route::GET('getAllSpeciesSize/{speciesId}', [SpeciesController::class, 'getAllSpeciesSize'])->middleware(['auth']);
Route::GET('getAllSpeciesItem/{speciesId}', [SpeciesController::class, 'getAllSpeciesItem'])->middleware(['auth']);
Route::get('itemList/{speciesId}',[SpeciesController::class, 'itemList'])->middleware(['auth'])->name('itemList');
Route::get('sizeList/{speciesId}',[SpeciesController::class, 'sizeList'])->middleware(['auth']);
Route::GET('getAllItem/{speciesId}', [SpeciesController::class, 'getAllItem'])->middleware(['auth']);

Route::get('editSpecies/{speciesId}',[SpeciesController::class, 'editSpecies'])->middleware(['auth']);
Route::get('editSpeciesSize/{sizeId}',[SpeciesController::class, 'editSpeciesSize'])->middleware(['auth']);
Route::get('editSpeciesItem/{itemId}',[SpeciesController::class, 'editSpeciesItem'])->middleware(['auth']);

Route::get('addSpeciesSize/{speciesId}',[SpeciesController::class, 'createSize'])->middleware(['auth']);
Route::get('addSpeciesItem/{speciesId}',[SpeciesController::class, 'createItem'])->middleware(['auth']);

Route::get('sizeCreateStore',[SpeciesController::class, 'storeSize'])->middleware(['auth']);
Route::post('itemCreateStore',[SpeciesController::class, 'storeItem'])->middleware(['auth']);

Route::get('sizeEditStore',[SpeciesController::class, 'updateSize'])->middleware(['auth']);
Route::get('itemEditStore',[SpeciesController::class, 'updateItem'])->middleware(['auth']);


//COMPANIES
Route::get('companyList',[CompanyController::class, 'index'])->middleware(['auth']);
Route::get('companyAdd',[CompanyController::class, 'create'])->middleware(['auth']);
//Route::get('companyView',[CompanyController::class, 'show'])->middleware(['auth']);
Route::get('companyEdit/{company}',[CompanyController::class, 'edit'])->middleware(['auth']);
Route::get('companyStore',[CompanyController::class, 'store'])->middleware(['auth'])->name('companyStore');
Route::get('companyUpdate',[CompanyController::class, 'update'])->middleware(['auth'])->name('companyUpdate');
Route::GET('getAllCompany', [CompanyController::class, 'getAllCompany'])->middleware(['auth']);

Route::GET('getAllTransaction', [TransactionController::class, 'getAlltransaction'])->middleware(['auth']);
Route::GET('getAllDetail/{transactionId}', [DetailTransactionController::class, 'getAllDetail'])->middleware(['auth']);
Route::get('/transaction/pi/{transaction}', [InvoiceController::class, 'cetak_pi'])->middleware(['auth']);
Route::get('/transaction/ipl/{transaction}', [InvoiceController::class, 'cetak_ipl'])->middleware(['auth']);

//to get size for all species
Route::GET('getItems/{speciesId}', [ItemController::class, 'getItemForSelectOption'])->middleware(['auth']);
Route::GET('getOneStore/{storeId}', [StoreController::class, 'getOneStore'])->middleware(['auth']);

//to get one full Rekening record with current rekening id
Route::get('/getOneRekening/{rekening}', function (Rekening $rekening) { 
    return $rekening; 
});

//to get item stock amount
Route::get('/getItemAmount/{item}', function (Item $item) { 
    return $item->amount; 
});


//to get one full Company record with current company id
Route::get('/getOneCompany/{company}', function (Company $company) { 
    return $company; 
});


//Presensi Harian
Route::GET('presenceHarianList',[PresenceController::class, 'index'])->middleware('auth');
Route::GET('presenceHarianHistory',[PresenceController::class, 'presenceHarianHistory'])->middleware('auth');
Route::get('getPresenceHarianHistory/{start}/{end}', [PresenceController::class, 'getPresenceHarianHistory'])->middleware('auth');

//Presensi Harian Import
Route::GET('presenceHarianImport',[PresenceController::class, 'createImport'])->middleware('auth');
Route::get('getPresenceHarianImportList/{presenceDate}', [PresenceController::class, 'excelPresenceHarianFileGenerator']);
Route::post('presenceHarianImportStore',[PresenceController::class, 'presenceHarianImportStore'])->middleware(['auth']);

//Presensi Harian Satuan
Route::get('getPresenceHarianEmployees',[PresenceController::class, 'getPresenceHarianEmployees'])->middleware('auth');
Route::post('storePresenceHarianEmployee',[PresenceController::class, 'storePresenceHarianEmployee'])->middleware(['auth']);
Route::GET('employeePresenceHarianHistory/{employee}',[PresenceController::class, 'employeePresenceHarianHistory'])->middleware('auth');
Route::get('getEmployeePresenceHarianHistory/{employeeId}/{start}/{end}', [PresenceController::class, 'getEmployeePresenceHarianHistory'])->middleware('auth');

//Presensi borongan
Route::GET('boronganList',[BoronganController::class, 'index'])->middleware('auth');
Route::GET('boronganCreate',[BoronganController::class, 'create'])->middleware('auth');
Route::POST('boronganStore',[BoronganController::class, 'storeBorongan'])->middleware('auth');
Route::GET('boronganWorkerAdd/{borongan}',[BoronganController::class, 'tambahDetailPekerjaBorongan'])->middleware('auth');
Route::GET('boronganWorkerList/{borongan}',[BoronganController::class, 'show'])->middleware('auth');
Route::GET('boronganDeleteRecord/{borongan}',[BoronganController::class, 'destroy'])->middleware('auth');
Route::get('getBorongans',[BoronganController::class, 'getBorongans'])->middleware('auth');
Route::POST('storePekerjaBorongan',[BoronganController::class, 'storePekerja'])->name('storePekerjaBorongan')->middleware('auth');


//Penggajian harian
Route::GET('salaryHarianList',[SalaryController::class, 'index'])->middleware('auth');
Route::POST('salaryHarianGenerate',[SalaryController::class, 'salaryHarianGenerate'])->middleware('auth');
Route::GET('getSalariesHarian',[SalaryController::class, 'getSalariesHarian'])->middleware('auth');
Route::GET('checkCetakGajiPegawaiHarian/{salary}',[SalaryController::class, 'checkCetakGajiPegawaiHarian'])->middleware('auth');
Route::GET('printSalaryHarianList/{salary}',[SalaryController::class, 'printSalaryHarianList'])->middleware('auth');
Route::GET('getSalariesHarianForCheck/{salary}',[SalaryController::class, 'getSalariesHarianForCheck'])->middleware('auth');







//Penggajian Lembur
Route::GET('checkCetakLemburPegawaiBulanan/{salary}',[SalaryController::class, 'checkCetakLemburPegawaiBulanan'])->middleware('auth');

//Penggajian Borongan
Route::GET('salaryBoronganList',[SalaryController::class, 'indexBorongan'])->middleware('auth');
Route::GET('getSalariesBorongan',[SalaryController::class, 'getSalariesBorongan'])->middleware('auth');
Route::POST('salaryBoronganGenerate',[SalaryController::class, 'salaryBoronganGenerate'])->middleware('auth');
Route::GET('checkCetakGajiPegawaiBorongan/{salary}',[SalaryController::class, 'checkCetakGajiPegawaiBorongan'])->middleware('auth');
Route::POST('markSalariesIsPaid/{salary}/{tanggalBayar}',[SalaryController::class, 'markSalariesIsPaid'])->middleware('auth');

/*
Route::POST('markStatusBorongan',[SalaryController::class, 'markStatusBorongan'])->middleware('auth');
Route::GET('presenceAddForm',[PresenceController::class, 'createForm'])->middleware('auth');
Route::get('getAllEmployeesForPresenceForm/{presenceDate}',[PresenceController::class, 'getAllEmployeesForPresenceForm'])->middleware('auth');

Route::GET('getBoronganSalariesForPrint/{salary}',[SalaryController::class, 'getBoronganSalariesForPrint'])->middleware('auth');
Route::GET('getDailySalariesDetail',[SalaryController::class, 'getDailySalariesDetail'])->middleware('auth');
Route::GET('getLemburPegawaiBulanan/{salary}',[SalaryController::class, 'getLemburPegawaiBulanan'])->middleware('auth');
*/

Route::GET('employeeList',[EmployeeController::class, 'index'])->name('employeeList')->middleware('auth');
Route::GET('employeeAdd',[EmployeeController::class, 'create'])->middleware('auth');
Route::GET('employeeEdit/{employee}',[EmployeeController::class, 'edit'])->middleware('auth');
Route::GET('profileEdit/{employee}',[EmployeeController::class, 'employeePersonalDataEdit'])->middleware('auth');
Route::GET('passedit/{employee}',[EmployeeController::class, 'editPassword'])->middleware('auth');
Route::POST('passUpdate',[EmployeeController::class, 'storePassword'])->name('passUpdate')->middleware('auth');
Route::POST('employeeStore',[EmployeeController::class, 'store'])->name('employeeStore')->middleware('auth');
Route::POST('employeeUpdate',[EmployeeController::class, 'update'])->name('employeeUpdate')->middleware('auth');
Route::POST('employeeMappingUpdate',[EmployeeController::class, 'updateMapping'])->name('employeeMappingUpdate')->middleware('auth');
Route::get('getAllEmployees',[EmployeeController::class, 'getAllEmployees'])->middleware('auth');
Route::GET('employeeMappingEdit/{employee}',[EmployeeController::class, 'editMapping'])->middleware('auth');

Route::GET('organizationStructureList',[OrganizationStructureController::class, 'index'])->middleware('auth');
Route::GET('organizationStructureAdd',[OrganizationStructureController::class, 'create'])->middleware('auth');
Route::POST('organizationStructureStore',[OrganizationStructureController::class, 'store'])->middleware('auth');
Route::POST('organizationStructureUpdate',[OrganizationStructureController::class, 'update'])->middleware('auth');
Route::GET('organizationStructureEdit/{organization_structure}',[OrganizationStructureController::class, 'edit'])->middleware('auth');

Route::GET('structuralPositionList',[StructuralPositionController::class, 'index'])->middleware('auth');
Route::GET('structuralPositionAdd',[StructuralPositionController::class, 'create'])->middleware('auth');
Route::POST('structuralPositionStore',[StructuralPositionController::class, 'store'])->middleware('auth');
Route::POST('structuralPositionUpdate',[StructuralPositionController::class, 'update'])->middleware('auth');
Route::GET('structuralPositionEdit/{structural_position}',[StructuralPositionController::class, 'edit'])->middleware('auth');

Route::GET('workPositionList',[WorkPositionController::class, 'index'])->middleware('auth');
Route::GET('workPositionAdd',[WorkPositionController::class, 'create'])->middleware('auth');
Route::POST('workPositionStore',[WorkPositionController::class, 'store'])->middleware('auth');
Route::POST('workPositionUpdate',[WorkPositionController::class, 'update'])->middleware('auth');
Route::GET('workPositionEdit/{work_position}',[WorkPositionController::class, 'edit'])->middleware('auth');

Route::GET('getAllOrgStructure',[OrganizationStructureController::class, 'list'])->middleware('auth');
Route::GET('getAllStructuralPosition',[StructuralPositionController::class, 'getAllStructuralPosition'])->middleware('auth');
Route::GET('getAllWorkPosition',[WorkPositionController::class, 'getAllWorkPosition'])->middleware('auth');

Route::post('orgStructureList', [EmployeeController::class, 'orgStructureList'])->name('orgStructureList');

require __DIR__.'/auth.php';
