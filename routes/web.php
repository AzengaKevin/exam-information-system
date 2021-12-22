<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExamsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\LevelsController;
use App\Http\Controllers\StreamsController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\SubjectsController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuardiansController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\ExamsAnalysisController;
use App\Http\Controllers\ExamsResultsController;
use App\Http\Controllers\HostelsController;
use App\Http\Controllers\ExamsScoresController;
use App\Http\Controllers\ExamsTranscriptsController;
use App\Http\Controllers\GradesController;
use App\Http\Controllers\GradingsController;
use App\Http\Controllers\LevelUnitsController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ResponsibilitiesController;
use App\Http\Controllers\TeachersResponsibilitiesController;

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

Route::get('/', function(){
    return view('welcome');
})->name('welcome');

Route::view('test/report-form', 'printouts.exams.report-form');

Route::group(['middleware' => ['auth']], function(){
    
    Route::get('/home', HomeController::class)->name('home');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('users', UsersController::class)
        ->only(['index']);

    Route::resource('levels',LevelsController::class)
           ->only(['index']);   
           
    Route::resource('streams',StreamsController::class)
           ->only(['index']);        
  
    Route::resource('teachers', TeachersController::class)
        ->only(['index','show']);

    Route::get('teachers/{teacher}/exams/{exam}', [TeachersController::class,'currentExamMarking'])->name('teachers.currentExamMarking');    
    
    Route::get('exams/levelUnit/{levelUnit}/subject/{subject}', [TeachersController::class,'studentToBeScored'])->name('teacher.studentToBeScored');    
        
    Route::resource('teachers.responsibilities', TeachersResponsibilitiesController::class)
        ->only('index');

    Route::resource('departments',DepartmentsController::class)
         ->only('index');    

    Route::resource('subjects',SubjectsController::class)
        ->only('index');   
    
    Route::resource('roles',RolesController::class)
        ->only('index'); 
    
    Route::resource('permissions',PermissionsController::class)
        ->only('index');
  
    Route::resource('guardians', GuardiansController::class)
        ->only('index');
  
    Route::resource('students', StudentsController::class)
        ->only(['index']);

    Route::get('/students/{student:adm_no}',[StudentsController::class,'show'])
        ->name('students.show');

    Route::resource('exams', ExamsController::class)
        ->only(['index', 'show']);

    Route::resource('exams.scores', ExamsScoresController::class)
        ->middleware('can:access-upload-scores-page')
        ->only(['index', 'create', 'store']);

    Route::resource('exams.analysis', ExamsAnalysisController::class)
        ->only(['index']);

    Route::resource('exams.results', ExamsResultsController::class)
        ->only(['index']);

    Route::resource('exams.transcripts', ExamsTranscriptsController::class)
        ->only(['index']);

    Route::get('/exams/{exam}/transcripts/show', [ExamsTranscriptsController::class, 'show'])
        ->name('exams.transcripts.show');

    Route::get('/exams/{exam}/transcripts/print-one', [ExamsTranscriptsController::class, 'printOne'])
        ->name('exams.transcripts.print-one');

    Route::get('/exams/{exam}/transcripts/print-bulk', [ExamsTranscriptsController::class, 'printBulk'])
        ->name('exams.transcripts.print-bulk');
    
    Route::resource('responsibilities', ResponsibilitiesController::class)
        ->only('index');

    Route::get('/level-units', [LevelUnitsController::class, 'index'])
        ->name('level-units.index');

    Route::get('/level-units/{levelUnit:alias}', [LevelUnitsController::class, 'show'])
        ->name('level-units.show');

    Route::post('/level-units', [LevelUnitsController::class, 'store'])
        ->name('level-units.store');
    
    Route::resource('hostels',HostelsController::class)
        ->only('index');
    
    Route::resource('grades',GradesController::class)
        ->only('index');

    Route::resource('gradings',GradingsController::class)
        ->only('index');
  
    Route::get('hostels/{hostel:slug}',[HostelsController::class,'show'])->name('hostels.show');


});