<?php

namespace Modules\System\Services;

use PDO;
use RuntimeException;

final class UserTrackingService
{
    private const MAX_EVENTS = 100;
    private const MAX_SECTIONS = 100;
    private const MAX_INTERVALS = 50;

    public function ingest(?int $userId, array $payload): array
    {
        $visitUuid = $this->uuid($payload['visitUuid'] ?? null);
        $pageUuid = $this->uuid($payload['pageViewUuid'] ?? null);
        $batchUuid = $this->uuid($payload['batchUuid'] ?? null);
        if (!$visitUuid || !$pageUuid || !$batchUuid) throw new RuntimeException('شناسه نشست رهگیری معتبر نیست.');

        $pdo = db();
        $owns = !$pdo->inTransaction();
        if ($owns) $pdo->beginTransaction();
        try {
            $sessionId = $this->upsertSession($pdo, $userId, $visitUuid, $payload);
            $pageViewId = $this->upsertPageView($pdo, $userId, $sessionId, $pageUuid, $payload);
            $claim=$pdo->prepare('INSERT IGNORE INTO tracking_ingestion_batches (batch_uuid,tracking_user_session_id,page_view_id) VALUES (?,?,?)');
            $claim->execute([$batchUuid,$sessionId,$pageViewId]);
            if($claim->rowCount()===0){if($owns)$pdo->commit();return ['visitId'=>$sessionId,'pageViewId'=>$pageViewId,'acceptedEvents'=>0,'duplicate'=>true];}
            $events = $this->insertEvents($pdo, $userId, $sessionId, $pageViewId, $payload['events'] ?? []);
            $this->upsertEngagements($pdo, $userId, $sessionId, $pageViewId, $payload['sections'] ?? []);
            $this->insertIntervals($pdo, $userId, $sessionId, $pageViewId, $payload['intervals'] ?? []);
            if ($events) {
                $q=$pdo->prepare('UPDATE tracking_user_sessions SET events_count=events_count+? WHERE tracking_user_session_id=?');
                $q->execute([$events,$sessionId]);
            }
            $pdo->prepare('UPDATE tracking_ingestion_batches SET events_count=? WHERE batch_uuid=?')->execute([$events,$batchUuid]);
            if ($owns) $pdo->commit();
            return ['visitId'=>$sessionId,'pageViewId'=>$pageViewId,'acceptedEvents'=>$events];
        } catch (\Throwable $e) {
            if ($owns && $pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    private function upsertSession(PDO $pdo, ?int $userId, string $uuid, array $p): int
    {
        $s = is_array($p['session'] ?? null) ? $p['session'] : [];
        $url=$this->text($p['url']??'',1000); $guest=$this->text($p['guestId']??'',64)?:null;
        $requestedStatus=$s['status']??'active';
        $status=in_array($requestedStatus,['active','idle','ended','expired','abandoned'],true)?$requestedStatus:'active';
        $requestedEndReason=$s['endReason']??null;
        $endReason=in_array($requestedEndReason,['logout','tab_closed','browser_closed','timeout','session_expired','network_lost','unknown'],true)?$requestedEndReason:null;
        $sql='INSERT INTO tracking_user_sessions (visit_uuid,user_id,session_id,guest_id,status,ip_address,user_agent,device_type,os,browser,browser_version,entry_page,exit_page,total_duration,page_views_count,started_at,last_activity_at,last_heartbeat_at,ended_at,end_reason,active_duration_ms,idle_duration_ms,visible_duration_ms,hidden_duration_ms,language,timezone,screen_width,screen_height,viewport_width,viewport_height,device_pixel_ratio,platform,app_version,tracking_version,consent_status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(3),NOW(3)) ON DUPLICATE KEY UPDATE user_id=COALESCE(VALUES(user_id),user_id),status=VALUES(status),exit_page=VALUES(exit_page),total_duration=GREATEST(total_duration,VALUES(total_duration)),page_views_count=GREATEST(page_views_count,VALUES(page_views_count)),last_activity_at=VALUES(last_activity_at),last_heartbeat_at=VALUES(last_heartbeat_at),ended_at=VALUES(ended_at),end_reason=VALUES(end_reason),active_duration_ms=GREATEST(active_duration_ms,VALUES(active_duration_ms)),idle_duration_ms=GREATEST(idle_duration_ms,VALUES(idle_duration_ms)),visible_duration_ms=GREATEST(visible_duration_ms,VALUES(visible_duration_ms)),hidden_duration_ms=GREATEST(hidden_duration_ms,VALUES(hidden_duration_ms)),viewport_width=VALUES(viewport_width),viewport_height=VALUES(viewport_height),updated_at=NOW(3)';
        $stmt=$pdo->prepare($sql);
        $started=$this->dateMs($s['startedAt']??null)??date('Y-m-d H:i:s.v');
        $ended=$status==='ended'?($this->dateMs($s['endedAt']??null)??date('Y-m-d H:i:s.v')):null;
        $device=$this->device((string)($_SERVER['HTTP_USER_AGENT']??''));
        $stmt->execute([$uuid,$userId,session_id()?:$uuid,$guest,$status,$this->ip(),$this->text($_SERVER['HTTP_USER_AGENT']??'',2000),$device['type'],$device['os'],$device['browser'],$device['version'],$url,$url,(int)floor($this->uint($s['totalMs']??0)/1000),$this->uint($s['pageViews']??1),$started,$this->dateMs($s['lastActivityAt']??null),date('Y-m-d H:i:s.v'),$ended,$endReason,$this->uint($s['activeMs']??0),$this->uint($s['idleMs']??0),$this->uint($s['visibleMs']??0),$this->uint($s['hiddenMs']??0),$this->text($s['language']??'',10),$this->text($s['timezone']??'',64),$this->small($s['screenWidth']??null),$this->small($s['screenHeight']??null),$this->small($s['viewportWidth']??null),$this->small($s['viewportHeight']??null),max(0,min(99,(float)($s['pixelRatio']??1))),$this->text($s['platform']??'',100),$this->text($s['appVersion']??'web',50),1,'unknown']);
        return $this->idByUuid($pdo,'tracking_user_sessions','tracking_user_session_id','visit_uuid',$uuid);
    }

    private function upsertPageView(PDO $pdo, ?int $userId, int $sessionId, string $uuid, array $p): int
    {
        $v=is_array($p['page']??null)?$p['page']:[];
        $url=$this->text($p['url']??'',1000); $path=$this->text($v['path']??'',1000);
        $sql='INSERT INTO tracking_user_page_views (page_view_uuid,tracking_user_session_id,user_id,tab_id,sequence_number,page_url,page_path,canonical_url,page_title,route_name,page_type,entity_type,entity_id,locale,entered_at,exited_at,duration,referrer,referrer_domain,query_params,utm_source,utm_medium,utm_campaign,scroll_depth,click_count,mouse_movement,is_exit_page,last_activity_at,last_heartbeat_at,total_duration_ms,visible_duration_ms,hidden_duration_ms,active_duration_ms,idle_duration_ms,reading_duration_ms,interaction_count,keypress_count,form_interaction_count,max_scroll_depth,max_scroll_y,content_height,first_interaction_at,last_interaction_at,exit_reason,is_completed,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(3)) ON DUPLICATE KEY UPDATE user_id=COALESCE(VALUES(user_id),user_id),exited_at=VALUES(exited_at),duration=GREATEST(duration,VALUES(duration)),scroll_depth=GREATEST(scroll_depth,VALUES(scroll_depth)),click_count=GREATEST(click_count,VALUES(click_count)),is_exit_page=VALUES(is_exit_page),last_activity_at=VALUES(last_activity_at),last_heartbeat_at=VALUES(last_heartbeat_at),total_duration_ms=GREATEST(total_duration_ms,VALUES(total_duration_ms)),visible_duration_ms=GREATEST(visible_duration_ms,VALUES(visible_duration_ms)),hidden_duration_ms=GREATEST(hidden_duration_ms,VALUES(hidden_duration_ms)),active_duration_ms=GREATEST(active_duration_ms,VALUES(active_duration_ms)),idle_duration_ms=GREATEST(idle_duration_ms,VALUES(idle_duration_ms)),reading_duration_ms=GREATEST(reading_duration_ms,VALUES(reading_duration_ms)),interaction_count=GREATEST(interaction_count,VALUES(interaction_count)),keypress_count=GREATEST(keypress_count,VALUES(keypress_count)),form_interaction_count=GREATEST(form_interaction_count,VALUES(form_interaction_count)),max_scroll_depth=GREATEST(max_scroll_depth,VALUES(max_scroll_depth)),max_scroll_y=GREATEST(max_scroll_y,VALUES(max_scroll_y)),content_height=VALUES(content_height),first_interaction_at=COALESCE(first_interaction_at,VALUES(first_interaction_at)),last_interaction_at=VALUES(last_interaction_at),exit_reason=VALUES(exit_reason),is_completed=VALUES(is_completed)';
        $q=$pdo->prepare($sql); $complete=!empty($v['completed']);
        $query=[]; parse_str((string)(parse_url($url,PHP_URL_QUERY)??''),$query); $this->stripSensitive($query);
        $q->execute([$uuid,$sessionId,$userId,$this->text($v['tabId']??'',64),$this->uint($v['sequence']??1),$url,$path,$this->text($v['canonicalUrl']??'',1000),$this->text($v['title']??'',255),$this->text($v['routeName']??'',100),$this->text($v['pageType']??'other',50),$this->text($v['entityType']??'',50)?:null,$this->positiveOrNull($v['entityId']??null),$this->text($v['locale']??'',10),$this->dateMs($v['enteredAt']??null)??date('Y-m-d H:i:s.v'),$complete?($this->dateMs($v['exitedAt']??null)??date('Y-m-d H:i:s.v')):null,(int)floor($this->uint($v['totalMs']??0)/1000),$this->text($v['referrer']??'',500),$this->text(parse_url((string)($v['referrer']??''),PHP_URL_HOST)??'',255),json_encode($query,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),$this->text($query['utm_source']??'',100),$this->text($query['utm_medium']??'',100),$this->text($query['utm_campaign']??'',100),$this->small($v['maxScrollDepth']??0),$this->uint($v['clickCount']??0),null,$complete?1:0,$this->dateMs($v['lastActivityAt']??null),date('Y-m-d H:i:s.v'),$this->uint($v['totalMs']??0),$this->uint($v['visibleMs']??0),$this->uint($v['hiddenMs']??0),$this->uint($v['activeMs']??0),$this->uint($v['idleMs']??0),$this->uint($v['readingMs']??0),$this->uint($v['interactionCount']??0),$this->uint($v['keypressCount']??0),$this->uint($v['formInteractionCount']??0),$this->small($v['maxScrollDepth']??0),$this->uint($v['maxScrollY']??0),$this->uint($v['contentHeight']??0),$this->dateMs($v['firstInteractionAt']??null),$this->dateMs($v['lastInteractionAt']??null),$this->text($v['exitReason']??'',50),$complete?1:0]);
        return $this->idByUuid($pdo,'tracking_user_page_views','tracking_user_page_view_id','page_view_uuid',$uuid);
    }

    private function insertEvents(PDO $pdo, ?int $userId, int $sessionId, int $pageViewId, mixed $rows): int
    {
        if(!is_array($rows))return 0; $rows=array_slice($rows,0,self::MAX_EVENTS); $count=0;
        $sql='INSERT IGNORE INTO tracking_user_events (event_uuid,tracking_user_session_id,user_id,page_view_id,sequence_number,event_name,event_category,event_action,event_label,occurred_at,client_timestamp_ms,event_version,target_type,target_id,target_name,target_text,entity_type,entity_id,section_key,position_x,position_y,viewport_x,viewport_y,scroll_x,scroll_y,scroll_depth,duration_ms,numeric_value,is_trusted,source,event_data,page_url,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?,NOW(3))';
        $q=$pdo->prepare($sql);
        foreach($rows as$r){if(!is_array($r)||!($uuid=$this->uuid($r['uuid']??null)))continue;$name=$this->slug($r['name']??'',100);if(!$name)continue;$data=is_array($r['data']??null)?$r['data']:[];$this->stripSensitive($data);$q->execute([$uuid,$sessionId,$userId,$pageViewId,$this->uint($r['sequence']??0),$name,$this->slug($r['category']??'',50),$this->slug($r['action']??'',100),$this->text($r['label']??'',255),$this->dateMs($r['at']??null)??date('Y-m-d H:i:s.v'),$this->uint($r['at']??0),1,$this->slug($r['targetType']??'',50),$this->text($r['targetId']??'',191),$this->text($r['targetName']??'',191),$this->safeTargetText($r['targetText']??''),$this->slug($r['entityType']??'',50),$this->positiveOrNull($r['entityId']??null),$this->slug($r['sectionKey']??'',100),$this->intOrNull($r['x']??null),$this->intOrNull($r['y']??null),$this->intOrNull($r['viewportX']??null),$this->intOrNull($r['viewportY']??null),$this->intOrNull($r['scrollX']??null),$this->intOrNull($r['scrollY']??null),$this->small($r['scrollDepth']??null),$this->positiveOrNull($r['durationMs']??null),isset($r['value'])?(float)$r['value']:null,isset($r['trusted'])?(!empty($r['trusted'])?1:0):null,'client',json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),$this->text($r['url']??'',500)]);$count+=$q->rowCount();}
        return$count;
    }

    private function upsertEngagements(PDO $pdo,?int$userId,int$sessionId,int$pageViewId,mixed$rows):void
    {if(!is_array($rows))return;$q=$pdo->prepare('INSERT INTO tracking_user_content_engagements (tracking_user_session_id,page_view_id,user_id,section_key,section_type,entity_type,entity_id,impression_count,visible_duration_ms,active_duration_ms,idle_duration_ms,reading_duration_ms,max_visibility_percent,interaction_count,click_count,first_seen_at,last_seen_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE impression_count=impression_count+VALUES(impression_count),visible_duration_ms=visible_duration_ms+VALUES(visible_duration_ms),active_duration_ms=active_duration_ms+VALUES(active_duration_ms),idle_duration_ms=idle_duration_ms+VALUES(idle_duration_ms),reading_duration_ms=reading_duration_ms+VALUES(reading_duration_ms),max_visibility_percent=GREATEST(max_visibility_percent,VALUES(max_visibility_percent)),interaction_count=interaction_count+VALUES(interaction_count),click_count=click_count+VALUES(click_count),first_seen_at=COALESCE(first_seen_at,VALUES(first_seen_at)),last_seen_at=VALUES(last_seen_at),updated_at=NOW(3)');foreach(array_slice($rows,0,self::MAX_SECTIONS)as$r){if(!is_array($r)||!($key=$this->slug($r['key']??'',100)))continue;$q->execute([$sessionId,$pageViewId,$userId,$key,$this->slug($r['type']??'section',50),$this->slug($r['entityType']??'',50),$this->positiveOrNull($r['entityId']??null),$this->uint($r['impressions']??0),$this->uint($r['visibleMs']??0),$this->uint($r['activeMs']??0),$this->uint($r['idleMs']??0),$this->uint($r['readingMs']??0),$this->small($r['maxVisibility']??0),$this->uint($r['interactions']??0),$this->uint($r['clicks']??0),$this->dateMs($r['firstSeenAt']??null),$this->dateMs($r['lastSeenAt']??null)]);}}

    private function insertIntervals(PDO $pdo,?int$userId,int$sessionId,int$pageViewId,mixed$rows):void
    {if(!is_array($rows))return;$q=$pdo->prepare('INSERT IGNORE INTO tracking_user_activity_intervals (interval_uuid,tracking_user_session_id,page_view_id,user_id,activity_type,started_at,ended_at,duration_ms,section_key) VALUES (?,?,?,?,?,?,?,?,?)');foreach(array_slice($rows,0,self::MAX_INTERVALS)as$r){if(!is_array($r)||!($uuid=$this->uuid($r['uuid']??null))||!in_array($r['type']??'',['active','idle','reading','hidden','disconnected'],true))continue;$q->execute([$uuid,$sessionId,$pageViewId,$userId,$r['type'],$this->dateMs($r['startedAt']??null)??date('Y-m-d H:i:s.v'),$this->dateMs($r['endedAt']??null),$this->uint($r['durationMs']??0),$this->slug($r['sectionKey']??'',100)]);}}

    private function idByUuid(PDO$p,string$table,string$id,string$field,string$uuid):int{$q=$p->prepare("SELECT `$id` FROM `$table` WHERE `$field`=? LIMIT 1");$q->execute([$uuid]);return(int)$q->fetchColumn();}
    private function uuid(mixed$v):?string{$v=strtolower(trim((string)$v));return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',$v)?$v:null;}
    private function text(mixed$v,int$n):string{return mb_substr(trim((string)$v),0,$n);}
    private function slug(mixed$v,int$n):string{return mb_substr(preg_replace('/[^a-zA-Z0-9_.:-]+/','_',trim((string)$v)),0,$n);}
    private function uint(mixed$v):int{return max(0,min(PHP_INT_MAX,(int)$v));}
    private function small(mixed$v):?int{return $v===null?null:max(0,min(65535,(int)$v));}
    private function intOrNull(mixed$v):?int{return $v===null?null:(int)$v;}
    private function positiveOrNull(mixed$v):?int{return $v===null||$v===''?null:$this->uint($v);}
    private function dateMs(mixed$v):?string{if(!is_numeric($v)||(int)$v<=0)return null;$sec=(int)floor((int)$v/1000);$ms=(int)$v%1000;return gmdate('Y-m-d H:i:s',$sec).'.'.str_pad((string)$ms,3,'0',STR_PAD_LEFT);}
    private function ip():string{$raw=(string)($_SERVER['HTTP_X_FORWARDED_FOR']??$_SERVER['REMOTE_ADDR']??'');return $this->text(trim(explode(',',$raw)[0]),45);}
    private function safeTargetText(mixed$v):string{$v=preg_replace('/\s+/u',' ',trim((string)$v));return mb_substr($v,0,120);}
    private function stripSensitive(array &$v):void{foreach(array_keys($v)as$k){if(preg_match('/pass|password|token|secret|otp|national|card|cvv|email|phone|message|content/i',(string)$k))unset($v[$k]);elseif(is_array($v[$k]))$this->stripSensitive($v[$k]);}}
    private function device(string$ua):array{$x=strtolower($ua);$type=str_contains($x,'bot')?'bot':(str_contains($x,'ipad')||str_contains($x,'tablet')?'tablet':(str_contains($x,'mobile')?'mobile':'desktop'));$os=str_contains($x,'windows')?'Windows':(str_contains($x,'android')?'Android':(str_contains($x,'iphone')||str_contains($x,'ipad')?'iOS':(str_contains($x,'mac os')?'macOS':(str_contains($x,'linux')?'Linux':'Unknown'))));$browser=str_contains($x,'edg/')?'Edge':(str_contains($x,'firefox/')?'Firefox':(str_contains($x,'chrome/')?'Chrome':(str_contains($x,'safari/')?'Safari':'Unknown')));preg_match('/(?:edg|firefox|chrome|version)\/([0-9.]+)/i',$ua,$m);return compact('type','os','browser')+['version'=>$m[1]??null];}
}
