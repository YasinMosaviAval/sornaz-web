<?php

namespace Modules\Analytics\Services;

use Core\database\DB;

class AdminGuideService
{
    private array $guides = [
        'user-registration' => ['title' => 'ثبت‌نام کاربر', 'file' => 'USER_REGISTRATION_FLOW.md'],
        'academy-registration' => ['title' => 'ثبت آموزشگاه', 'file' => 'ACADEMY_REGISTRATION_FLOW.md'],
        'main-branch-registration' => ['title' => 'ثبت شعبه اصلی آموزشگاه', 'file' => 'MAIN_BRANCH_REGISTRATION_FLOW.md'],
    ];

    public function all(string $locale = 'fa'): array
    {
        $this->seedMissing(); $out=[];
        foreach ($this->guides as $key=>$meta) {
            $setting=DB::table('f_settings')->where('variable_name','admin.guide.'.$key)->whereNull('deleted_at')->first();
            if (!$setting) continue;
            $rows=DB::table('f_translations')->where('table_name','f_settings')->where('table_id',(int)$setting['setting_id'])->whereNull('deleted_at')->get();
            $text=[]; foreach($rows as $row)$text[$row['locale']][$row['field']]=$row['value'];
            $chosen=$text[$locale]??$text['fa']??[];
            $out[]=['key'=>$key,'title'=>$chosen['title']??$meta['title'],'content'=>$chosen['content']??'','fa'=>$text['fa']??[],'en'=>$text['en']??[]];
        }
        return $out;
    }

    public function save(int $actor,string $key,array $data): void
    {
        if (!isset($this->guides[$key])) throw new \RuntimeException('راهنمای موردنظر یافت نشد.');
        $setting=DB::table('f_settings')->where('variable_name','admin.guide.'.$key)->first();
        if (!$setting){$id=(int)DB::table('f_settings')->insertGetId(['variable_name'=>'admin.guide.'.$key,'page'=>'admin','table_name'=>'admin_guides','status'=>'active','created_by'=>$actor,'updated_by'=>$actor]);}
        else $id=(int)$setting['setting_id'];
        foreach(['fa','en'] as $locale) foreach(['title','content'] as $field){$value=trim((string)($data[$locale][$field]??''));if($value==='')throw new \RuntimeException('عنوان و متن هر دو زبان الزامی است.');$row=DB::table('f_translations')->where('table_name','f_settings')->where('table_id',$id)->where('locale',$locale)->where('field',$field)->first();$v=['value'=>$value,'version'=>1,'updated_by'=>$actor,'deleted_at'=>null,'deleted_by'=>null];if($row)DB::table('f_translations')->where('translation_id',(int)$row['translation_id'])->update($v);else DB::table('f_translations')->insert(['table_name'=>'f_settings','table_id'=>$id,'field'=>$field,'locale'=>$locale,'created_by'=>$actor]+$v);}
    }

    private function seedMissing(): void
    {
        foreach($this->guides as $key=>$meta){$keyName='admin.guide.'.$key;$setting=DB::table('f_settings')->where('variable_name',$keyName)->first();$path=base_path('docs/'.$meta['file']);$content=is_file($path)?(string)file_get_contents($path):'';if(!$setting)$id=(int)DB::table('f_settings')->insertGetId(['variable_name'=>$keyName,'page'=>'admin','table_name'=>'admin_guides','status'=>'active','created_by'=>1,'updated_by'=>1]);else$id=(int)$setting['setting_id'];foreach(['fa'=>[$meta['title'],$content],'en'=>[$meta['title'],$content]] as $locale=>$values)foreach(['title','content'] as $i=>$field){$row=DB::table('f_translations')->where('table_name','f_settings')->where('table_id',$id)->where('locale',$locale)->where('field',$field)->first();if(!$row)DB::table('f_translations')->insert(['table_name'=>'f_settings','table_id'=>$id,'field'=>$field,'locale'=>$locale,'value'=>$values[$i],'version'=>1,'created_by'=>1,'updated_by'=>1]);}}
    }
}
