<?php
namespace App\Http\Requests\Auth;
use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rules\Password;
final class RegisterRequest extends ApiRequest { public function authorize(): bool{return true;} public function rules(): array{return ['full_name'=>['required','string','min:2','max:120'],'email'=>['required','email:rfc','max:255','unique:users,email'],'password'=>['required','confirmed',Password::min(12)->mixedCase()->numbers()->symbols()]];} }
