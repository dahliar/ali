<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DetailTransactionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CompanyController;
use App\Models\Rekening; 
use App\Models\Company; 
use App\Models\Item; 
use App\Models\Transaction; 
use App\Models\DetailTransaction; 
use App\Models\User; 




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


Route::get('unauthenticated', function () {
    return view('partial.footer');
})->name('unauthenticated');

/*
Route::get('employeeList',[EmployeeController::class, 'index'])->middleware(['auth']);
Route::get('employeeAdd',[EmployeeController::class, 'create'])->middleware(['auth']);
Route::get('employeeView',[EmployeeController::class, 'show'])->middleware(['auth']);
Route::get('employeeEdit',[EmployeeController::class, 'edit'])->middleware(['auth']);
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


//Route::get('/detailtransactionAdd/{transaction}', function (Transaction $transaction) { 
//    return view('detail.detailAdd', compact('transaction'));
//});



//ITEM STOCKS
//ITEM STOCKS
//ITEM STOCKS
//ITEM STOCKS
Route::get('itemStockList',[ItemController::class, 'index'])->middleware(['auth'])->name('itemStockList');
Route::GET('getAllStockItem/{speciesId}', [ItemController::class, 'getAllStockItem'])->middleware(['auth']);


Route::get('itemStockView/{itemId}',[ItemController::class, 'show'])->middleware(['auth']);
Route::GET('getItemHistory/{speciesId}', [ItemController::class, 'getItemHistory'])->middleware(['auth']);



Route::get('itemStockAdd/{itemId}',[StoreController::class, 'create'])->middleware(['auth'])->name('itemStockAdd');
Route::get('itemStockEdit/{store}',[StoreController::class, 'edit'])->middleware(['auth'])->name('itemStockAdd');
Route::get('itemStoreDetail/{storeId}',[StoreController::class, 'itemStoreDetail'])->middleware(['auth']);
Route::get('storeAdd',[StoreController::class, 'store'])->middleware(['auth'])->name('storeAdd');
Route::get('storeUpdate',[StoreController::class, 'update'])->middleware(['auth'])->name('storeUpdate');






//SPECIES
//SPECIES
//SPECIES
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
Route::get('itemCreateStore',[SpeciesController::class, 'storeItem'])->middleware(['auth']);

Route::get('sizeEditStore',[SpeciesController::class, 'updateSize'])->middleware(['auth']);
Route::get('itemEditStore',[SpeciesController::class, 'updateItem'])->middleware(['auth']);


//COMPANIES
Route::get('companyList',[CompanyController::class, 'index'])->middleware(['auth']);
Route::get('companyAdd',[CompanyController::class, 'create'])->middleware(['auth']);
Route::get('companyView',[CompanyController::class, 'show'])->middleware(['auth']);
Route::get('companyEdit/{companies}',[CompanyController::class, 'edit'])->middleware(['auth']);
Route::get('companyStore',[CompanyController::class, 'store'])->middleware(['auth'])->name('companyStore');
Route::get('companyUpdate',[CompanyController::class, 'update'])->middleware(['auth'])->name('companyUpdate');
Route::GET('getAllCompany', [CompanyController::class, 'getAllCompany'])->middleware(['auth']);






Route::post('orgStructureList', [EmployeeController::class, 'orgStructureList'])->middleware(['auth']);
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





Route::GET('employeeList',[EmployeeController::class, 'index'])->name('employeeList')->middleware('auth');
Route::GET('employeeAdd',[EmployeeController::class, 'create'])->middleware('auth');
Route::GET('employeeEdit/{employee}',[EmployeeController::class, 'edit'])->middleware('auth');
Route::GET('profileEdit/{employee}',[EmployeeController::class, 'employeePersonalDataEdit'])->middleware('auth');
Route::GET('employeeMappingEdit/{employee}',[EmployeeController::class, 'editMapping'])->middleware('auth');




Route::POST('employeeStore',[EmployeeController::class, 'store'])->name('employeeStore')->middleware('auth');
Route::POST('employeeUpdate',[EmployeeController::class, 'update'])->name('employeeUpdate')->middleware('auth');
Route::POST('employeeMappingUpdate',[EmployeeController::class, 'updateMapping'])->name('employeeMappingUpdate')->middleware('auth');
Route::get('getAllEmployees',[EmployeeController::class, 'getAllEmployees'])->middleware('auth');

Route::post('orgStructureList', [EmployeeController::class, 'orgStructureList'])->name('orgStructureList');

require __DIR__.'/auth.php';
