<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
final class BarangaySeeder extends Seeder { public function run(): void { $names=['Almanza Uno','Almanza Dos','B. F. International Village','Daniel Fajardo','Elias Aldana','Ilaya','Manuyo Uno','Manuyo Dos','Pamplona Uno','Pamplona Dos','Pamplona Tres','Pilar','Pulang Lupa Uno','Pulang Lupa Dos','Talon Uno','Talon Dos','Talon Tres','Talon Kuatro','Talon Singko','Zapote']; foreach($names as $name){ $slug=Str::slug($name); if(!DB::table('barangays')->where('slug',$slug)->exists()){ DB::table('barangays')->insert(['id'=>(string)Str::uuid(),'name'=>$name,'slug'=>$slug,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()]); } else { DB::table('barangays')->where('slug',$slug)->update(['name'=>$name,'is_active'=>true,'updated_at'=>now()]); } } } }
