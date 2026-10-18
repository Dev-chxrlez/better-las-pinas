<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
final class ReportCategorySeeder extends Seeder { public function run(): void { $names=['Road Damage','Flooding and Drainage','Garbage and Waste','Streetlight Problems','Sidewalk Obstructions','Public Infrastructure','Traffic Concerns','Other Community Issues']; foreach($names as $order=>$name){ $slug=Str::slug($name); if(!DB::table('report_categories')->where('slug',$slug)->exists()){ DB::table('report_categories')->insert(['id'=>(string)Str::uuid(),'name'=>$name,'slug'=>$slug,'sort_order'=>$order+1,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()]); } else { DB::table('report_categories')->where('slug',$slug)->update(['name'=>$name,'sort_order'=>$order+1,'is_active'=>true,'updated_at'=>now()]); } } } }
