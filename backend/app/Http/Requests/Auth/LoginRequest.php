<?php
namespace App\Http\Requests\Auth;
use App\Http\Requests\ApiRequest;
final class LoginRequest extends ApiRequest { public function authorize(): bool{return true;} public function rules(): array{return ['email'=>['required','email'],'password'=>['required','string']];} }
