<?php
namespace Modules\Education\Services;

use Modules\Notation\Services\NotationService;
use RuntimeException;

/** Monophonic reading questions; grades microphone observations, never a supplied pass flag. */
final class MusicPracticeNotation
{
    private const WHOLE = 967680;
    private const ALTER = [''=>0,'n'=>0,'#'=>1,'b'=>-1,'##'=>2,'bb'=>-2,'k'=>-.5,'o'=>.5,'d'=>-.5,'+'=>.5,'bk'=>-1.5,'#o'=>1.5];
    private const IRANIAN = [
        'iran_shur'=>[3,3,4,4,2,4,4],'iran_mahur'=>[4,4,2,4,4,4,2],'iran_rastpanjgah'=>[4,4,2,4,4,4,2],
        'iran_homayun'=>[3,5,2,4,2,4,4],'iran_nava'=>[4,2,4,4,3,3,4],'iran_segah'=>[3,4,3,3,4,4,3],
        'iran_chahargah'=>[3,5,2,4,3,5,2],'iran_abuata'=>[3,4,4,2,4,4,3],'iran_bayat_tork'=>[4,4,2,4,4,3,3],
        'iran_afshari'=>[4,2,4,4,3,3,4],'iran_dashti'=>[2,4,4,3,3,4,4],'iran_bayat_esfahan'=>[4,2,4,4,3,5,2],
    ];

    public static function question(mixed $input, NotationService $service): array
    {
        if (!is_array($input) || !is_array($input['sheet']??null)) throw new RuntimeException('صورت سؤال نت‌نویسی معتبر نیست.',422);
        $data=$service->validate($input['sheet']);
        $sheet=['metadata'=>json_decode($data['metadata'],true,512,JSON_THROW_ON_ERROR),'score'=>json_decode($data['score'],true,512,JSON_THROW_ON_ERROR)];
        return ['sheet'=>$sheet,'preview'=>self::preview($input['preview']??null),'reference'=>self::reference($sheet)];
    }

    private static function validateReferenceShape(array $sheet): void
    {
        $meta = $sheet['metadata'];
        if (!empty($sheet['score']['subsections']) || count($meta['staves']) !== 1
            || count($sheet['score']['measures']) > 8 || str_contains($meta['clef'], '_8')) self::unsupported();
    }

    public static function reference(array $sheet): array
    {
        $meta=$sheet['metadata'];$bars=$sheet['score']['measures'];

        self::validateReferenceShape($sheet);
        $beat=self::units(['duration'=>$meta['tempo_note'],'dots'=>$meta['tempo_dots']??0]);
        $ms=60000/($meta['bpm']*$beat);$offset=0;$events=[];$time=$meta['time'];$key=$meta['key'];$previousTie=false;$previousPitch='';
        foreach($bars as $bar){
            if (!empty($bar['repeat']) || !empty($bar['multiRest']) || !empty($bar['tempo']) || !empty($bar['tempo_unit']) || !empty($bar['clefChanges'])) self::unsupported();
            $time=$bar['time']??$time;$key=$bar['key']??$key;$signature=self::signature($meta['scale_type']??'',$key);
            [$top,$bottom]=array_map('intval',explode('/',$time));$capacity=$bar['length']??(int)($top*self::WHOLE/$bottom);
            self::barEvents($events,$bar,$signature,$ms,$offset,$previousTie,$previousPitch);
            $offset+=$capacity;
        }
        $duration=(int)round($offset*$ms);
        foreach($events as $event)if($event[1]-$event[0]<150)throw new RuntimeException('برای ارزیابی صوتی، کشش هر نت باید حداقل ۰٫۱۵ ثانیه باشد.',422);
        if(!$events || count($events)>128 || $duration>30000 || $duration<300) throw new RuntimeException('صورت سؤال باید یک ملودی ۰٫۳ تا ۳۰ ثانیه‌ای با حداکثر ۱۲۸ نت باشد.',422);
        return ['notes'=>$events,'durationMs'=>$duration,'beatMs'=>(int)round(60000/$meta['bpm']),'pitchTolerance'=>isset(self::IRANIAN[$meta['scale_type']??''])?20:35];
    }

    private static function barEvents(array &$events,array $bar,array $signature,float $ms,int $offset,bool &$previousTie,string &$previousPitch): void
    {
            $carry=[];$notes=$bar['notes'];usort($notes,fn($a,$b)=>$a['at']<=>$b['at']);
            foreach($notes as $note){
                self::requireMelodyNote($note);
                if ($note['rest']) {$previousTie=false;continue;}
                preg_match('/^([A-G])(\d)$/D',$note['pitch'],$pitch);
                $alter=$carry[$note['pitch']]??($signature[$pitch[1]]??0);$acc=$note['accidental']??'';
                if($acc!==''){$alter=self::ALTER[$acc];$carry[$note['pitch']]=$alter;}
                $midi=((int)$pitch[2]+1)*12+['C'=>0,'D'=>2,'E'=>4,'F'=>5,'G'=>7,'A'=>9,'B'=>11][$pitch[1]]+$alter;
                $start=(int)round(($offset+$note['at'])*$ms);$end=(int)round(($offset+$note['at']+self::units($note))*$ms);
                $last=count($events)-1;
                if($previousTie && !empty($note['tiePrevious']) && $previousPitch===$note['pitch'] && $last>=0 && abs($events[$last][1]-$start)<=1)$events[$last][1]=$end;
                else $events[]=[$start,$end,$midi];
                $previousTie=!empty($note['tieNext']);$previousPitch=$note['pitch'];
            }
    }

    private static function requireMelodyNote(array $note): void
    {
        if (($note['staff']??1)!==1 || ($note['voice']??1)!==1 || !empty($note['pitches']) || !empty($note['graces']) || !empty($note['ornament']) || !empty($note['tremolo']) || !empty($note['harmonic']) || !empty($note['slide']) || !empty($note['octaveShift']) || ($note['noteSize']??'')==='cue' || in_array($note['articulation']??'',['staccato','staccatissimo','fermata','breath','caesura'],true)) self::unsupported();
    }

    private static function unsupported(): never
    {
        throw new RuntimeException('برای ارزیابی صوتی، یک ملودی تک‌صدایی بدون آکورد، زینت، تکرار، تغییر تمپو/اکتاو، نت راهنما یا تغییر طول اجرایی تعریف کنید.',422);
    }

    private static function units(array $note): int
    {
        $units=NotationService::DURATIONS[$note['duration']]*15120*(2-pow(.5,$note['dots']??0));
        if(isset($note['tuplet']))$units*=$note['tuplet']['normal']/$note['tuplet']['actual'];
        return (int)round($units);
    }

    private static function signature(string $mode,string $key): array
    {
        if(isset(self::IRANIAN[$mode])){
            $letters='CDEFGAB';$natural=[0,4,8,10,14,18,22];$start=strpos($letters,$key[0]);$value=$natural[$start]+(str_ends_with($key,':k')?-1:(str_ends_with($key,':o')?1:(str_ends_with($key,'b')?-2:0)));$out=[];
            for($i=0;$i<7;$i++){$index=($start+$i)%7;$base=$natural[$index]+($start+$i>=7?24:0);$out[$letters[$index]]=($value-$base)/2;$value+=self::IRANIAN[$mode][$i];}return $out;
        }
        $keys=['C'=>0,'G'=>1,'D'=>2,'A'=>3,'E'=>4,'B'=>5,'F#'=>6,'C#'=>7,'F'=>-1,'Bb'=>-2,'Eb'=>-3,'Ab'=>-4,'Db'=>-5,'Gb'=>-6,'Cb'=>-7,'Am'=>0,'Em'=>1,'Bm'=>2,'F#m'=>3,'C#m'=>4,'G#m'=>5,'D#m'=>6,'A#m'=>7,'Dm'=>-1,'Gm'=>-2,'Cm'=>-3,'Fm'=>-4,'Bbm'=>-5,'Ebm'=>-6,'Abm'=>-7];
        $count=$keys[$key]??0;$letters=$count<0?'BEADGCF':'FCGDAEB';$out=[];for($i=0;$i<abs($count);$i++)$out[$letters[$i]]=$count<0?-1:1;return $out;
    }

    public static function recording(mixed $input): array
    {
        if(!is_array($input))throw new RuntimeException('پاسخ صوتی معتبر نیست.',422);
        $duration=MusicPracticeDefinition::integer($input['durationMs']??null,300,40000);$frames=$input['frames']??null;
        if(!is_array($frames)||!array_is_list($frames)||count($frames)<6||count($frames)>800)throw new RuntimeException('دادهٔ ضبط معتبر نیست.',422);
        $previous=-50;$clean=[];foreach($frames as $frame){
            if(!is_array($frame)||!array_is_list($frame)||count($frame)!==3)throw new RuntimeException('قاب صوتی معتبر نیست.',422);
            $at=MusicPracticeDefinition::integer($frame[0],0,$duration);$hz=MusicPracticeDefinition::integer($frame[1],0,500000);$energy=MusicPracticeDefinition::integer($frame[2],0,1000);
            if($at-$previous<20||$at-$previous>150)throw new RuntimeException('ترتیب قاب‌های صوتی معتبر نیست.',422);
            $clean[]=[$at,$hz,$energy];$previous=$at;
        }
        if($duration-$previous>200)throw new RuntimeException('ضبط ناقص است.',422);
        return ['durationMs'=>$duration,'frames'=>$clean];
    }

    public static function grade(array $reference,array $recording,string $assessment): array
    {
        $frames=$recording['frames'];$notes=$reference['notes'];$peak=max(array_column($frames,2));$threshold=max(8,$peak*.12);$onsets=self::onsets($frames);
        if(!$onsets||$peak<8)return ['passed'=>false,'pitch'=>0,'rhythm'=>0];
        $offset=$onsets[0]-$notes[0][0];$tolerance=max(100,$reference['beatMs']*.25);$matched=0;$next=0;$pitchGood=0;$durationGood=0;
        foreach($notes as [$start,$end,$expected]){
            while($next<count($onsets)&&$onsets[$next]-$offset<$start-$tolerance)$next++;
            if($next<count($onsets)&&abs($onsets[$next]-$offset-$start)<=$tolerance){$matched++;$next++;}
            [$duration,$pitch]=self::noteMatch($frames,$start,$end,$expected,$offset,$threshold,$reference['pitchTolerance']);$durationGood+=$duration;$pitchGood+=$pitch;
        }
        $quiet=self::quietRests($frames,$notes,$offset,$tolerance,$threshold,$reference['durationMs']);
        $count=count($notes);$rhythm=(int)round(100*min($matched,$durationGood)/$count);$pitch=(int)round(100*$pitchGood/$count);
        $passed=$rhythm>=80&&count($onsets)<=$count+max(1,(int)floor($count*.15))&&$quiet&&($assessment==='rhythm'||$pitch>=80);
        return ['passed'=>$passed,'pitch'=>$pitch,'rhythm'=>$rhythm];
    }

    private static function quietRests(array $frames,array $notes,int $offset,float $tolerance,float $threshold,int $durationMs): bool
    {
        $restSamples=0;$restQuiet=0;foreach($frames as [$at,$hz,$energy]){$time=$at-$offset;if($time<0||$time>$durationMs)continue;$inside=false;foreach($notes as [$start,$end])if($time>=$start-$tolerance&&$time<=$end+$tolerance){$inside=true;break;}if(!$inside){$restSamples++;if($energy<$threshold)$restQuiet++;}}
        return !$restSamples||$restQuiet/$restSamples>=.7;
    }

    private static function noteMatch(array $frames,int $start,int $end,float $expected,int $offset,float $threshold,int $pitchTolerance): array
    {
            $trim=min(80,($end-$start)*.2);$samples=0;$voiced=0;$correct=0;
            foreach($frames as [$at,$hz,$energy]){if($at-$offset<$start+$trim||$at-$offset>$end-$trim)continue;$samples++;if($energy>=$threshold){$voiced++;if($hz>0&&abs((69+12*log(($hz/100)/440,2))-$expected)*100<=$pitchTolerance)$correct++;}}


        $covered=$samples>=max(1,(int)floor(($end-$start-2*$trim)/50*.7));
        return [(int)($covered&&$voiced/$samples>=.65),(int)($covered&&$correct/$samples>=.7)];
    }

    private static function onsets(array $frames): array
    {
        $peak=max(array_column($frames,2));$threshold=max(8,$peak*.12);$onsets=[];$previousEnergy=0;$previousPitch=0;
        foreach($frames as [$at,$hz,$energy]){
            $pitch=$hz>0?69+12*log(($hz/100)/440,2):0;
            if($energy>=$threshold&&($previousEnergy<$threshold||$energy>$previousEnergy*1.6&&$energy-$previousEnergy>$peak*.12||$pitch>0&&$previousPitch>0&&abs($pitch-$previousPitch)>.45)&&(!$onsets||$at-$onsets[array_key_last($onsets)]>=100))$onsets[]=$at;
            $previousEnergy=$energy;$previousPitch=$pitch;
        }
        return $onsets;
    }

    private static function preview(mixed $value): string
    {
        if(!is_string($value)||strlen($value)>120000||preg_match('/<!DOCTYPE|<!ENTITY|<\?/i',$value))throw new RuntimeException('پیش‌نمایش نت معتبر نیست.',422);
        $doc=new \DOMDocument();$previous=libxml_use_internal_errors(true);try{$valid=$doc->loadXML($value,LIBXML_NONET);}finally{libxml_clear_errors();libxml_use_internal_errors($previous);}
        if(!$valid||$doc->documentElement?->localName!=='svg')throw new RuntimeException('پیش‌نمایش نت معتبر نیست.',422);
        $tags=['svg','g','path','rect','ellipse','circle','line','polyline','polygon','text','tspan','defs','clipPath'];
        $geometry=['x','y','x1','y1','x2','y2','rx','ry','cx','cy','r','width','height','viewBox','font-size','stroke-width','stroke-dasharray','dx','dy'];
        foreach(iterator_to_array($doc->getElementsByTagName('*')) as $node){
            if(!in_array($node->localName,$tags,true)||$node->namespaceURI!=='http://www.w3.org/2000/svg')throw new RuntimeException('پیش‌نمایش نت معتبر نیست.',422);
            foreach(iterator_to_array($node->attributes) as $attribute){$name=$attribute->name;$raw=$attribute->value;$valid=false;
                if(in_array($name,['style','class','pointer-events'],true)||str_starts_with($name,'data-')||str_starts_with($name,'aria-')){$node->removeAttribute($name);continue;}
                $valid=self::previewAttribute($name,$raw,$geometry);
                if(!$valid)throw new RuntimeException('پیش‌نمایش نت معتبر نیست.',422);
            }
        }
        return $doc->saveXML($doc->documentElement);
    }
    private static function previewAttribute(string $name,string $raw,array $geometry): bool
    {
        $valid=false;
                if(in_array($name,$geometry,true))$valid=(bool)preg_match('/^[\d., eE+%\-]+$/D',$raw);
                elseif($name==='d')$valid=strlen($raw)<60000&&(bool)preg_match('/^[MmZzLlHhVvCcSsQqTtAa\d., eE+\-]+$/D',$raw);
                elseif(in_array($name,['fill','stroke'],true))$valid=(bool)preg_match('/^(?:none|transparent|currentColor|black|white|#[a-fA-F0-9]{3,8})$/D',$raw);
                elseif($name==='transform')$valid=(bool)preg_match('/^(?:(?:translate|scale|rotate|matrix|skewX|skewY)\([\d., eE+\-]+\)\s*)+$/D',$raw);
                elseif(in_array($name,['font-family','font-weight','font-style','text-anchor','fill-rule','clip-rule','stroke-linecap','stroke-linejoin'],true))$valid=(bool)preg_match('/^[a-zA-Z0-9 _,\-]+$/D',$raw);
                elseif($name==='id')$valid=(bool)preg_match('/^[a-zA-Z0-9_\-]{1,120}$/D',$raw);
                elseif($name==='clip-path')$valid=(bool)preg_match('/^url\(#[a-zA-Z0-9_\-]{1,120}\)$/D',$raw);
                elseif($name==='xmlns')$valid=$raw==='http://www.w3.org/2000/svg';
        return $valid;
    }
}
