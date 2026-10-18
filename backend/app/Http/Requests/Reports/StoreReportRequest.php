<?php
namespace App\Http\Requests\Reports;
use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;
final class StoreReportRequest extends ApiRequest { public function authorize(): bool{return $this->user()?->can('submit report')??false;} public function rules(): array{return ['category_id'=>['required','uuid',Rule::exists('report_categories','id')->where('is_active',true)],'barangay_id'=>['required','uuid',Rule::exists('barangays','id')->where('is_active',true)],'title'=>['required','string','min:5','max:120'],'description'=>['required','string','min:20','max:5000'],'location_text'=>['required','string','min:8','max:1000'],'latitude'=>['nullable','numeric','between:-90,90'],'longitude'=>['nullable','numeric','between:-180,180'],'photo'=>['nullable','file','image','mimes:jpeg,jpg,png,webp','max:10240']];} }
