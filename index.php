<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 5.1.1
*/namespace
Adminer;const
VERSION="5.1.1";error_reporting(24575);set_error_handler(function($xc,$zc){return!!preg_match('~^Undefined (array key|offset|index)~',$zc);},E_WARNING|E_NOTICE);$Tc=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($Tc||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$Oi=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($Oi)$$X=$Oi;}}if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($g=null){return($g?:Db::$be);}function
adminer(){return
Adminer::$be;}function
driver(){return
Driver::$be;}function
connect(array$Bb){$J=Driver::connect($Bb[0],$Bb[1],$Bb[2]);return(is_object($J)?$J:null);}function
idf_unescape($u){if(!preg_match('~^[`\'"[]~',$u))return$u;$te=substr($u,-1);return
str_replace($te.$te,$te,substr($u,1,-1));}function
q($Q){return
connection()->quote($Q);}function
escape_string($X){return
substr(q($X),1,-1);}function
idx($va,$x,$k=null){return($va&&array_key_exists($x,$va)?$va[$x]:$k);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
number_type(){return'((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';}function
remove_slashes(array$_g,$Tc=false){if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){while(list($x,$X)=each($_g)){foreach($X
as$me=>$W){unset($_g[$x][$me]);if(is_array($W)){$_g[$x][stripslashes($me)]=$W;$_g[]=&$_g[$x][stripslashes($me)];}else$_g[$x][stripslashes($me)]=($Tc?$W:stripslashes($W));}}}}function
bracket_escape($u,$Ca=false){static$zi=array(':'=>':1',']'=>':2','['=>':3','"'=>':4');return
strtr($u,($Ca?array_flip($zi):$zi));}function
min_version($fj,$Ee="",$g=null){$g=connection($g);$th=$g->server_info;if($Ee&&preg_match('~([\d.]+)-MariaDB~',$th,$B)){$th=$B[1];$fj=$Ee;}return$fj&&version_compare($th,$fj)>=0;}function
charset(Db$f){return(min_version("5.5.3",0,$f)?"utf8mb4":"utf8");}function
ini_bool($Wd){$X=ini_get($Wd);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($ej,$N,$V,$F){$_SESSION["pwds"][$ej][$N][$V]=($_COOKIE["adminer_key"]&&is_string($F)?array(encrypt_string($F,$_COOKIE["adminer_key"])):$F);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$m=0,$qb=null){$qb=connection($qb);$I=$qb->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return($K?$K[$m]:false);}function
get_vals($H,$d=0){$J=array();$I=connection()->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$d];}return$J;}function
get_key_vals($H,$g=null,$wh=true){$g=connection($g);$J=array();$I=$g->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($wh)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$g=null,$l="<p class='error'>"){$qb=connection($g);$J=array();$I=$qb->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!$g&&$l&&(defined('Adminer\PAGE_HEADER')||$l=="-- "))echo$l.error()."\n";return$J;}function
unique_array($K,array$w){foreach($w
as$v){if(preg_match("~PRIMARY|UNIQUE~",$v["type"])){$J=array();foreach($v["columns"]as$x){if(!isset($K[$x]))continue
2;$J[$x]=$K[$x];}return$J;}}}function
escape_key($x){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$x,$B))return$B[1].idf_escape(idf_unescape($B[2])).$B[3];return
idf_escape($x);}function
where(array$Z,array$n=array()){$J=array();foreach((array)$Z["where"]as$x=>$X){$x=bracket_escape($x,true);$d=escape_key($x);$m=idx($n,$x,array());$Rc=$m["type"];$J[]=$d.(JUSH=="sql"&&$Rc=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($Rc,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($m,q($X)))));if(JUSH=="sql"&&preg_match('~char|text~',$Rc)&&preg_match("~[^ -@]~",$X))$J[]="$d = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$x)$J[]=escape_key($x)." IS NULL";return
implode(" AND ",$J);}function
where_check($X,array$n=array()){parse_str($X,$Va);remove_slashes(array(&$Va));return
where($Va,$n);}function
where_link($s,$d,$Y,$Bf="="){return"&where%5B$s%5D%5Bcol%5D=".urlencode($d)."&where%5B$s%5D%5Bop%5D=".urlencode(($Y!==null?$Bf:"IS NULL"))."&where%5B$s%5D%5Bval%5D=".urlencode($Y);}function
convert_fields(array$e,array$n,array$M=array()){$J="";foreach($e
as$x=>$X){if($M&&!in_array(idf_escape($x),$M))continue;$wa=convert_field($n[$x]);if($wa)$J
.=", $wa AS ".idf_escape($x);}return$J;}function
cookie($C,$Y,$_e=2592000){header("Set-Cookie: $C=".urlencode($Y).($_e?"; expires=".gmdate("D, d M Y H:i:s",time()+$_e)." GMT":"")."; path=".preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]).(HTTPS?"; secure":"")."; HttpOnly; SameSite=lax",false);}function
get_settings($zb){parse_str($_COOKIE[$zb],$xh);return$xh;}function
get_setting($x,$zb="adminer_settings"){$xh=get_settings($zb);return$xh[$x];}function
save_settings(array$xh,$zb="adminer_settings"){cookie($zb,http_build_query($xh+get_settings($zb)));}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==1))session_start();}function
stop_session($bd=false){$Wi=ini_bool("session.use_cookies");if(!$Wi||$bd){session_write_close();if($Wi&&@ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($x){return$_SESSION[$x][DRIVER][SERVER][$_GET["username"]];}function
set_session($x,$X){$_SESSION[$x][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($ej,$N,$V,$j=null){$Si=remove_from_uri(implode("|",array_keys(SqlDriver::$ac))."|username|ext|".($j!==null?"db|":"").($ej=='mssql'||$ej=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Si,$B);return"$B[1]?".(sid()?SID."&":"").($ej!="server"||$N!=""?urlencode($ej)."=".urlencode($N)."&":"").($_GET["ext"]?"ext=".urlencode($_GET["ext"])."&":"")."username=".urlencode($V).($j!=""?"&db=".urlencode($j):"").($B[2]?"&$B[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($A,$Re=null){if($Re!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($A!==null?$A:$_SERVER["REQUEST_URI"]))][]=$Re;}if($A!==null){if($A=="")$A=".";header("Location: $A");exit;}}function
query_redirect($H,$A,$Re,$Ig=true,$Dc=true,$Mc=false,$mi=""){if($Dc){$Lh=microtime(true);$Mc=!connection()->query($H);$mi=format_time($Lh);}$Fh=($H?adminer()->messageQuery($H,$mi,$Mc):"");if($Mc){adminer()->error
.=error().$Fh.script("messagesPrint();")."<br>";return
false;}if($Ig)redirect($A,$Re.$Fh);return
true;}class
Queries{static$Dg=array();static$Lh=0;}function
queries($H){if(!Queries::$Lh)Queries::$Lh=microtime(true);Queries::$Dg[]=(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";";return
connection()->query($H);}function
apply_queries($H,array$T,$_c='Adminer\table'){foreach($T
as$R){if(!queries("$H ".$_c($R)))return
false;}return
true;}function
queries_redirect($A,$Re,$Ig){$Dg=implode("\n",Queries::$Dg);$mi=format_time(Queries::$Lh);return
query_redirect($Dg,$A,$Re,$Ig,false,!$Ig,$mi);}function
format_time($Lh){return
sprintf('%.3f s',max(0,microtime(true)-$Lh));}function
relative_uri(){return
str_replace(":","%3a",preg_replace('~^[^?]*/([^?]*)~','\1',$_SERVER["REQUEST_URI"]));}function
remove_from_uri($Yf=""){return
substr(preg_replace("~(?<=[?&])($Yf".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_file($x,$Nb=false,$Rb=""){$Sc=$_FILES[$x];if(!$Sc)return
null;foreach($Sc
as$x=>$X)$Sc[$x]=(array)$X;$J='';foreach($Sc["error"]as$x=>$l){if($l)return$l;$C=$Sc["name"][$x];$ui=$Sc["tmp_name"][$x];$vb=file_get_contents($Nb&&preg_match('~\.gz$~',$C)?"compress.zlib://$ui":$ui);if($Nb){$Lh=substr($vb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$Lh))$vb=iconv("utf-16","utf-8",$vb);elseif($Lh=="\xEF\xBB\xBF")$vb=substr($vb,3);}$J
.=$vb;if($Rb)$J
.=(preg_match("($Rb\\s*\$)",$vb)?"":$Rb)."\n\n";}return$J;}function
upload_error($l){$Me=($l==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($l?'Unable to upload a file.'.($Me?" ".sprintf('Maximum allowed file size is %sB.',$Me):""):'File does not exist.');}function
repeat_pattern($ig,$y){return
str_repeat("$ig{0,65535}",$y/65535)."$ig{0,".($y%65535)."}";}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
format_number($X){return
strtr(number_format($X,0,".",','),preg_split('~~u','0123456789',-1,PREG_SPLIT_NO_EMPTY));}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$Nc=false){$J=table_status($R,$Nc);return($J?reset($J):array("Name"=>$R));}function
column_foreign_keys($R){$J=array();foreach(adminer()->foreignKeys($R)as$p){foreach($p["source"]as$X)$J[$X][]=$p;}return$J;}function
fields_from_edit(){$J=array();foreach((array)$_POST["field_keys"]as$x=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$x];$_POST["fields"][$X]=$_POST["field_vals"][$x];}}foreach((array)$_POST["fields"]as$x=>$X){$C=bracket_escape($x,true);$J[$C]=array("field"=>$C,"privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>1,"auto_increment"=>($x==driver()->primary),);}return$J;}function
dump_headers($Jd,$bf=false){$J=adminer()->dumpHeaders($Jd,$bf);$Uf=$_POST["output"];if($Uf!="text")header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($Jd).".$J".($Uf!="file"&&preg_match('~^[0-9a-z]+$~',$Uf)?".$Uf":""));session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv(array$K){foreach($K
as$x=>$X){if(preg_match('~["\n,;\t]|^0|\.\d*0$~',$X)||$X==="")$K[$x]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($_POST["format"]=="tsv"?"\t":";")),$K)."\r\n";}function
apply_sql_function($r,$d){return($r?($r=="unixepoch"?"DATETIME($d, '$r')":($r=="count distinct"?"COUNT(DISTINCT ":strtoupper("$r("))."$d)"):$d);}function
get_temp_dir(){$J=ini_get("upload_tmp_dir");if(!$J){if(function_exists('sys_get_temp_dir'))$J=sys_get_temp_dir();else{$o=@tempnam("","");if(!$o)return'';$J=dirname($o);unlink($o);}}return$J;}function
file_open_lock($o){if(is_link($o))return;$q=@fopen($o,"c+");if(!$q)return;chmod($o,0660);if(!flock($q,LOCK_EX)){fclose($q);return;}return$q;}function
file_write_unlock($q,$Hb){rewind($q);fwrite($q,$Hb);ftruncate($q,strlen($Hb));file_unlock($q);}function
file_unlock($q){flock($q,LOCK_UN);fclose($q);}function
first(array$va){return
reset($va);}function
password_file($h){$o=get_temp_dir()."/adminer.key";if(!$h&&!file_exists($o))return'';$q=file_open_lock($o);if(!$q)return'';$J=stream_get_contents($q);if(!$J){$J=rand_string();file_write_unlock($q,$J);}else
file_unlock($q);return$J;}function
rand_string(){return
md5(uniqid(strval(mt_rand()),true));}function
select_value($X,$_,array$m,$li){if(is_array($X)){$J="";foreach($X
as$me=>$W)$J
.="<tr>".($X!=array_values($X)?"<th>".h($me):"")."<td>".select_value($W,$_,$m,$li);return"<table>$J</table>";}if(!$_)$_=adminer()->selectLink($X,$m);if($_===null){if(is_mail($X))$_="mailto:$X";if(is_url($X))$_=$X;}$J=adminer()->editVal($X,$m);if($J!==null){if(!is_utf8($J))$J="\0";elseif($li!=""&&is_shortable($m))$J=shorten_utf8($J,max(0,+$li));else$J=h($J);}return
adminer()->selectVal($J,$_,$m,$X);}function
is_mail($nc){$xa='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$Zb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$ig="$xa+(\\.$xa+)*@($Zb?\\.)+$Zb";return
is_string($nc)&&preg_match("(^$ig(,\\s*$ig)*\$)i",$nc);}function
is_url($Q){$Zb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^(https?)://($Zb?\\.)+$Zb(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$Q);}function
is_shortable(array$m){return
preg_match('~char|text|json|lob|geometry|point|linestring|polygon|string|bytea~',$m["type"]);}function
count_rows($R,array$Z,$ge,array$pd){$H=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($ge&&(JUSH=="sql"||count($pd)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$pd).")$H":"SELECT COUNT(*)".($ge?" FROM (SELECT 1$H GROUP BY ".implode(", ",$pd).") x":$H));}function
slow_query($H){$j=adminer()->database();$ni=adminer()->queryTimeout();$Ah=driver()->slowQuery($H,$ni);$g=null;if(!$Ah&&support("kill")){$g=connect(adminer()->credentials());if($g&&($j==""||$g->select_db($j))){$pe=get_val(connection_id(),0,$g);echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$pe&token=".get_token()."'); }, 1000 * $ni);");}}ob_flush();flush();$J=@get_key_vals(($Ah?:$H),$g,false);if($g){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$Gg=rand(1,1e6);return($Gg^$_SESSION["token"]).":$Gg";}function
verify_token(){list($vi,$Gg)=explode(":",$_POST["token"]);return($Gg^$_SESSION["token"])==$vi;}function
lzw_decompress($Ia){$Vb=256;$Ja=8;$eb=array();$Tg=0;$Ug=0;for($s=0;$s<strlen($Ia);$s++){$Tg=($Tg<<8)+ord($Ia[$s]);$Ug+=8;if($Ug>=$Ja){$Ug-=$Ja;$eb[]=$Tg>>$Ug;$Tg&=(1<<$Ug)-1;$Vb++;if($Vb>>$Ja)$Ja++;}}$Ub=range("\0","\xFF");$J="";$oj="";foreach($eb
as$s=>$db){$mc=$Ub[$db];if(!isset($mc))$mc=$oj.$oj[0];$J
.=$mc;if($s)$Ub[]=$oj.$mc[0];$oj=$mc;}return$J;}function
script($Ch,$yi="\n"){return"<script".nonce().">$Ch</script>$yi";}function
script_src($Ti){return"<script src='".h($Ti)."'".nonce()."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
input_hidden($C,$Y=""){return"<input type='hidden' name='".h($C)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace("\0","&#0;",htmlspecialchars($Q,ENT_QUOTES,'utf-8'));}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($C,$Y,$Ya,$re="",$Af="",$cb="",$se=""){$J="<input type='checkbox' name='$C' value='".h($Y)."'".($Ya?" checked":"").($se?" aria-labelledby='$se'":"").">".($Af?script("qsl('input').onclick = function () { $Af };",""):"");return($re!=""||$cb?"<label".($cb?" class='$cb'":"").">$J".h($re)."</label>":$J);}function
optionlist($Ff,$lh=null,$Xi=false){$J="";foreach($Ff
as$me=>$W){$Gf=array($me=>$W);if(is_array($W)){$J
.='<optgroup label="'.h($me).'">';$Gf=$W;}foreach($Gf
as$x=>$X)$J
.='<option'.($Xi||is_string($x)?' value="'.h($x).'"':'').($lh!==null&&($Xi||is_string($x)?(string)$x:$X)===$lh?' selected':'').'>'.h($X);if(is_array($W))$J
.='</optgroup>';}return$J;}function
html_select($C,array$Ff,$Y="",$_f="",$se=""){return"<select name='".h($C)."'".($se?" aria-labelledby='$se'":"").">".optionlist($Ff,$Y)."</select>".($_f?script("qsl('select').onchange = function () { $_f };",""):"");}function
html_radios($C,array$Ff,$Y=""){$J="";foreach($Ff
as$x=>$X)$J
.="<label><input type='radio' name='".h($C)."' value='".h($x)."'".($x==$Y?" checked":"").">".h($X)."</label>";return$J;}function
confirm($Re="",$mh="qsl('input')"){return
script("$mh.onclick = () => confirm('".($Re?js_escape($Re):'Are you sure?')."');","");}function
print_fieldset($t,$ye,$ij=false){echo"<fieldset><legend>","<a href='#fieldset-$t'>$ye</a>",script("qsl('a').onclick = partial(toggle, 'fieldset-$t');",""),"</legend>","<div id='fieldset-$t'".($ij?"":" class='hidden'").">\n";}function
bold($La,$cb=""){return($La?" class='active $cb'":($cb?" class='$cb'":""));}function
js_escape($Q){return
addcslashes($Q,"\r\n'\\/");}function
pagination($E,$Eb){return" ".($E==$Eb?$E+1:'<a href="'.h(remove_from_uri("page").($E?"&page=$E".($_GET["next"]?"&next=".urlencode($_GET["next"]):""):"")).'">'.($E+1)."</a>");}function
hidden_fields(array$_g,array$Md=array(),$tg=''){$J=false;foreach($_g
as$x=>$X){if(!in_array($x,$Md)){if(is_array($X))hidden_fields($X,array(),$x);else{$J=true;echo
input_hidden(($tg?$tg."[$x]":$x),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),(SERVER!==null?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
enum_input($U,$ya,array$m,$Y,$qc=null){preg_match_all("~'((?:[^']|'')*)'~",$m["length"],$He);$J=($qc!==null?"<label><input type='$U'$ya value='$qc'".((is_array($Y)?in_array($qc,$Y):$Y===$qc)?" checked":"")."><i>".'empty'."</i></label>":"");foreach($He[1]as$s=>$X){$X=stripcslashes(str_replace("''","'",$X));$Ya=(is_array($Y)?in_array($X,$Y):$Y===$X);$J
.=" <label><input type='$U'$ya value='".h($X)."'".($Ya?' checked':'').'>'.h(adminer()->editVal($X,$m)).'</label>';}return$J;}function
input(array$m,$Y,$r,$Ba=false){$C=h(bracket_escape($m["field"]));echo"<td class='function'>";if(is_array($Y)&&!$r){$Y=json_encode($Y,128|64|256);$r="json";}$Sg=(JUSH=="mssql"&&$m["auto_increment"]);if($Sg&&!$_POST["save"])$r=null;$kd=(isset($_GET["select"])||$Sg?array("orig"=>'original'):array())+adminer()->editFunctions($m);$Wb=stripos($m["default"],"GENERATED ALWAYS AS ")===0?" disabled=''":"";$ya=" name='fields[$C]'$Wb".($Ba?" autofocus":"");$wc=driver()->enumLength($m);if($wc){$m["type"]="enum";$m["length"]=$wc;}echo
driver()->unconvertFunction($m)." ";$R=$_GET["edit"]?:$_GET["select"];if($m["type"]=="enum")echo
h($kd[""])."<td>".adminer()->editInput($R,$m,$ya,$Y);else{$xd=(in_array($r,$kd)||isset($kd[$r]));echo(count($kd)>1?"<select name='function[$C]'$Wb>".optionlist($kd,$r===null||$xd?$r:"")."</select>".on_help("event.target.value.replace(/^SQL\$/, '')",1).script("qsl('select').onchange = functionChange;",""):h(reset($kd))).'<td>';$Yd=adminer()->editInput($R,$m,$ya,$Y);if($Yd!="")echo$Yd;elseif(preg_match('~bool~',$m["type"]))echo"<input type='hidden'$ya value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked='checked'":"")."$ya value='1'>";elseif($m["type"]=="set"){preg_match_all("~'((?:[^']|'')*)'~",$m["length"],$He);foreach($He[1]as$s=>$X){$X=stripcslashes(str_replace("''","'",$X));$Ya=in_array($X,explode(",",$Y),true);echo" <label><input type='checkbox' name='fields[$C][$s]' value='".h($X)."'".($Ya?' checked':'').">".h(adminer()->editVal($X,$m)).'</label>';}}elseif(preg_match('~blob|bytea|raw|file~',$m["type"])&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$C'>";elseif($r=="json"||preg_match('~^jsonb?$~',$m["type"]))echo"<textarea$ya cols='50' rows='12' class='jush-js'>".h($Y).'</textarea>';elseif(($ji=preg_match('~text|lob|memo~i',$m["type"]))||preg_match("~\n~",$Y)){if($ji&&JUSH!="sqlite")$ya
.=" cols='50' rows='12'";else{$L=min(12,substr_count($Y,"\n")+1);$ya
.=" cols='30' rows='$L'";}echo"<textarea$ya>".h($Y).'</textarea>';}else{$Ii=driver()->types();$Oe=(!preg_match('~int~',$m["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$m["length"],$B)?((preg_match("~binary~",$m["type"])?2:1)*$B[1]+($B[3]?1:0)+($B[2]&&!$m["unsigned"]?1:0)):($Ii[$m["type"]]?$Ii[$m["type"]]+($m["unsigned"]?0:1):0));if(JUSH=='sql'&&min_version(5.6)&&preg_match('~time~',$m["type"]))$Oe+=7;echo"<input".((!$xd||$r==="")&&preg_match('~(?<!o)int(?!er)~',$m["type"])&&!preg_match('~\[\]~',$m["full_type"])?" type='number'":"")." value='".h($Y)."'".($Oe?" data-maxlength='$Oe'":"").(preg_match('~char|binary~',$m["type"])&&$Oe>20?" size='".($Oe>99?60:40)."'":"")."$ya>";}echo
adminer()->editHint($R,$m,$Y);$Uc=0;foreach($kd
as$x=>$X){if($x===""||!$X)break;$Uc++;}if($Uc&&count($kd)>1)echo
script("qsl('td').oninput = partial(skipOriginal, $Uc);");}}function
process_input(array$m){if(stripos($m["default"],"GENERATED ALWAYS AS ")===0)return;$u=bracket_escape($m["field"]);$r=idx($_POST["function"],$u);$Y=$_POST["fields"][$u];if($m["type"]=="enum"||driver()->enumLength($m)){if($Y==-1)return
false;if($Y=="")return"NULL";}if($m["auto_increment"]&&$Y=="")return
null;if($r=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?idf_escape($m["field"]):false);if($r=="NULL")return"NULL";if($m["type"]=="set")$Y=implode(",",(array)$Y);if($r=="json"){$r="";$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}if(preg_match('~blob|bytea|raw|file~',$m["type"])&&ini_bool("file_uploads")){$Sc=get_file("fields-$u");if(!is_string($Sc))return
false;return
driver()->quoteBinary($Sc);}return
adminer()->processInput($m,$Y,$r);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$oh="<ul>\n";foreach(table_status('',true)as$R=>$S){$C=adminer()->tableName($S);if(isset($S["Engine"])&&$C!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array())),1));if(!$I||$I->fetch_row()){$wg="<a href='".h(ME."select=".urlencode($R)."&where[0][op]=".urlencode($_GET["where"][0]["op"])."&where[0][val]=".urlencode($_GET["where"][0]["val"]))."'>$C</a>";echo"$oh<li>".($I?$wg:"<p class='error'>$wg: ".error())."\n";$oh="";}}}echo($oh?"<p class='message'>".'No tables.':"</ul>")."\n";}function
on_help($kb,$zh=0){return
script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $kb, $zh) }, onmouseout: helpMouseout});","");}function
edit_form($R,array$n,$K,$Ri,$l=''){$Xh=adminer()->tableName(table_status1($R,true));page_header(($Ri?'Edit':'Insert'),$l,array("select"=>array($R,$Xh)),$Xh);adminer()->editRowPrint($R,$n,$K,$Ri);if($K===false){echo"<p class='error'>".'No rows.'."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";if(!$n)echo"<p class='error'>".'You have no privileges to update this table.'."\n";else{echo"<table class='layout'>".script("qsl('table').onkeydown = editingKeydown;");$Ba=!$_POST;foreach($n
as$C=>$m){echo"<tr><th>".adminer()->fieldName($m);$k=idx($_GET["set"],bracket_escape($C));if($k===null){$k=$m["default"];if($m["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$k,$Pg))$k=$Pg[1];if(JUSH=="sql"&&preg_match('~binary~',$m["type"]))$k=bin2hex($k);}$Y=($K!==null?($K[$C]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$m["type"])&&is_array($K[$C])?implode(",",$K[$C]):(is_bool($K[$C])?+$K[$C]:$K[$C])):(!$Ri&&$m["auto_increment"]?"":(isset($_GET["select"])?false:$k)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$m);$r=($_POST["save"]?idx($_POST["function"],$C,""):($Ri&&preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$Ri&&$Y==$m["default"]&&preg_match('~^[\w.]+\(~',$Y))$r="SQL";if(preg_match("~time~",$m["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$r="now";}if($m["type"]=="uuid"&&$Y=="uuid()"){$Y="";$r="uuid";}if($Ba!==false)$Ba=($m["auto_increment"]||$r=="now"||$r=="uuid"?null:true);input($m,$Y,$r,$Ba);if($Ba)$Ba=false;echo"\n";}if(!support("table")&&!fields($R))echo"<tr>"."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;")."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>"."\n";echo"</table>\n";}echo"<p>\n";if($n){echo"<input type='submit' value='".'Save'."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($Ri?'Save and continue edit':'Save and insert next')."' title='Ctrl+Shift+Enter'>\n",($Ri?script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".'Saving'."…', this); };"):"");}echo($Ri?"<input type='submit' name='delete' value='".'Delete'."'>".confirm()."\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
shorten_utf8($Q,$y=80,$Rh=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$y).")($)?)u",$Q,$B))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$y).")($)?)",$Q,$B);return
h($B[1]).$Rh.(isset($B[2])?"":"<i>…</i>");}function
icon($Id,$C,$Hd,$pi){return"<button type='submit' name='$C' title='".h($pi)."' class='icon icon-$Id'><span>$Hd</span></button>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}if($_GET["file"]=="favicon.ico"){header("Content-Type: image/x-icon");echo
lzw_decompress("\0\0\0` \0�\0\n @\0�C��\"\0`E�Q����?�tvM'�Jd�d\\�b0\0�\"��fӈ��s5����A�XPaJ�0���8�#R�T��z`�#.��c�X��Ȁ?�-\0�Im?�.�M��\0ȯ(̉��/(%�\0");}elseif($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M��h��g�б���\"C���d<��f�a��:;NB�q�R;1Lf�9��u7\$)\$L;3��A��`%�E�!���e9&���r4�M��A��v2�\r&:iΖs���0��\"3�Má���-;�L�C@��i:cs�,�(a�G#ã��e���ɐ�9kS�Ѻu�>�d����d��c�ñ��:6Z�c�A��rf�����[����Y�N/��d�9I�8�7f\"�V3Y������)����ƣ���-4U���oD:�xjH��b{���&�єt1��֋U�v8# ȵ!pp2��\0c%�\r��7����<�8�s�	��;��H΀2���6o3��P�%�s������>�����J��KJ�@dB�\0Z���\0�ct�0X��*D!	B��1\r5# @��i*N��q,N�2ÔW�ij^��0�>��z�D�4Q8�sd~�!���ȯ�\$�(�s9td�����:,(�=�+���R,����:6S�:�G�rl����쀟Hh��9,0\0�?��P��!\$2�UO�՚4v�#��AiW���<Ձ�ȹ\"�r�h������m�-�`(p@@pe]L\\iL�Or�:aj�-u`��S/=�{:ɭvҶ5�Л�T��,\\x3�c��R��+W��f�Sc%��c�����G=RCJ�2,C���c��0�/@��1\rz��\r�S�X�M*��nW,H��\rye��g.8�5�3Z��(8R��ׯ�5��H��H�3�zr��jX t6�C%Y�)�ͻo8�.V,�&���I�e��1�#��f\\�.<�'?|Ɠ�oGC�6�O��mDT�ݬX�����W����{mW�[�4\r�T�#-��\$X�b���\n�����_3k����M��M	����hA���&��0��W�]��R��P�:��{�Lqj�`-� �[�no��*��Л�Po�Q� '`X[[a�2��DdPoAu������k`\$Y0%�0�q���:�ƅ\0�h@!�;�eւ�j0FP�:�vC�i7l��C���\r�Hy�RM�o�C\0��yl�\$�\$��/������.0����38��Ls~a��:N���F ��;#�}��2Hq�55�	}���H��#I�l#/�����d�.<��6����:��r8'Fp���^\0��\$��bk����ZP�.���ȇA\$̰w���f�I�2�p��g�UA\$R�%G��@]4&2���zm2\$R����)�9Hu���!!�ւ��G�\0bZ/���4�M�Z>P)�s�8�C(s+��O�@tKQ�n����Y�7�E\n41�*�`���MTNyR*:���tf���d�[^͸��&���c�S��`\r�1��(�N�aPi�F�A�RTb%P*�E�����hq�:@�hˡz��2�d�L�_s��`k[�tޔ4�ǆ�<��+3����DxI]w��ֽ���,\"{9�ڏ�s:U�D�P���������\"R\"�\$�!Y��H�A2�T|ރj}s�7�xpb:������5�Zԗk����ؖ��K�sx���qh(\"Ւo0���ـ���H�]u��9B'EW%�&�A�1�A����:���h�lM��V��J	I�PK��g�9/T�ʆJ��m�0����*࣍�\r�e\0Ji8�\$���N�v����=X��P8��-`@.<!��0��ӝ<Sz�2IN��b�i�Q���ymB(���{M�8 H��b\\�i3��@�3ϐ@ט7\$��Q���	-%���Q��nOL�Ђ� �2�X(`�eL\nPR�n����|v�R�<gbOK�B�^mk�Ǜ˻?h/	#dkb�q�\rr:�M��9O�ɘ��7܂�D|#����+�/oS\r�RU�cbt�m��`����p-���:S1�kց�7�u�����3a���Yv�/����G��Ps��qO6��XAT��ʯ!e�l�m�=�E6�oH�8��:=�Lc3����/�&�g@9b;d�/��p���\"����OjS8���_���\$w��7�o�W��.)z�O��M�A{1D�J�d�RRZj�h!�oM�N�)m�Yl�۳%f���m�E�����z����í�/�٢��Rvy@b�!�1�+;|t��ڊhƝg��k\0� �a�.����:�������xr((MsD�֖�Z�I��|� ���8�ʜ2�x)�S�����'uXaκ~{�v6�6T���s�A�K���s\\(b\re����U������u��T���i��� �|jJJX�7�V;�N�]C[�W�C\n�7s�݆�E���9ֲ��b���v_y�M6�H��~�jJE�g\0�F�{N�1\0���1��>�4/o�m�\0�,�L^\r�P��4˪��,�R,@l�X\n@`\r��	��\r���� � ���	�����j �	@�@�\n �	 �	\0j@�P�@�	0���@�	 �	\$N	 V\0�``\n\0�\n �\n@�%����\n\0`\r����	��\r���\0�R�v��	\0�`�	���z}\0\\�O6��M�2��88�>2PC@���FpQPY�aPi\n�sp{��p��	p�	�\nP�	0o�����P�\n �\0�kb�l\0^�@�\0`��@���\"\n��1�0&'@Zhú��07�*S��5���W�_0g�o�yЁ	1UБ	P�	С�qk �`�0�0��1�\r�^��\"Y	`�\n�� �5 �\0�	 p\n��\n��`� �q���(���!C��A�18�S�AGpu�Q�Q[�a\n � 0� qn\rRp�	 �@b\r`�\r`�\r��	�����d���	,\n��``��\n��`d��Q��?&Q'�k&Rq�u��1�\n��\n\0�Ђ2�����R\n�	\0�*0�`���\n�`\n@��fd\0���`� �\n�@�	��Dø`p�� ���O���\$B\\��'��(����/&�K'2w��@�	 ��	3���3)2�s� �; �\0�h�����@�\n@��� f��%1,��ʃ�\$�G*}fF�(�@�\r�@\rx�\$�J.�r�A�H�A+�U�'B���aB��X��^���L�@�0�-�ը����hhBi�C�F�FGD�\n���e�R��4@C��f���F`�mwG� ���A�U4��\0���Ig�I���TUAKx����T�F�v��\$YT��D�Ct0�M�L��r�1C��N}��7k42�ld��\n@o�*T)d�q�D@p �4��ԷF���WE��Iz���KQ�39M�h~�*L!��F�EG+��n�⎖�)R[C���GH�I ڟM�� �J�Ʈȏ���NN�,��̏@��)R�n���fJOcM�*��6��\r�-Y�l�5pi�kEt�� �FT�]4U��猁N�gL4��u|�U�F��Pu\n*'�*yM����D��<��sU���5d�5x��W�^`�L���&d�����5c����j\n�5�{� ����z��&�@E�,�@�fe�\"@gf%�`��gJ7n& �~@��h��e�`*�f ����hCH4�\n.FF,�|W��7�\n\$#��`�h�~��W���|�\n� �\$���H�hC�\"\n��d���-��f���V�m�j���f��g\"hB!wf6p,+�p��q��s4�;�V��L`%�W8;�\0\r��#`ڒwF�wSeC�uN\"�u�Z2�T|�q`����v�w �v7[uvp�mv�xÓwÿy7x���4�wqy�����x�0��<͗�gL�/�chJMrwt*�z�t�\0�z��~�yWgx�t�#zɣ}�h�\0�w��=��+�����{��h�|W�w��iܤ�!h+�Է�zx)}��M�wXE�@��и��[��/I�c������x7݅E�w�xi����/A��NE�=�7t�HlG�!��o�X� �@�Ew8�N�W�BX���Wnx�8��������v��j���Vł���#E|k|��pb�}8����`���Ï��w��8��IC��Ō��Xً��z��y�	��B=��	�o���x��������kj6�j�;�%�yKny�]�Y'�\"\0�Z��Mnb�nɳ����	�Y��ō��lC�X��s�b����w��W�������w��t��a�Ql9�l�����7f��Uj����ix��������x9�7u��|�1Y��~���=�8w���x ��󠘿�xj���'����ڡK��#�Z5��C�1z�eY����#�\0ˣ�-�yy|&�-�8�_ZA�Wh�B�v�7�}��R1Z#�p�´�6���imy\$\r�y�i�mk=��A��W�ە��tnWv��#L^�#X�y��zuw�}�_���w���;�W�:�z�O�:�;�:\r���:�Pˢ'�����Z;�z#��!�������NUei���7w�%~@߳�3��}�Ez�Z����,f9wV�ŴL-467i�M�6f3롲��E�	��׆�D�R");}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M��h��g���h0�LЁ�d91��	��o6�P���nf0�q����s4��H�E\$�J%R�a��k\r�c)�Xa���3:\r�5���q���1��'���E��2C(�hc�X��31��d1�f#	��g9Φ�\$\$b5�Ú�ʹ^�Z�F���3�0����x�_!#3�n82JΆ��,:5�9ʾr�ͪUa��m�>�5�k���}�F#1��â4L&A�W��n��Lhkr�f7�>�f5�t9��w\$#�:�1��l2̧Mn�cc����9�>����z̸��4J��<����NkT:AP`�)Ћ���ӂ�� >���\rC����N�=8��|8��\\�����+��[�cx�#��H�<�\"��wơtL8��LL�J��0�\r0�3�c|} ��#ID�+�at �\r�s��I��P9�r�:���9;�����r���S�u6O��F2��[����,�#�i\0��Q�l3S�m��î �9Sh�2�c��3��R1�`1.qmR��k���t�{N\$�rO6�c@�D��\n��Sh�0�U�j��D7O������6�I��<�ʢ��P�z�<B��,�����4\r/�P\r�MO����^��f���H�anJ��Af=bC^2�8h׊Cu�\0���ss����4&/�a�.9�c�d��~3�㸚���QeQ��qG�c��i�<��U�e�J?9=��,�B�P�5�߾�X`0L۩N*l!���&©��2�a_p>�Kd���s����@8�B3�ß\n��C���<�1[���9c�>T�l^�0L ��U�ڷ�;��4�7d1d���9����4�~�ɇ!�iotnH�8wYHlk�\$<1��(h3n�h��� �;��8�oa8�1�2���:z��\"�A��i�Z��\n��Vp�Q�����q�^��������J|��P��a&A���p�ȉ����(2@p��\\�P���<�7HM\n�-�0��9!p-c��;��8]K�So��?G �C2N�!�0���a�-�+�6�&�~�U�+b\$k� ��9��8�^Aj�3rC�\\�1��PdD, �A�8���~#y�%`");}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("':�̢���i1��1��	4������Q6a&��:OAI��e:NF�D|�!���Cy��m2��\"���r<�̱���/C�#����:DbqSe�J�˦Cܺ\n\n��ǱS\rZ��H\$RAܞS+XKvtd�g:��6��EvXŞ�j��mҩej�2�M�����B��&ʮ�L�C�3���Q0�L��-x�\n��D���yNa�Pn:�����s��͐�(�cL��/���(�5{���Qy4��g-�����i4ڃf��(��bU���k��o7�&�ä�*ACb����`.����\r����������\n��Ch�<\r)`�إ`�7�Cʒ���Z���X�<�Q�1X���@�0dp9EQ�f����F�\r��!���(h��)��\np'#Č��H�(i*�r��&<#��7K��~�# ��A:N6�����l�,�\r��JP�3�!@�2>Cr���h�N��]�(a0M3�2��6��U��E2'!<��#3R�<�����X���CH�7�#n�+��a\$!��2��P�0�.�wd�r:Y����E��!]�<��j��@�\\�pl�_\r�Z���ғ�TͩZ�s�3\"�~9���j��P�)Q�YbݕD�Yc��`��z�c��Ѩ��'�#t�BOh�*2��<ŒO�fg-Z����#��8a�^��+r2b��\\��~0�������W����n��p!#�`��Z��6�1�2��@�ky��9\r��B3�pޅ�6��<�!p�G�9�n�o�6s��#F�3���bA��6�9���Z�#��6��%?�s��\"��|؂�)�b�Jc\r����N�s��ih8����ݟ�:�;��H�ތ�u�I5�@�1��A�PaH^\$H�v��@ÛL~���b9�'�����S?P�-���0�C�\nR�m�4���ȓ:���Ը�2��4��h(k\njI��6\"�EY�#��W�r�\r��G8�@t���Xԓ��BS\nc0�k�C I\rʰ<u`A!�)��2��C�\0=��� ���P�1�ӢK!�!��p�Is�,6�d���i1+����k���<��^�	�\n��20�Fԉ_\$�)f\0��C8E^��/3W!א)�u�*���&\$�2�Y\n�]��Ek�DV�\$�J���xTse!�RY� R��`=L���ޫ\nl_.!�V!�\r\nH�k��\$א`{1	|�����i<jRrPTG|��w�4b�\r���4d�,�E��6���<�h[N�q@Oi�>'ѩ\r����;�]#��}�0�ASI�Jd�A/Q����⸵�@t\r�UG��_G�<��<y-I�z򄤝�\"�P��B\0������q`��vA��a̡J�R�ʮ)��JB.�T��L��y����Cpp�\0(7�cYY�a��M��1�em4�c��r��S)o����p�C!I���Sb�0m��(d�EH����߳�X���/���P���y�X��85��\$+�֖���gd�����y��ϝ�J��� �lE��ur�,dCX�}e������m�]��2�̽�(-z����Z��;I��\\�) ,�\n�>�)����\rVS\njx*w`ⴷSFi��d��,���Z�JFM}Њ ��\\Z�P��`�z�Z�E]�d��ɟO�cmԁ]� ������%�\"w4��\n\$��zV�SQD�:�6���G�wM��S0B�-s��)�Z�cǁ2��δA;��n�Wz/A�Zh�G~�c�c%�[�D�&l�FR�77|�I���3��g0�L���a��c�0RJ�2��%���F� S� �L�^� tr���t����ʩ;��.喚Ł�>����[�a�N���^�(!g�@1����N�z�<b�ݖ�����O,��Cu��D�tj޹I;)�݀�\nn�c��Ȃ�W<s�	�\0�hN�P�9��{ue��ut뵕������3��=��g�����J����WQ�0���w9p-���	�������'5��\nO��e)M�)_k�z\0V�����;j�l��\n����x�Pf�-�`C�.@&]#\0ڶp�y͖ƛ�t�d�� ��b}�	G1�m�ru���*�_�xD�3�q��B�sQ��u��s%�\n�5s�ut���{�s�y���N��4�,J{4@��\0��P���^��=��l���`�e~F١h3o�\"��q�R<iUT�[Q��U��M�6�T. ��0'�pe\\�����5����pCe	ٕ�\"*�M	����D���?�h��2���zU�@7�C�4�a��iE!f�\$�B��<�9o*\$��lH�\$ �@����P\rN�Y�n<\$�	�Q�=�F&��*@]\0��� W'd� z\$��j�P[��\$���0#&��_�`+�B)�w�v%	����LcJ��RS��i`�Ů	�F�W	��\nBP\n�\r\0}	瑩0�Z���/`j\$�: �8ie���φx�����a ���Gn�sgO��U%VU��@�N��ϐ�d+�(oJ�@X���zM'F٣�WhV�I^٢�1>�@�\"���� ��Q�R!�\\�`[������.�0fb�F;���Fp�p/t`����(��V���b�Ȳ�(��H�l����ԯ1v�����H��1T�3�q���1�Ѫf�\nT\$���Nq+��`ލv�ǜ�\r�Vm���r���'ϸ��g%�\"L�m����(�(CLz��\"h�X�m=�\\H\n0U�� f&M\$�g\$�U`a\rP�>`�#g��h��`�R4H��'�����GK;\"M�ۨT�h�BE�n\"b>���\r���#�\0�N:�#_	QQ1{	f:B���R�&���)J��Br�+�K.\$�Pq�-r�S%TIT&Q���{#2o(*P��5�`�1H���'	<T�d����s��,N�� ����^\r%�3��\r&��4�B�/\0�kLH\$�4d�>���/�ඵ�H���*���3J�А�<�Hh��p�'��O/&�2I.�x3V.�s5�e3�ێZ�(�9E�g�;R�;�J��Q�@��vgz@������'dZ&�,U���F��b*�D��H! �\r�;%�x'G#��͠w��#�֠�2;#�Bv�X��a�\nb�{4K�G��%���GuE`\\\rB\r\0�-mW\rM\"��#E�cFbF�nz���@4J��[\$��%2V��%�&T�V��d�4hemN�-;Eľ%E�E�r�<\"@�F�P�L �߭�4E����z`�u�7�N�4��\0�F:h�K�h/:�\"�M�Z��\r+P4\r?��S��O;B��0\$FCEp��M\"�%H4D�|��LN�FtE��g���5�=J\r\"��޼5��4�K�P\rbZ�\r\"pEQ'DwK�W0��g'�l\"h�QF�C,�Cc���IH�P�hF]5�& f�T��iSTUS�����[4�[u�Ne�\$o�K��O ��b\" 5�\0�D�)E�%\"�]��/���ЌJ�6U�d��`��a)V-0��DӔbM�)���������`��%�ELt��+��6C7j�d��:�V4ơ3� -�R\rG�IT��#�<4-CgCP{V�\$'����g��R@�'��S=%���F�k:��k��9����e]aO��G9�;��-6��8W��*�x\"U��YlB���������	��\n��p���l����Z�m\0�5����Oq̨��b�W1s@��K�-p���E�Spw\nGWoQ�qG}vp�w}q��q�\\�7�RZ�@��t��t�;pG}w׀/%\"L�E\0t�h�)�\r��J�\\W@�	�|D#S��ƃV��R�z�2���v�����	�}�����(�\0y<�X\r��x���q�<��Isk1S�-Q4Yq8�#��v���d.ֹS;q�!,'(���<.�J7H�\"��.����u�����#�Q�\re�r�Xv[�h\$�{-�Y���JBg��iM8��'�\nƘtDZ~/�b���8��\$��DbR�O�O��`O5S>����[�D�ꔸ����_3X�)��'��Jd\r�X����UD�U�X8�x�-旅�P�N`�	�\n�Z���@Ra48��:���\0�x���N�\\�0%��f��\\��>\"@^\0Zx�Z�\0ZaBr#�X��\r��{��˕�flFb\0[�ވ\0[�6���	��� �=��\n��WB��\$'�kG�(\$y�e9�(8�& h��Rܔ��o�ȼ Ǉ���Y��4��7_��d��9�'���������z\r���  ����v�G��O8���MOh'��X�S0�\0\0�	��9�s?���I�MY�8� 9����HO��,4	��xs��P�*G����c8��Qɠ��wB|�z	@�	���9c�K��QG�bFj�X��oS�\$��dFHĂP�@ѧ<嶴�,�}�m��r��\"�'k�`��c�x��e�C��C��:���:X� �T���^�d�Æqh��s���Lv�Ү0\r,4�\r_v�L�j�jM��b[  ��ls���Z�@�����;f��`2Yc�e�'�Mer��F\$�!��\n��	*0\r�AN�LP��jٓ����;ƣV�Q|(��3����[p��8���|�^\r�Bf/�D���Ҟ B��_�N5M�� \$�\naZЦ���~�Ule�rŧr��Z�aZ�����գs8R�G�Z��w���N�_Ʊ�Yϣ�m����]��;ƚL�����c������Ű��I�Q3��O��|�y*`� �5��4�;&v8�#�R�8+`X�bV�6�ƫi�3F��E���oc82�M�\"����G�Wb\rO�C�Vd�ӭ�w\\�ͯ*cSi�Qү��R`�d7}	���)�ϴ�,�+bd�۹�FN�3��L\\��eRn\$&\\r��+d��]O5kq,&\"D�CU6j�p���\\'�@o�~�5N=�|�&�!��B�w�H�yyz7��(Ǎ���b5(3փ_\0`z�b�Уr��8	�Z�v�8L˓�)��S�M<�*7\$��\rR�b���B%��ƴDs�z�R>[�Q����&Q������'\r�pp�z�/<��}L�#��Ε���Z��\"t��\n��.4�g�P��p�D�n�ʹN��F�d\0`^����\rnȂ׳#_�� w(�2�<7-��X޹\0��s��,^�hC,�!:�\rK��.��Ӣ�Ţ���\\��+v�Z��\0�Q9eʛ˞E�w?>�\$}��D#���c�0MV3�%Y���\r��tj5��7��{ŝ�Lz=�<��8I�M�����G����L�\$��2��{(�pe?u�,R�d*X�4�����\0\"@���}<.@��	��N��\$�XU�js�/��<>\"* �#\$����&CPI	��t������?� ��	�O��\\��_��Q5Y�H@���b��c�h����뱖��O0T�'�8�w�����j+H�v_#�����06�w֎�X��d+�ܓ\\��\n\0	\\�>s��A	PF�d8m'@�\nH�\0�c�OwS�����Y�`�����R��Dna\"��~�?�m���|@6��+�GxV��\0��W�Ӱ�nw���.�؃b��9Í��E�|E���\rЈr�\"��x���-���\rN6�n�\$Ҭ�-B�H�^�)��y&��ךW�ǧ�bv�R�	���N\0��n�	T��`8X��A\r:{O�@\" �!��\$K�qo��jY֪J�����h}d<1I�xd����TT4NeeC0䥿�:D�F�5L�*::H�jZ��F�R�MրnS\n>PO�[�\$V8;#�K\\'�B��R�د��R�_�8�j��*Ej�\\~v���v��p@T�X�\0002dE	�H�V���D�\"Q'EDJB~A��A�Il*'\n�Y��.�+�9��pg���/�\"�1�8�0�IA�FCȨ�V*a��P�d�У5H\"�A��6�s�Y��;訞�/��0��v}y�\r����ץ1�u\"ˋ�m��_�0焄`���\\B1^\nk\r]lh�}]HBW`��0�꨹rFf�)�W,�ҧ]sm9'O�xԽ�,�9J8��?�4�����\"҅�۽�<�-S����M�;�v��6y|�Z����%�a�#8��TC�!�p��\n��CZ(�9|��0<BL\r�\n�]�PB0�&�+IŌ�G��`hu��\0��\0005��S@\"Uؔ@��\0�\$��ސ\"Ҡ��]l/	��I�B4��.�6���,C ��@j�d>dE�*D@j����f`��:En�bĀ71��)C<@A�Y!������e�\\o��Y��F�,M�\nlt����/)�\\43)��2��ɸ�)���N[ ppp1���#��Ð�p\0��Œ���^{��A��TH��6�����\n\0P�H�.\r��x|�T�FD0��P�y�0��%����K��d�����B���C�%E)�T�s He5)�4� r��!ۚ*Lp1<�f�N��'�+�LJ�Sa�������\"���\"�l��q��,�>H�m HV�/�lC�&��H)c�&Y2���%���n\n^N(6�D� ����Gq��!�\0.�#��\0vr,�M��&A�������9#�X��B�h��!W\0�_\r{���@�09IL22wA��)�H^^@r�pG��7Dd.�I5�|��1P���k/��Mez���}҂!x�~�� qbHu?Jl�C ��g�\nl�EU	F�|��1r��U��&8F<'� dӣ5%��Y�t���Ⱥ�EA�!�/@����G���tx���9�~�I:)&�RZ�~��	L!K��BX��-��h��c/�o��P�I���NJ2�|����O�V�I�\$�0)e�� ��M릒:H�\$�y��1\n�7�m�@sX@T@w	6�TX�5+�'\\�`��_S�	�_0�1!�n_s�g9�x\r�g,��O9\$ݥ&�z�bQSf!��D*���U�\n0s�|(�C���A�'��t�r|��&���?rn��Tkx���X=i��,\$3t[�r�9?��Ʊ�d�1��fH��4���<(:?����ס����KUJع�QC�BT�P��\0}2\0�z���+�*HH�Q%�)+!(�g�UD�:y�\0�\0*%�@\"|�Sh|��y\\��'�¨����ml e`V��1�����*M��Z�%�����j���Q}�(~	�	P���HW�wZ���(-*F��F闤�~�OQΈ�i�XMeF���J��ja5&��EzN�N�Tt���p �PQƏ�Eb+����H-�\$j'�S��=�cM�ֱ+3bO����6�&[)��7DuM�j�VL�i:��*�fA8�\nle��\\n�l鄖]D%~!W�2�7TU�]���!����=bJ��'���p�>1��f����\nx�&�s�'���P/\"*�Lo#c������^I�(#���7V���\$F��!р^�\$�,�|�YH�(MЊ�\nA\n�R��*��l'`i����ȸ�o��A�Q�6ȍ�H��ԍ�&0Sݷ�z��\0R�ߏ��F �f�IP0���|�G��Q�:\0��Q�H)�\"�8|%Ϙ��*�\"�ܓStJ�xj�M�P���!�@*2�y'\0P	ŪX�R�)\$ߍ�S�U�-ZD'�� ��5H�����\n/�㐙|��mR#^Ӎ*�Ȫe8B�灅:5OE_��=���U�5Tm \n\0CZZ�����/�k\\+Cϓ(W!MH��p3�V��B�)`�@BU#��t�Q��/��Q��Q Q��ʢ�~P������P��#�����^@_���_������|���9T�&s��n�%!pW�Q\0b��s��[�G���\r�=\"F�P��g\0!�sh��t\$+O����	�ө7I�M�cS�i�t�Y�ŐC�F�J�[�}spQga��y�)ZAwDX<���-�F�Q�36t���=�[]5���!�\$�a`{���������!��H�lx�R�{vM�;g�{(���f���Il��*�p\r�^���ĤK�V9�K r)��e�!�vԷ`D��D�AȺ���@�D��ZF�P� x��c���AM�h�i�4\0��֘*�,֟�3�����LM�(cG\\ǐ�l�S��cͼ��mp�LqI�V��6E�=�Ī1��	u�|-��l�0�+\"~�m�T0�h.��@^Pյ�5m�D�l��2��m�[iېn�qJ���ޘr��đ��\\}�M��\"�rq݋�J��מ��f�s����r����X�7� ��N\"�*�8��|�:��I�-%D�F�7�|D0�\\��<�H\"�(E\0�\n\$����u�^@]�*+�\$��ڄE��{���t2���念���Y���Ǆ�)��~\$Ӌ���e�&+\0�B��+�n�[\"���I���j��~������s�_�)������b�ݨ�\$�X���*��\\5����G�)�WL��N1�+��aI҄�^Tj]���\$U�>�����N��2!�<e�����v��;��ɬ��րZ��ҪC��/x=���1r����y8�ި\$*�\r�c~�\nz���:o�-B����#ܰ�7\\)����Ĕ(5��@�[茅��a�#UK��~�2��A�FY�3�!C�����S߬Z�%,�_N���>q�� :lמ�!U�Y���X��W� r�@k����a���E�E�E�P�\\e�a���w��\rK���\0�z����v�`���9l�F\"9�������P�\n�p�H�?�P5�|+�x����j�z�_���Jq`,������\\	Ѫ�K�L�r;��9��>E'��H��I*u�&QL�+�ua�()�!��Y��^Љ�*��b��;�\r)�V8l�e|G��6�G\\���{�\0� .�B4��8�K_)���kj:����þ&,����I��\"\"�15���|T.&�va�\nK]u� �k{	��?3�Ĝ[�,[���J�PC��k�\rFة�k	��9�^I��I)SQ�B�Ugh�g��M�X���3�����x���!��Mcd���7�k�y�Z�,�����)n���J:�N�8�E�y\n��Z�W�;�!��v62#A0��{�-i��7~\0VX���^�11-�+�t̗��VG���.eyY�RD\r��;�1l2�/��VF��d�\$PЮ0=@kS�0h��Ɉ@��/'(O�P-Ӕ4G>�(r�X6�����%XZ@�:��'#0�6kE|���\$|H;���e�� g�%�W���fO1�#�ï��v̒fI9��(O��d���w	9]��f}̷G��Ds���������X40��B��f����2̄�+A}�͝0}�K9�F�ݪ���Nh�/7eT���sl����\n��4勳P~wO()Ig|9�ţ�ji6���ݸ�(�e����jηo>B�ϩ)��K��u��(�}q�1��od�V[Ĵ\n�gRvL�Mзr\nF#{�t*#�x��ܤ�9Ds��k/7up:^z�F��q��ϋĨ�2���2n��%��y��i�Ù *�8-��tH�&l����\r����4i�d�8`��\"�����h��ZB�vT\0ަ9���+䙂~��\$ީ(��L�P\\�{��XA�������i:�gzѨ�\$���`y�W�馴���oA��	��5��Ğ\$�tU�����6jG����P�:�\r�3O\"I�����DZ��7��kh9�f+�ܝ,��pO�Y�Ni=���\\�����u_�d��g\n��/���lMTI�\"T��t���0�-���?��3Pm�\09��,`|e5bɵ��\$5<�-�RU����,��U\n���ts�Q�@	����tk�_9f��R���\0�؄�J���`uͮ���%Sl�p\0S7\0�P>�&߄�>&[g����φ�R�0�����P6C ���d�d�=�GP\r��=a(5���)��?a\0�kvN�U�n���)r��=���L2߮��Э�Q�P�H�S�Mv\\X��=ͣ�)�(�\0��!0����v�#q��eT���ʳN�T�ޱԨ~�]�p��x�i���5T���Dl��@E3����peC����ڀ�����I�O���Pbz\\N�ܹ�I�E��9h��j�ϰm��_e�v�u�:4;����S*@�~[,�^����ɼ��X���3~�	ƙ��CX5��xZ�BO��a��@�L)-�\";{V&u��(�<^�ӻ�dT�bgB\r od�;�@\\���B�ژЕ��\n� ˜=`)	̤�JCZyknt��\"��O����RB�޾�`�~	o�#&xC[b7��,˂!�2��+����x\0;�*uN���K&8=s�p�\\����aЏ���?��1�|9P��y���3I�K��tZż4,T���SN,z��f�e���(J�?|;�/��3�:]M�(#�Y1��4��S'����s�M��&��7rH�LZ|D�'���m~�4��y�c!��U�)u����d�9���kÚ[8af�8\0N^C��D���H_���و�˚&8n*�8��\r�:�����n���,8�8���f�\"�\$H���껀=���&����u���\n�2�#\n���>%h�\"��`	)yW��V�f0q=�� I��%�r؍\\�EW9zC)��	�|ǨHBQ�����:5��b�F\\�`�01��a�a,.'d�ϐ9��N�]C	r\0�~j�#,-�K�y�an�����	b�K����,M���!����/�@1��B�fG���d\$?�d���oG\\9��VN5UJ��ф�ɪ�c�����[Gm���L���^�v({�ɹs�<-�]}&�\n����\0����[腑zH���nD+��R�ldY��=��WV�s�:-��H�_���8jzj����5����\"\0\"N?@;�O{�aN�];,���SOFH�Ӫ���QD��R!���\\��l ¯оc���G�D�����I�H�/.x\"���Rߤ��)�	^�A�]��WB颶.d���h�����W��L��	}�x��&4�?���z��t󳷴����<*�8����e}{HZ��1,(o�o�xW�t�2�P�#�A*�����o[���.x>NP�jO��*�\$LAo�F�\0����>	�\r|=sF�p�\"�ڲuuXx�b�4\n�Z|��\"A����mvB;���`y�(mB8�����dE��FH��8�qs�>!U�R��搪�Y�H�qp\n���<G����b��o�Tj:���974V���:���	3�IB�U��{�����^�4�Z����\0'�n�^WEvI��s��y�I�6�V-)\$C�I!����Y%)4�]p��ח���4���\r%��\\,8�Y2�Mv�B*R�\"dw��o�(	�/\"�hm,W\0c@��{^�߮�ZDP�]�u6P/��_�L���\r�i�P��Nh�&jl�_��\"����N%��uFc ����4L�1�b{\"ٞ��@��ۛ�M��\"w��O��.�2�L�>E�P�GG�9d�VN���/��[u+y�6��8{�Y�9Z@bн�D����\"�߀�{�p�� S��Y|刢?�оow\"�8�D�����{�`~��\0b/!˄/�`8��<��!��Å��Ӿ�9�v!V(?P����ߪr��?J��@{��}��I��_�\"�o���,�k��@��������2�1����v���~��V��%�06K����2��n�1q'k��T�U鉪EH����_������/Ϥ&��_K+��q��,�~�i�eB~î�3��Ch㜪ߨ	{]�T�����#����������a��_�����-�8��������A7~X���??�׊�+���\"����񧺨C������0�X|)ii�\n���ڢ.)B1�o��\n�X��80A������t�.)4oo=��/��(�\0��C ��: �\r�V,Ad���n�Pa ��o�)�\"� ��F�z��2�o�c\n2!d#��\0�@4\0�;�������.�Z���=ؚ;\\�f.FZ@O\$\0�!�3���*\0��0���p�bA�����Q���\0���Y�j�´�{���N\n\0\$�~��Ԡ��VJ��%��� U�t�������`�i|#w��>��#�:2��E\$5 �#�\rdY�\$�B^���pD5ȴ���K B���i�&�rl\0������m�,���框B	9}��Po���\\0������QR�\nP)�\0�2��d?�Ah�k��\r��\0W@�B*\$�����j�����)L�x�p{�-U�k�yz����mA�T�A�8��ओ�:aG���jǘ\0�����B���A�\"�.�� Oe\0z�>Ep��AKX�hh\rd�-�`4V!�fxV��%���8�x�ǧ�-�'���r��PZ!��:<Hl\0");}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("v0��F����==��FS	��_6MƳ���r:�E�CI��o:�C��Xc��\r�؄J(:=�E���a28�x�?�'�i�SANN���xs�NB��Vl0���S	��Ul�(D|҄��P��>�E�㩶yHch��-3Eb�� �b��pE�p�9.����~\n�?Kb�iw|�`��d.�x8EN��!��2��3���\r���Y���y6GFmY�8o7\n\r�0�<d4�E'�\n#�\r���.�C!�^t�(��bqH��.���s���2�N�q٤�9��#{�c�����3nӸ2��r�:<�+�9�CȨ���\n<�\r`��/b�\\���!�H�2SڙF#8Ј�I�78�K��*ں�!���鎑��+��:+���&�2|�:��9���:��N���pA/#�� �0D�\\�'�1����2�a@��+J�.�c,�����1��@^.B��ь�`OK=�`B��P�6����>(�eK%! ^!Ϭ�B��HS�s8^9�3�O1��.Xj+���M	#+�F�:�7�S�\$0�V(�FQ�\r!I��*�X�/̊���67=�۪X3݆؇���^��gf#W��g��8ߋ�h�7��E�k\r�ŹG�)��t�We4�V؝����&7�\0R��N!0�1W���y�CP��!��i|�gn��.\r�0�9�Aݸ���۶�^�8v�l\"�b�|�yHY�2�9�0�߅�.��:y���6�:�ؿ�n�\0Q�7��bk�<\0��湸�-�B�{��;�����W����&�/n�w��2A׵�����A�0yu)���kLƹtk�\0�;�d�=%m.��ŏc5�f���*�@4�� ���c�Ƹ܆|�\"맳�h�\\�f�P�N��q����s�f�~P��pHp\n~���>T_��QOQ�\$�V��S�pn1�ʚ��}=���L��Jeuc�����aA|;��ȓN��-��Z�@R��ͳ� �	��.��2�����`RE���^iP1&��ވ(���\$�C�Y�5�؃��axh@��=Ʋ�+>`��ע���\r!�b���r��2p�(=����!�es�X4G�Hhc �M�S.��|YjH��zB�SV��0�j�\nf\r�����D�o��%��\\1���MI`(�:�!�-�3=0������S���gW�e5��z�(h��d�r�ӫ�Ki�@Y.�����\$@�s�ѱEI&��Df�SR}��rڽ?�x\"�@ng����PI\\U��<�5X\"E0��t8��Y�=�`=��>�Q�4B�k���+p`�(8/N�qSK�r����i�O*[J��RJY�&u���7������#�>���Xû�?AP���CD�D���\$�����Y��<���X[�d�d��:��a\$�����Π��W�/ɂ�!+eYIw=9���i�;q\r\n���1��x�0]Q�<�zI9~W��9RD�KI6��L���C�z�\"0NW�WzH4��x�g�ת�x&�F�aӃ��\\�x��=�^ԓ���KH��x��ٓ0�EÝ҂ɚ�X�k,��R���~	��̛�Ny��Sz���6\0D	���؏�hs|.��=I�x}/�uN���'R���n'�|so8r��t����a�\0�5�P�֠dẘ��̕q����5(X�Hp|K�2`�]FU�~!��=� �|�,up�\\���C�o�T�e╙C�}*��f�#�shp��5����mZ�x��fn~v)DH4�e��v��V��by�T��̥,���<�y,̫֞�2���z^����K��2�xo	� ���2� I��a�h�~��c�ej�6��)�]����5�͍dG׊E�t�'N�=V��ɜ@����b^����p:k��1�StTԙ�F�F��`��`��{{���4��7�pcP�ط��V��9�ىLt�	M�����{�C�l��n47s�PL��!�9{l a�������!pG%��)�<��2*�<�9rV����)�|�A����Ip=�\n7d�>j�^6�\09�#�՗7�T�[���i:���X�D�'&8�/�����;�#�f�%��Kj3��;��Z�^�]��NQw�tȬ\$����ҹ���ǎ�-��;�L�X�+��P�̄�:�N���� \0ǲ�P��y�jt>��.[�<w�\"|��so-�;';�ǟ��t\r��t�	�I������T��\nL\n�)���(A�a�\" ���	�&�P��@O\n師0�(M&��b\0��@�@�\n`�=�������*̔��8�/��kH�F���\"�F�����B&�,�<����4b��eN�)�FEO��NSN��O��\r�.x��\"��k�D\r�� �0�p[�2RI0Z�������'���f�ix�P0d��|�h�O���mkH�Β��7���\nn�����eP\"�0x�P����02�n6�Wχ�N[�!����6ﰣ\r.u\rp��P��.(�mGt\rox���1!\n�\r�:��z+�lV�'���|?P�P��:�0�� �bT��au�x`��co}��O�1W���q8�l��\\��u��@���\$NePKq��g�A(�mc�L'`Bh\r-�!�b`���k �������`N�0�	���nN�`��D\0�@~����`K���]���|���ʾ�A#��i�Y�xf�\r�4 ,v�\0ދQ�ɠN��Ro���m��� 1�&Ǫ�p�r ��np�6%�%ly\rb�ʕ(�S)')@�ޯD�M�I�s {&�KH�@d�l�wf0��x��6��~3OP�h0\"��D�+�A�\$I�`b�\$��\$�R�L�� Q\"R�%���R�FV�Ny+F\n��	 �%fz���*�T���Mɾ�R�%@ڝ6\"�bN�5.r�\0�W���d��4�'l|9.#`���憀�أj6�Τ�v����vڥ�\rh\r�s7i�\"@�\\DŰi8#q8��	�\0ֶbL. �\rdTb@E �c2`P( B'�����0��/��|�3����R.So*���cA)4K�}�:S����\0O8�B@�@�CC@�A'B\0N=�;S�7S�;��D	��MW7s�ED�\rŨ�p��<�DȺ�9 �}4����_o.��rԉI\r�HQz�EsB��\0e�J�� ��KwHt�J4,^25h2�i%;�=���LL6}��7#w<�lrT�;tPl76�P�rJ�\n@���5\0P!`\\\r@�\"C�-\0RSH~F倵ņO�@ǭ����g���)F�*h�\0�p�COu6�ҎYO�Rg w9B�Ӛ��L\"䘵�_63gU5\r7,6\"��1����y��V�%VğWX��]O��J�	#XQGIXɰ��Sq�+�(��q�R�GH.l6[R�0\0�%H��C}Sr7��7�cYK���)�.�C��r�;�Ц)�M+�3�� ��4��|�Ϊ1�ZJ`׉5W��L��-Smx��H��dR*����JЦ\r���|52����-C-1R�R��T`N�e@'Ʀ*�*`�>���\0|��C!nE,�ag�.��b�f�Ý8ӓ_���a`G���p�`�m�6��Rz�\0���[-#mO�1H\rd�M�MNMqnM��nq����R6�m�On-t�v��æ\r�]`���-�`j���X�Mo�]`OU�AF����37�p�>'J'm�('M=j9jV�ZbBn�<�@�<���fe�:\0�K(��N���uN����-!��1vҍH(�Qg��µ���xC�<@� �c�[�c\\2o,5�˃q0m}�i~+��e�Ѷ��*�}��Ƞ�}��M��~���|�̘\r�� �@�\"hB�\$B�2�c\$g�\$�5b?�6!w��+~�l1����`��	s������	��.�v7m�Ec`Q�ecb6���`�\"&f�x�\"�2�E~Fz��\$�[/�0,w~`u��>w�%���X�\$دv�V�\"-�R����%W���D�@ʀVo�����E@�y���h���1��\"tЙ�O���/�������!�[����`:x}�@]��b� �@�����5�U(K�y���S������>8D͸����yw�=�|T,�'L��Y�����\\�L�͌��d���.����@�ђ���9<��`9E��Z�C�ײ\\h�=�qR�`GGW�X{��5�-L��RJ\$JP+�7X����ulӘh̵���Y�P�g����z����u�iwyL�y�����cY�7yF<�v\r��57�d�O�g�k�Yq�8�p�	���\n��*'�9\"�`���w[�G��HD�y�_]�c��iR�˖�o��w����9	�{��]�Oݍ8��C�67�:I�v�S����:_�U7��1�z��ڵyy���͹���M�0ͬ��c0�z��?��z�7}W�'����5��_eƸ��zm�l\nC�X_�(��Ý��{@�}�X`SgBլD��u��ñ�!�k~���Y�O�vK�\0�c�r�r�(�^`�n��;y�7�z+�{���W�:\$�M����\"I����%�om����Š	��,PK����9������ʅ���g\n�޸a~����x�%�~���W�ؽ����R��ٌ�݋X��x����%���z�SzթX?�y#}��L);�!��yߕ�s��������و����:��x�z+UԺ���|��1��u��HO�'��c����ϩ{���c�<g��/���V:퉠�Ɋ�<���\\3����e\0�Z��Tx�Zq\nl����_������I_�Z�����y���,ۙ]����9�ۚ͠,회�����k�:;�}-��������E\0S~2����\\	��UͺTV3�o�E�|�E��� C�m�Ϡ���I�=�\0�Н�z��kGٹ\0��ّ�9��1	�y�������=5ۛ���<Й��]G�Й��S��cՇ�!\r��DR�]�P'�������pLtǚ�H+`Ӿ�=�e9ڇ�Q{�9_b\$5��l�Uzy�n�z`xb�k�M	�3�� Z\r���q]�)ֽ{#�c���WI�\r��8�\r��3�䩽a���SI�'�^a�~e�D稟��>o�2 N���ސP>cΞ���������^G�����Y���͞��~x����^��Rg\\��\$+�ՍP�kY*4��~��,��Mݶ�W-�hhG�_Iԉv-��?iv��e>T\"\$��[Ը+,�)�K����u�q?KW�\rk�L%�}�tԻ�~�0��|Pk��՟T�=�?hE�n=Es�~�����xJH��K�Vuk�?X?��7�B)��ci��D����\rא>D'�,ʟ�>v�@�X�+\rr���@\r��U�X������ׯ�����Ӏ��1P>U,�3�G��>>tѥ}����\"=�D}<T������%�9���i�ʫ�o�1�e]���h�i�&]�|��*���l����1�D\r)XZRY�l��\"�E��/���8�ײ�*�ByK��4��5���Nrz\\�p�ӽ9�Yz�JH�S��>/�4C������&����sC��I�;Z,ۆb3���\rϖ��{�|v�D\nٟNpÁ^����Ay�0az�<��Ԝ��MPS0ڠ�jew=�Ooz�4��>h1��L%R�S�	���}�u82��𦴮9o��n�cM<�uƶ0\r�p�~�A�\nj�Q��3z��(�;�3E�a�]�eU��l40�,u����f��f��H0݆\$1����C�A�fi������嬇�>�Xc���ʂT\"��6pHg�D�H�?�\"p�l&�K/��?����`2l�m�*��TB�K\"��ϋ�ɠ���\$P\"o�eV�k�<��o��I�r�:�=�(�x2�����*Ȁ@=hCE��F6u+�,Z�Y��i����r�^lP�x,g��*�Ȗ��QE)1i�hJ��\"�IF���l�Y�|��T�f�V�}U��eo�	5Q?)���\0c��M�F�ʼ�ڑj�l��lP��m�4��\r�*�`⨸���LMiqb���V�)Q�W��R^a.>���g	�o�x��\n\0P`@��`\$�4鏍r��Z�#H��&�ncv)�lF�	 N�coX�F�9 g�����\"�9�'�4t�^�!�<	��#����0���#�h�G|\n�Bx\0����P�T0������6\0TD~#���<�Ԑ���@)H�b1�I�[h�Gj7��_��!0	�CX@���\0�\n1���@Z@\"ǎ8�N��\0��jH�\0�E����#�ۊi:�����i#�hD���s#�!��T�L��\$\$�)��B�׎�v�)����-?h���c�P	 Oto\$p�օB7����#�~��D#�-0dbDH�	�Rl�J?�\"�`\n�A?\0T,(�RM����p\$�\"�:I�SRz�t�\$��'I>�쓜���'QM262�P���R���	��\\�cxF�&�69�^��	�pN)OHj:Q��~�y\0�G�yz�Fc�Ĺ)s*<mQ�\n(2��2��8�zT�#~[� ��G?-��1�.P;�\\�&���5֍\0mT�Qn)p\$���\\�����v��Zr�K�(c\n�Yw����s/�gK�]nb��e�.�K��P�p:K�C�{6arؘl���1�)��/���04�KU���S@x���˜��Q����vT�̄�c.��ʚ�:�� 0�ķ�L1چT0b�h�ZL�]�A�q�VN�L�\nr�0�V#\0��Ta������2�`�`2,\r��-imMhSI�T�xC�,�L��.IOt��	7�_����T�rg�I�Kxq8gI��qM�cDŦA3	� �r`\\�U��L���Ӝ�'A9[�8PQ08���2�1� rBK`�P%�Tq	�C�F����_�h�Qu<�C\n��@�<y��,&C��v0%PO\"x�*��綔�2�&���X)=p.\0�{�(N'�y��.2�\$��>a���&��)��@\0�Ov����#X���\nNz�,�xbi��ꁦ����=i��ZyR����g�3M�V�s�\n\nq���\0�?�Ah�A;`�;m���(KAz\r��!���O\0EBJP�BޡXO\0�@�Q4�V0�kuX�tYK٫h�=���@�(P9�'\0�1��\nc��ϙ�Lh��B�k\nU����	F� �1�6�+��B�R���Gަe���4m:��	\"Z=�J��L�w��O:HR�A���7�ލ�`�͜?� J��B[_�:2��p�:�XϩL�A��B��B�.Ra��|�r�AF�џ&�T��iir&S����4��e0F�L1�1��.Q�k@�+���T��a��}J�����s���:�q��4��%B!V�4їt�-�*�~��d�a_�@��;c*mJRR�Y4ϥۘf�d؁8ɇ9��*�09�w�f/t�k�.J��P�@v�����[ҐR�;��\\�c��M�TJN+^X ��Kp7T2�,䨀��d(��V����FK�1�x.X?��)�*ND���R�U,,=2�+I�k��\r�F��j�7z�R�u*\nHϓRګ�����\rC�eLJqպ��L���HwN!��Ёt�\"\"�ǳU5U�h!4��`��9��Ԭc: E:����T򞴪��Y]aR�\0�����\0�#!�x\\UM�f�#Q3�T�fÈ���MBy���D���X���TՀ(�)+MSꉤ�����=lkL*�V1�\n�\r�7��UzaU���d��Pi��P��4Is0��7���cR��\$gJ�G�Ct	>`tQB�����\0��l��	8J�O���m%�`�^��t�\$� T ]�z`_��Yo&Z���jXF�yx�f\n��vhHtͅıA�����گ0��o^ �X���\$�W\n�����A���\$b�KX��%(����`A%X�a^���M��,���¯-u�?I�?Q�B,�i	H�RDn�c����\\Z`��mY����:8�����\0��;� le�¼(6m�vv�¼U*�Q���wK:�n 1���aS#�g��8{CUy@A�ـ�B��:b޴TG��j(�MtR�i�m��Y6�'��PV*:���+�6�;���u����q��C�iKK��֯��q�@�x�EԠa�k-�?�O���f��-��_Q�z��ׁ�^P�i�;�hvϵ����j8��2����mp�gh�m��K�]�\0��\\�:�׵��#m�_\0���^����kh���6�\$ųG�nK�����ŵik�l2�ˀE�I��!�1�g�3���o�>%T��)0\0��X��q�.P�U���71�5�p�}\\aX�����Wb���jz-e��36�+��7PW-���h:�,����@G��u�žb�����0*�|n�R���r�R��PAڻ]��Rck���\097�խ{u3&]��C���ޱ,ݤ;F����/WƆ���D-`���F8-e�'\r�ʹ��'�X���*������ވ;J��A�/H�b����we��2<��X��nM�}�@�z�dp�1��F3�7^��F�P�Xjk�����{тU:`��W�IO���W�nX�^t��Ͻ\r���y�z�l�JEM]�ӱ{�3��	�C�c슥J#��v�d�r�\0�����_�.�u˻]x7fW-�ѿ�p���v�1�908%�t%S�p~�\$a`3�xEh���@ہ�`�KK��bn���!x\"��� �{�\\	�yW?2�ʄ)r�\r������H\0���>[\0�\0L\rp0�iR�H��|@J٭q�2��&�����\r#&�BH��ڢ�f�0�JKr�ky��W��e3�)xe����J�;��z�Q���\$\0�������V4�Aa��3�)�Dnw,0�����ch�F�.8�&L~��L��a�+D��#\0>@�\r��7p?Z���< ���8��l���Hd5�0�\0�_���Ą2b�`z�7�l7X��q4l �@�=9�>v\$���\"H�gؚ�	�(�4�Tq���(��6���a���:@�!�ٶ�t�]�:/����₍�n�	ܱ���ob��\n���\$��Oj�����Hҷ��o���ﱀ��d\0�5{c�&G	\$Z��8��D���z��!����5\0|SS��X�xk�\\�c�݁��Xy��Y���C��Ҵ-�-���yLu�̯NܪL�=õ��X\"�Z1|\"	�����l��cؒ4�\$��.*>@+@C����X�\0\0(*T@�p��@�s/ y��\$�2G�f ���V<��\"^� �|�� \0dV :Yp�Y��[�!M\\���cx�&^B3���p#	����8�C��8w��5�\0�6�.�r�8��\0�ۀy#�n�������6�qβ���H1pu�g@%@p�q\0d8Q\\��:�]��\0\\^c0�&,>6K%�L�{؝���j��g<�Y��z�hFB:҉�w-ϖkʹ����ᬣ��r�m�	+y�����o7��qM1�b����z�R�i���;�0(�e[C���Վ�<Rl�6hR\0N��ǥQ��8�S�*��k+���\\��Bv ^��Q�hD���&74�L��Y8뿕�2��X�Kt�!�H���W����ނ��i`������k|��҈�8�NC�czFX�L�V���^X�Z.'�K�˨ &KCC)q1�]��5dP9�@ubp����ZLƾ��\r v�|���Y�	[[��v� �鈹o��h�t��mB�#R�-�H@�R0� w��@�暋y����\0�q���w��\0�q��d��N־��05�b�sZ���L���*�3��ć����cj��tB�C�H&�T۟לtV>�0͵�{菎2�\"����yu������7�µ�9\r~_��>P������;bZ�\\q�IX����ʨ��V�<d:��[�(ti�r���[: fu`���^���V*��v_�k�Z���9���6C���_`�C6J������F�.ζ&=���衬���\$���L�\"w�=�nZ^[oV6ۺ-���[����v�T�(2 J��Ҩ}o�Er�(��L�K#@�(�ɏ�&4���m�\" pۻ����n\\=��.P�A�5��F�b�Ѧݗi��Iґs�\0005���]�.���+\"���ˠ���s������@|��D=ئ�.'ψ�M���ˠ�=K�U���c>a�.`n�Vv�ď�������P\r��g\"���=<Hi�+��w\r��X�ll��:TG]<7�K��[o&)�~��gd�RxI���\0�@k��-��Dp(ҿ�/s`�+�t\r��s�\$��wn�D�E��NK�A�n���<�pƒX���I��#�;�z�:��vm�j\n	6��Z�چ�ೡ�{�؂Y C���CP�D�C	�vs�\n1pjZ�Z��7��zj����h�,l�5���qտm�9��[|o:0���n?���~����A��E��Ia�m����6/,�Q�'�\n��3*�Ӟ��!o�ГC7<y��=�W�\"�H�RK�!�/h,�t���)�ٷ��5.m��ʱ��8o����B��%6a�H��6�'o�JjV6~��J�3�=2�%�V(��p�\0�ͼ��z����Z�M[ʬ`Q��;�X	(!��\0N�t����s����i�6z���U;m��\r�0��\0�b3�̰jc������zz�U倫b6-Pk�#{���M��Jx���\$��Rn�O��t	��7��7g�8.��GNIJq`70(�\\�gH��ӳ���e3F�Z���RP	�GS�Z��ru�3�:�+���L(�����.��\$�«EJ+s���j'>�< �t����@C�^������2`]�)��*\$�f��MUA��A�7�aB�B�N�P3�����];Y��s���'Hf�<�t�|�/�WK�h0*�ᨓ��\0e<S����]j�g[��>n�vs�n�3Ih��.�t��\"<_jzb��s��3�M{c�p��O����ng�.~����}m�p9���2w�Ӡ!S()����x\\�`Z��Q�N�ë�J�o�h{�=Ju�Pi�P��@�A��?��p{��\0003�@������?A*�ü�)�B;�>N�w糬p��z�+מ�Ϲ}��@p�����L.}��M�9x�{�`��C�)���]��X�M�μ���]'�g_<A�.��'�<�\0X\0�㳃v�]�<[p��=.ѱ���*};Q�Y�wCɳ��w���v,�W�]���<@@����p����\ny\"z~)�7����t�͝���u�@�Sl@}�ǃ�o߾��A<xkl�#��Z�\r�z�V,�y`�Sӡ�����&���6��p��Ex�wﱍm\rO�\0���\r����g|�\0^�{�!�*��h}@^1&ȸ��o����t��_�I���t=<?�)��������OU{Z7�J`�@��~*�&��\0^yQ-�9/ɉ�}du��������\$\0002��\"9ǿ���O࡮�?��!�U���K������?y�/�\n5���[\"��\n��>+��4\"�y�A����^�y�-OV�]�?׫�c�	�}���hw���z���O����O���_X����c�	%?���������P){�>��`F}��|�޿	��BŰ�~;ն��>��ψ|��;���~����}�ҿ}�����������ɇ޽\\'����?����}�����\$�F�o�~=���|��߇����Y1���,���?U�?�{�ձ�W�����}���\$�gpk�\\ ��bN7�+�ہ�\r�g΄�\0^\0�B@-	T���6������\nﺅ5��o(m���~�Nq���8���_8�Oʳs��|��:�s�/��d!\0��4\0D�� p�[٫Lꋩa^/�+�\r\0���\0z������bl���n����?�\n@6�]���p`�-�83�\0�p@>@f	\$�'���PH?p\n\0\$���\n@*\0b�4p\"�j!Q�ᅜ�:�d����`+0� ��a�\0�M������GS�����\n����H�0�%��\$Z(\"AJ\n�0��\0��.	�#oL+�����m\0��\00;A�k.�\0A�'A��A#���@�׀����F�b��H�W�x\r@���\0�V��a���M�J��p�?4���?�`n)�5~���j���R\\�~�*K\0����pA�L�O�Yz\rl��a2m.��ޣ&A���,̐;�-���K #\r2HK!�YHi\0#\$pj�]��(�P,����]oA.��\$�T����3!p/��\$�0����dW�����be���΁��\n����6�����\$,�,�J�#@;�@0��&L�G\r_��l��E��^ \0��1\0�	DI\n\0��0!or�[	�hɐy)����k{��k���\n��\0�p��B�\rj �\n�ʎV>�\rJK���6\"W��	�F�{�t���\0�;����<����%hp���̓ �Bi\0�\0�6�h�譯��������@)�JЕ�J���CL�	�>\0��,�_�~vX8 ���0�w#^2I\"kLB�1Co�A�H�\r�ϰ��jF��>�q\"iOP\r�8�~H�ϐ���(.K�(\n�lj���M���f`1�\n�8��>��	,,å\n�ܡ�xZ�:C	X�'�t�P����p���ȸ��@ళ4=\ríB\r�T��C�S�p�3<>��\0��1������\$���\rPe�8��3�\n?��0��X;̿�z˨;��\r���҂W�׌T�y��p0��D.��B���A�D�[�9أ�m�!h��%\0�8:�S�m���pB62d?��˅�)���h�̿ҁ�0�D��C�&Ě����\r�h`��	\"\rx\0�@ ����h��\$ndI!�қ2ΑD�\\CQ9���\rB��KY�檀#�R����������	�,����K8L�1�2P�3�&\\P�9�,: �CB�AN�]4A؄/�@��	<T�U�0�Ϧ�v\"T;p눐-�P\r�\0�HM�TEBW�q_EU�Xac�q��cI@!\\Q�\0�HQ�BL\"��7��1���Ŵ��U��Cc�nC	\"�1eE���S1\n�����E�ZMi�\0�\nj E��=	������_�~C	4QW[\r�6��ú%x9H5~A%*@D��Cv���IC����7`\"c-	���H��+�F&�x ��-�8\$�E�de'=��@i�tf�2�LC�ˊ�J�z�d׀�<A0тT 叄\0�6��BT=��D0���l����P�\r���U�T��\n`�M�!����i�l><�l,��F��P�Ѭ0�8i��F����F�\r�+Ѭ������Z? 7F�`B��/��x�'\n�*ˌ�>l2Ч?���q���tk�GG\0��:B�Lt�q����mģ���4p���������kKh7�P!o \n�na���,�bD����2�\0Ah�ڢ��r�!�0\"	�Y!R��(��G�+1���J:a�Be��:�vX�4hR:8��y����>����< Q�/��\$47�2���˙������+ K�G��P\nP3�t��;����5lL��]tN\0��ZRV\0�A���P�8�6����\\��\0�\rl~��	^p%Q�,b�tt����E�B�ĈQ��<�6��Df�x 3�xE�yĚ)(��H�\r̈�\0��4q���̋��|=�=A�(���\0005��17�_� D��hFQ9H�d��;Ȁ\\%-���-L'�e -�4�fd��+3��J�z,�#8��G\0؊(��Գ����I.d���\$�c��[#9��N~�����%#{���D�s�됨��@�\0�h*a2�f�ؕ�j�����o�|��B�D.�QH .�D�cx5�z���oQCBTv�����lq=�³Tv�@E�C�28z�DI�(�������\"L'T�<z�h'���',�RtI%�ip;�\$�Q)��\0��q�%'�?R`\0���\"��Cr��*��vPV�҂��8\0��?�F&!T;����&@9\0002CS)TJL�AdD<��o'�bq개���q���)�zA��&\r\\<�=�c�CD=��pI`4�0�*�tp�0|CtAh����)+=( �r�'��A؍d��J�ԩ����;�.�\"΀,���B�*��	�R��b@5�W�/+�.�c���TO,v��)B�b�E&(L�H������r��Ed��6J�������+�?PJ곤����8����\nK-���K',��r�E�����+,�1�7-|�����-pn�KO-ĵK-��)�1@��,l9r��!-�����Y+�%��\0�-̴2��#,��E�.�(\0�D�.����C���v9`82�AE/8[\n�/,E@�I�/�T�]�X�����`\r��5V����0	RPn8�1�qf��%@(3N_�2����P@��\0\$�J�]�AI���0�j�GPx����赜Ĳ\"K���2B<�_@���+R��)��ږ�1�K�)DL{�8(\r��22�HB�Ar:@w�h��\0��o�	8�}�\",	�����!U��/���\r�_�\$#@5~�b��^���O�ձ��8�]1�3��𥑿p�/�c\n�����R[C>�^� ׃��1�A�Q�Z�	Dp���B��Ĳ%�j���I�\0��.[�;�3����!�.Ժ�~�%1��SS��3lsfkJ��0� ���)+ ` l�S)9�>P!��\0��`%����5N�y��ֺ���2S/<�.�d႓'\0T\n:P.��>C����0��\0��`(��7h\n���P`\"M�\n@�`\\�����\ni5��()�\0����)5\$�7hs��O�	�\"��Oz<S}@\r!��̻68�n��4�Ъr�L�j'G6P�fM�ⅡKL��LڳnͮT�/=񑑪&F;�麀��&>�A0V\$b@4#��(\n���? 	�`���H3o����.�͗6l��ͤ=ۏE�L�~�6y\0���@\0�p}SpN�B���Fdl�\n&\0^�z�/N�9|�\0�N�9�۪�X�;����:�Ӭ�:�i*y;|��+��O(�}��(8��/\\δEx�o�=8��Ԡ<��\0%oU��x\r����Jïu=-=�o潺��S��V��BS�Odd��Y��7 �P��	>�p���9\0Z^\n S�#�p+|?�����'>R���\$:0�0	�ϻ>��AHI�TD�S�Oȋ\0���>|���T)4�H�ϧ\nt���\0�l���i>�h���l�@�O��b)�O�z7���>�ࢣx���?��	Џ��+\0�>�\r��@\0�@Hi�@PKA\0003��?A<�!@J��t�OA�\0���p�:1�!>[���O�`�ϒ��?h?�\"���4����BTZT#� �H# �AȒS��W��TX�S�Ȉ]�Xc0?T��# �SyJ6fLo3����,���ҷ�5>�Ќ��&��\0 E�B���,K � �)4���F���/��	�Z�2�P܇4GoD��߀��8\"B�0�+p6��3aC2> 6QH�O�TQ@9�&\r`��O��4U�[E@�3��?�L�����P<mT��mQD��4NP/FE�D�`M�o�Q��yU��F�Z�g5F�[Tm�FiU�7?��\0[K�F��n�&�TN\0�8�FH�Tj��Gu�D���z-WGe�9Q�>�ht-�G\r��>�(�C��?�+- ��Q�D�!t+��\"4Q�D�!���O\"!����p\n�J����H�`2R��U@9�P�e�h�J��&PaO�=��B��I8����a�J&UQ�H�q���\$��v�i�D�)��ЈJ<5)<\r�'b��B(�t�Ҷ#\$���#���!?m,ʜR�-�;��)9Kp+T56�m#T)��K�Y4�ŀ�.BAR�8o �0�����KE.��д�=KRJ�T��@��TR�H�*�N��H�2��RL�4KS6xt�Q;J0CP�K�M)���+Hm��SN\n-54ΤYH-6�6SsM��T�aL�8�\0�]8t�ST��ް�+NM7�5J|,��ӡM�:P��P��QeM�7��>�E�\$`3�O;4j?�}\r@7Ct�+�+,\0�1\0�;@B��S�7-64��N8b0�yOP%T���m>+��O�>��Q�O�4!d���B��!R�O���R�&���UG�]>��TP@#�Q��+�T��\$�}@�x��>���S�P];�\0�E�QT��%P������<��=�\r�:���WD�]�Y\0�Xo�R��ڴN�-E�?tS��F����1�0�R�)�r�ۿ�7�O�=�=�'�5�Xl����>+��Ĳ<`L�(�74GRd�:����EPK���,aU� �QO���Ш���d�}O����D�CdQ�M=���»,?�HBUD��>RUAU*:eR�>������IT\rR��UQ\"*6��}Uu'�XO�S�ZS���OS�T�F�Vt�3�O�Z� ��UG�QU�C�X�\n�eP\0 ,�՗G\rOuaԥV-Sc�1T�Ul�HR�ZU�h���,�V`9F��M�5tмj�T��MF�[�UU�oE]uE�CTu\\�|�W�]T,�QW!UmաV�ZUv\0�Wp�uG�=&\nB���X��4����^��V)>�b�U�X��HCuI;m�yV\\5aU�Y-c��U{Y]Ֆ��W���xaz�7@�f5^5U�	�ձYu K���C\0O�#ց(zBաVUU�hՃ֑M��s�%Y]��	����/䄞@���OMS8��T�>\0;u7Of�L��MVMZ�l��P{A�[ T��(�<V�Z}d�S��Yeo �յS\n�CTOq�L��P02�	���/6D�b��>�������O����\0,N�	f\n`\\�\"��*G��>�\nU�\0V�bVs��]]��#Wj()��\0�8	�A�x��*�E�C]�u��Wb�\\�쀎�	)\0�] ,`�\0�ut倊\$c�\0�?p�`&��^26���e^�tU�WL\rv@)��8\0������!r��_@��Q���	�%��:�\\�+\$�?�u�����@\"�e^�+�&>��I\0�9i\0�]�D@!��]Ex���(��X^es�W�]��ҧ�]=x��U]`\n��XB?�`�_Ņ\0�̻a`\\�#W�����%>�|�W�aM�`��ŒDu�%^@؉a%v`'Ww]�	���w]튕뀒�0����es�EWn�ީ5X7aj9i\0�b�U�W��PiF\0�`�u�X�b���؍7�����w�c����%(��M�^vU�W�^�}C�W�^ŏ����{돏�_���X�a�����aՐ@�X�_u~\0�~u���_���_��C��`����?=�V+W�bŋU�X�cjQ6_���VO��������u�\"��J7 \$�`X	��-�6n�)b�V\$GcX\\�i�dh \$חe vX^��	Y�`���\r\0�d�!�%]�ߨ��c<�s���d8S�X�8x*�\$�P`�S�ʍ�uv}�\n?��U�X}c%��jF���}�I#�^���\0�^ЕV��G_�3dF�i��`>W�ȕV�Z3b��v}�[b`v��f��6�Zwg��3�	8%�6ڈ?\n���QhŤ��X�i��6Pڥj��Z)\0֦Zah\n�F\0�j�kU��\0 x3\0׌���������Z'�O�څʏX	����k�~ )WV>�VX�j�޶���7�	h�ڳ^8��x��-�V���7t�u��?ը���%7r; %����	\0�h��c�X�i�V�k�����%�`�X-k�����?E��W�l��!�s`�x�4Yn\r�Arڣ_�FڏƑ���ɀ�?s���Y�me��N�m��`�Y~�u5��[n��@'Ze���Z�bp���k���[uk����}l���ۈ�����[�d �C[�o\0*�叻o���+�`ő���]ժ \$\0��\\�s���^�6���`��\r-V���bX	�*�W`	@'�X	6ˤD�Uϐa���邭l�v�aa�\0W\\b]���*\$�[5q�\0��[���H�۰@�C�[�m���u[�l=�&��nU���lP�	@�7rM70�mk��Ο8-x���i5��=N�e��I��8\n�%��7�\nU�Z�^j9�������Y�c��c��_t5��[�a�ЗE�V?��y[�\r�C�ʏM��AYt-�i6Y����H�4\n����ܡm��W*]Am��WWۅ`\r�wL�/e���U۷um��S]oap	�ܥt����]u��W\n�}s��K���7d�?=u��ݗoUķݮ[@\$���{�m۸%�v�ڟv��W4ܮ?���t��]]�gݣu�Wxڡv��C]��Q�'#�=����`0��E��sE��)%%p%��u@Ww�F�E�Gv���X�J=���nXkIN����0Q�aa��+��qm��A`a\0���dl�W���h�	XdM��i�g���<׋hH\ns��\r������	��ҏ	�\$Ig��6:#�:���2^�mx�'��z��I�\nEz�X3z���^wh���\\�t%�7I�ގ�ְ#mf���ϣڕ��C��i��@(^4�9ar���7��Vj>��_{U��Y[h�� *��r=����K^�	r��f�u7Nh0	ִ��f�9v��j(�AG7e�V���n�\n�+��]d������W�\\^\r�� _C`,kT�kM��¥hp\n�]�y|���\0�|ő��#�g����_XZVu�^:M���{~͊催?{�N�(\n������\$ױ���.YaGv_]�}z9v;XdL��@*\0�x����Xa`�76�`��\0�u�_6}\$�?n���j~�_�y&��7,�3��H0���`�u�!ڃje�W�l-����?q\0�\$�큕u����sU�h��ڏ�@�<F�`���[Up���J^�qͮI���N^Ih���ױ^��h��\"��x)\$ԓ5�I6[:������8&\0007A�?%�V�`��\0 ,��?�2X4`�zH��1���e{�6��zJL�瀃�u��<Ȏ͢��@\r�s�ۛ��{xA�f��v>_�L���I�̈́V���pU���_%��#��W:eu�!���l�v\rN�^��󩀌>��##m�w@'����������3�ulV@\"�p}Ʒڮ>݌־Z�7}�W`��]�	\0/ݻd�k8\0���2��a~�l`�?v�m׹xxU�X�_շ�WE_կ���=��\n�5^�#m�pަ�@*aqz�����i���Zo��\0\"]��G���gzN!U�%���`\\8g\$D�D���̘k�`��������\0�j\r��K!p�� \"\0���8>#�g�m`�~&Va��>��\$�~U���f��6	�`�L��ز5�Tݩe����|w�ٰ�`�6������v�3��+��a�Œ;6�^�v��Vh1b?5�7r[ڎm���Zd�Q��b�rbAVoY�:~C�`O:}��\\����Y݌%��hި��2PV&8����d2V��ق��l`��\0�a�s�ި�ZL?}�6��+cF,�>��4���j*�T�Yw=�U������Y�0�/�ÄN5������ׯ��wBW��\nc�Xɍ��x�b?x*Q�-�u8V�\\Spu�XH��>�{���?��;��/w�7�^7^��/X�x}�w���8�{���'x��gZ�q-������4����� ��_i�5����{g��w�\0���8k����V��k͟m`���!>��ӏ�\"@<�h��F���!�h�k��p@ ��i�z�G��	�/A��6����3����s�!�U���B7I��0L�x|9t��.(��?G5�x2��6]�2�.C=V�����u�<���^JO��K�1A�p:�dӑh\r�3��>Lk���8B���^�G��{)�:�Lx쿠3�nj���P��fQ9@��&Q9B\0�0d�>��#�|E��F�`)_�����T� PI��hp�����P`)���N?���qh�D���Idݓ�Yb�a����d�ySނhkXФ�t��VY\0W���c`�h``!e�j�ge�t6YX(ZB.Z։e�,���e���Y��e�h�!��e�F\\p\"�˖MvUݥcŦ�[M�9��7��\n���_\"R�\09�賤u\0����`� v�`aUf�kA6��4-@��^�ðݗn�+���t)6c�JA�1� ��Y%�9\0;B�q6�����(��:Q�R��pD�0��@l�Q)4����C���FkLT2K��\nBH�CH\$�ƹ��X!tI��f�vb���s2��ۥ��\0E�vL`a\0�ҪF�XN@њt��/��OP�(���1�����u��\$AS��x�R�� ���W�̣���8l�\"�x�-�|\nP(�1�JaU@�D	�,�zO`p�[��A0.@�����O�0�p	�Ч�z>\r\$����\r042�r:�7J��r�)��B��p���H���d�Z�=�)��vXL��)u�~x��ш��#�8�������/�}��@�(�1��*���g�-?\0005��6J��P�j��Y�,U\0�\r\\��'�/�%?p���10a/��\\1�x���+,� &�-ܥ:��f�|�+h\"D�p3�`6F���)6�������\nN.=�d�2݄�3I��O��!�g�f�3�g��|r���!�J�)䣡ˆ�hv�ֆ������!U�z:#��~��g��Jz�5�K����ן��2��.}�_�拹��6T��zV~\0:2�(�#�T�����lzf\"hu9���hpaT�J��:�'�A�8H���^�P��!!h�:��h�Ě`�l�淤�'���n026��L����09��69���{ޠ4��Q��)1/���	�Mt���CeBY�R��\\��ЃXM�|\0004���+Kh�(�U�K�eQ�e0�OU�~�HA[�t��M���Q �}@����\"�2��_Y����I���n�41�]T��!�Xf�	�N��d�=��h���i6,���O�50�ʰ�0���>��f,��a���3�������>�W��O\"�Zy�E,`�������B�m+z�=<�[Q�d�H��>�7�eo�f#%EY0;��R\\ذ��)%��&��=�� !�=�dX,&�!Uj�'�l��@�f��S�x�@�9G4�\\C�cF�R^�A�,�ީ\"W꘥��1&��	�1���K*B�hˏg�P\r, �����E&ZFh>�xk@��x�d�\n=>��S���\n�aj�����5j��*p;��:�>aTh�H�?����c��E3>���>`3S� ���C�\r�HKJǛ�>��h�H�N�j%�%�`ɳf���9#�۔��>TK��~Y3��h�bQa��M`��Q�L����E�Ik�4�:�뽮ƺ 1\0ɮԿ���ͯ%z�k�^~Z�kn� 5��%z�k�#&���뵯\rF��k�n�Z���6�;\0��N����ZF�<��&SpI�tYDC#��Ǜ\r9�f��Ee���Ie(��ڟ�\n��M̟\$�\rS��2�r��ι\\�(�HN�\"E������\"yT鞴{\rQ�K�o��GQ�z��������v�oZl[!�c��_iw5}pn�![�W��}efkl��毛)r�Ƅ4,n�59�M~�!��:�ʻ9V��zz���޳[=l���ަ#8�T1�%�Jh���R�6�[+�'!�m\"!^G�?��9:�)	c�NmJa��6�Y��S��[K�T�\rk�#�0\"�(l�)�ӛXl�ha�W6ZU�ap�m�c�lEiF��1�ߨtr�=퓦��f\0����d�%��0�H\$փ�_ퟵ�=�����{4>C����Am��V�t왷Yd������;o����y�+m��uiR\"��`8f��=iD�F��+�~ ���;r��FɌnm����f}e]�f�'�8�N�V�U([WF�U��o�� 1��t���n!��[1�_���am��ۚ���C������9\"pf#Xx\"��fP�\$iL(5��\0ZeR�e���ݡ�n����0�eUg��n��۫�����Y��=C�����A�gX.�mU���ꥌ��)^+8��BUWK�lBM��Za�㲾�4AC�Y�⃇���^��9F�'�d��F}�0�dlG�+�~D�;�n�X��X7\"Ɲ�i���	��Ѝ�&��Ũih\r�k[�n�P�f&�6�����tۼ%�~��r�\$.�h�P�[��n��k\0ݶ�����U���y��a��< �o����칼Y-��n�.��P�\$֠����_��n���t/���z�H~��Jf��;��������铧�۱齽�L'o뿣��8n�)4���ǹ��4�f�#(��V��%��A��&0�W��\n����@�4A1�i�q��������e6�)]ݞ+�b�;������KV��	�Bg4�c[��x�{KV5��Xﯿ����e1J����緝�W�Q�� Kﻱ�\r��t���eT�j�p'c������E;Z�\r�#I�	���&n���x��c�@����\\%�\r�c\\n���|B��-�.��p\r�����1�'�U\n��UQw�r|@�>�s�D��� n�E��.ɨ\rЇ�W�ŗ\0005����[�ŏ4��KSp/�z����ƴ����|���M����a�(�,��gI�!w�3@�@��{�PM�������\r��L��<�\\k��e@9�+�9NAlp��Ty�y��uDAj?�W\\n�A�)iq��SC���w�մGC5�ǳ(T�\$���l�\\���zp-��������86�Rj�\ri���.[���!����SEG�ɈFB�PZ�#o�Ne������i\0��j�v���������\$�\n�mή��fF��*[r���F�[�r�\\��JÞ��.�;�G\0����a�<[��3���jS�é����� <��\r�y�5��g���\\���,jٕ��j�>���S]�]gMQ�Kv��YG�F���_��>��/�%����,��P���7�Q�*���6o�HJ��x8�(���vz��d�+.�ba�N� K���c����*�e��\"<��;�7|�sU&7\r���n#�G�a/��o��9���w�/��ŵ��]d�e!:y���&q0�ZS'�����I:\\��?8���\\O9���}��v\\��������Ͼ���S�={��R���^n�;C@�b%=���]rţ�@A.��Д&�= ���vQt+C��aГ�n����wBZ�t%��Ez��\$���t*��M�Ut+Km/�P��n��oKБCy�t+� ��U˥�H`�������wѯBZ��՟�>���L�q�K45�3ҿK��hX!W�0R;G*3�͚�<��Q��Kہ�;�N����+��.s�1�Q���Wi\"`>rI̟OQlrI���\0�mJ��AL,���!�1��oQ�ɞ!Q�>RAF��Q[�Q�}���� ��Jyd� �	����7��ʜ\r�9�y�E�ᄺ8�O�6ťծd\np�zS�J�����0h1�E���1�O�֕�Z�{���6�R�V��n�5�J	�W��֕��ZzP����<o�ZQz{j�չ.�;��\$Y��8���p�#o��Ui\\��)������ך�d]|���:��[#���Q��X���d�祢ѻ�}�[9j_������ذD\\�h���W=s�8	c4��V����{)� ]q�R�c��u	�?_�n;��z��`	f�P���X�����O4�f1� ���n5��L\\n���#}1v'�_j0��'H�N#�YB���CU@gkY�Ըb?a�-��<q<�3):[K	#�f{�##^(����R�'� 51���^�٪C�\\���핾�p}Du3Ф��&�1W�h�S�g=�Dw�_V��¯�ORm���bC���;�nU����w\r��������n�Ҙ@��p7D�v���7;��\n�������\"���1ׇU���-�\$�ܜ����Ff�\n��@8�X�SY�p{x�ٗ)�B��4��@���!�H��Z.�:q�IZ�T��ǹ��Q>t#u�۹��[��ڦ�;�w��Jq!u������w��Ё d��16Ex�YG�?E'�{Z!w�f���ƊJY�1����TU��WRG�x9���Շ�J��;{�ٙ�7Yn�����;��{ҟv��0<܌Uߑ2�G	1���܌���Ap-w�ӱ�F�)���%�s��ih�'p�\n�D��x���\$����Z�h�r���\0ؑV(��p`���%[Z 4���YӺNI;���I�R��e(a��t�9�}�x�aI���w\\ժ�<M̯�.N׼����C��\"Ə���|�@O>��^K!�����C��:��{5��׏~L��9\\���(��^I'�����@��q�\"�e���SyMl����OV�8BOVƸ0Wc  �>0��l'����nT��0�>dy�SW��V\0_�x;�h�]�����R��u�Y�mK�\nLyQ��xPB0/��?��#\0T�FU(!?�/���@���%D0ғ�E]҃��b���aU����p�Hb^��.d>�w��^�z\0�'�^���%/T�fn]x��Qe�x#ޗTePS枒�A���A�:��G�O>��� ����@3zeN����%>��п��9_>b���g�d����f�����X�_B�#�_L#��m\0��u(���� \0��djiS �h�sPJE�ãx����TY(o1-WG6��s0B0�SQ�`�>�\0O[��y����)[��\\B���y�\\i�j��>�r~P\rZ\\o��X�zEz6%��?6�Wf�>��Π�e�7�^�JQ��W�(�^؏��\r����Wz�u��ޛlg�^��Yz]���^�s��i6cgjG׶_{z�η���}�	W�բ8�^�8U�WOc�x=����o`��\\Al-y�\0��y'�T`�>���ܕq���{�[���{�k]��Kއ��C�{�l����zϏ��v�]���������^|1_���`al����\r��[=�}�^���:��`�\r�m���ۧz6	 %����%ڣ�E�^�n!�������`�_&�]�'��t��6�Xi�\n��`��^\n����m�6�|����V��o?�׽��O�7+�+m����`��_�c�`�m���8]��/�8�����\n�R�U�]�WXYC^lW?[�lO�v��d-7X\0��e���{pNx���n~��anu���|���`��W����z��,Ι�/˗[������|�o����|�����6�Go���\\|�o��x�}y�/�?\\mO�v���'ض<�Qoͽ�����ٿ\\�pEֶj�_��iD�fX��'hN���m��V\\���'�5_y��X��	l�������g��%{�:�볩���;S�}�}�ˏ��y��u��}��7�Z�e���>c��ͪ^��md]��N	dG�U�Σ��SM�fW�?��;���^r��w�����78y)\r1T21@�a���\$�{��\\�-\$�b�����wzd��}��A�_}��w߀Q�W��gL��N��4��N�?pay}�k�zy7\$!�_�����_�~��3����&�'����ɬEY�Æ%~tIgI����a��l^9�pg����5�n�(:��g�Yߠ���ǜ�g~r�@�t\n�-��\r��*C�vc��E���9f�z�݀\n�P�����n�����,Q��\0Ә�m�Mpˣ�y'h��s��y�f��m�Fi3�K���dy��送���!6QUݕ�)�@<\\��Ni�Ag*�K��	�LS菘���&g	��7`�g�Fy�F��1�;G��iX�D����3@�\rߚ4<f\"��);�8\0�JY��g��9��F�`()C\$�q8ϱ�\nz�� J@�F\r\0��\0��o���\n4B��A�\$\0��AV�34X���P��)hIZ�:�rA	S�q�,�R��HDN'�'Q��Qh``��1��œhJ�3d�0���\\@*q8NڙE�e&�H!PXFeMT\$�sdj��v���Q�mgԫ|[�������q�����'=l���\$l��}�F��\0���?1k[Cn�RX@h<����`� ��;s]1�0f&Q�\"[������<�b-Aڅ�\nh������,��1�y�k|��J����@����+l\\mK�D��R0@:&�����\\\nxmY���Q|&���y�� e+~A����4\0<�\0�ԍǌ\rv��IZ�\r�jR��1�JMK�m5�V��q��(�_��t����LVy�; x�Gڎ��!�ɭ���@�p�i�z��v�~:bp�ӵ���� ���@�SZ���Nro`�<���N�>���,uh��C�7 �bE���wx��\"��/pIET�!a��X�h%E���0�M�k��%pL`��8��	k��%�I�J�9�8��ӟ�(���/\n\na�!/1�P7I�%�*�R�AEu�>\n\\w��`��V����i��K��]e�؛T��Y������h��60[��3�1 \$��{Ӵư_]1'�8@� %ƙ*0\\�Att������'����fP8䓊hv�F#�H�|Ʉ�3M\\��wzY��'�#(�Q���m�E�@_Bl��A&�����w�[��\0kFM����7�R���}m��!�`��L�й�ȁ�)�+�ǰPsO��\0�ک��9�#0u��\"��a�K÷#�.g\\�؃b��P�}H7g���MHxF�)<M	�*9T>��Q�;��;�t�(2��#\"`�;�i��\"\"��=���ۦ�\$�K��H����\0�[��xp��Y��1*O�A��	3�p��Nم�Dp�!B'X���y�D�m��Ķ5z�\r�1�W��?�R6ڄtz�X�m��ZYB?z���#��l��\0�B;�ꄴb<���!'�NX��@�\"�HА@���O�>è�I��!*,���/k4D'H.�-�O������&�Ad���c��M��'iH\$�6�f��,��8NП�3�Nf.�hlR���0�ٕ�u~���>(�[b��Q6��E<(������Q���g�~�U	;�wd��r��H�P=\r~o�R�8N-�PIB���=܂	 U,��TE2��l(�'���p�9mx'̐�hh�Ԩ�xh��i���pZΚ��#�DT�}!���-ж��@��|�KA�.���4ԅ6M�n�oX���\"�ܨ��G=У��)\0����(��� �\"7�f��PڣFb#v�!B�oN���>�KLL\r�/�2L* ���A��.~�=(T�-��.\n|m^0A���\"OA��+E��a(��(]hYܛII\"ܥ�thN/N�Q�^}ZQg@`�Y[ܢ�V4-�����8��F��r=Ujm!��h�	��æ\$����COr�!�����I!��9mV�U�۱B��EeuB�& f�O�m���qm��3�6�I��H��n�F�0D7(mp�F�@�E\n��R�G���<U����2`uBN���]x�	-�2��H�@5�rW83�xr��҇�3�6��@���K[+�u�qb��xO��]�&�Ԍ�Y˾��*0�?	�mf	��k��M��C�,q]\r����0��H�X�)\n	��!s�Y�A�u����Xv���bJ>���J	ְ��n�TG�����p\r�.sH��5����MD�3Ek\n��S�G]\r�!�xHI��nRN\"��Y ao8,��=�t��S:��s�,��m�6�T]D�Fm�.K���O��� ��S�n�~�+��;΂\"���=R5(p_�;����q��BeQ\n�9CtHP�l-�ܮ�l@(A����?\n���o*֝��|�i�����u��E��~���[��	m���b&�n�ZxDA�\0�,��A8!��b+9�x&ѽ�;�����4Hl�� ��h�ʀ�\"BV��l6��G���b��D��|^;��P[�7�V>�,˓7���H ��7(A�\\\"�9I5�\n����(!G�����5G���K��T�\$������\$�I��%�N:t`z%JWXF��\$�#v�4��.���Īh��%�ӧS��ـ i��j�;����C�z�;.&���aCD�\0��G���k�y����u���!�bm4(	e����Q5���l��6'2��n�bs�Tz��1\$�/e��L���'�z�V\\�]:뉗	�Ӣ;v~QZ�;�J|������_l����w����f�fq���=v�\n�؈�̎\"E��L����_��I�h\$�\"�?U#\0����f��)�P�UF#��f	J&\0#�p��CD��\n�)��|-��;�A����9�� P���EJ a��p�S�6EPlB�ܸ�MB�MߛR<m\0�-��\"�E)��Ɋ�p^0�po�I�~L��	J����w���^5�'��jqP�do��3e��D�s�	��k�Îb`>��}�(�H��Р�[�'sr�d�3P�,..Q�kN̽32K؇���ŝ1}��q�k�Ob���rԏ5�#w�\"�l]��I��J��0�!��x�Qn��'����Q&xbr��o,�<�<�t�;�B�  OI\0@\0HNY��=��O\"���_&��2s��g�^I�mOP�v�ʂ�I�b�e<�O�b8�I��'�N����˂y)�����O\"��<�ؘ�/��<�;�z^/D_����ީ���{\r�C�`_q�I*e �}�+��(�\n�Ae��P�	���?����^�0�zq\"jT�F&�UB\0���Ѷ+�K�0������ArQ�}>1p�Pl���N�R;\rGd�&�\$)�V��9[`��ǯN�F\r�@!��U��U��iCRaFs�ך��\n!\$���=��`�Ň��%�v2y=��c2����h��|Ǵ*�^2Fj����R��ߛ�aDG���r6�u�-�}����r�%�����} �ih����++�(�-��uV��_�+^����\"����*�\rX��-`V�j_��4{P�Qzb�w�W��X��d����p�f�Yҳ8������]���_���+:cJF�^r�}]��8�K.��i{ܝM�Y��%�@#.9b��Ɂ����҇�_N���,LX>5^�����g�lX;�`����S�\0�\r0ee>�i���V\r, �W��W@�4p��lXT��Z���:�\n�O��קP�2a�2�����W�-azu�28'�c-�|��1�:��{H܇�b��i�5���2#X#�íp�}��>^�.w~����\nŦ�B#r�|��v��������1IC+Y�h�,S� �b���_��+#�VE�\0��Y���ͣX`����}��L^�.U#���d#��1�dXM��^��et�m��(�ϯAXǹ���?+߇�5d�@�7C���W&.�l�@%�(��a�GT��E�T��9�FA��c��`e�t��I���\\�/b�,��?Y��<�d�Q��zi~ZY��4&nq���Zk������?D0�n��V�S'Vv�Y�3}(u�BkT#E��K2L��A�@� �DP�4�2�+,	�T�[�U em��>�[�����9B5���L#��𕐆��s*��L����P�5o(Q��B�&�6�7�pD\nr^��p�_޿�DT䄎�G�,J�\"\0(#�Έ&,X4v�(���>��,>��.E\$�Nm�	0-�ͪ��(crl�R&�w010#�;�i7dNLf��[�A�I�Z��\"bB�����lv�]�P)�x]`ŀt!l)�I^\nA\rR��U5TW�3P�v�pp��C4��a�I\$4��R���&R�D�`����\0�x�A���(&�� �Fmx��#��.L��T�mo*��Ą?h\"�03�Mm\"2C���؏^1� �\"�0��`��9�;�=�ʑ�q{I<��.y�	�b�Eȋ�t�/3�2ԣ4O	'�xv.�@C�Q�\$Y��y�\\��p��R�\n|\r��z�`\n�^t+xgX��p \0�7JF�F�p\0\r@\0003���F��`\r�o���]�� �7�i�6\0p\0����@\rdmH�\0�������q��\0`\0�GĎ�\0\$�\0o#�G̍�8�w�.�\0�̐` �r��\0q#���Y\0\r��6\0a#zHT�0 ����\0�G��)�Jd����P\0�G����rC��\0��\0�H��%�C\$��摪\0��9&Kdn�9�M#VGx)(rO����u\$�F��i)r6dn�6��\ni`rNd�Hӑ�\0�H���@\r��H���%fJܔ�,2T\$��]��\$�J���\"rOd�\0000\0b\0�K\\�`28�����%\nH�&�T�s�2\0k\$Il�@\\dl���#����(�E�\0005��\$��쒩%[��Ɍ��%�GL�)!H\$�I��%vH䕠��\0001�J\0a\$vK��i5�@\$�I��&�Iܑ�6�4�I�%\$�M,�� �@��\09��%fH�i�Kd�I��\$�F��	�j\$n�3�/%zI��y2�D\$�\09��\$H<��*Y\0I����K��)/`\rd�b\0j�H̝�/`\$�I{�w#�J���@I��%%G���<RU\$���\0i'G�9�3�J\$����&FO,��\r�����&�G܎I RV��I�5\$�I��i0�R\$�In��(�M<��D���ɉ�S\$:G��?����\0004�_(\$���2p��4��\$�L\\�i2P��Ib��\0�F�)+RM��J:�9#�P��:��\0004�Q&D��I\"�A�I-��%ZQ��	9�r��H��(^G���Jғ%���='�Gt�	K�P\$��\$�}\$�P<�9;Ғ\${HݓčJ���A��J#��%*L���r\$��8��\$nH��I5�l��F��)�Q�i8E����'�M\$��-R�L��e��HvMl�IF�v��I��w#jRԟ�22�d{�&�k*>J��I5z�\\H��'(�|�y;��#C���vOԕ�(2��w�5�9)RT�Y6R��.�ޕS&�K\$��32ne9Il��&�RxIW�d�I���+&L�Y\\\0�	ʱ��(�N0)P e	�1K+&G|��\\��}��{\$2Oh�<�9%CIƓ(Nt��?����e\0a+�Gx4����풵)NW����}�N�\r�W(�V,�9-2=\$�J6��*8�t�9Z�@\$��M.�\$�#d�� �H��I��'%�TD��I�\$����,\nVL� �d\0���%HD��@�o��K>��%Y��9RrA��k�)&:Y���9�`�pI8�%BGT��k2��q�Z�K+�L��F�¥n�C�*.W���Y���j�~.�)FGl�Y2rA�6Je��-G��FR{���C,6H<��Er�e��A��,�H��99�?%�K�_+�N��	o�\$�H��o,\nO���FR}%Ix��,�Wd��+r�\$����\$fU\$�#��J���.]���Nr��-�֕�)rR̸�G2ʤ�Iӕ�.*J��)FR���IM�[&�S|��R2�\$�J���+T���tr5�II��&\nZt�YMR��Kԓ#'�IT�yA��eD�0��pZH̐y@Kdj�֖�#nW�)]r6�^�@�s%BKܣ�u�����L�vY<�Yd�`��Ir�K\$�GܾR>���4��&�Qd�9@�;�\r�ȕ�#�RD��-�b&\0J��\$�Nԝ�]�C��J��(VOd���G�S�֗�(�QD�y42������q'JX|�i]G�	H瓧.�Wd��<r5%|I���#ؘt��\\S��I���0�W4�Y;��\$�Kk��\$zL���J2J\$nLT�-&\\ܗ \$��e�d��'jG�ũks���٘1(2QĮ	��\$�I9�\r\$�Kģ	lR�e��J\0�P��B�z�:ɋ�\$�NԱ)F�E��KC�A\$�Y����ҍ�\n�ۙ(nFԨiM򡤎�;��#�a��D��̜�{#NL4��#Ҍ\$qJi��+F�=�b�J[�&Uԧ�S�&\\L��K\$�f4��(\ndqJ���,�NL�	��\$�I��o%�W��9u뤢3���)�]썹BR�%TI��2�Q�i(�>�\"̧��\$�I�iu2���J6��'�O����rQ���Ƙ�+�H4�Id�:����[(�FԤ�=�&t�9��,>^l���%\$���F��vG��9R\0I&8��'�Vd��\$��e�K[�W+aT�9?r��%���a�i'se\r�F�S4.G��I��8��\rړ�\$�H�װ�O�K0�)�S\\�I�3:e\nKԚ�0T��i`R��H����.�Q\\�)d�IeKD�/#fP��K��%��ݒ�2�S��	���DLR&):L��I�z%\r��C)Za�	�D�T���*:H��I|Re��\$�\r*j]��ɢ2��q�2�E)�]���`��k�/#�Q4�=RE�JV�{*�W�r��VLϘW3�O�yr�V�J���&>kd�و�E�\nʑ�s&nm��:�s��I*�34\"l��GҾ\0��i�{42O<�YU�i�<Jb��%�W�é��'�ME�Q4�gt��+RĞ�Fr@3\"��A��J���W`�Ꮪ�����ך'ez`r�p0������[�9uC�6'��V(M�^��k[/Y�ĉ&�-�X�vc�9�@\n�ˍj�-u\n���xW�0_�81z,������b�\$J�E{kk#T��{Ș^pj�`)�_0N'~����J��3\0u@�2_o��&pa(�	k��1�d�6���ǰ�A��\0:�h\0�L\n� ���2aO99�����,@g([XL��6�4W��Z���r���3�V�&`�ma�ky�/�Ԯ�\0u8��4���	]�{]DG-��=���LGވ��PrX�0\nk��±����Hh���B��xŘRF�+�AQ8]nlh���|W�/AW�Q�D�w��5�Ο}�_��e{��U�&�yf�r���A�]�b�`?q(�;3�g],�}�bu��	�+�A�>ݜ���~�aXN�\\j�(����\r_Z�ZW���ur�9|���(W�;X����ϸ'k-�e�\rj9��k@�TF6�dD��}I#G��f�/�\0���T��Ȫƙ��d�,�O����\0SY���alu`kK��(���;h.W�lj�m��\0�	n��6/ҧ���_��qH%�k��_+�X� �\"��Fd����]���p���\r��)�d�%`*���P�D,��z�t�+�\0-���=k������OG�6�^p�o��l��k��%v�]l,Q2kזu�W���z�~	�Sѧ*O]Z��	{��!2����d��E�P~��://�r�9�Sޥ��ݞ�\$ʹ�q�R���רO/����b\n����?�jό_P��q�����N|_��A8\\�T窯�>��G.\rix\\��@aW�-�|���x`kN\\\0����f�y���3�\0�ϳ{��=��*��1����_P�p+�kK����>~�u��\n2쟎�@\n�T5�o��,ߟ|�	8k����%��|�>�5{�`dl��	��*�-|\$k���&�!���@,���be'�Fß�?2˗�D����|�7�x������ڟԺ��b�4��I��Wƀ�����\0OX�r�c�i����\r.�b�i:�9�s�(�iWR���s-ps��,<aj�b�Yɳ��Ӭ.[ľ�x��i�s�h�/\$|��k����1�D��8r��񕧳��F��A.�*���('�?�v��]�x�xtV7PX-8:�L��#��h?Mֽ|��t\rYn��}Y>&�*�z\r��)�k~2���ue��'<�aY�D�����q���A�p5���9�^h��M��L�?������\0���`�+ѡ4ĕl3��X(�#���.����S�#�Ϻ�%9�z\\����h8�IW�7Ŗ%	%�T-gS���A��c*K�hO�\\�;q��iP�:�s{���������#�4~@�2\"����v(k���?����j��\nP����¿խl4����]'@�����#��XC;�9�j4�P�+@^uH�ԬhwP|�V�Z�\0hi=���\0>��S��R�.aC����#\r���MD5���KAh�/��J)t�tT;���\0��:I�u�\nS�V�Q,��D���̚&�h/�J[�=��5��}�t�ϡ�A�o,��'k�(��][C��u���;�>QP}���\n�k��6���ܹ��\\���T#��Б����d��S��^.���Bmk]\n*�R�AQq�9�eu�TN(�P����xު-�+	5P����F���t-^\0Y�o9f�u��s�h��F���J��E��e�c0�\\~�&�u�)�d�g���������Z7�#M�C}`:�J5�bhYQ���F�t���kg������j�m��<&�Q�Z�CހSEso���\\)@J�ޠ��@'���5Gl5�.T�Ѫ~�GZ�0f�\nYoQ���@�%:9kZ�P@_AG�\n�%�J�#��\0WG�}��8K�2PL�.���>4Mփ���D�Z�j'�}��Q�\rE�MjA+��wP*��H��ަ\\�\n�:����:F���*�}��?�SE�����h�	��P�=l�:��)ЌX�E��5�E�ب�P��\rE\ru�� P���@��e!�H�o�R!�OA�vmJH����N�2�3�Fo��aR!��H����kn��Q�Y\\��-\r��S|Ev`��6k��=��Z�Ȋ��,�;�P\0��YkH`	 i@�`����赙o����aZn|L��� V��L~6��kI(�;�-i\0�G�o����I�%�K[N43�&�� 0�c̰ȹj`�Hq_@!�+�#9)�����L�%-��OJ�m2����4��aS9�c\$�JRԣ'4� #j��%,�O�p�f�#�J-,��<T��)���x�nsH��y�e�٥�J-p�.w��S�,=�\$}A���?��g��sZ�G�r�Z_3y��,B�\$}^��7�{�X2�e>�c�JR{�G`&�~�3\0:K���A��]���K���ƃ���0��:�-�S��4��W�H!mu,���i�GBb]L}^�	���U�RԦh���pTǧIS;�L��4��,��I�#�M�����SF��G���x�l-�GQ���DI��5%�tҩ�SX}�ڀ�!\0ӡ�\$'�6 ���4@��Gr|=:��}4Jj��h��^��:���5��U��M��\n�U�̧���[�����V>�5)�0�\$�?��\n�Zb���(S�b<����r�ŧ�Q\"[]:ƆJۊg�\n)�R��N���:E��iѭ��YN��rۺdӽV�S����zp��%T�(�_g?^v`~�d��]�-^�e�;iٔ	ERO��;x��U�bC��MX\"��o;u�TgkSэR�&�\r9Ū4�iM1�_\\v���}c�S�ƷҊ�>��k��Ѳ�_��9�:�*~���&��e�KM~V]l�����`ƿ���@*��\0���bO�|@�ݵX�����6\r(�y�L�\r������hu8�r����_������[���=��P�x->���S�pL��������.�N��}:�6��W���^;D�*�`\$����b/D>Й: \n�F¨�J��,5�\n����|�A���2�y����N-P&�qa\0χc�Th��?.:������/�X�˕f�GJ�{�T��He��ڏ��@SϨMNΐC���*),Pb��z��9+��OCJ�=A]���\$�*A�(W����� �[����6\0P[xi`��l��T��O�}�\$,�C�Q#�H�hr�u�4�)ԀP\0��\n�'���j_�|_PH��E:�8Q��m�(��NYy|��4ڧ�.0��S8��%us�#N����=ur����6��Or�������f8�ݘ�S�[�8]���V)PT�Y	QN�F��T�C�.Ԧ@*��:���e@�~|e��c*�-^��Q�!�-`K�-�\"]L�j��P�#(��S}���[��*� D�H���u���i�-D\$`Yy�R�,bW�\0UWr��x��\nEt�֋��F��p�IڨI���惘�z�� ���R�r�Zp�i�����\r\rUL�(�Nr�)ZdS�g,-���UQ�-U%����.Қ�A7�c���Ld��g%��p���?u	IUP`UY}E%�O���1�@�>������^1^�B}����s�j�ϲ��Q���:�?�2@+4g؈쎠C��K����,��F@�D���\nѺ�8X!ј!Ӄ��A��#�2%x�\"��X)ra`\\���F���\n.rvX�x����P�X�3��\0ɀ.&�|��3�s��@-<�]S\$���/�*�-��8g��J�\0��\0�\0��	+`\r�\n��p�\0�	  ��%�\"�J\"�[�y!ľ(ϫ���܏���~(��\0ְH����\0006�ڰd��������\0qXN��aY^��+�IXq�c�;�]�!��̸Mj���k��J�hJ��~���UX*��b��Ս�V\"�%X�b����+�#\0sXΰ�����+�>H�Y\"�ub�g���VJ=�Y2�d��ĕ�+VZ�����d��=�!VS��X�g�e��՘�{VW��X������d��s��X�}d��\r�;%��Y���j���+L�\0aY�PfZ��%Vj��X�k\rh��U�*�J�mZ��*��� ���ZR��*���+��Zb�d���u�+V��Y��L�j�ՙ�I��Z���kj�u�+[Vn�IY���c*���[V��X���i���kָ��Zڵl��5��a�_��Z���\n����gֲ��1���m*۲�+oɠ��%޳��j����V�wY�Unە�+uV­�XH%n��U�+Vs��[ʵ�c\n�5��vW��[�iup�5�«h��w[�=pZ�R��lV��%ڶ�p�u��j�3�Y�Y�pJ�u��N���\\vK�i�����\r�5[�Kp�;�̫�� ��[��̫����ebW2��\\��d�:�Uë��\$�e\\����z浐e��8��-޹�c�n�ȫ�Kw�]V�MmYn�ʫ�V\0��]\"�emJ�e��U�]w]��]u*�����s��]R�}vz��֤�WC��~�%u���+�Wj�c]ҹ�x\n�5���V���^�ub���k�%�[V�-uz���k�Vp�]�P���ߵ��jU��8�N��fj�k�ך�X&�y����W��\"��vZ��'ץQ�]��y�bU�+��p�u^J��wj�U��W��c^���{z�5��\0002�3^vH�{��R�k�Wʯ}_6�|��U�k�Wͯ�_N��0\r2H�B�ԯg_*�\r~9)R]�'��-W\$~��{��\"+���g;_Դl����evW�/�\$���vv\0@9ɺ�	.F�90�\0��ϯA)Z�]�����I��_n��}�������\$���k:��l��o&Z���������'��<��6��I�c`J���j��<,�*�w`��݂��2^,�ְ�`ҽ��K�,\$�:��&F�-��	�,'�B��%�<�K6+�����a2�����6�,�R��aR��k\0E���o��ar�̑;\r�,8�b�'z�m�k�,;�j�'� X+5�թS��bá�\$��pW����*O��;�#l?\$��bą{*�ţR6Xp�\$��\r{��Ū�6X�H�_f�͇�5lPؕ��b=G��:�5��RWα_F�����.lEJH�ob�Ō���\$�ؕ�!bJ݌��n�X\"�\r#��E�i:�2,bIB��b]F��Kv&�EJ>�`B����7�SX��#�P����@,o�ر\r`��<��r8\$t؁�Ic�F�k)%����\0�� ���,lnX̑�%��sP�@��ٲ�M�+�-,NI���c�ą�+2�,@�6�7b&�d�[#�>�NIV�;cGu��#,l#X�Kb�U��\$F�SXP�]b�I���,x�^�{d�F��k�Ol�H��a�ǌ�k(�3�HIA��b��Ŕ�'^��+-WX��!��)�=L��33�b0���B��w�U߱&g~���*ţk�X۱1er���*�(,s�β�[�˭r[/6YlVYy��b�����\"�[�jI��\$R�ݕ{v\\��X���&�͌+1�Y�Y��_���:�ѠG!]�H���\0�g�%> �>.� jB�w�R���ʆ `	K�ֺRXh&L>�ͩ�`U�/�!bUP�������V�Q�~Eg�u8jD+b@%Ӌ�\rNR��!u��(ET�{�D֑�g����+�ΰ���Vxh �=Zp�����J�*��Y��If����K��ާ`\\�&�\n\n�T\rWsϹ~#I	a���k���s��fzo=	*\0��-.�^���-2B+k�+-�%i@f��LA���%?�����5�(IY��f��U�\nBKol٭e��8�<\0�7�n	Y��]iFn�U�64�n����BJ�=�9+1�IY̳�C*�2�B>�}�\0P�!i�b��Tm'��\\�=���:�Y�HO}^� ����T��1���B��u�:�c�6Q��t���{�5��/���ʳci��z�\">��(��avt��j-L��#Z�H�� �E�T<��͡�Ol�UZ �e臫���������a�O���2�=��S�Rh���;j�cu�u�֋l�ڣ�-?��{GVpl݀+�{D�ͽ��F�o--���j���}��Ӧ�0����Mj)��h<�G��E��D������:}qgr���[@�e�5��Z�E��e�26�-`,z|KFFӪ��BV����z��F^������F�_\r����P	��Z���Z����k�-1N���I�5��V.+�[Z\0e���3Z�56'��뵱>�ŭJ�S�״N�{��a��z�Ou�.b{�>\"���Z8��io��o�x��<!6M헊CX0��@\\��Ȣ�ؿ����: T�П�G�h����|�a���7C�э�@4�ym߱=g*�\\��-@����H6�M�M������*(�Һڙ�l����2�~�9���d�h6��[sg�m٣1�����J�}���O\r���(��2�h�m�6_09���Rq���#�:Ѯ�-�`D�\"T��Yb/�G���8}��휹(oZ��zG�}�tP���Gz��b0�@�.��h΢�ųF{qM�m�D������<I.�`Z=�\"��7�xV�\0���(�Aڪ\" JV��ϱ���'�ö�[|[bm�L�-����ؙ���H4��D �K��E����,�K��O���Wl\0'��%%J���|c>���\0007�Mp��T�+��Z��S�*RdHP�m�������{�i͊��۬fݔ�vmO�\0��,\0c�����YC.�i��u� Np�J�2�j�J�������8wp��+u�*o�Q����H�+��5��Gu�q.>忰y@p[�*Xf �@���P��e�i����jh���/n��^t0�Cq'��Z��q��c�Ku\0aC�@6m.�〞�mN����nyq�>byFK.@�����ډ�7��x*�r4D��(P�#.)�Z������ʻm�*rkr.���4W ���2=ndgKz�W%�-lPf�	��8�\n�U�2�	S3���.\0�\\��o���@\r�/�C�/sJ*���'��k�d���F!�D�����~��-A����`\\%���������GN����ؠˍ΀�K���l]������.xς�3>\n��ءM\n�	�RR���&p��P��r@��8s. ��'ܶvn�\0	\\\0���� \\?j�R���c��*�+͵Wf�n��\0l	2���Y)5 @\n4t�'�Ҁh�.�S�d�=5T�f��6I���w�N�B�]��u��7�ƃŹ�6(�8t�N���	��q�lU�3\0�.��)^���e�����`�ĺ��\nݶ%OpZn�]SSj�:��(�׮�ۓ��s�\"մ;� ���2hVA��DvK����o��U�{/��\r�\$Ϟ�n��tF[���9�}�#v@\"�f&�o�n�E�vu);S��7;�݆qV�.����);��vF�\r�ˌ��5E`���V\08�B�={)�>��?�l���a������WrH5��o:�M��p9@�����ٺ�W�k܈Z��`���PjP���(\$n���U-�Cթ��m��r;뽷z������\n�D�ۿ���\0lC�w��1;��N���2����J3뿧֮��7B��6+�\$��GL�J�n�E\$\\�\$�b��OExQpEZ���f�+xd�\$þ�M`� �)���ϛ*@��g�k�;O��/YS��Qr����e�@��*c�!�{Ȁ~�M\0SC�y%��7���>��y�1�\n�*�%�=���t��\$P#7��^R7f`5;cV���B*��Jv�.�4�ۍGU�r���A4\0005����ʐ�\"H� �\n:�w����BloB�2Dy�Nin�d��(");}exit;}if($_GET["script"]=="version"){$o=get_temp_dir()."/adminer.version";@unlink($o);$q=file_open_lock($o);if($q)file_write_unlock($q,serialize(array("signature"=>$_POST["signature"],"version"=>$_POST["version"])));exit;}if(!$_SERVER["REQUEST_URI"])$_SERVER["REQUEST_URI"]=$_SERVER["ORIG_PATH_INFO"];if(!strpos($_SERVER["REQUEST_URI"],'?')&&$_SERVER["QUERY_STRING"]!="")$_SERVER["REQUEST_URI"].="?$_SERVER[QUERY_STRING]";if($_SERVER["HTTP_X_FORWARDED_PREFIX"])$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));@ini_set("session.use_trans_sid",'0');if(!defined("SID")){session_cache_limiter("");session_name("adminer_sid");session_set_cookie_params(0,preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),"",HTTPS,true);session_start();}remove_slashes(array(&$_GET,&$_POST,&$_COOKIE),$Tc);if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);@set_time_limit(0);@ini_set("precision",'15');function
get_lang(){return'en';}function
lang($_i,$of=null){if(is_array($_i)){$pg=($of==1?0:1);$_i=$_i[$pg];}$_i=str_replace("%d","%s",$_i);$of=format_number($of);return
sprintf($_i,$of);}abstract
class
SqlDb{static$be;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach($N,$V,$F);abstract
function
quote($Q);abstract
function
select_db($Jb);abstract
function
query($H,$Ji=false);function
multi_query($H){return$this->multi=$this->query($H);}function
store_result(){return$this->multi;}function
next_result(){return
false;}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($gc,$V,$F,array$Ff=array()){$Ff[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$Ff[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($gc,$V,$F,$Ff);}catch(\Exception$Bc){return$Bc->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($Q){return$this->pdo->quote($Q);}function
query($H,$Ji=false){$I=$this->pdo->query($H);$this->error="";if(!$I){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error='Unknown error.';return
false;}$this->store_result($I);return$I;}function
store_result($I=null){if(!$I){$I=$this->multi;if(!$I)return
false;}if($I->columnCount()){$I->num_rows=$I->rowCount();return$I;}$this->affected_rows=$I->rowCount();return
true;}function
next_result(){$I=$this->multi;if(!is_object($I))return
false;$I->_offset=0;return@$I->nextRowset();}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch(\PDO::FETCH_NUM);}function
fetch_field(){$K=(object)$this->getColumnMeta($this->_offset++);$U=$K->pdo_type;$K->type=($U==\PDO::PARAM_INT?0:15);$K->charsetnr=($U==\PDO::PARAM_LOB||(isset($K->flags)&&in_array("blob",(array)$K->flags))?63:0);return$K;}function
seek($D){for($s=0;$s<$D;$s++)$this->fetch();}}}function
add_driver($t,$C){SqlDriver::$ac[$t]=$C;}function
get_driver($t){return
SqlDriver::$ac[$t];}abstract
class
SqlDriver{static$be;static$ac=array();static$Jc=array();static$le;protected$conn;protected$types=array();var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$operators=array();var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();static
function
connect($N,$V,$F){$f=new
Db;return($f->attach($N,$V,$F)?:$f);}function
__construct(Db$f){$this->conn=$f;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$m){}function
unconvertFunction(array$m){}function
select($R,array$M,array$Z,array$pd,array$Hf=array(),$z=1,$E=0,$wg=false){$ge=(count($pd)<count($M));$H=adminer()->selectQueryBuild($M,$Z,$pd,$Hf,$z,$E);if(!$H)$H="SELECT".limit(($_GET["page"]!="last"&&$z&&$pd&&$ge&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$M)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($pd&&$ge?"\nGROUP BY ".implode(", ",$pd):"").($Hf?"\nORDER BY ".implode(", ",$Hf):""),$z,($E?$z*$E:0),"\n");$Lh=microtime(true);$J=$this->conn->query($H);if($wg)echo
adminer()->selectQuery($H,$Lh,!$J);return$J;}function
delete($R,$Eg,$z=0){$H="FROM ".table($R);return
queries("DELETE".($z?limit1($R,$H,$Eg):" $H$Eg"));}function
update($R,array$O,$Eg,$z=0,$ph="\n"){$cj=array();foreach($O
as$x=>$X)$cj[]="$x = $X";$H=table($R)." SET$ph".implode(",$ph",$cj);return
queries("UPDATE".($z?limit1($R,$H,$Eg,$ph):" $H$Eg"));}function
insert($R,array$O){return
queries("INSERT INTO ".table($R).($O?" (".implode(", ",array_keys($O)).")\nVALUES (".implode(", ",$O).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,array$L,array$G){return
false;}function
begin(){return
queries("BEGIN");}function
commit(){return
queries("COMMIT");}function
rollback(){return
queries("ROLLBACK");}function
slowQuery($H,$ni){}function
convertSearch($u,array$X,array$m){return$u;}function
convertOperator($Bf){return$Bf;}function
value($X,array$m){return(method_exists($this->conn,'value')?$this->conn->value($X,$m):(is_resource($X)?stream_get_contents($X):$X));}function
quoteBinary($dh){return
q($dh);}function
warnings(){}function
tableHelp($C,$je=false){}function
hasCStyleEscapes(){return
false;}function
engines(){return
array();}function
supportsIndex(array$S){return!is_view($S);}function
checkConstraints($R){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($R)."
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'",$this->conn);}function
allFields(){$J=array();foreach(get_rows("SELECT TABLE_NAME AS tab, COLUMN_NAME AS field, IS_NULLABLE AS nullable, DATA_TYPE AS type, CHARACTER_MAXIMUM_LENGTH AS length".(JUSH=='sql'?", COLUMN_KEY = 'PRI' AS `primary`":"")."
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY TABLE_NAME, ORDINAL_POSITION",$this->conn)as$K){$K["null"]=($K["nullable"]=="YES");$J[$K["tab"]][]=$K;}return$J;}}add_driver("sqlite","SQLite");if(isset($_GET["sqlite"])){define('Adminer\DRIVER',"sqlite");if(class_exists("SQLite3")&&$_GET["ext"]!="pdo"){abstract
class
SqliteDb
extends
SqlDb{var$extension="SQLite3";private$link;function
attach($o,$V,$F){$this->link=new
\SQLite3($o);$fj=$this->link->version();$this->server_info=$fj["versionString"];return'';}function
query($H,$Ji=false){$I=@$this->link->query($H);$this->error="";if(!$I){$this->errno=$this->link->lastErrorCode();$this->error=$this->link->lastErrorMsg();return
false;}elseif($I->numColumns())return
new
Result($I);$this->affected_rows=$this->link->changes();return
true;}function
quote($Q){return(is_utf8($Q)?"'".$this->link->escapeString($Q)."'":"x'".first(unpack('H*',$Q))."'");}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($I){$this->result=$I;}function
fetch_assoc(){return$this->result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$d=$this->offset++;$U=$this->result->columnType($d);return(object)array("name"=>$this->result->columnName($d),"type"=>($U==SQLITE3_TEXT?15:0),"charsetnr"=>($U==SQLITE3_BLOB?63:0),);}function
__destruct(){$this->result->finalize();}}}elseif(extension_loaded("pdo_sqlite")){abstract
class
SqliteDb
extends
PdoDb{var$extension="PDO_SQLite";function
attach($o,$V,$F){$this->dsn(DRIVER.":$o","","");$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}}}if(class_exists('Adminer\SqliteDb')){class
Db
extends
SqliteDb{function
attach($o,$V,$F){parent::attach($o,$V,$F);$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}function
select_db($o){if(is_readable($o)&&$this->query("ATTACH ".$this->quote(preg_match("~(^[/\\\\]|:)~",$o)?$o:dirname($_SERVER["SCRIPT_FILENAME"])."/$o")." AS a"))return!self::attach($o,'','');return
false;}}}class
Driver
extends
SqlDriver{static$Jc=array("SQLite3","PDO_SQLite");static$le="sqlite";protected$types=array(array("integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0));var$insertFunctions=array();var$editFunctions=array("integer|real|numeric"=>"+/-","text"=>"||",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");var$functions=array("hex","length","lower","round","unixepoch","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");static
function
connect($N,$V,$F){if($F!="")return'Database does not support password.';return
parent::connect(":memory:","","");}function
__construct(Db$f){parent::__construct($f);if(min_version(3.31,0,$f))$this->generated=array("STORED","VIRTUAL");}function
structuredTypes(){return
array_keys($this->types[0]);}function
insertUpdate($R,array$L,array$G){$cj=array();foreach($L
as$O)$cj[]="(".implode(", ",$O).")";return
queries("REPLACE INTO ".table($R)." (".implode(", ",array_keys(reset($L))).") VALUES\n".implode(",\n",$cj));}function
tableHelp($C,$je=false){if($C=="sqlite_sequence")return"fileformat2.html#seqtab";if($C=="sqlite_master")return"fileformat2.html#$C";}function
checkConstraints($R){preg_match_all('~ CHECK *(\( *(((?>[^()]*[^() ])|(?1))*) *\))~',get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R),0,$this->conn),$He);return
array_combine($He[2],$He[2]);}function
allFields(){$J=array();foreach(tables_list()as$R=>$U){foreach(fields($R)as$m)$J[$R][]=$m;}return$J;}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return
idf_escape($u);}function
get_databases($ad){return
array();}function
limit($H,$Z,$z,$D=0,$ph=" "){return" $H$Z".($z?$ph."LIMIT $z".($D?" OFFSET $D":""):"");}function
limit1($R,$H,$Z,$ph="\n"){return(preg_match('~^INTO~',$H)||get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($H,$Z,1,0,$ph):" $H WHERE rowid = (SELECT rowid FROM ".table($R).$Z.$ph."LIMIT 1)");}function
db_collation($j,$hb){return
get_val("PRAGMA encoding");}function
logged_user(){return
get_current_user();}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view') ORDER BY (name = 'sqlite_sequence'), name");}function
count_tables($i){return
array();}function
table_status($C=""){$J=array();foreach(get_rows("SELECT name AS Name, type AS Engine, 'rowid' AS Oid, '' AS Auto_increment FROM sqlite_master WHERE type IN ('table', 'view') ".($C!=""?"AND name = ".q($C):"ORDER BY name"))as$K){$K["Rows"]=get_val("SELECT COUNT(*) FROM ".idf_escape($K["Name"]));$J[$K["Name"]]=$K;}foreach(get_rows("SELECT * FROM sqlite_sequence".($C!=""?" WHERE name = ".q($C):""),null,"")as$K)$J[$K["name"]]["Auto_increment"]=$K["seq"];return$J;}function
is_view($S){return$S["Engine"]=="view";}function
fk_support($S){return!get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
fields($R){$J=array();$G="";foreach(get_rows("PRAGMA table_".(min_version(3.31)?"x":"")."info(".table($R).")")as$K){$C=$K["name"];$U=strtolower($K["type"]);$k=$K["dflt_value"];$J[$C]=array("field"=>$C,"type"=>(preg_match('~int~i',$U)?"integer":(preg_match('~char|clob|text~i',$U)?"text":(preg_match('~blob~i',$U)?"blob":(preg_match('~real|floa|doub~i',$U)?"real":"numeric")))),"full_type"=>$U,"default"=>(preg_match("~^'(.*)'$~",$k,$B)?str_replace("''","'",$B[1]):($k=="NULL"?null:$k)),"null"=>!$K["notnull"],"privileges"=>array("select"=>1,"insert"=>1,"update"=>1,"where"=>1,"order"=>1),"primary"=>$K["pk"],);if($K["pk"]){if($G!="")$J[$G]["auto_increment"]=false;elseif(preg_match('~^integer$~i',$U))$J[$C]["auto_increment"]=true;$G=$C;}}$Fh=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R));$u='(("[^"]*+")+|[a-z0-9_]+)';preg_match_all('~'.$u.'\s+text\s+COLLATE\s+(\'[^\']+\'|\S+)~i',$Fh,$He,PREG_SET_ORDER);foreach($He
as$B){$C=str_replace('""','"',preg_replace('~^"|"$~','',$B[1]));if($J[$C])$J[$C]["collation"]=trim($B[3],"'");}preg_match_all('~'.$u.'\s.*GENERATED ALWAYS AS \((.+)\) (STORED|VIRTUAL)~i',$Fh,$He,PREG_SET_ORDER);foreach($He
as$B){$C=str_replace('""','"',preg_replace('~^"|"$~','',$B[1]));$J[$C]["default"]=$B[3];$J[$C]["generated"]=strtoupper($B[4]);}return$J;}function
indexes($R,$g=null){$g=connection($g);$J=array();$Fh=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R),0,$g);if(preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i',$Fh,$B)){$J[""]=array("type"=>"PRIMARY","columns"=>array(),"lengths"=>array(),"descs"=>array());preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i',$B[1],$He,PREG_SET_ORDER);foreach($He
as$B){$J[""]["columns"][]=idf_unescape($B[2]).$B[4];$J[""]["descs"][]=(preg_match('~DESC~i',$B[5])?'1':null);}}if(!$J){foreach(fields($R)as$C=>$m){if($m["primary"])$J[""]=array("type"=>"PRIMARY","columns"=>array($C),"lengths"=>array(),"descs"=>array(null));}}$Jh=get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($R),$g);foreach(get_rows("PRAGMA index_list(".table($R).")",$g)as$K){$C=$K["name"];$v=array("type"=>($K["unique"]?"UNIQUE":"INDEX"));$v["lengths"]=array();$v["descs"]=array();foreach(get_rows("PRAGMA index_info(".idf_escape($C).")",$g)as$ch){$v["columns"][]=$ch["name"];$v["descs"][]=null;}if(preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($C).' ON '.idf_escape($R),'~').' \((.*)\)$~i',$Jh[$C],$Pg)){preg_match_all('/("[^"]*+")+( DESC)?/',$Pg[2],$He);foreach($He[2]as$x=>$X){if($X)$v["descs"][$x]='1';}}if(!$J[""]||$v["type"]!="UNIQUE"||$v["columns"]!=$J[""]["columns"]||$v["descs"]!=$J[""]["descs"]||!preg_match("~^sqlite_~",$C))$J[$C]=$v;}return$J;}function
foreign_keys($R){$J=array();foreach(get_rows("PRAGMA foreign_key_list(".table($R).")")as$K){$p=&$J[$K["id"]];if(!$p)$p=$K;$p["source"][]=$K["from"];$p["target"][]=$K["to"];}return$J;}function
view($C){return
array("select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+~iU','',get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($C))));}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):array());}function
information_schema($j){return
false;}function
error(){return
h(connection()->error);}function
check_sqlite_name($C){$Jc="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($Jc)\$~",$C)){connection()->error=sprintf('Please use one of the extensions %s.',str_replace("|",", ",$Jc));return
false;}return
true;}function
create_database($j,$c){if(file_exists($j)){connection()->error='File exists.';return
false;}if(!check_sqlite_name($j))return
false;try{$_=new
Db();$_->attach($j,'','');}catch(\Exception$Bc){connection()->error=$Bc->getMessage();return
false;}$_->query('PRAGMA encoding = "UTF-8"');$_->query('CREATE TABLE adminer (i)');$_->query('DROP TABLE adminer');return
true;}function
drop_databases($i){connection()->attach(":memory:",'','');foreach($i
as$j){if(!@unlink($j)){connection()->error='File exists.';return
false;}}return
true;}function
rename_database($C,$c){if(!check_sqlite_name($C))return
false;connection()->attach(":memory:",'','');connection()->error='File exists.';return@rename(DB,$C);}function
auto_increment(){return" PRIMARY KEY AUTOINCREMENT";}function
alter_table($R,$C,$n,$cd,$mb,$rc,$c,$_a,$eg){$Vi=($R==""||$cd);foreach($n
as$m){if($m[0]!=""||!$m[1]||$m[2]){$Vi=true;break;}}$b=array();$Sf=array();foreach($n
as$m){if($m[1]){$b[]=($Vi?$m[1]:"ADD ".implode($m[1]));if($m[0]!="")$Sf[$m[0]]=$m[1][0];}}if(!$Vi){foreach($b
as$X){if(!queries("ALTER TABLE ".table($R)." $X"))return
false;}if($R!=$C&&!queries("ALTER TABLE ".table($R)." RENAME TO ".table($C)))return
false;}elseif(!recreate_table($R,$C,$b,$Sf,$cd,$_a))return
false;if($_a){queries("BEGIN");queries("UPDATE sqlite_sequence SET seq = $_a WHERE name = ".q($C));if(!connection()->affected_rows)queries("INSERT INTO sqlite_sequence (name, seq) VALUES (".q($C).", $_a)");queries("COMMIT");}return
true;}function
recreate_table($R,$C,array$n,array$Sf,array$cd,$_a="",$w=array(),$cc="",$ja=""){if($R!=""){if(!$n){foreach(fields($R)as$x=>$m){if($w)$m["auto_increment"]=0;$n[]=process_field($m,$m);$Sf[$x]=idf_escape($x);}}$vg=false;foreach($n
as$m){if($m[6])$vg=true;}$ec=array();foreach($w
as$x=>$X){if($X[2]=="DROP"){$ec[$X[1]]=true;unset($w[$x]);}}foreach(indexes($R)as$ne=>$v){$e=array();foreach($v["columns"]as$x=>$d){if(!$Sf[$d])continue
2;$e[]=$Sf[$d].($v["descs"][$x]?" DESC":"");}if(!$ec[$ne]){if($v["type"]!="PRIMARY"||!$vg)$w[]=array($v["type"],$ne,$e);}}foreach($w
as$x=>$X){if($X[0]=="PRIMARY"){unset($w[$x]);$cd[]="  PRIMARY KEY (".implode(", ",$X[2]).")";}}foreach(foreign_keys($R)as$ne=>$p){foreach($p["source"]as$x=>$d){if(!$Sf[$d])continue
2;$p["source"][$x]=idf_unescape($Sf[$d]);}if(!isset($cd[" $ne"]))$cd[]=" ".format_foreign_key($p);}queries("BEGIN");}$Ta=array();foreach($n
as$m){if(preg_match('~GENERATED~',$m[3]))unset($Sf[array_search($m[0],$Sf)]);$Ta[]="  ".implode($m);}$Ta=array_merge($Ta,array_filter($cd));foreach(driver()->checkConstraints($R)as$Va){if($Va!=$cc)$Ta[]="  CHECK ($Va)";}if($ja)$Ta[]="  CHECK ($ja)";$hi=($R==$C?"adminer_$C":$C);if(!queries("CREATE TABLE ".table($hi)." (\n".implode(",\n",$Ta)."\n)"))return
false;if($R!=""){if($Sf&&!queries("INSERT INTO ".table($hi)." (".implode(", ",$Sf).") SELECT ".implode(", ",array_map('Adminer\idf_escape',array_keys($Sf)))." FROM ".table($R)))return
false;$Fi=array();foreach(triggers($R)as$Di=>$oi){$Ci=trigger($Di,$R);$Fi[]="CREATE TRIGGER ".idf_escape($Di)." ".implode(" ",$oi)." ON ".table($C)."\n$Ci[Statement]";}$_a=$_a?"":get_val("SELECT seq FROM sqlite_sequence WHERE name = ".q($R));if(!queries("DROP TABLE ".table($R))||($R==$C&&!queries("ALTER TABLE ".table($hi)." RENAME TO ".table($C)))||!alter_indexes($C,$w))return
false;if($_a)queries("UPDATE sqlite_sequence SET seq = $_a WHERE name = ".q($C));foreach($Fi
as$Ci){if(!queries($Ci))return
false;}queries("COMMIT");}return
true;}function
index_sql($R,$U,$C,$e){return"CREATE $U ".($U!="INDEX"?"INDEX ":"").idf_escape($C!=""?$C:uniqid($R."_"))." ON ".table($R)." $e";}function
alter_indexes($R,$b){foreach($b
as$G){if($G[0]=="PRIMARY")return
recreate_table($R,$R,array(),array(),array(),"",$b);}foreach(array_reverse($b)as$X){if(!queries($X[2]=="DROP"?"DROP INDEX ".idf_escape($X[1]):index_sql($R,$X[0],$X[1],"(".implode(", ",$X[2]).")")))return
false;}return
true;}function
truncate_tables($T){return
apply_queries("DELETE FROM",$T);}function
drop_views($hj){return
apply_queries("DROP VIEW",$hj);}function
drop_tables($T){return
apply_queries("DROP TABLE",$T);}function
move_tables($T,$hj,$fi){return
false;}function
trigger($C,$R){if($C=="")return
array("Statement"=>"BEGIN\n\t;\nEND");$u='(?:[^`"\s]+|`[^`]*`|"[^"]*")+';$Ei=trigger_options();preg_match("~^CREATE\\s+TRIGGER\\s*$u\\s*(".implode("|",$Ei["Timing"]).")\\s+([a-z]+)(?:\\s+OF\\s+($u))?\\s+ON\\s*$u\\s*(?:FOR\\s+EACH\\s+ROW\\s)?(.*)~is",get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($C)),$B);$qf=$B[3];return
array("Timing"=>strtoupper($B[1]),"Event"=>strtoupper($B[2]).($qf?" OF":""),"Of"=>idf_unescape($qf),"Trigger"=>$C,"Statement"=>$B[4],);}function
triggers($R){$J=array();$Ei=trigger_options();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($R))as$K){preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode("|",$Ei["Timing"]).')\s*(.*?)\s+ON\b~i',$K["sql"],$B);$J[$K["name"]]=array($B[1],$B[2]);}return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
begin(){return
queries("BEGIN");}function
last_id($I){return
get_val("SELECT LAST_INSERT_ROWID()");}function
explain($f,$H){return$f->query("EXPLAIN QUERY PLAN $H");}function
found_rows($S,$Z){}function
types(){return
array();}function
create_sql($R,$_a,$Ph){$J=get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($R));foreach(indexes($R)as$C=>$v){if($C=='')continue;$J
.=";\n\n".index_sql($R,$v['type'],$C,"(".implode(", ",array_map('Adminer\idf_escape',$v['columns'])).")");}return$J;}function
truncate_sql($R){return"DELETE FROM ".table($R);}function
use_sql($Jb){}function
trigger_sql($R){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($R)));}function
show_variables(){$J=array();foreach(get_rows("PRAGMA pragma_list")as$K){$C=$K["name"];if($C!="pragma_list"&&$C!="compile_options"){$J[$C]=array($C,'');foreach(get_rows("PRAGMA $C")as$K)$J[$C][1].=implode(", ",$K)."\n";}}return$J;}function
show_status(){$J=array();foreach(get_vals("PRAGMA compile_options")as$Ef)$J[]=explode("=",$Ef,2);return$J;}function
convert_field($m){}function
unconvert_field($m,$J){return$J;}function
support($Oc){return
preg_match('~^(check|columns|database|drop_col|dump|indexes|descidx|move_col|sql|status|table|trigger|variables|view|view_trigger)$~',$Oc);}}add_driver("pgsql","PostgreSQL");if(isset($_GET["pgsql"])){define('Adminer\DRIVER',"pgsql");if(extension_loaded("pgsql")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="PgSQL";var$timeout=0;private$link,$string,$database=true;function
_error($xc,$l){if(ini_bool("html_errors"))$l=html_entity_decode(strip_tags($l));$l=preg_replace('~^[^:]*: ~','',$l);$this->error=$l;}function
attach($N,$V,$F){$j=adminer()->database();set_error_handler(array($this,'_error'));$this->string="host='".str_replace(":","' port='",addcslashes($N,"'\\"))."' user='".addcslashes($V,"'\\")."' password='".addcslashes($F,"'\\")."'";$Kh=adminer()->connectSsl();if(isset($Kh["mode"]))$this->string
.=" sslmode='".$Kh["mode"]."'";$this->link=@pg_connect("$this->string dbname='".($j!=""?addcslashes($j,"'\\"):"postgres")."'",PGSQL_CONNECT_FORCE_NEW);if(!$this->link&&$j!=""){$this->database=false;$this->link=@pg_connect("$this->string dbname='postgres'",PGSQL_CONNECT_FORCE_NEW);}restore_error_handler();if($this->link)pg_set_client_encoding($this->link,"UTF8");return($this->link?'':$this->error);}function
quote($Q){return(function_exists('pg_escape_literal')?pg_escape_literal($this->link,$Q):"'".pg_escape_string($this->link,$Q)."'");}function
value($X,array$m){return($m["type"]=="bytea"&&$X!==null?pg_unescape_bytea($X):$X);}function
select_db($Jb){if($Jb==adminer()->database())return$this->database;$J=@pg_connect("$this->string dbname='".addcslashes($Jb,"'\\")."'",PGSQL_CONNECT_FORCE_NEW);if($J)$this->link=$J;return$J;}function
close(){$this->link=@pg_connect("$this->string dbname='postgres'");}function
query($H,$Ji=false){$I=@pg_query($this->link,$H);$this->error="";if(!$I){$this->error=pg_last_error($this->link);$J=false;}elseif(!pg_num_fields($I)){$this->affected_rows=pg_affected_rows($I);$J=true;}else$J=new
Result($I);if($this->timeout){$this->timeout=0;$this->query("RESET statement_timeout");}return$J;}function
warnings(){return
h(pg_last_notice($this->link));}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($I){$this->result=$I;$this->num_rows=pg_num_rows($I);}function
fetch_assoc(){return
pg_fetch_assoc($this->result);}function
fetch_row(){return
pg_fetch_row($this->result);}function
fetch_field(){$d=$this->offset++;$J=new
\stdClass;$J->orgtable=pg_field_table($this->result,$d);$J->name=pg_field_name($this->result,$d);$J->type=pg_field_type($this->result,$d);$J->charsetnr=($J->type=="bytea"?63:0);return$J;}function
__destruct(){pg_free_result($this->result);}}}elseif(extension_loaded("pdo_pgsql")){class
Db
extends
PdoDb{var$extension="PDO_PgSQL";var$timeout=0;function
attach($N,$V,$F){$j=adminer()->database();$gc="pgsql:host='".str_replace(":","' port='",addcslashes($N,"'\\"))."' client_encoding=utf8 dbname='".($j!=""?addcslashes($j,"'\\"):"postgres")."'";$Kh=adminer()->connectSsl();if(isset($Kh["mode"]))$gc
.=" sslmode='".$Kh["mode"]."'";return$this->dsn($gc,$V,$F);}function
select_db($Jb){return(adminer()->database()==$Jb);}function
query($H,$Ji=false){$J=parent::query($H,$Ji);if($this->timeout){$this->timeout=0;parent::query("RESET statement_timeout");}return$J;}function
warnings(){}function
close(){}}}class
Driver
extends
SqlDriver{static$Jc=array("PgSQL","PDO_PgSQL");static$le="pgsql";var$operators=array("=","<",">","<=",">=","!=","~","!~","LIKE","LIKE %%","ILIKE","ILIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");var$functions=array("char_length","lower","round","to_hex","to_timestamp","upper");var$grouping=array("avg","count","count distinct","max","min","sum");static
function
connect($N,$V,$F){$f=parent::connect($N,$V,$F);if(is_string($f))return$f;$fj=get_val("SELECT version()",0,$f);$f->flavor=(preg_match('~CockroachDB~',$fj)?'cockroach':'');$f->server_info=preg_replace('~^\D*([\d.]+[-\w]*).*~','\1',$fj);if(min_version(9,0,$f))$f->query("SET application_name = 'Adminer'");if($f->flavor=='cockroach')add_driver(DRIVER,"CockroachDB");return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array('Numbers'=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),'Date and time'=>array("date"=>13,"time"=>17,"timestamp"=>20,"timestamptz"=>21,"interval"=>0),'Strings'=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),'Binary'=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),'Network'=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"macaddr8"=>23,"txid_snapshot"=>0),'Geometry'=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),);if(min_version(9.2,0,$f)){$this->types['Strings']["json"]=4294967295;if(min_version(9.4,0,$f))$this->types['Strings']["jsonb"]=4294967295;}$this->insertFunctions=array("char"=>"md5","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",);if(min_version(12,0,$f))$this->generated=array("STORED");}function
enumLength(array$m){$tc=$this->types['User types'][$m["type"]];return($tc?type_values($tc):"");}function
setUserTypes($Ii){$this->types['User types']=array_flip($Ii);}function
insertReturning($R){$_a=array_filter(fields($R),function($m){return$m['auto_increment'];});return(count($_a)==1?" RETURNING ".idf_escape(key($_a)):"");}function
insertUpdate($R,array$L,array$G){foreach($L
as$O){$Ri=array();$Z=array();foreach($O
as$x=>$X){$Ri[]="$x = $X";if(isset($G[idf_unescape($x)]))$Z[]="$x = $X";}if(!(($Z&&queries("UPDATE ".table($R)." SET ".implode(", ",$Ri)." WHERE ".implode(" AND ",$Z))&&connection()->affected_rows)||queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($O)).") VALUES (".implode(", ",$O).")")))return
false;}return
true;}function
slowQuery($H,$ni){$this->conn->query("SET statement_timeout = ".(1000*$ni));$this->conn->timeout=1000*$ni;return$H;}function
convertSearch($u,array$X,array$m){$ki="char|text";if(strpos($X["op"],"LIKE")===false)$ki
.="|date|time(stamp)?|boolean|uuid|inet|cidr|macaddr|".number_type();return(preg_match("~$ki~",$m["type"])?$u:"CAST($u AS text)");}function
quoteBinary($dh){return"'\\x".bin2hex($dh)."'";}function
warnings(){return$this->conn->warnings();}function
tableHelp($C,$je=false){$Ae=array("information_schema"=>"infoschema","pg_catalog"=>($je?"view":"catalog"),);$_=$Ae[$_GET["ns"]];if($_)return"$_-".str_replace("_","-",$C).".html";}function
supportsIndex(array$S){return$S["Engine"]!="view";}function
hasCStyleEscapes(){static$Pa;if($Pa===null)$Pa=(get_val("SHOW standard_conforming_strings",0,$this->conn)=="off");return$Pa;}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return
idf_escape($u);}function
get_databases($ad){return
get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");}function
limit($H,$Z,$z,$D=0,$ph=" "){return" $H$Z".($z?$ph."LIMIT $z".($D?" OFFSET $D":""):"");}function
limit1($R,$H,$Z,$ph="\n"){return(preg_match('~^INTO~',$H)?limit($H,$Z,1,0,$ph):" $H".(is_view(table_status1($R))?$Z:$ph."WHERE ctid = (SELECT ctid FROM ".table($R).$Z.$ph."LIMIT 1)"));}function
db_collation($j,$hb){return
get_val("SELECT datcollate FROM pg_database WHERE datname = ".q($j));}function
logged_user(){return
get_val("SELECT user");}function
tables_list(){$H="SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = current_schema()";if(support("materializedview"))$H
.="
UNION ALL
SELECT matviewname, 'MATERIALIZED VIEW'
FROM pg_matviews
WHERE schemaname = current_schema()";$H
.="
ORDER BY 1";return
get_key_vals($H);}function
count_tables($i){$J=array();foreach($i
as$j){if(connection()->select_db($j))$J[$j]=count(tables_list());}return$J;}function
table_status($C=""){static$zd;if($zd===null)$zd=get_val("SELECT 'pg_table_size'::regproc");$J=array();foreach(get_rows("SELECT
	c.relname AS \"Name\",
	CASE c.relkind WHEN 'r' THEN 'table' WHEN 'm' THEN 'materialized view' ELSE 'view' END AS \"Engine\"".($zd?",
	pg_table_size(c.oid) AS \"Data_length\",
	pg_indexes_size(c.oid) AS \"Index_length\"":"").",
	obj_description(c.oid, 'pg_class') AS \"Comment\",
	".(min_version(12)?"''":"CASE WHEN c.relhasoids THEN 'oid' ELSE '' END")." AS \"Oid\",
	c.reltuples as \"Rows\",
	n.nspname
FROM pg_class c
JOIN pg_namespace n ON(n.nspname = current_schema() AND n.oid = c.relnamespace)
WHERE relkind IN ('r', 'm', 'v', 'f', 'p')
".($C!=""?"AND relname = ".q($C):"ORDER BY relname"))as$K)$J[$K["Name"]]=$K;return$J;}function
is_view($S){return
in_array($S["Engine"],array("view","materialized view"));}function
fk_support($S){return
true;}function
fields($R){$J=array();$ra=array('timestamp without time zone'=>'timestamp','timestamp with time zone'=>'timestamptz',);foreach(get_rows("SELECT
	a.attname AS field,
	format_type(a.atttypid, a.atttypmod) AS full_type,
	pg_get_expr(d.adbin, d.adrelid) AS default,
	a.attnotnull::int,
	col_description(c.oid, a.attnum) AS comment".(min_version(10)?",
	a.attidentity".(min_version(12)?",
	a.attgenerated":""):"")."
FROM pg_class c
JOIN pg_namespace n ON c.relnamespace = n.oid
JOIN pg_attribute a ON c.oid = a.attrelid
LEFT JOIN pg_attrdef d ON c.oid = d.adrelid AND a.attnum = d.adnum
WHERE c.relname = ".q($R)."
AND n.nspname = current_schema()
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum")as$K){preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~',$K["full_type"],$B);list(,$U,$y,$K["length"],$ka,$va)=$B;$K["length"].=$va;$Xa=$U.$ka;if(isset($ra[$Xa])){$K["type"]=$ra[$Xa];$K["full_type"]=$K["type"].$y.$va;}else{$K["type"]=$U;$K["full_type"]=$K["type"].$y.$ka.$va;}if(in_array($K['attidentity'],array('a','d')))$K['default']='GENERATED '.($K['attidentity']=='d'?'BY DEFAULT':'ALWAYS').' AS IDENTITY';$K["generated"]=($K["attgenerated"]=="s"?"STORED":"");$K["null"]=!$K["attnotnull"];$K["auto_increment"]=$K['attidentity']||preg_match('~^nextval\(~i',$K["default"])||preg_match('~^unique_rowid\(~',$K["default"]);$K["privileges"]=array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1);if(preg_match('~(.+)::[^,)]+(.*)~',$K["default"],$B))$K["default"]=($B[1]=="NULL"?null:idf_unescape($B[1]).$B[2]);$J[$K["field"]]=$K;}return$J;}function
indexes($R,$g=null){$g=connection($g);$J=array();$Yh=get_val("SELECT oid FROM pg_class WHERE relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema()) AND relname = ".q($R),0,$g);$e=get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $Yh AND attnum > 0",$g);foreach(get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey, indoption, (indpred IS NOT NULL)::int as indispartial
FROM pg_index i, pg_class ci
WHERE i.indrelid = $Yh AND ci.oid = i.indexrelid
ORDER BY indisprimary DESC, indisunique DESC",$g)as$K){$Qg=$K["relname"];$J[$Qg]["type"]=($K["indispartial"]?"INDEX":($K["indisprimary"]?"PRIMARY":($K["indisunique"]?"UNIQUE":"INDEX")));$J[$Qg]["columns"]=array();$J[$Qg]["descs"]=array();if($K["indkey"]){foreach(explode(" ",$K["indkey"])as$Td)$J[$Qg]["columns"][]=$e[$Td];foreach(explode(" ",$K["indoption"])as$Ud)$J[$Qg]["descs"][]=(intval($Ud)&1?'1':null);}$J[$Qg]["lengths"]=array();}return$J;}function
foreign_keys($R){$J=array();foreach(get_rows("SELECT conname, condeferrable::int AS deferrable, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = (SELECT pc.oid FROM pg_class AS pc INNER JOIN pg_namespace AS pn ON (pn.oid = pc.relnamespace) WHERE pc.relname = ".q($R)." AND pn.nspname = current_schema())
AND contype = 'f'::char
ORDER BY conkey, conname")as$K){if(preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA',$K['definition'],$B)){$K['source']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$B[1])));if(preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~',$B[2],$Fe)){$K['ns']=idf_unescape($Fe[2]);$K['table']=idf_unescape($Fe[4]);}$K['target']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$B[3])));$K['on_delete']=(preg_match("~ON DELETE (driver()->onActions)~",$B[4],$Fe)?$Fe[1]:'NO ACTION');$K['on_update']=(preg_match("~ON UPDATE (driver()->onActions)~",$B[4],$Fe)?$Fe[1]:'NO ACTION');$J[$K['conname']]=$K;}}return$J;}function
view($C){return
array("select"=>trim(get_val("SELECT pg_get_viewdef(".get_val("SELECT oid FROM pg_class WHERE relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema()) AND relname = ".q($C)).")")));}function
collations(){return
array();}function
information_schema($j){return
get_schema()=="information_schema";}function
error(){$J=h(connection()->error);if(preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s',$J,$B))$J=$B[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($B[3]).'})(.*)~','\1<b>\2</b>',$B[2]).$B[4];return
nl_br($J);}function
create_database($j,$c){return
queries("CREATE DATABASE ".idf_escape($j).($c?" ENCODING ".idf_escape($c):""));}function
drop_databases($i){connection()->close();return
apply_queries("DROP DATABASE",$i,'Adminer\idf_escape');}function
rename_database($C,$c){connection()->close();return
queries("ALTER DATABASE ".idf_escape(DB)." RENAME TO ".idf_escape($C));}function
auto_increment(){return"";}function
alter_table($R,$C,$n,$cd,$mb,$rc,$c,$_a,$eg){$b=array();$Dg=array();if($R!=""&&$R!=$C)$Dg[]="ALTER TABLE ".table($R)." RENAME TO ".table($C);$qh="";foreach($n
as$m){$d=idf_escape($m[0]);$X=$m[1];if(!$X)$b[]="DROP $d";else{$bj=$X[5];unset($X[5]);if($m[0]==""){if(isset($X[6]))$X[1]=($X[1]==" bigint"?" big":($X[1]==" smallint"?" small":" "))."serial";$b[]=($R!=""?"ADD ":"  ").implode($X);if(isset($X[6]))$b[]=($R!=""?"ADD":" ")." PRIMARY KEY ($X[0])";}else{if($d!=$X[0])$Dg[]="ALTER TABLE ".table($C)." RENAME $d TO $X[0]";$b[]="ALTER $d TYPE$X[1]";$rh=$R."_".idf_unescape($X[0])."_seq";$b[]="ALTER $d ".($X[3]?"SET".preg_replace('~GENERATED ALWAYS(.*) STORED~','EXPRESSION\1',$X[3]):(isset($X[6])?"SET DEFAULT nextval(".q($rh).")":"DROP DEFAULT"));if(isset($X[6]))$qh="CREATE SEQUENCE IF NOT EXISTS ".idf_escape($rh)." OWNED BY ".idf_escape($R).".$X[0]";$b[]="ALTER $d ".($X[2]==" NULL"?"DROP NOT":"SET").$X[2];}if($m[0]!=""||$bj!="")$Dg[]="COMMENT ON COLUMN ".table($C).".$X[0] IS ".($bj!=""?substr($bj,9):"''");}}$b=array_merge($b,$cd);if($R=="")array_unshift($Dg,"CREATE TABLE ".table($C)." (\n".implode(",\n",$b)."\n)");elseif($b)array_unshift($Dg,"ALTER TABLE ".table($R)."\n".implode(",\n",$b));if($qh)array_unshift($Dg,$qh);if($mb!==null)$Dg[]="COMMENT ON TABLE ".table($C)." IS ".q($mb);foreach($Dg
as$H){if(!queries($H))return
false;}return
true;}function
alter_indexes($R,$b){$h=array();$bc=array();$Dg=array();foreach($b
as$X){if($X[0]!="INDEX")$h[]=($X[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($X[1]):"\nADD".($X[1]!=""?" CONSTRAINT ".idf_escape($X[1]):"")." $X[0] ".($X[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$X[2]).")");elseif($X[2]=="DROP")$bc[]=idf_escape($X[1]);else$Dg[]="CREATE INDEX ".idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R)." (".implode(", ",$X[2]).")";}if($h)array_unshift($Dg,"ALTER TABLE ".table($R).implode(",",$h));if($bc)array_unshift($Dg,"DROP INDEX ".implode(", ",$bc));foreach($Dg
as$H){if(!queries($H))return
false;}return
true;}function
truncate_tables($T){return
queries("TRUNCATE ".implode(", ",array_map('Adminer\table',$T)));}function
drop_views($hj){return
drop_tables($hj);}function
drop_tables($T){foreach($T
as$R){$P=table_status1($R);if(!queries("DROP ".strtoupper($P["Engine"])." ".table($R)))return
false;}return
true;}function
move_tables($T,$hj,$fi){foreach(array_merge($T,$hj)as$R){$P=table_status1($R);if(!queries("ALTER ".strtoupper($P["Engine"])." ".table($R)." SET SCHEMA ".idf_escape($fi)))return
false;}return
true;}function
trigger($C,$R){if($C=="")return
array("Statement"=>"EXECUTE PROCEDURE ()");$e=array();$Z="WHERE trigger_schema = current_schema() AND event_object_table = ".q($R)." AND trigger_name = ".q($C);foreach(get_rows("SELECT * FROM information_schema.triggered_update_columns $Z")as$K)$e[]=$K["event_object_column"];$J=array();foreach(get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers'."
$Z
ORDER BY event_manipulation DESC")as$K){if($e&&$K["Event"]=="UPDATE")$K["Event"].=" OF";$K["Of"]=implode(", ",$e);if($J)$K["Event"].=" OR $J[Event]";$J=$K;}return$J;}function
triggers($R){$J=array();foreach(get_rows("SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = ".q($R))as$K){$Ci=trigger($K["trigger_name"],$R);$J[$Ci["Trigger"]]=array($Ci["Timing"],$Ci["Event"]);}return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF"),"Type"=>array("FOR EACH ROW","FOR EACH STATEMENT"),);}function
routine($C,$U){$L=get_rows('SELECT routine_definition AS definition, LOWER(external_language) AS language, *
FROM information_schema.routines
WHERE routine_schema = current_schema() AND specific_name = '.q($C));$J=idx($L,0,array());$J["returns"]=array("type"=>$J["type_udt_name"]);$J["fields"]=get_rows('SELECT parameter_name AS field, data_type AS type, character_maximum_length AS length, parameter_mode AS inout
FROM information_schema.parameters
WHERE specific_schema = current_schema() AND specific_name = '.q($C).'
ORDER BY ordinal_position');return$J;}function
routines(){return
get_rows('SELECT specific_name AS "SPECIFIC_NAME", routine_type AS "ROUTINE_TYPE", routine_name AS "ROUTINE_NAME", type_udt_name AS "DTD_IDENTIFIER"
FROM information_schema.routines
WHERE routine_schema = current_schema()
ORDER BY SPECIFIC_NAME');}function
routine_languages(){return
get_vals("SELECT LOWER(lanname) FROM pg_catalog.pg_language");}function
routine_id($C,$K){$J=array();foreach($K["fields"]as$m){$y=$m["length"];$J[]=$m["type"].($y?"($y)":"");}return
idf_escape($C)."(".implode(", ",$J).")";}function
last_id($I){$K=(is_object($I)?$I->fetch_row():array());return($K?$K[0]:0);}function
explain($f,$H){return$f->query("EXPLAIN $H");}function
found_rows($S,$Z){if(preg_match("~ rows=([0-9]+)~",get_val("EXPLAIN SELECT * FROM ".idf_escape($S["Name"]).($Z?" WHERE ".implode(" AND ",$Z):"")),$Pg))return$Pg[1];}function
types(){return
get_key_vals("SELECT oid, typname
FROM pg_type
WHERE typnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema())
AND typtype IN ('b','d','e')
AND typelem = 0");}function
type_values($t){$wc=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $t ORDER BY enumsortorder");return($wc?"'".implode("', '",array_map('addslashes',$wc))."'":"");}function
schemas(){return
get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");}function
get_schema(){return
get_val("SELECT current_schema()");}function
set_schema($fh,$g=null){if(!$g)$g=connection();$J=$g->query("SET search_path TO ".idf_escape($fh));driver()->setUserTypes(types());return$J;}function
foreign_keys_sql($R){$J="";$P=table_status1($R);$Yc=foreign_keys($R);ksort($Yc);foreach($Yc
as$Xc=>$Wc)$J
.="ALTER TABLE ONLY ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." ADD CONSTRAINT ".idf_escape($Xc)." $Wc[definition] ".($Wc['deferrable']?'DEFERRABLE':'NOT DEFERRABLE').";\n";return($J?"$J\n":$J);}function
create_sql($R,$_a,$Ph){$Vg=array();$sh=array();$P=table_status1($R);if(is_view($P)){$gj=view($R);return
rtrim("CREATE VIEW ".idf_escape($R)." AS $gj[select]",";");}$n=fields($R);if(count($P)<2||empty($n))return
false;$J="CREATE TABLE ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." (\n    ";foreach($n
as$m){$bg=idf_escape($m['field']).' '.$m['full_type'].default_value($m).($m['null']?"":" NOT NULL");$Vg[]=$bg;if(preg_match('~nextval\(\'([^\']+)\'\)~',$m['default'],$He)){$rh=$He[1];$Eh=first(get_rows((min_version(10)?"SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = ".q(idf_unescape($rh)):"SELECT * FROM $rh"),null,"-- "));$sh[]=($Ph=="DROP+CREATE"?"DROP SEQUENCE IF EXISTS $rh;\n":"")."CREATE SEQUENCE $rh INCREMENT $Eh[increment_by] MINVALUE $Eh[min_value] MAXVALUE $Eh[max_value]".($_a&&$Eh['last_value']?" START ".($Eh["last_value"]+1):"")." CACHE $Eh[cache_value];";}}if(!empty($sh))$J=implode("\n\n",$sh)."\n\n$J";$G="";foreach(indexes($R)as$Rd=>$v){if($v['type']=='PRIMARY'){$G=$Rd;$Vg[]="CONSTRAINT ".idf_escape($Rd)." PRIMARY KEY (".implode(', ',array_map('Adminer\idf_escape',$v['columns'])).")";}}foreach(driver()->checkConstraints($R)as$rb=>$tb)$Vg[]="CONSTRAINT ".idf_escape($rb)." CHECK $tb";$J
.=implode(",\n    ",$Vg)."\n) WITH (oids = ".($P['Oid']?'true':'false').");";if($P['Comment'])$J
.="\n\nCOMMENT ON TABLE ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." IS ".q($P['Comment']).";";foreach($n
as$Qc=>$m){if($m['comment'])$J
.="\n\nCOMMENT ON COLUMN ".idf_escape($P['nspname']).".".idf_escape($P['Name']).".".idf_escape($Qc)." IS ".q($m['comment']).";";}foreach(get_rows("SELECT indexdef FROM pg_catalog.pg_indexes WHERE schemaname = current_schema() AND tablename = ".q($R).($G?" AND indexname != ".q($G):""),null,"-- ")as$K)$J
.="\n\n$K[indexdef];";return
rtrim($J,';');}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
trigger_sql($R){$P=table_status1($R);$J="";foreach(triggers($R)as$Bi=>$Ai){$Ci=trigger($Bi,$P['Name']);$J
.="\nCREATE TRIGGER ".idf_escape($Ci['Trigger'])." $Ci[Timing] $Ci[Event] ON ".idf_escape($P["nspname"]).".".idf_escape($P['Name'])." $Ci[Type] $Ci[Statement];;\n";}return$J;}function
use_sql($Jb){return"\connect ".idf_escape($Jb);}function
show_variables(){return
get_rows("SHOW ALL");}function
process_list(){return
get_rows("SELECT * FROM pg_stat_activity ORDER BY ".(min_version(9.2)?"pid":"procpid"));}function
convert_field($m){}function
unconvert_field($m,$J){return$J;}function
support($Oc){return
preg_match('~^(check|database|table|columns|sql|indexes|descidx|comment|view|'.(min_version(9.3)?'materializedview|':'').'scheme|'.(min_version(11)?'procedure|':'').'routine|sequence|trigger|type|variables|drop_col'.(connection()->flavor=='cockroach'?'':'|processlist').'|kill|dump)$~',$Oc);}function
kill_process($X){return
queries("SELECT pg_terminate_backend(".number($X).")");}function
connection_id(){return"SELECT pg_backend_pid()";}function
max_connections(){return
get_val("SHOW max_connections");}}add_driver("oracle","Oracle (beta)");if(isset($_GET["oracle"])){define('Adminer\DRIVER',"oracle");if(extension_loaded("oci8")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="oci8";var$_current_db;private$link;function
_error($xc,$l){if(ini_bool("html_errors"))$l=html_entity_decode(strip_tags($l));$l=preg_replace('~^[^:]*: ~','',$l);$this->error=$l;}function
attach($N,$V,$F){$this->link=@oci_new_connect($V,$F,$N,"AL32UTF8");if($this->link){$this->server_info=oci_server_version($this->link);return'';}$l=oci_error();return$l["message"];}function
quote($Q){return"'".str_replace("'","''",$Q)."'";}function
select_db($Jb){$this->_current_db=$Jb;return
true;}function
query($H,$Ji=false){$I=oci_parse($this->link,$H);$this->error="";if(!$I){$l=oci_error($this->link);$this->errno=$l["code"];$this->error=$l["message"];return
false;}set_error_handler(array($this,'_error'));$J=@oci_execute($I);restore_error_handler();if($J){if(oci_num_fields($I))return
new
Result($I);$this->affected_rows=oci_num_rows($I);oci_free_statement($I);}return$J;}}class
Result{var$num_rows;private$result,$offset=1;function
__construct($I){$this->result=$I;}private
function
convert($K){foreach((array)$K
as$x=>$X){if(is_a($X,'OCILob')||is_a($X,'OCI-Lob'))$K[$x]=$X->load();}return$K;}function
fetch_assoc(){return$this->convert(oci_fetch_assoc($this->result));}function
fetch_row(){return$this->convert(oci_fetch_row($this->result));}function
fetch_field(){$d=$this->offset++;$J=new
\stdClass;$J->name=oci_field_name($this->result,$d);$J->type=oci_field_type($this->result,$d);$J->charsetnr=(preg_match("~raw|blob|bfile~",$J->type)?63:0);return$J;}function
__destruct(){oci_free_statement($this->result);}}}elseif(extension_loaded("pdo_oci")){class
Db
extends
PdoDb{var$extension="PDO_OCI";var$_current_db;function
attach($N,$V,$F){return$this->dsn("oci:dbname=//$N;charset=AL32UTF8",$V,$F);}function
select_db($Jb){$this->_current_db=$Jb;return
true;}}}class
Driver
extends
SqlDriver{static$Jc=array("OCI8","PDO_OCI");static$le="oracle";var$insertFunctions=array("date"=>"current_date","timestamp"=>"current_timestamp",);var$editFunctions=array("number|float|double"=>"+/-","date|timestamp"=>"+ interval/- interval","char|clob"=>"||",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");var$functions=array("length","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");function
__construct(Db$f){parent::__construct($f);$this->types=array('Numbers'=>array("number"=>38,"binary_float"=>12,"binary_double"=>21),'Date and time'=>array("date"=>10,"timestamp"=>29,"interval year"=>12,"interval day"=>28),'Strings'=>array("char"=>2000,"varchar2"=>4000,"nchar"=>2000,"nvarchar2"=>4000,"clob"=>4294967295,"nclob"=>4294967295),'Binary'=>array("raw"=>2000,"long raw"=>2147483648,"blob"=>4294967295,"bfile"=>4294967296),);}function
begin(){return
true;}function
insertUpdate($R,array$L,array$G){foreach($L
as$O){$Ri=array();$Z=array();foreach($O
as$x=>$X){$Ri[]="$x = $X";if(isset($G[idf_unescape($x)]))$Z[]="$x = $X";}if(!(($Z&&queries("UPDATE ".table($R)." SET ".implode(", ",$Ri)." WHERE ".implode(" AND ",$Z))&&connection()->affected_rows)||queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($O)).") VALUES (".implode(", ",$O).")")))return
false;}return
true;}function
hasCStyleEscapes(){return
true;}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return
idf_escape($u);}function
get_databases($ad){return
get_vals("SELECT DISTINCT tablespace_name FROM (
SELECT tablespace_name FROM user_tablespaces
UNION SELECT tablespace_name FROM all_tables WHERE tablespace_name IS NOT NULL
)
ORDER BY 1");}function
limit($H,$Z,$z,$D=0,$ph=" "){return($D?" * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $H$Z) t WHERE rownum <= ".($z+$D).") WHERE rnum > $D":($z?" * FROM (SELECT $H$Z) WHERE rownum <= ".($z+$D):" $H$Z"));}function
limit1($R,$H,$Z,$ph="\n"){return" $H$Z";}function
db_collation($j,$hb){return
get_val("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");}function
logged_user(){return
get_val("SELECT USER FROM DUAL");}function
get_current_db(){$j=connection()->_current_db?:DB;unset(connection()->_current_db);return$j;}function
where_owner($tg,$Vf="owner"){if(!$_GET["ns"])return'';return"$tg$Vf = sys_context('USERENV', 'CURRENT_SCHEMA')";}function
views_table($e){$Vf=where_owner('');return"(SELECT $e FROM all_views WHERE ".($Vf?:"rownum < 0").")";}function
tables_list(){$gj=views_table("view_name");$Vf=where_owner(" AND ");return
get_key_vals("SELECT table_name, 'table' FROM all_tables WHERE tablespace_name = ".q(DB)."$Vf
UNION SELECT view_name, 'view' FROM $gj
ORDER BY 1");}function
count_tables($i){$J=array();foreach($i
as$j)$J[$j]=get_val("SELECT COUNT(*) FROM all_tables WHERE tablespace_name = ".q($j));return$J;}function
table_status($C=""){$J=array();$ih=q($C);$j=get_current_db();$gj=views_table("view_name");$Vf=where_owner(" AND ");foreach(get_rows('SELECT table_name "Name", \'table\' "Engine", avg_row_len * num_rows "Data_length", num_rows "Rows" FROM all_tables WHERE tablespace_name = '.q($j).$Vf.($C!=""?" AND table_name = $ih":"")."
UNION SELECT view_name, 'view', 0, 0 FROM $gj".($C!=""?" WHERE view_name = $ih":"")."
ORDER BY 1")as$K)$J[$K["Name"]]=$K;return$J;}function
is_view($S){return$S["Engine"]=="view";}function
fk_support($S){return
true;}function
fields($R){$J=array();$Vf=where_owner(" AND ");foreach(get_rows("SELECT * FROM all_tab_columns WHERE table_name = ".q($R)."$Vf ORDER BY column_id")as$K){$U=$K["DATA_TYPE"];$y="$K[DATA_PRECISION],$K[DATA_SCALE]";if($y==",")$y=$K["CHAR_COL_DECL_LENGTH"];$J[$K["COLUMN_NAME"]]=array("field"=>$K["COLUMN_NAME"],"full_type"=>$U.($y?"($y)":""),"type"=>strtolower($U),"length"=>$y,"default"=>$K["DATA_DEFAULT"],"null"=>($K["NULLABLE"]=="Y"),"privileges"=>array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1),);}return$J;}function
indexes($R,$g=null){$J=array();$Vf=where_owner(" AND ","aic.table_owner");foreach(get_rows("SELECT aic.*, ac.constraint_type, atc.data_default
FROM all_ind_columns aic
LEFT JOIN all_constraints ac ON aic.index_name = ac.constraint_name AND aic.table_name = ac.table_name AND aic.index_owner = ac.owner
LEFT JOIN all_tab_cols atc ON aic.column_name = atc.column_name AND aic.table_name = atc.table_name AND aic.index_owner = atc.owner
WHERE aic.table_name = ".q($R)."$Vf
ORDER BY ac.constraint_type, aic.column_position",$g)as$K){$Rd=$K["INDEX_NAME"];$jb=$K["DATA_DEFAULT"];$jb=($jb?trim($jb,'"'):$K["COLUMN_NAME"]);$J[$Rd]["type"]=($K["CONSTRAINT_TYPE"]=="P"?"PRIMARY":($K["CONSTRAINT_TYPE"]=="U"?"UNIQUE":"INDEX"));$J[$Rd]["columns"][]=$jb;$J[$Rd]["lengths"][]=($K["CHAR_LENGTH"]&&$K["CHAR_LENGTH"]!=$K["COLUMN_LENGTH"]?$K["CHAR_LENGTH"]:null);$J[$Rd]["descs"][]=($K["DESCEND"]&&$K["DESCEND"]=="DESC"?'1':null);}return$J;}function
view($C){$gj=views_table("view_name, text");$L=get_rows('SELECT text "select" FROM '.$gj.' WHERE view_name = '.q($C));return
reset($L);}function
collations(){return
array();}function
information_schema($j){return
get_schema()=="INFORMATION_SCHEMA";}function
error(){return
h(connection()->error);}function
explain($f,$H){$f->query("EXPLAIN PLAN FOR $H");return$f->query("SELECT * FROM plan_table");}function
found_rows($S,$Z){}function
auto_increment(){return"";}function
alter_table($R,$C,$n,$cd,$mb,$rc,$c,$_a,$eg){$b=$bc=array();$Of=($R?fields($R):array());foreach($n
as$m){$X=$m[1];if($X&&$m[0]!=""&&idf_escape($m[0])!=$X[0])queries("ALTER TABLE ".table($R)." RENAME COLUMN ".idf_escape($m[0])." TO $X[0]");$Nf=$Of[$m[0]];if($X&&$Nf){$sf=process_field($Nf,$Nf);if($X[2]==$sf[2])$X[2]="";}if($X)$b[]=($R!=""?($m[0]!=""?"MODIFY (":"ADD ("):"  ").implode($X).($R!=""?")":"");else$bc[]=idf_escape($m[0]);}if($R=="")return
queries("CREATE TABLE ".table($C)." (\n".implode(",\n",$b)."\n)");return(!$b||queries("ALTER TABLE ".table($R)."\n".implode("\n",$b)))&&(!$bc||queries("ALTER TABLE ".table($R)." DROP (".implode(", ",$bc).")"))&&($R==$C||queries("ALTER TABLE ".table($R)." RENAME TO ".table($C)));}function
alter_indexes($R,$b){$bc=array();$Dg=array();foreach($b
as$X){if($X[0]!="INDEX"){$X[2]=preg_replace('~ DESC$~','',$X[2]);$h=($X[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($X[1]):"\nADD".($X[1]!=""?" CONSTRAINT ".idf_escape($X[1]):"")." $X[0] ".($X[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$X[2]).")");array_unshift($Dg,"ALTER TABLE ".table($R).$h);}elseif($X[2]=="DROP")$bc[]=idf_escape($X[1]);else$Dg[]="CREATE INDEX ".idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R)." (".implode(", ",$X[2]).")";}if($bc)array_unshift($Dg,"DROP INDEX ".implode(", ",$bc));foreach($Dg
as$H){if(!queries($H))return
false;}return
true;}function
foreign_keys($R){$J=array();$H="SELECT c_list.CONSTRAINT_NAME as NAME,
c_src.COLUMN_NAME as SRC_COLUMN,
c_dest.OWNER as DEST_DB,
c_dest.TABLE_NAME as DEST_TABLE,
c_dest.COLUMN_NAME as DEST_COLUMN,
c_list.DELETE_RULE as ON_DELETE
FROM ALL_CONSTRAINTS c_list, ALL_CONS_COLUMNS c_src, ALL_CONS_COLUMNS c_dest
WHERE c_list.CONSTRAINT_NAME = c_src.CONSTRAINT_NAME
AND c_list.R_CONSTRAINT_NAME = c_dest.CONSTRAINT_NAME
AND c_list.CONSTRAINT_TYPE = 'R'
AND c_src.TABLE_NAME = ".q($R);foreach(get_rows($H)as$K)$J[$K['NAME']]=array("db"=>$K['DEST_DB'],"table"=>$K['DEST_TABLE'],"source"=>array($K['SRC_COLUMN']),"target"=>array($K['DEST_COLUMN']),"on_delete"=>$K['ON_DELETE'],"on_update"=>null,);return$J;}function
truncate_tables($T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views($hj){return
apply_queries("DROP VIEW",$hj);}function
drop_tables($T){return
apply_queries("DROP TABLE",$T);}function
last_id($I){return
0;}function
schemas(){$J=get_vals("SELECT DISTINCT owner FROM dba_segments WHERE owner IN (SELECT username FROM dba_users WHERE default_tablespace NOT IN ('SYSTEM','SYSAUX')) ORDER BY 1");return($J?:get_vals("SELECT DISTINCT owner FROM all_tables WHERE tablespace_name = ".q(DB)." ORDER BY 1"));}function
get_schema(){return
get_val("SELECT sys_context('USERENV', 'SESSION_USER') FROM dual");}function
set_schema($hh,$g=null){if(!$g)$g=connection();return$g->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($hh));}function
show_variables(){return
get_rows('SELECT name, display_value FROM v$parameter');}function
show_status(){$J=array();$L=get_rows('SELECT * FROM v$instance');foreach(reset($L)as$x=>$X)$J[]=array($x,$X);return$J;}function
process_list(){return
get_rows('SELECT
	sess.process AS "process",
	sess.username AS "user",
	sess.schemaname AS "schema",
	sess.status AS "status",
	sess.wait_class AS "wait_class",
	sess.seconds_in_wait AS "seconds_in_wait",
	sql.sql_text AS "sql_text",
	sess.machine AS "machine",
	sess.port AS "port"
FROM v$session sess LEFT OUTER JOIN v$sql sql
ON sql.sql_id = sess.sql_id
WHERE sess.type = \'USER\'
ORDER BY PROCESS
');}function
convert_field($m){}function
unconvert_field($m,$J){return$J;}function
support($Oc){return
preg_match('~^(columns|database|drop_col|indexes|descidx|processlist|scheme|sql|status|table|variables|view)$~',$Oc);}}add_driver("mssql","MS SQL");if(isset($_GET["mssql"])){define('Adminer\DRIVER',"mssql");if(extension_loaded("sqlsrv")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="sqlsrv";private$link,$result;private
function
get_error(){$this->error="";foreach(sqlsrv_errors()as$l){$this->errno=$l["code"];$this->error
.="$l[message]\n";}$this->error=rtrim($this->error);}function
attach($N,$V,$F){$sb=array("UID"=>$V,"PWD"=>$F,"CharacterSet"=>"UTF-8");$Kh=adminer()->connectSsl();if(isset($Kh["Encrypt"]))$sb["Encrypt"]=$Kh["Encrypt"];if(isset($Kh["TrustServerCertificate"]))$sb["TrustServerCertificate"]=$Kh["TrustServerCertificate"];$j=adminer()->database();if($j!="")$sb["Database"]=$j;$this->link=@sqlsrv_connect(preg_replace('~:~',',',$N),$sb);if($this->link){$Vd=sqlsrv_server_info($this->link);$this->server_info=$Vd['SQLServerVersion'];}else$this->get_error();return($this->link?'':$this->error);}function
quote($Q){$Ki=strlen($Q)!=strlen(utf8_decode($Q));return($Ki?"N":"")."'".str_replace("'","''",$Q)."'";}function
select_db($Jb){return$this->query(use_sql($Jb));}function
query($H,$Ji=false){$I=sqlsrv_query($this->link,$H);$this->error="";if(!$I){$this->get_error();return
false;}return$this->store_result($I);}function
multi_query($H){$this->result=sqlsrv_query($this->link,$H);$this->error="";if(!$this->result){$this->get_error();return
false;}return
true;}function
store_result($I=null){if(!$I)$I=$this->result;if(!$I)return
false;if(sqlsrv_field_metadata($I))return
new
Result($I);$this->affected_rows=sqlsrv_rows_affected($I);return
true;}function
next_result(){return$this->result?!!sqlsrv_next_result($this->result):false;}}class
Result{var$num_rows;private$result,$offset=0,$fields;function
__construct($I){$this->result=$I;}private
function
convert($K){foreach((array)$K
as$x=>$X){if(is_a($X,'DateTime'))$K[$x]=$X->format("Y-m-d H:i:s");}return$K;}function
fetch_assoc(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_ASSOC));}function
fetch_row(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_NUMERIC));}function
fetch_field(){if(!$this->fields)$this->fields=sqlsrv_field_metadata($this->result);$m=$this->fields[$this->offset++];$J=new
\stdClass;$J->name=$m["Name"];$J->type=($m["Type"]==1?254:15);$J->charsetnr=0;return$J;}function
seek($D){for($s=0;$s<$D;$s++)sqlsrv_fetch($this->result);}function
__destruct(){sqlsrv_free_stmt($this->result);}}function
last_id($I){return
get_val("SELECT SCOPE_IDENTITY()");}function
explain($f,$H){$f->query("SET SHOWPLAN_ALL ON");$J=$f->query($H);$f->query("SET SHOWPLAN_ALL OFF");return$J;}}else{abstract
class
MssqlDb
extends
PdoDb{function
select_db($Jb){return$this->query(use_sql($Jb));}function
lastInsertId(){return$this->pdo->lastInsertId();}}function
last_id($I){return
connection()->lastInsertId();}function
explain($f,$H){}if(extension_loaded("pdo_sqlsrv")){class
Db
extends
MssqlDb{var$extension="PDO_SQLSRV";function
attach($N,$V,$F){return$this->dsn("sqlsrv:Server=".str_replace(":",",",$N),$V,$F);}}}elseif(extension_loaded("pdo_dblib")){class
Db
extends
MssqlDb{var$extension="PDO_DBLIB";function
attach($N,$V,$F){return$this->dsn("dblib:charset=utf8;host=".str_replace(":",";unix_socket=",preg_replace('~:(\d)~',';port=\1',$N)),$V,$F);}}}}class
Driver
extends
SqlDriver{static$Jc=array("SQLSRV","PDO_SQLSRV","PDO_DBLIB");static$le="mssql";var$insertFunctions=array("date|time"=>"getdate");var$editFunctions=array("int|decimal|real|float|money|datetime"=>"+/-","char|text"=>"+",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");var$functions=array("len","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$generated=array("PERSISTED","VIRTUAL");var$onActions="NO ACTION|CASCADE|SET NULL|SET DEFAULT";static
function
connect($N,$V,$F){if($N=="")$N="localhost:1433";return
parent::connect($N,$V,$F);}function
__construct(Db$f){parent::__construct($f);$this->types=array('Numbers'=>array("tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20),'Date and time'=>array("date"=>10,"smalldatetime"=>19,"datetime"=>19,"datetime2"=>19,"time"=>8,"datetimeoffset"=>10),'Strings'=>array("char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823),'Binary'=>array("binary"=>8000,"varbinary"=>8000,"image"=>2147483647),);}function
insertUpdate($R,array$L,array$G){$n=fields($R);$Ri=array();$Z=array();$O=reset($L);$e="c".implode(", c",range(1,count($O)));$Oa=0;$Zd=array();foreach($O
as$x=>$X){$Oa++;$C=idf_unescape($x);if(!$n[$C]["auto_increment"])$Zd[$x]="c$Oa";if(isset($G[$C]))$Z[]="$x = c$Oa";else$Ri[]="$x = c$Oa";}$cj=array();foreach($L
as$O)$cj[]="(".implode(", ",$O).")";if($Z){$Kd=queries("SET IDENTITY_INSERT ".table($R)." ON");$J=queries("MERGE ".table($R)." USING (VALUES\n\t".implode(",\n\t",$cj)."\n) AS source ($e) ON ".implode(" AND ",$Z).($Ri?"\nWHEN MATCHED THEN UPDATE SET ".implode(", ",$Ri):"")."\nWHEN NOT MATCHED THEN INSERT (".implode(", ",array_keys($Kd?$O:$Zd)).") VALUES (".($Kd?$e:implode(", ",$Zd)).");");if($Kd)queries("SET IDENTITY_INSERT ".table($R)." OFF");}else$J=queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($O)).") VALUES\n".implode(",\n",$cj));return$J;}function
begin(){return
queries("BEGIN TRANSACTION");}function
tableHelp($C,$je=false){$Ae=array("sys"=>"catalog-views/sys-","INFORMATION_SCHEMA"=>"information-schema-views/",);$_=$Ae[get_schema()];if($_)return"relational-databases/system-$_".preg_replace('~_~','-',strtolower($C))."-transact-sql";}}function
idf_escape($u){return"[".str_replace("]","]]",$u)."]";}function
table($u){return($_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($u);}function
get_databases($ad){return
get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");}function
limit($H,$Z,$z,$D=0,$ph=" "){return($z?" TOP (".($z+$D).")":"")." $H$Z";}function
limit1($R,$H,$Z,$ph="\n"){return
limit($H,$Z,1,0,$ph);}function
db_collation($j,$hb){return
get_val("SELECT collation_name FROM sys.databases WHERE name = ".q($j));}function
logged_user(){return
get_val("SELECT SUSER_NAME()");}function
tables_list(){return
get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");}function
count_tables($i){$J=array();foreach($i
as$j){connection()->select_db($j);$J[$j]=get_val("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");}return$J;}function
table_status($C=""){$J=array();foreach(get_rows("SELECT ao.name AS Name, ao.type_desc AS Engine, (SELECT value FROM fn_listextendedproperty(default, 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ".($C!=""?"AND name = ".q($C):"ORDER BY name"))as$K)$J[$K["Name"]]=$K;return$J;}function
is_view($S){return$S["Engine"]=="VIEW";}function
fk_support($S){return
true;}function
fields($R){$ob=get_key_vals("SELECT objname, cast(value as varchar(max)) FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', ".q(get_schema()).", 'table', ".q($R).", 'column', NULL)");$J=array();$Wh=get_val("SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') AND name = ".q($R));foreach(get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name, t.name type, d.definition [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = ".q($Wh))as$K){$U=$K["type"];$y=(preg_match("~char|binary~",$U)?intval($K["max_length"])/($U[0]=='n'?2:1):($U=="decimal"?"$K[precision],$K[scale]":""));$J[$K["name"]]=array("field"=>$K["name"],"full_type"=>$U.($y?"($y)":""),"type"=>$U,"length"=>$y,"default"=>(preg_match("~^\('(.*)'\)$~",$K["default"],$B)?str_replace("''","'",$B[1]):$K["default"]),"default_constraint"=>$K["default_constraint"],"null"=>$K["is_nullable"],"auto_increment"=>$K["is_identity"],"collation"=>$K["collation_name"],"privileges"=>array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1),"primary"=>$K["is_primary_key"],"comment"=>$ob[$K["name"]],);}foreach(get_rows("SELECT * FROM sys.computed_columns WHERE object_id = ".q($Wh))as$K){$J[$K["name"]]["generated"]=($K["is_persisted"]?"PERSISTED":"VIRTUAL");$J[$K["name"]]["default"]=$K["definition"];}return$J;}function
indexes($R,$g=null){$J=array();foreach(get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = ".q($R),$g)as$K){$C=$K["name"];$J[$C]["type"]=($K["is_primary_key"]?"PRIMARY":($K["is_unique"]?"UNIQUE":"INDEX"));$J[$C]["lengths"]=array();$J[$C]["columns"][$K["key_ordinal"]]=$K["column_name"];$J[$C]["descs"][$K["key_ordinal"]]=($K["is_descending_key"]?'1':null);}return$J;}function
view($C){return
array("select"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',get_val("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = ".q($C))));}function
collations(){$J=array();foreach(get_vals("SELECT name FROM fn_helpcollations()")as$c)$J[preg_replace('~_.*~','',$c)][]=$c;return$J;}function
information_schema($j){return
get_schema()=="INFORMATION_SCHEMA";}function
error(){return
nl_br(h(preg_replace('~^(\[[^]]*])+~m','',connection()->error)));}function
create_database($j,$c){return
queries("CREATE DATABASE ".idf_escape($j).(preg_match('~^[a-z0-9_]+$~i',$c)?" COLLATE $c":""));}function
drop_databases($i){return
queries("DROP DATABASE ".implode(", ",array_map('Adminer\idf_escape',$i)));}function
rename_database($C,$c){if(preg_match('~^[a-z0-9_]+$~i',$c))queries("ALTER DATABASE ".idf_escape(DB)." COLLATE $c");queries("ALTER DATABASE ".idf_escape(DB)." MODIFY NAME = ".idf_escape($C));return
true;}function
auto_increment(){return" IDENTITY".($_POST["Auto_increment"]!=""?"(".number($_POST["Auto_increment"]).",1)":"")." PRIMARY KEY";}function
alter_table($R,$C,$n,$cd,$mb,$rc,$c,$_a,$eg){$b=array();$ob=array();$Of=fields($R);foreach($n
as$m){$d=idf_escape($m[0]);$X=$m[1];if(!$X)$b["DROP"][]=" COLUMN $d";else{$X[1]=preg_replace("~( COLLATE )'(\\w+)'~",'\1\2',$X[1]);$ob[$m[0]]=$X[5];unset($X[5]);if(preg_match('~ AS ~',$X[3]))unset($X[1],$X[2]);if($m[0]=="")$b["ADD"][]="\n  ".implode("",$X).($R==""?substr($cd[$X[0]],16+strlen($X[0])):"");else{$k=$X[3];unset($X[3]);unset($X[6]);if($d!=$X[0])queries("EXEC sp_rename ".q(table($R).".$d").", ".q(idf_unescape($X[0])).", 'COLUMN'");$b["ALTER COLUMN ".implode("",$X)][]="";$Nf=$Of[$m[0]];if(default_value($Nf)!=$k){if($Nf["default"]!==null)$b["DROP"][]=" ".idf_escape($Nf["default_constraint"]);if($k)$b["ADD"][]="\n $k FOR $d";}}}}if($R=="")return
queries("CREATE TABLE ".table($C)." (".implode(",",(array)$b["ADD"])."\n)");if($R!=$C)queries("EXEC sp_rename ".q(table($R)).", ".q($C));if($cd)$b[""]=$cd;foreach($b
as$x=>$X){if(!queries("ALTER TABLE ".table($C)." $x".implode(",",$X)))return
false;}foreach($ob
as$x=>$X){$mb=substr($X,9);queries("EXEC sp_dropextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($C).", @level2type = N'Column', @level2name = ".q($x));queries("EXEC sp_addextendedproperty
@name = N'MS_Description',
@value = $mb,
@level0type = N'Schema',
@level0name = ".q(get_schema()).",
@level1type = N'Table',
@level1name = ".q($C).",
@level2type = N'Column',
@level2name = ".q($x));}return
true;}function
alter_indexes($R,$b){$v=array();$bc=array();foreach($b
as$X){if($X[2]=="DROP"){if($X[0]=="PRIMARY")$bc[]=idf_escape($X[1]);else$v[]=idf_escape($X[1])." ON ".table($R);}elseif(!queries(($X[0]!="PRIMARY"?"CREATE $X[0] ".($X[0]!="INDEX"?"INDEX ":"").idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R):"ALTER TABLE ".table($R)." ADD PRIMARY KEY")." (".implode(", ",$X[2]).")"))return
false;}return(!$v||queries("DROP INDEX ".implode(", ",$v)))&&(!$bc||queries("ALTER TABLE ".table($R)." DROP ".implode(", ",$bc)));}function
found_rows($S,$Z){}function
foreign_keys($R){$J=array();$zf=array("CASCADE","NO ACTION","SET NULL","SET DEFAULT");foreach(get_rows("EXEC sp_fkeys @fktable_name = ".q($R).", @fktable_owner = ".q(get_schema()))as$K){$p=&$J[$K["FK_NAME"]];$p["db"]=$K["PKTABLE_QUALIFIER"];$p["ns"]=$K["PKTABLE_OWNER"];$p["table"]=$K["PKTABLE_NAME"];$p["on_update"]=$zf[$K["UPDATE_RULE"]];$p["on_delete"]=$zf[$K["DELETE_RULE"]];$p["source"][]=$K["FKCOLUMN_NAME"];$p["target"][]=$K["PKCOLUMN_NAME"];}return$J;}function
truncate_tables($T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views($hj){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$hj)));}function
drop_tables($T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables($T,$hj,$fi){return
apply_queries("ALTER SCHEMA ".idf_escape($fi)." TRANSFER",array_merge($T,$hj));}function
trigger($C,$R){if($C=="")return
array();$L=get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = ".q($C));$J=reset($L);if($J)$J["Statement"]=preg_replace('~^.+\s+AS\s+~isU','',$J["text"]);return$J;}function
triggers($R){$J=array();foreach(get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = ".q($R))as$K)$J[$K["name"]]=array($K["Timing"],$K["Event"]);return$J;}function
trigger_options(){return
array("Timing"=>array("AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("AS"),);}function
schemas(){return
get_vals("SELECT name FROM sys.schemas");}function
get_schema(){if($_GET["ns"]!="")return$_GET["ns"];return
get_val("SELECT SCHEMA_NAME()");}function
set_schema($fh){$_GET["ns"]=$fh;return
true;}function
create_sql($R,$_a,$Ph){if(is_view(table_status1($R))){$gj=view($R);return"CREATE VIEW ".table($R)." AS $gj[select]";}$n=array();$G=false;foreach(fields($R)as$C=>$m){$X=process_field($m,$m);if($X[6])$G=true;$n[]=implode("",$X);}foreach(indexes($R)as$C=>$v){if(!$G||$v["type"]!="PRIMARY"){$e=array();foreach($v["columns"]as$x=>$X)$e[]=idf_escape($X).($v["descs"][$x]?" DESC":"");$C=idf_escape($C);$n[]=($v["type"]=="INDEX"?"INDEX $C":"CONSTRAINT $C ".($v["type"]=="UNIQUE"?"UNIQUE":"PRIMARY KEY"))." (".implode(", ",$e).")";}}foreach(driver()->checkConstraints($R)as$C=>$Va)$n[]="CONSTRAINT ".idf_escape($C)." CHECK ($Va)";return"CREATE TABLE ".table($R)." (\n\t".implode(",\n\t",$n)."\n)";}function
foreign_keys_sql($R){$n=array();foreach(foreign_keys($R)as$cd)$n[]=ltrim(format_foreign_key($cd));return($n?"ALTER TABLE ".table($R)." ADD\n\t".implode(",\n\t",$n).";\n\n":"");}function
truncate_sql($R){return"TRUNCATE TABLE ".table($R);}function
use_sql($Jb){return"USE ".idf_escape($Jb);}function
trigger_sql($R){$J="";foreach(triggers($R)as$C=>$Ci)$J
.=create_trigger(" ON ".table($R),trigger($C,$R)).";";return$J;}function
convert_field($m){}function
unconvert_field($m,$J){return$J;}function
support($Oc){return
preg_match('~^(check|comment|columns|database|drop_col|dump|indexes|descidx|scheme|sql|table|trigger|view|view_trigger)$~',$Oc);}}class
Adminer{static$be;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($h=false){return
password_file($h);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
serverName($N){return
h($N);}function
database(){return
DB;}function
databases($ad=true){return
get_databases($ad);}function
operators(){return
driver()->operators;}function
schemas(){return
schemas();}function
queryTimeout(){return
2;}function
headers(){}function
csp(array$Cb){return$Cb;}function
head($Gb=null){return
true;}function
css(){$J=array();foreach(array("","-dark")as$Ze){$o="adminer$Ze.css";if(file_exists($o))$J[]="$o?v=".crc32(file_get_contents($o));}return$J;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.'System'.'<td>',html_select("auth[driver]",SqlDriver::$ac,DRIVER,"loginDriver(this);")),adminer()->loginFormField('server','<tr><th>'.'Server'.'<td>','<input name="auth[server]" value="'.h(SERVER).'" title="hostname[:port]" placeholder="localhost" autocapitalize="off">'),adminer()->loginFormField('username','<tr><th>'.'Username'.'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'.script("const authDriver = qs('#username').form['auth[driver]']; authDriver && authDriver.onchange();")),adminer()->loginFormField('password','<tr><th>'.'Password'.'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.'Database'.'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".'Login'."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],'Permanent login')."\n";}function
loginFormField($C,$Ad,$Y){return$Ad.$Y."\n";}function
login($Be,$F){if($F=="")return
sprintf('Adminer does not support accessing a database without a password, <a href="https://www.adminer.org/en/password/"%s>more information</a>.',target_blank());return
true;}function
tableName(array$Vh){return
h($Vh["Name"]);}function
fieldName(array$m,$Hf=0){$U=$m["full_type"];$mb=$m["comment"];return'<span title="'.h($U.($mb!=""?($U?": ":"").$mb:'')).'">'.h($m["field"]).'</span>';}function
selectLinks(array$Vh,$O=""){echo'<p class="links">';$Ae=array("select"=>'Select data');if(support("table")||support("indexes"))$Ae["table"]='Show structure';$je=false;if(support("table")){$je=is_view($Vh);if($je)$Ae["view"]='Alter view';else$Ae["create"]='Alter table';}if($O!==null)$Ae["edit"]='New item';$C=$Vh["Name"];foreach($Ae
as$x=>$X)echo" <a href='".h(ME)."$x=".urlencode($C).($x=="edit"?$O:"")."'".bold(isset($_GET[$x])).">$X</a>";echo
doc_link(array(JUSH=>driver()->tableHelp($C,$je)),"?"),"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$Uh){return
array();}function
backwardKeysPrint(array$Da,array$K){}function
selectQuery($H,$Lh,$Mc=false){$J="</p>\n";if(!$Mc&&($kj=driver()->warnings())){$t="warnings";$J=", <a href='#$t'>".'Warnings'."</a>".script("qsl('a').onclick = partial(toggle, '$t');","")."$J<div id='$t' class='hidden'>\n$kj</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>(".format_time($Lh).")</span>".(support("sql")?" <a href='".h(ME)."sql=".urlencode($H)."'>".'Edit'."</a>":"").$J;}function
sqlCommandQuery($H){return
shorten_utf8(trim($H),1000);}function
sqlPrintAfter(){}function
rowDescription($R){return"";}function
rowDescriptions(array$L,array$dd){return$L;}function
selectLink($X,array$m){}function
selectVal($X,$_,array$m,$Rf){$J=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$m["type"])&&!preg_match("~var~",$m["type"])?"<code>$X</code>":(preg_match('~json~',$m["type"])?"<code class='jush-js'>$X</code>":$X)));if(preg_match('~blob|bytea|raw|file~',$m["type"])&&!is_utf8($X))$J="<i>".lang(array('%d byte','%d bytes'),strlen($Rf))."</i>";return($_?"<a href='".h($_)."'".(is_url($_)?target_blank():"").">$J</a>":$J);}function
editVal($X,array$m){return$X;}function
tableStructurePrint(array$n,$Vh=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".'Column'."<td>".'Type'.(support("comment")?"<td>".'Comment':"")."</thead>\n";$Oh=driver()->structuredTypes();foreach($n
as$m){echo"<tr><th>".h($m["field"]);$U=h($m["full_type"]);$c=h($m["collation"]);echo"<td><span title='$c'>".(in_array($U,(array)$Oh['User types'])?"<a href='".h(ME.'type='.urlencode($U))."'>$U</a>":$U.($c&&isset($Vh["Collation"])&&$c!=$Vh["Collation"]?" $c":""))."</span>",($m["null"]?" <i>NULL</i>":""),($m["auto_increment"]?" <i>".'Auto Increment'."</i>":"");$k=h($m["default"]);echo(isset($m["default"])?" <span title='".'Default value'."'>[<b>".($m["generated"]?"<code class='jush-".JUSH."'>$k</code>":$k)."</b>]</span>":""),(support("comment")?"<td>".h($m["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$w){echo"<table>\n";foreach($w
as$C=>$v){ksort($v["columns"]);$wg=array();foreach($v["columns"]as$x=>$X)$wg[]="<i>".h($X)."</i>".($v["lengths"][$x]?"(".$v["lengths"][$x].")":"").($v["descs"][$x]?" DESC":"");echo"<tr title='".h($C)."'><th>$v[type]<td>".implode(", ",$wg)."\n";}echo"</table>\n";}function
selectColumnsPrint(array$M,array$e){print_fieldset("select",'Select',$M);$s=0;$M[""]=array();foreach($M
as$x=>$X){$X=idx($_GET["columns"],$x,array());$d=select_input(" name='columns[$s][col]'",$e,$X["col"],($x!==""?"selectFieldChange":"selectAddRow"));echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$s][fun]",array(-1=>"")+array_filter(array('Functions'=>driver()->functions,'Aggregation'=>driver()->grouping)),$X["fun"]).on_help("event.target.value && event.target.value.replace(/ |\$/, '(') + ')'",1).script("qsl('select').onchange = function () { helpClose();".($x!==""?"":" qsl('select, input', this.parentNode).onchange();")." };","")."($d)":$d)."</div>\n";$s++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$e,array$w){print_fieldset("search",'Search',$Z);foreach($w
as$s=>$v){if($v["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$v["columns"]))."</i>) AGAINST"," <input type='search' name='fulltext[$s]' value='".h($_GET["fulltext"][$s])."'>",script("qsl('input').oninput = selectFieldChange;",""),checkbox("boolean[$s]",1,isset($_GET["boolean"][$s]),"BOOL"),"</div>\n";}$Sa="this.parentNode.firstChild.onchange();";foreach(array_merge((array)$_GET["where"],array(array()))as$s=>$X){if(!$X||("$X[col]$X[val]"!=""&&in_array($X["op"],adminer()->operators())))echo"<div>".select_input(" name='where[$s][col]'",$e,$X["col"],($X?"selectFieldChange":"selectAddRow"),"(".'anywhere'.")"),html_select("where[$s][op]",adminer()->operators(),$X["op"],$Sa),"<input type='search' name='where[$s][val]' value='".h($X["val"])."'>",script("mixin(qsl('input'), {oninput: function () { $Sa }, onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});",""),"</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$Hf,array$e,array$w){print_fieldset("sort",'Sort',$Hf);$s=0;foreach((array)$_GET["order"]as$x=>$X){if($X!=""){echo"<div>".select_input(" name='order[$s]'",$e,$X,"selectFieldChange"),checkbox("desc[$s]",1,isset($_GET["desc"][$x]),'descending')."</div>\n";$s++;}}echo"<div>".select_input(" name='order[$s]'",$e,"","selectAddRow"),checkbox("desc[$s]",1,false,'descending')."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($z){echo"<fieldset><legend>".'Limit'."</legend><div>","<input type='number' name='limit' class='size' value='".intval($z)."'>",script("qsl('input').oninput = selectFieldChange;",""),"</div></fieldset>\n";}function
selectLengthPrint($li){if($li!==null)echo"<fieldset><legend>".'Text length'."</legend><div>","<input type='number' name='text_length' class='size' value='".h($li)."'>","</div></fieldset>\n";}function
selectActionPrint(array$w){echo"<fieldset><legend>".'Action'."</legend><div>","<input type='submit' value='".'Select'."'>"," <span id='noindex' title='".'Full table scan'."'></span>","<script".nonce().">\n","const indexColumns = ";$e=array();foreach($w
as$v){$Fb=reset($v["columns"]);if($v["type"]!="FULLTEXT"&&$Fb)$e[$Fb]=1;}$e[""]=1;foreach($e
as$x=>$X)json_row($x);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$oc,array$e){}function
selectColumnsProcess(array$e,array$w){$M=array();$pd=array();foreach((array)$_GET["columns"]as$x=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$M[$x]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$pd[]=$M[$x];}}return
array($M,$pd);}function
selectSearchProcess(array$n,array$w){$J=array();foreach($w
as$s=>$v){if($v["type"]=="FULLTEXT"&&$_GET["fulltext"][$s]!="")$J[]="MATCH (".implode(", ",array_map('Adminer\idf_escape',$v["columns"])).") AGAINST (".q($_GET["fulltext"][$s]).(isset($_GET["boolean"][$s])?" IN BOOLEAN MODE":"").")";}foreach((array)$_GET["where"]as$x=>$X){if("$X[col]$X[val]"!=""&&in_array($X["op"],adminer()->operators())){$tg="";$pb=" $X[op]";if(preg_match('~IN$~',$X["op"])){$Od=process_length($X["val"]);$pb
.=" ".($Od!=""?$Od:"(NULL)");}elseif($X["op"]=="SQL")$pb=" $X[val]";elseif($X["op"]=="LIKE %%")$pb=" LIKE ".adminer()->processInput($n[$X["col"]],"%$X[val]%");elseif($X["op"]=="ILIKE %%")$pb=" ILIKE ".adminer()->processInput($n[$X["col"]],"%$X[val]%");elseif($X["op"]=="FIND_IN_SET"){$tg="$X[op](".q($X["val"]).", ";$pb=")";}elseif(!preg_match('~NULL$~',$X["op"]))$pb
.=" ".adminer()->processInput($n[$X["col"]],$X["val"]);if($X["col"]!="")$J[]=$tg.driver()->convertSearch(idf_escape($X["col"]),$X,$n[$X["col"]]).$pb;else{$ib=array();foreach($n
as$C=>$m){if(isset($m["privileges"]["where"])&&(preg_match('~^[-\d.'.(preg_match('~IN$~',$X["op"])?',':'').']+$~',$X["val"])||!preg_match('~'.number_type().'|bit~',$m["type"]))&&(!preg_match("~[\x80-\xFF]~",$X["val"])||preg_match('~char|text|enum|set~',$m["type"]))&&(!preg_match('~date|timestamp~',$m["type"])||preg_match('~^\d+-\d+-\d+~',$X["val"])))$ib[]=$tg.driver()->convertSearch(idf_escape($C),$X,$m).$pb;}$J[]=($ib?"(".implode(" OR ",$ib).")":"1 = 0");}}}return$J;}function
selectOrderProcess(array$n,array$w){$J=array();foreach((array)$_GET["order"]as$x=>$X){if($X!="")$J[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$x])?" DESC":"");}return$J;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$dd){return
false;}function
selectQueryBuild(array$M,array$Z,array$pd,array$Hf,$z,$E){return"";}function
messageQuery($H,$mi,$Mc=false){restart_session();$Cd=&get_session("queries");if(!idx($Cd,$_GET["db"]))$Cd[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\n…";$Cd[$_GET["db"]][]=array($H,time(),$mi);$Hh="sql-".count($Cd[$_GET["db"]]);$J="<a href='#$Hh' class='toggle'>".'SQL command'."</a>\n";if(!$Mc&&($kj=driver()->warnings())){$t="warnings-".count($Cd[$_GET["db"]]);$J="<a href='#$t' class='toggle'>".'Warnings'."</a>, $J<div id='$t' class='hidden'>\n$kj</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$Hh' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1000)."</code></pre>".($mi?" <span class='time'>($mi)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".urlencode(DB),"db=".urlencode($_GET["db"]),ME).'sql=&history='.(count($Cd[$_GET["db"]])-1)).'">'.'Edit'.'</a>':'').'</div>';}function
editRowPrint($R,array$n,$K,$Ri){}function
editFunctions(array$m){$J=($m["null"]?"NULL/":"");$Ri=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$x=>$kd){if(!$x||(!isset($_GET["call"])&&$Ri)){foreach($kd
as$ig=>$X){if(!$ig||preg_match("~$ig~",$m["type"]))$J
.="/$X";}}if($x&&$kd&&!preg_match('~set|blob|bytea|raw|file|bool~',$m["type"]))$J
.="/SQL";}if($m["auto_increment"]&&!$Ri)$J='Auto Increment';return
explode("/",$J);}function
editInput($R,array$m,$ya,$Y){if($m["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$ya value='-1' checked><i>".'original'."</i></label> ":"").($m["null"]?"<label><input type='radio'$ya value=''".($Y!==null||isset($_GET["select"])?"":" checked")."><i>NULL</i></label> ":"").enum_input("radio",$ya,$m,$Y,$Y===0?0:null);return"";}function
editHint($R,array$m,$Y){return"";}function
processInput(array$m,$Y,$r=""){if($r=="SQL")return$Y;$C=$m["field"];$J=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$r))$J="$r()";elseif(preg_match('~^current_(date|timestamp)$~',$r))$J=$r;elseif(preg_match('~^([+-]|\|\|)$~',$r))$J=idf_escape($C)." $r $J";elseif(preg_match('~^[+-] interval$~',$r))$J=idf_escape($C)." $r ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)?$Y:$J);elseif(preg_match('~^(addtime|subtime|concat)$~',$r))$J="$r(".idf_escape($C).", $J)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$r))$J="$r($J)";return
unconvert_field($m,$J);}function
dumpOutput(){$J=array('text'=>'open','file'=>'save');if(function_exists('gzencode'))$J['gz']='gzip';return$J;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpDatabase($j){}function
dumpTable($R,$Ph,$je=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Ph)dump_csv(array_keys(fields($R)));}else{if($je==2){$n=array();foreach(fields($R)as$C=>$m)$n[]=idf_escape($C)." $m[full_type]";$h="CREATE TABLE ".table($R)." (".implode(", ",$n).")";}else$h=create_sql($R,$_POST["auto_increment"],$Ph);set_utf8mb4($h);if($Ph&&$h){if($Ph=="DROP+CREATE"||$je==1)echo"DROP ".($je==2?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($je==1)$h=remove_definer($h);echo"$h;\n\n";}}}function
dumpData($R,$Ph,$H){if($Ph){$Je=(JUSH=="sqlite"?0:1048576);$n=array();$Ld=false;if($_POST["format"]=="sql"){if($Ph=="TRUNCATE+INSERT")echo
truncate_sql($R).";\n";$n=fields($R);if(JUSH=="mssql"){foreach($n
as$m){if($m["auto_increment"]){echo"SET IDENTITY_INSERT ".table($R)." ON;\n";$Ld=true;break;}}}}$I=connection()->query($H,1);if($I){$Zd="";$Na="";$oe=array();$ld=array();$Rh="";$Pc=($R!=''?'fetch_assoc':'fetch_row');while($K=$I->$Pc()){if(!$oe){$cj=array();foreach($K
as$X){$m=$I->fetch_field();if(idx($n[$m->name],'generated')){$ld[$m->name]=true;continue;}$oe[]=$m->name;$x=idf_escape($m->name);$cj[]="$x = VALUES($x)";}$Rh=($Ph=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$cj):"").";\n";}if($_POST["format"]!="sql"){if($Ph=="table"){dump_csv($oe);$Ph="INSERT";}dump_csv($K);}else{if(!$Zd)$Zd="INSERT INTO ".table($R)." (".implode(", ",array_map('Adminer\idf_escape',$oe)).") VALUES";foreach($K
as$x=>$X){if($ld[$x]){unset($K[$x]);continue;}$m=$n[$x];$K[$x]=($X!==null?unconvert_field($m,preg_match(number_type(),$m["type"])&&!preg_match('~\[~',$m["full_type"])&&is_numeric($X)?$X:q(($X===false?0:$X))):"NULL");}$dh=($Je?"\n":" ")."(".implode(",\t",$K).")";if(!$Na)$Na=$Zd.$dh;elseif(strlen($Na)+4+strlen($dh)+strlen($Rh)<$Je)$Na
.=",$dh";else{echo$Na.$Rh;$Na=$Zd.$dh;}}}if($Na)echo$Na.$Rh;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";if($Ld)echo"SET IDENTITY_INSERT ".table($R)." OFF;\n";}}function
dumpFilename($Jd){return
friendly_url($Jd!=""?$Jd:(SERVER!=""?SERVER:"localhost"));}function
dumpHeaders($Jd,$bf=false){$Uf=$_POST["output"];$Hc=(preg_match('~sql~',$_POST["format"])?"sql":($bf?"tar":"csv"));header("Content-Type: ".($Uf=="gz"?"application/x-gzip":($Hc=="tar"?"application/x-tar":($Hc=="sql"||$Uf!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($Uf=="gz"){ob_start(function($Q){return
gzencode($Q);},1e6);}return$Hc;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.'Alter database'."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?'Alter schema':'Create schema')."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.'Database schema'."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".'Privileges'."</a>\n":"");return
true;}function
navigation($Ye){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$jf=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$jf)<0?h($jf):"")."</a>","</span></h1>\n";if($Ye=="auth"){$Uf="";foreach((array)$_SESSION["pwds"]as$ej=>$uh){foreach($uh
as$N=>$Zi){$C=h(get_setting("vendor-$ej-$N")?:get_driver($ej));foreach($Zi
as$V=>$F){if($F!==null){$Mb=$_SESSION["db"][$ej][$N][$V];foreach(($Mb?array_keys($Mb):array(""))as$j)$Uf
.="<li><a href='".h(auth_url($ej,$N,$V,$j))."'>($C) ".h($V.($N!=""?"@".adminer()->serverName($N):"").($j!=""?" - $j":""))."</a>\n";}}}}if($Uf)echo"<ul id='logins'>\n$Uf</ul>\n".script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");}else{$T=array();if($_GET["ns"]!==""&&!$Ye&&DB!=""){connection()->select_db(DB);$T=table_status('',true);}adminer()->syntaxHighlighting($T);adminer()->databasesPrint($Ye);$ia=array();if(DB==""||!$Ye){if(support("sql")){$ia[]="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".'SQL command'."</a>";$ia[]="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".'Import'."</a>";}$ia[]="<a href='".h(ME)."dump=".urlencode(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".'Export'."</a>";}$Pd=$_GET["ns"]!==""&&!$Ye&&DB!="";if($Pd)$ia[]='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".'Create table'."</a>";echo($ia?"<p class='links'>\n".implode("\n",$ia)."\n":"");if($Pd){if($T)adminer()->tablesPrint($T);else
echo"<p class='message'>".'No tables.'."</p>\n";}}}function
syntaxHighlighting(array$T){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=5.1.1");if(support("sql")){echo"<script".nonce().">\n";if($T){$Ae=array();foreach($T
as$R=>$U)$Ae[]=preg_quote($R,'/');echo"var jushLinks = { ".JUSH.": [ '".js_escape(ME).(support("table")?"table=":"select=")."\$&', /\\b(".implode("|",$Ae).")\\b/g ] };\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";}echo"</script>\n";}echo
script("syntaxHighlighting('".preg_replace('~^(\d\.?\d).*~s','\1',connection()->server_info)."'".(connection()->flavor=='maria'?", 'maria'":(connection()->flavor=='cockroach'?", 'cockroach'":"")).");");}function
databasesPrint($Ye){$i=adminer()->databases();if(DB&&$i&&!in_array(DB,$i))array_unshift($i,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$Kb=script("mixin(qsl('select'), {onmousedown: dbMouseDown, onchange: dbChange});");echo"<span title='".'Database'."'>".'DB'.":</span> ".($i?html_select("db",array(""=>"")+$i,DB).$Kb:"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n"),"<input type='submit' value='".'Use'."'".($i?" class='hidden'":"").">\n";if(support("scheme")){if($Ye!="db"&&DB!=""&&connection()->select_db(DB)){echo"<br><span>".'Schema'.":</span> ".html_select("ns",array(""=>"")+adminer()->schemas(),$_GET["ns"]).$Kb;if($_GET["ns"]!="")set_schema($_GET["ns"]);}}foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
tablesPrint(array$T){echo"<ul id='tables'>".script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");foreach($T
as$R=>$P){$C=adminer()->tableName($P);if($C!="")echo'<li><a href="'.h(ME).'select='.urlencode($R).'"'.bold($_GET["select"]==$R||$_GET["edit"]==$R,"select")." title='".'Select data'."'>".'select'."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.urlencode($R).'"'.bold(in_array($R,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"])),(is_view($P)?"view":"structure"))." title='".'Show structure'."'>$C</a>":"<span>$C</span>")."\n";}echo"</ul>\n";}}class
Plugins{private
static$ta=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true);var$plugins;var$error='';private$hooks=array();function
__construct($ng){if($ng===null){$ng=array();$Ha="adminer-plugins";if(is_dir($Ha)){foreach(glob("$Ha/*.php")as$o)$Qd=include_once"./$o";}$Bd=" href='https://www.adminer.org/plugins/#use'".target_blank();if(file_exists("$Ha.php")){$Qd=include_once"./$Ha.php";if(is_array($Qd)){foreach($Qd
as$mg)$ng[get_class($mg)]=$mg;}else$this->error
.=sprintf('%s must <a%s>return an array</a>.',"<b>$Ha.php</b>",$Bd)."<br>";}foreach(get_declared_classes()as$cb){if(!$ng[$cb]&&preg_match('~^Adminer\w~i',$cb)){$Ng=new
\ReflectionClass($cb);$ub=$Ng->getConstructor();if($ub&&$ub->getNumberOfRequiredParameters())$this->error
.=sprintf('<a%s>Configure</a> %s in %s.',$Bd,"<b>$cb</b>","<b>$Ha.php</b>")."<br>";else$ng[$cb]=new$cb;}}}$this->plugins=$ng;$la=new
Adminer;$ng[]=$la;$Ng=new
\ReflectionObject($la);foreach($Ng->getMethods()as$We){foreach($ng
as$mg){$C=$We->getName();if(method_exists($mg,$C))$this->hooks[$C][]=$mg;}}}function
__call($C,array$Zf){$ua=array();foreach($Zf
as$x=>$X)$ua[]=&$Zf[$x];$J=null;foreach($this->hooks[$C]as$mg){$Y=call_user_func_array(array($mg,$C),$ua);if($Y!==null){if(!self::$ta[$C])return$Y;$J=$Y+(array)$J;}}return$J;}}if(function_exists('adminer_object'))Adminer::$be=adminer_object();elseif(is_dir("adminer-plugins")||file_exists("adminer-plugins.php"))Adminer::$be=new
Plugins(null);else
Adminer::$be=new
Adminer;SqlDriver::$ac=array("server"=>"MySQL / MariaDB")+SqlDriver::$ac;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\MySQLi{static$be;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach($N,$V,$F){mysqli_report(MYSQLI_REPORT_OFF);list($Fd,$og)=explode(":",$N,2);$Kh=adminer()->connectSsl();if($Kh)$this->ssl_set($Kh['key'],$Kh['cert'],$Kh['ca'],'','');$J=@$this->real_connect(($N!=""?$Fd:ini_get("mysqli.default_host")),($N.$V!=""?$V:ini_get("mysqli.default_user")),($N.$V.$F!=""?$F:ini_get("mysqli.default_pw")),null,(is_numeric($og)?intval($og):ini_get("mysqli.default_port")),(is_numeric($og)?$og:null),($Kh?($Kh['verify']!==false?2048:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,false);return($J?'':$this->error);}function
set_charset($Ua){if(parent::set_charset($Ua))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $Ua");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($Q){return"'".$this->escape_string($Q)."'";}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach($N,$V,$F){if(ini_bool("mysql.allow_local_infile"))return
sprintf('Disable %s or enable %s or %s extensions.',"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$this->link=@mysql_connect(($N!=""?$N:ini_get("mysql.default_host")),("$N$V"!=""?$V:ini_get("mysql.default_user")),("$N$V$F"!=""?$F:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($Ua){if(function_exists('mysql_set_charset')){if(mysql_set_charset($Ua,$this->link))return
true;mysql_set_charset('utf8',$this->link);}return$this->query("SET NAMES $Ua");}function
quote($Q){return"'".mysql_real_escape_string($Q,$this->link)."'";}function
select_db($Jb){return
mysql_select_db($Jb,$this->link);}function
query($H,$Ji=false){$I=@($Ji?mysql_unbuffered_query($H,$this->link):mysql_query($H,$this->link));$this->error="";if(!$I){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($I===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($I);}}class
Result{var$num_rows;private$result;private$offset=0;function
__construct($I){$this->result=$I;$this->num_rows=mysql_num_rows($I);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_field(){$J=mysql_fetch_field($this->result,$this->offset++);$J->orgtable=$J->table;$J->charsetnr=($J->blob?63:0);return$J;}function
__destruct(){mysql_free_result($this->result);}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach($N,$V,$F){$Ff=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);$Kh=adminer()->connectSsl();if($Kh){if($Kh['key'])$Ff[\PDO::MYSQL_ATTR_SSL_KEY]=$Kh['key'];if($Kh['cert'])$Ff[\PDO::MYSQL_ATTR_SSL_CERT]=$Kh['cert'];if($Kh['ca'])$Ff[\PDO::MYSQL_ATTR_SSL_CA]=$Kh['ca'];if(isset($Kh['verify']))$Ff[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$Kh['verify'];}return$this->dsn("mysql:charset=utf8;host=".str_replace(":",";unix_socket=",preg_replace('~:(\d)~',';port=\1',$N)),$V,$F,$Ff);}function
set_charset($Ua){return$this->query("SET NAMES $Ua");}function
select_db($Jb){return$this->query("USE ".idf_escape($Jb));}function
query($H,$Ji=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$Ji);return
parent::query($H,$Ji);}}}class
Driver
extends
SqlDriver{static$Jc=array("MySQLi","MySQL","PDO_MySQL");static$le="sql";var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");static
function
connect($N,$V,$F){$f=parent::connect($N,$V,$F);if(is_string($f)){if(function_exists('iconv')&&!is_utf8($f)&&strlen($dh=iconv("windows-1250","utf-8",$f))>strlen($f))$f=$dh;return$f;}$f->set_charset(charset($f));$f->query("SET sql_quote_show_create = 1, autocommit = 1");$f->flavor=(preg_match('~MariaDB~',$f->server_info)?'maria':'mysql');add_driver(DRIVER,($f->flavor=='maria'?"MariaDB":"MySQL"));return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array('Numbers'=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),'Date and time'=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),'Strings'=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),'Lists'=>array("enum"=>65535,"set"=>64),'Binary'=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),'Geometry'=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$f))$this->types['Strings']["json"]=4294967295;if(min_version('',10.7,$f)){$this->types['Strings']["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version(9,'',$f)){$this->types['Numbers']["vector"]=16383;$this->insertFunctions['vector']='string_to_vector';}if(min_version(5.7,10.2,$f))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$m){return(preg_match("~binary~",$m["type"])?"<code class='jush-sql'>UNHEX</code>":($m["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):(preg_match("~geometry|point|linestring|polygon~",$m["type"])?"<code class='jush-sql'>GeomFromText</code>":"")));}function
insert($R,array$O){return($O?parent::insert($R,$O):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,array$L,array$G){$e=array_keys(reset($L));$tg="INSERT INTO ".table($R)." (".implode(", ",$e).") VALUES\n";$cj=array();foreach($e
as$x)$cj[$x]="$x = VALUES($x)";$Rh="\nON DUPLICATE KEY UPDATE ".implode(", ",$cj);$cj=array();$y=0;foreach($L
as$O){$Y="(".implode(", ",$O).")";if($cj&&(strlen($tg)+$y+strlen($Y)+strlen($Rh)>1e6)){if(!queries($tg.implode(",\n",$cj).$Rh))return
false;$cj=array();$y=0;}$cj[]=$Y;$y+=strlen($Y)+2;}return
queries($tg.implode(",\n",$cj).$Rh);}function
slowQuery($H,$ni){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$ni FOR $H";elseif(preg_match('~^(SELECT\b)(.+)~is',$H,$B))return"$B[1] /*+ MAX_EXECUTION_TIME(".($ni*1000).") */ $B[2]";}}function
convertSearch($u,array$X,array$m){return(preg_match('~char|text|enum|set~',$m["type"])&&!preg_match("~^utf8~",$m["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($u USING ".charset($this->conn).")":$u);}function
warnings(){$I=$this->conn->query("SHOW WARNINGS");if($I&&$I->num_rows){ob_start();print_select_result($I);return
ob_get_clean();}}function
tableHelp($C,$je=false){$De=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower("information-schema-".($De?"$C-table/":str_replace("_","-",$C)."-table.html"));if(DB=="mysql")return($De?"mysql$C-table/":"system-schema.html");}function
hasCStyleEscapes(){static$Pa;if($Pa===null){$Ih=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Pa=(strpos($Ih,'NO_BACKSLASH_ESCAPES')===false);}return$Pa;}function
engines(){$J=array();foreach(get_rows("SHOW ENGINES")as$K){if(preg_match("~YES|DEFAULT~",$K["Support"]))$J[]=$K["Engine"];}return$J;}}function
idf_escape($u){return"`".str_replace("`","``",$u)."`";}function
table($u){return
idf_escape($u);}function
get_databases($ad){$J=get_session("dbs");if($J===null){$H="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$J=($ad?slow_query($H):get_vals($H));restart_session();set_session("dbs",$J);stop_session();}return$J;}function
limit($H,$Z,$z,$D=0,$ph=" "){return" $H$Z".($z?$ph."LIMIT $z".($D?" OFFSET $D":""):"");}function
limit1($R,$H,$Z,$ph="\n"){return
limit($H,$Z,1,0,$ph);}function
db_collation($j,array$hb){$J=null;$h=get_val("SHOW CREATE DATABASE ".idf_escape($j),1);if(preg_match('~ COLLATE ([^ ]+)~',$h,$B))$J=$B[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$h,$B))$J=$hb[$B[1]][-1];return$J;}function
logged_user(){return
get_val("SELECT USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$i){$J=array();foreach($i
as$j)$J[$j]=count(get_vals("SHOW TABLES IN ".idf_escape($j)));return$J;}function
table_status($C="",$Nc=false){$J=array();foreach(get_rows($Nc?"SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($C!=""?"AND TABLE_NAME = ".q($C):"ORDER BY Name"):"SHOW TABLE STATUS".($C!=""?" LIKE ".q(addcslashes($C,"%_\\")):""))as$K){if($K["Engine"]=="InnoDB")$K["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$K["Comment"]);if(!isset($K["Engine"]))$K["Comment"]="";if($C!="")$K["Name"]=$C;$J[$K["Name"]]=$K;}return$J;}function
is_view(array$S){return$S["Engine"]===null;}function
fk_support(array$S){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$S["Engine"]);}function
fields($R){$De=(connection()->flavor=='maria');$J=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$K){$m=$K["COLUMN_NAME"];$U=$K["COLUMN_TYPE"];$md=$K["GENERATION_EXPRESSION"];$Kc=$K["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Kc,$ld);preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$U,$Ge);$k=$K["COLUMN_DEFAULT"];if($k!=""){$ie=preg_match('~text|json~',$Ge[1]);if(!$De&&$ie)$k=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($k));if($De||$ie){$k=($k=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($B){return
stripslashes(str_replace("''","'",$B[1]));},$k));}if(!$De&&preg_match('~binary~',$Ge[1])&&preg_match('~^0x(\w*)$~',$k,$B))$k=pack("H*",$B[1]);}$J[$m]=array("field"=>$m,"full_type"=>$U,"type"=>$Ge[1],"length"=>$Ge[2],"unsigned"=>ltrim($Ge[3].$Ge[4]),"default"=>($ld?($De?$md:stripslashes($md)):$k),"null"=>($K["IS_NULLABLE"]=="YES"),"auto_increment"=>($Kc=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$Kc,$B)?$B[1]:""),"collation"=>$K["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$K[PRIVILEGES],where,order")),"comment"=>$K["COLUMN_COMMENT"],"primary"=>($K["COLUMN_KEY"]=="PRI"),"generated"=>($ld[1]=="PERSISTENT"?"STORED":$ld[1]),);}return$J;}function
indexes($R,$g=null){$J=array();foreach(get_rows("SHOW INDEX FROM ".table($R),$g)as$K){$C=$K["Key_name"];$J[$C]["type"]=($C=="PRIMARY"?"PRIMARY":($K["Index_type"]=="FULLTEXT"?"FULLTEXT":($K["Non_unique"]?($K["Index_type"]=="SPATIAL"?"SPATIAL":"INDEX"):"UNIQUE")));$J[$C]["columns"][]=$K["Column_name"];$J[$C]["lengths"][]=($K["Index_type"]=="SPATIAL"?null:$K["Sub_part"]);$J[$C]["descs"][]=null;}return$J;}function
foreign_keys($R){static$ig='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$J=array();$_b=get_val("SHOW CREATE TABLE ".table($R),1);if($_b){preg_match_all("~CONSTRAINT ($ig) FOREIGN KEY ?\\(((?:$ig,? ?)+)\\) REFERENCES ($ig)(?:\\.($ig))? \\(((?:$ig,? ?)+)\\)(?: ON DELETE (driver()->onActions))?(?: ON UPDATE (driver()->onActions))?~",$_b,$He,PREG_SET_ORDER);foreach($He
as$B){preg_match_all("~$ig~",$B[2],$Ch);preg_match_all("~$ig~",$B[5],$fi);$J[idf_unescape($B[1])]=array("db"=>idf_unescape($B[4]!=""?$B[3]:$B[4]),"table"=>idf_unescape($B[4]!=""?$B[4]:$B[3]),"source"=>array_map('Adminer\idf_unescape',$Ch[0]),"target"=>array_map('Adminer\idf_unescape',$fi[0]),"on_delete"=>($B[6]?:"RESTRICT"),"on_update"=>($B[7]?:"RESTRICT"),);}}return$J;}function
view($C){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($C),1)));}function
collations(){$J=array();foreach(get_rows("SHOW COLLATION")as$K){if($K["Default"])$J[$K["Charset"]][-1]=$K["Collation"];else$J[$K["Charset"]][]=$K["Collation"];}ksort($J);foreach($J
as$x=>$X)sort($J[$x]);return$J;}function
information_schema($j){return($j=="information_schema")||(min_version(5.5)&&$j=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($j,$c){return
queries("CREATE DATABASE ".idf_escape($j).($c?" COLLATE ".q($c):""));}function
drop_databases(array$i){$J=apply_queries("DROP DATABASE",$i,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$J;}function
rename_database($C,$c){$J=false;if(create_database($C,$c)){$T=array();$hj=array();foreach(tables_list()as$R=>$U){if($U=='VIEW')$hj[]=$R;else$T[]=$R;}$J=(!$T&&!$hj)||move_tables($T,$hj,$C);drop_databases($J?array(DB):array());}return$J;}function
auto_increment(){$Aa=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$v){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$v["columns"],true)){$Aa="";break;}if($v["type"]=="PRIMARY")$Aa=" UNIQUE";}}return" AUTO_INCREMENT$Aa";}function
alter_table($R,$C,array$n,array$cd,$mb,$rc,$c,$_a,$eg){$b=array();foreach($n
as$m){if($m[1]){$k=$m[1][3];if(preg_match('~ GENERATED~',$k)){$m[1][3]=(connection()->flavor=='maria'?"":$m[1][2]);$m[1][2]=$k;}$b[]=($R!=""?($m[0]!=""?"CHANGE ".idf_escape($m[0]):"ADD"):" ")." ".implode($m[1]).($R!=""?$m[2]:"");}else$b[]="DROP ".idf_escape($m[0]);}$b=array_merge($b,$cd);$P=($mb!==null?" COMMENT=".q($mb):"").($rc?" ENGINE=".q($rc):"").($c?" COLLATE ".q($c):"").($_a!=""?" AUTO_INCREMENT=$_a":"");if($R=="")return
queries("CREATE TABLE ".table($C)." (\n".implode(",\n",$b)."\n)$P$eg");if($R!=$C)$b[]="RENAME TO ".table($C);if($P)$b[]=ltrim($P);return($b||$eg?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$b).$eg):true);}function
alter_indexes($R,$b){$Ta=array();foreach($b
as$X)$Ta[]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$Ta));}function
truncate_tables(array$T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views(array$hj){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$hj)));}function
drop_tables(array$T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables(array$T,array$hj,$fi){$Rg=array();foreach($T
as$R)$Rg[]=table($R)." TO ".idf_escape($fi).".".table($R);if(!$Rg||queries("RENAME TABLE ".implode(", ",$Rg))){$Qb=array();foreach($hj
as$R)$Qb[table($R)]=view($R);connection()->select_db($fi);$j=idf_escape(DB);foreach($Qb
as$C=>$gj){if(!queries("CREATE VIEW $C AS ".str_replace(" $j."," ",$gj["select"]))||!queries("DROP VIEW $j.$C"))return
false;}return
true;}return
false;}function
copy_tables(array$T,array$hj,$fi){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$C=($fi==DB?table("copy_$R"):idf_escape($fi).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $C"))||!queries("CREATE TABLE $C LIKE ".table($R))||!queries("INSERT INTO $C SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){$Ci=$K["Trigger"];if(!queries("CREATE TRIGGER ".($fi==DB?idf_escape("copy_$Ci"):idf_escape($fi).".".idf_escape($Ci))." $K[Timing] $K[Event] ON $C FOR EACH ROW\n$K[Statement];"))return
false;}}foreach($hj
as$R){$C=($fi==DB?table("copy_$R"):idf_escape($fi).".".table($R));$gj=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $C"))||!queries("CREATE VIEW $C AS $gj[select]"))return
false;}return
true;}function
trigger($C,$R){if($C=="")return
array();$L=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($C));return
reset($L);}function
triggers($R){$J=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K)$J[$K["Trigger"]]=array($K["Timing"],$K["Event"]);return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
routine($C,$U){$ra=array("bool","boolean","integer","double precision","real","dec","numeric","fixed","national char","national varchar");$Dh="(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";$tc=driver()->enumLength;$Hi="((".implode("|",array_merge(array_keys(driver()->types()),$ra)).")\\b(?:\\s*\\(((?:[^'\")]|$tc)++)\\))?"."\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?)(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s,]+)['\"]?)?";$ig="$Dh*(".($U=="FUNCTION"?"":driver()->inout).")?\\s*(?:`((?:[^`]|``)*)`\\s*|\\b(\\S+)\\s+)$Hi";$h=get_val("SHOW CREATE $U ".idf_escape($C),2);preg_match("~\\(((?:$ig\\s*,?)*)\\)\\s*".($U=="FUNCTION"?"RETURNS\\s+$Hi\\s+":"")."(.*)~is",$h,$B);$n=array();preg_match_all("~$ig\\s*,?~is",$B[1],$He,PREG_SET_ORDER);foreach($He
as$Yf)$n[]=array("field"=>str_replace("``","`",$Yf[2]).$Yf[3],"type"=>strtolower($Yf[5]),"length"=>preg_replace_callback("~$tc~s",'Adminer\normalize_enum',$Yf[6]),"unsigned"=>strtolower(preg_replace('~\s+~',' ',trim("$Yf[8] $Yf[7]"))),"null"=>true,"full_type"=>$Yf[4],"inout"=>strtoupper($Yf[1]),"collation"=>strtolower($Yf[9]),);return
array("fields"=>$n,"comment"=>get_val("SELECT ROUTINE_COMMENT FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = ".q($C)),)+($U!="FUNCTION"?array("definition"=>$B[11]):array("returns"=>array("type"=>$B[12],"length"=>$B[13],"unsigned"=>$B[15],"collation"=>$B[16]),"definition"=>$B[17],"language"=>"SQL",));}function
routines(){return
get_rows("SELECT ROUTINE_NAME AS SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return
array();}function
routine_id($C,array$K){return
idf_escape($C);}function
last_id($I){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$f,$H){return$f->query("EXPLAIN ".(min_version(5.1)&&!min_version(5.7)?"PARTITIONS ":"").$H);}function
found_rows(array$S,array$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$_a,$Ph){$J=get_val("SHOW CREATE TABLE ".table($R),1);if(!$_a)$J=preg_replace('~ AUTO_INCREMENT=\d+~','',$J);return$J;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($Jb){return"USE ".idf_escape($Jb);}function
trigger_sql($R){$J="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")),null,"-- ")as$K)$J
.="\nCREATE TRIGGER ".idf_escape($K["Trigger"])." $K[Timing] $K[Event] ON ".table($K["Table"])." FOR EACH ROW\n$K[Statement];;\n";return$J;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$m){if(preg_match("~binary~",$m["type"]))return"HEX(".idf_escape($m["field"]).")";if($m["type"]=="bit")return"BIN(".idf_escape($m["field"])." + 0)";if(preg_match("~geometry|point|linestring|polygon~",$m["type"]))return(min_version(8)?"ST_":"")."AsWKT(".idf_escape($m["field"]).")";}function
unconvert_field(array$m,$J){if(preg_match("~binary~",$m["type"]))$J="UNHEX($J)";if($m["type"]=="bit")$J="CONVERT(b$J, UNSIGNED)";if(preg_match("~geometry|point|linestring|polygon~",$m["type"])){$tg=(min_version(8)?"ST_":"");$J=$tg."GeomFromText($J, $tg"."SRID($m[field]))";}return$J;}function
support($Oc){return!preg_match("~scheme|sequence|type|view_trigger|materializedview".(min_version(8)?"":"|descidx".(min_version(5.1)?"":"|event|partitioning")).(min_version('8.0.16','10.2.1')?"":"|check")."~",$Oc);}function
kill_process($X){return
queries("KILL ".number($X));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types(){return
array();}function
type_values($t){return"";}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($fh,$g=null){return
true;}}define('Adminer\JUSH',Driver::$le);define('Adminer\SERVER',$_GET[DRIVER]);define('Adminer\DB',$_GET["db"]);define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').(SERVER!==null?DRIVER."=".urlencode(SERVER).'&':'').($_GET["ext"]?"ext=".urlencode($_GET["ext"]).'&':'').(isset($_GET["username"])?"username=".urlencode($_GET["username"]).'&':'').(DB!=""?'db='.urlencode(DB).'&'.(isset($_GET["ns"])?"ns=".urlencode($_GET["ns"])."&":""):''));function
page_header($pi,$l="",$Ma=array(),$qi=""){page_headers();if(is_ajax()&&$l){page_messages($l);exit;}if(!ob_get_level())ob_start(null,4096);$ri=$pi.($qi!=""?": $qi":"");$si=strip_tags($ri.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang="en" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$si,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=5.1.1"),'">
';$Db=adminer()->css();$yd=false;$wd=false;foreach($Db
as$o){if(strpos($o,"adminer.css")!==false)$yd=true;if(strpos($o,"adminer-dark.css")!==false)$wd=true;}$Gb=($yd?($wd?null:false):($wd?:null));$Pe=" media='(prefers-color-scheme: dark)'";if($Gb!==false)echo"<link rel='stylesheet'".($Gb?"":$Pe)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=5.1.1")."'>\n";echo"<meta name='color-scheme' content='".($Gb===null?"light dark":($Gb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=5.1.1");if(adminer()->head($Gb))echo"<link rel='shortcut icon' type='image/x-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=favicon.ico&version=5.1.1")."'>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=favicon.ico&version=5.1.1")."'>\n";foreach($Db
as$X)echo"<link rel='stylesheet'".(preg_match('~-dark~',$X)&&!$Gb?$Pe:"")." href='".h($X)."'>\n";echo"\n<body class='".'ltr'." nojs'>\n";$o=get_temp_dir()."/adminer.version";if(!$_COOKIE["adminer_version"]&&function_exists('openssl_verify')&&file_exists($o)&&filemtime($o)+86400>time()){$fj=unserialize(file_get_contents($o));$Bg="-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwqWOVuF5uw7/+Z70djoK
RlHIZFZPO0uYRezq90+7Amk+FDNd7KkL5eDve+vHRJBLAszF/7XKXe11xwliIsFs
DFWQlsABVZB3oisKCBEuI71J4kPH8dKGEWR9jDHFw3cWmoH3PmqImX6FISWbG3B8
h7FIx3jEaw5ckVPVTeo5JRm/1DZzJxjyDenXvBQ/6o9DgZKeNDgxwKzH+sw9/YCO
jHnq1cFpOIISzARlrHMa/43YfeNRAm/tsBXjSxembBPo7aQZLAWHmaj5+K19H10B
nCpz9Y++cipkVEiKRGih4ZEvjoFysEOdRLj6WiD/uUNky4xGeA6LaJqh5XpkFkcQ
fQIDAQAB
-----END PUBLIC KEY-----
";if(openssl_verify($fj["version"],base64_decode($fj["signature"]),$Bg)==1)$_COOKIE["adminer_version"]=$fj["version"];}echo
script("mixin(document.body, {onkeydown: bodyKeydown, onclick: bodyClick".(isset($_COOKIE["adminer_version"])?"":", onload: partial(verifyVersion, '".VERSION."', '".js_escape(ME)."', '".get_token()."')")."});
document.body.classList.replace('nojs', 'js');
const offlineMessage = '".js_escape('You are offline.')."';
const thousandsSeparator = '".js_escape(',')."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'></div>\n",script("mixin(qs('#help'), {onmouseover: () => { helpOpen = 1; }, onmouseout: helpMouseout});"),"<div id='content'>\n","<span id='menuopen' class='jsonly'>".icon("move","","menu","")."</span>".script("qs('#menuopen').onclick = event => { qs('#foot').classList.toggle('foot'); event.stopPropagation(); }");if($Ma!==null){$_=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($_?:".").'">'.get_driver(DRIVER).'</a> » ';$_=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$N=adminer()->serverName(SERVER);$N=($N!=""?$N:'Server');if($Ma===false)echo"$N\n";else{echo"<a href='".h($_)."' accesskey='1' title='Alt+Shift+1'>$N</a> » ";if($_GET["ns"]!=""||(DB!=""&&is_array($Ma)))echo'<a href="'.h($_."&db=".urlencode(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a> » ';if(is_array($Ma)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> » ';foreach($Ma
as$x=>$X){$Sb=(is_array($X)?$X[1]:h($X));if($Sb!="")echo"<a href='".h(ME."$x=").urlencode(is_array($X)?$X[0]:$X)."'>$Sb</a> » ";}}echo"$pi\n";}}echo"<h2>$ri</h2>\n","<div id='ajaxstatus' class='jsonly hidden'></div>\n";restart_session();page_messages($l);$i=&get_session("dbs");if(DB!=""&&$i&&!in_array(DB,$i,true))$i=null;stop_session();define('Adminer\PAGE_HEADER',1);}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Cb){$_d=array();foreach($Cb
as$x=>$X)$_d[]="$x $X";header("Content-Security-Policy: ".implode("; ",$_d));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self'","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
get_nonce(){static$lf;if(!$lf)$lf=base64_encode(rand_string());return$lf;}function
page_messages($l){$Si=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$Ve=idx($_SESSION["messages"],$Si);if($Ve){echo"<div class='message'>".implode("</div>\n<div class='message'>",$Ve)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$Si]);}if($l)echo"<div class='error'>$l</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($Ye=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($Ye);echo"</div>\n";if($Ye!="auth")echo'<form action="" method="post">
<p class="logout">
<span>',h($_GET["username"])."\n",'</span>
<input type="submit" name="logout" value="Logout" id="logout">
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($df){while($df>=2147483648)$df-=4294967296;while($df<=-2147483649)$df+=4294967296;return(int)$df;}function
long2str(array$W,$jj){$dh='';foreach($W
as$X)$dh
.=pack('V',$X);if($jj)return
substr($dh,0,end($W));return$dh;}function
str2long($dh,$jj){$W=array_values(unpack('V*',str_pad($dh,4*ceil(strlen($dh)/4),"\0")));if($jj)$W[]=strlen($dh);return$W;}function
xxtea_mx($qj,$pj,$Sh,$me){return
int32((($qj>>5&0x7FFFFFF)^$pj<<2)+(($pj>>3&0x1FFFFFFF)^$qj<<4))^int32(($Sh^$pj)+($me^$qj));}function
encrypt_string($Nh,$x){if($Nh=="")return"";$x=array_values(unpack("V*",pack("H*",md5($x))));$W=str2long($Nh,true);$df=count($W)-1;$qj=$W[$df];$pj=$W[0];$Cg=floor(6+52/($df+1));$Sh=0;while($Cg-->0){$Sh=int32($Sh+0x9E3779B9);$ic=$Sh>>2&3;for($Wf=0;$Wf<$df;$Wf++){$pj=$W[$Wf+1];$cf=xxtea_mx($qj,$pj,$Sh,$x[$Wf&3^$ic]);$qj=int32($W[$Wf]+$cf);$W[$Wf]=$qj;}$pj=$W[0];$cf=xxtea_mx($qj,$pj,$Sh,$x[$Wf&3^$ic]);$qj=int32($W[$df]+$cf);$W[$df]=$qj;}return
long2str($W,false);}function
decrypt_string($Nh,$x){if($Nh=="")return"";if(!$x)return
false;$x=array_values(unpack("V*",pack("H*",md5($x))));$W=str2long($Nh,false);$df=count($W)-1;$qj=$W[$df];$pj=$W[0];$Cg=floor(6+52/($df+1));$Sh=int32($Cg*0x9E3779B9);while($Sh){$ic=$Sh>>2&3;for($Wf=$df;$Wf>0;$Wf--){$qj=$W[$Wf-1];$cf=xxtea_mx($qj,$pj,$Sh,$x[$Wf&3^$ic]);$pj=int32($W[$Wf]-$cf);$W[$Wf]=$pj;}$qj=$W[$df];$cf=xxtea_mx($qj,$pj,$Sh,$x[$Wf&3^$ic]);$pj=int32($W[0]-$cf);$W[0]=$pj;$Sh=int32($Sh-0x9E3779B9);}return
long2str($W,true);}$kg=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($x)=explode(":",$X);$kg[$x]=$X;}}function
add_invalid_login(){$Fa=get_temp_dir()."/adminer.invalid";foreach(glob("$Fa*")?:array($Fa)as$o){$q=file_open_lock($o);if($q)break;}if(!$q)$q=file_open_lock("$Fa-".rand_string());if(!$q)return;$ee=unserialize(stream_get_contents($q));$mi=time();if($ee){foreach($ee
as$fe=>$X){if($X[0]<$mi)unset($ee[$fe]);}}$de=&$ee[adminer()->bruteForceKey()];if(!$de)$de=array($mi+30*60,0);$de[1]++;file_write_unlock($q,serialize($ee));}function
check_invalid_login(array&$kg){$ee=array();foreach(glob(get_temp_dir()."/adminer.invalid*")as$o){$q=file_open_lock($o);if($q){$ee=unserialize(stream_get_contents($q));file_unlock($q);break;}}$de=idx($ee,adminer()->bruteForceKey(),array());$kf=($de[1]>29?$de[0]-time():0);if($kf>0)auth_error(lang(array('Too many unsuccessful logins, try again in %d minute.','Too many unsuccessful logins, try again in %d minutes.'),ceil($kf/60)),$kg);}$za=$_POST["auth"];if($za){session_regenerate_id();$ej=$za["driver"];$N=$za["server"];$V=$za["username"];$F=(string)$za["password"];$j=$za["db"];set_password($ej,$N,$V,$F);$_SESSION["db"][$ej][$N][$V][$j]=true;if($za["permanent"]){$x=implode("-",array_map('base64_encode',array($ej,$N,$V,$j)));$xg=adminer()->permanentLogin(true);$kg[$x]="$x:".base64_encode($xg?encrypt_string($F,$xg):"");cookie("adminer_permanent",implode(" ",$kg));}if(count($_POST)==1||DRIVER!=$ej||SERVER!=$N||$_GET["username"]!==$V||DB!=$j)redirect(auth_url($ej,$N,$V,$j));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$x)set_session($x,null);unset_permanent($kg);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),'Logout successful.'.' '.'Thanks for using Adminer, consider <a href="https://www.adminer.org/en/donation/">donating</a>.');}elseif($kg&&!$_SESSION["pwds"]){session_regenerate_id();$xg=adminer()->permanentLogin();foreach($kg
as$x=>$X){list(,$bb)=explode(":",$X);list($ej,$N,$V,$j)=array_map('base64_decode',explode("-",$x));set_password($ej,$N,$V,decrypt_string(base64_decode($bb),$xg));$_SESSION["db"][$ej][$N][$V][$j]=true;}}function
unset_permanent(array&$kg){foreach($kg
as$x=>$X){list($ej,$N,$V,$j)=array_map('base64_decode',explode("-",$x));if($ej==DRIVER&&$N==SERVER&&$V==$_GET["username"]&&$j==DB)unset($kg[$x]);}cookie("adminer_permanent",implode(" ",$kg));}function
auth_error($l,array&$kg){$vh=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$vh]||$_GET[$vh])&&!$_SESSION["token"])$l='Session expired, please login again.';else{restart_session();add_invalid_login();$F=get_password();if($F!==null){if($F===false)$l
.=($l?'<br>':'').sprintf('Master password expired. <a href="https://www.adminer.org/en/extension/"%s>Implement</a> %s method to make it permanent.',target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);}unset_permanent($kg);}}if(!$_COOKIE[$vh]&&$_GET[$vh]&&ini_bool("session.use_only_cookies"))$l='Session support must be enabled.';$Zf=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$Zf["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header('Login',$l,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth")))echo"<p class='message'>".'The action will be performed after successful login with the same credentials.'."\n";echo"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($kg);page_header('No extension',sprintf('None of the supported PHP extensions (%s) are available.',implode(", ",Driver::$Jc)),false);page_footer("auth");exit;}$f='';if(isset($_GET["username"])&&is_string(get_password())){list($Fd,$og)=explode(":",SERVER,2);if(preg_match('~^\s*([-+]?\d+)~',$og,$B)&&($B[1]<1024||$B[1]>65535))auth_error('Connecting to privileged ports is not allowed.',$kg);check_invalid_login($kg);$Bb=adminer()->credentials();$f=Driver::connect($Bb[0],$Bb[1],$Bb[2]);if(is_object($f)){Db::$be=$f;Driver::$be=new
Driver($f);if($f->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$Be=null;if(!is_object($f)||($Be=adminer()->login($_GET["username"],get_password()))!==true){$l=(is_string($f)?nl_br(h($f)):(is_string($Be)?$Be:'Invalid credentials.')).(preg_match('~^ | $~',get_password())?'<br>'.'There is a space in the input password which might be the cause.':'');auth_error($l,$kg);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header('Logout','Invalid CSRF token. Send the form again.');page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($za&&$_POST["token"])$_POST["token"]=get_token();$l='';if($_POST){if(!verify_token()){$Wd="max_input_vars";$Ne=ini_get($Wd);if(extension_loaded("suhosin")){foreach(array("suhosin.request.max_vars","suhosin.post.max_vars")as$x){$X=ini_get($x);if($X&&(!$Ne||$X<$Ne)){$Wd=$x;$Ne=$X;}}}$l=(!$_POST["token"]&&$Ne?sprintf('Maximum number of allowed fields exceeded. Please increase %s.',"'$Wd'"):'Invalid CSRF token. Send the form again.'.' '.'If you did not send this request from Adminer then close this page.');}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){$l=sprintf('Too big POST data. Reduce the data or increase the %s configuration directive.',"'post_max_size'");if(isset($_GET["sql"]))$l
.=' '.'You can upload a big SQL file via FTP and import it from server.';}function
print_select_result($I,$g=null,array$Lf=array(),$z=0){$Ae=array();$w=array();$e=array();$Ka=array();$Ii=array();$J=array();for($s=0;(!$z||$s<$z)&&($K=$I->fetch_row());$s++){if(!$s){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr>";for($ke=0;$ke<count($K);$ke++){$m=$I->fetch_field();$C=$m->name;$Kf=(isset($m->orgtable)?$m->orgtable:"");$Jf=(isset($m->orgname)?$m->orgname:$C);if($Lf&&JUSH=="sql")$Ae[$ke]=($C=="table"?"table=":($C=="possible_keys"?"indexes=":null));elseif($Kf!=""){if(isset($m->table))$J[$m->table]=$Kf;if(!isset($w[$Kf])){$w[$Kf]=array();foreach(indexes($Kf,$g)as$v){if($v["type"]=="PRIMARY"){$w[$Kf]=array_flip($v["columns"]);break;}}$e[$Kf]=$w[$Kf];}if(isset($e[$Kf][$Jf])){unset($e[$Kf][$Jf]);$w[$Kf][$Jf]=$ke;$Ae[$ke]=$Kf;}}if($m->charsetnr==63)$Ka[$ke]=true;$Ii[$ke]=$m->type;echo"<th".($Kf!=""||$m->name!=$Jf?" title='".h(($Kf!=""?"$Kf.":"").$Jf)."'":"").">".h($C).($Lf?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($C),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}echo"</thead>\n";}echo"<tr>";foreach($K
as$x=>$X){$_="";if(isset($Ae[$x])&&!$e[$Ae[$x]]){if($Lf&&JUSH=="sql"){$R=$K[array_search("table=",$Ae)];$_=ME.$Ae[$x].urlencode($Lf[$R]!=""?$Lf[$R]:$R);}else{$_=ME."edit=".urlencode($Ae[$x]);foreach($w[$Ae[$x]]as$fb=>$ke)$_
.="&where".urlencode("[".bracket_escape($fb)."]")."=".urlencode($K[$ke]);}}elseif(is_url($X))$_=$X;if($X===null)$X="<i>NULL</i>";elseif($Ka[$x]&&!is_utf8($X))$X="<i>".lang(array('%d byte','%d bytes'),strlen($X))."</i>";else{$X=h($X);if($Ii[$x]==254)$X="<code>$X</code>";}if($_)$X="<a href='".h($_)."'".(is_url($_)?target_blank():'').">$X</a>";echo"<td".($Ii[$x]<=9||$Ii[$x]==246?" class='number'":"").">$X";}}echo($s?"</table>\n</div>":"<p class='message'>".'No rows.')."\n";return$J;}function
referencable_primary($nh){$J=array();foreach(table_status('',true)as$Xh=>$R){if($Xh!=$nh&&fk_support($R)){foreach(fields($Xh)as$m){if($m["primary"]){if($J[$Xh]){unset($J[$Xh]);break;}$J[$Xh]=$m;}}}}return$J;}function
textarea($C,$Y,$L=10,$ib=80){echo"<textarea name='".h($C)."' rows='$L' cols='$ib' class='sqlarea jush-".JUSH."' spellcheck='false' wrap='off'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($ya,array$Ff,$Y="",$_f="",$lg=""){$ei=($Ff?"select":"input");return"<$ei$ya".($Ff?"><option value=''>$lg".optionlist($Ff,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$lg'>").($_f?script("qsl('$ei').onchange = $_f;",""):"");}function
json_row($x,$X=null){static$Uc=true;if($Uc)echo"{";if($x!=""){echo($Uc?"":",")."\n\t\"".addcslashes($x,"\r\n\t\"\\/").'": '.($X!==null?'"'.addcslashes($X,"\r\n\"\\/").'"':'null');$Uc=false;}else{echo"\n}\n";$Uc=true;}}function
edit_type($x,array$m,array$hb,array$ed=array(),array$Lc=array()){$U=$m["type"];echo"<td><select name='".h($x)."[type]' class='type' aria-labelledby='label-type'>";if($U&&!array_key_exists($U,driver()->types())&&!isset($ed[$U])&&!in_array($U,$Lc))$Lc[]=$U;$Oh=driver()->structuredTypes();if($ed)$Oh['Foreign keys']=$ed;echo
optionlist(array_merge($Lc,$Oh),$U),"</select><td>","<input name='".h($x)."[length]' value='".h($m["length"])."' size='3'".(!$m["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($hb?"<input list='collations' name='".h($x)."[collation]'".(preg_match('~(char|text|enum|set)$~',$U)?"":" class='hidden'")." value='".h($m["collation"])."' placeholder='(".'collation'.")'>":''),(driver()->unsigned?"<select name='".h($x)."[unsigned]'".(!$U||preg_match(number_type(),$U)?"":" class='hidden'").'><option>'.optionlist(driver()->unsigned,$m["unsigned"]).'</select>':''),(isset($m['on_update'])?"<select name='".h($x)."[on_update]'".(preg_match('~timestamp|datetime~',$U)?"":" class='hidden'").'>'.optionlist(array(""=>"(".'ON UPDATE'.")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?"CURRENT_TIMESTAMP":$m["on_update"])).'</select>':''),($ed?"<select name='".h($x)."[on_delete]'".(preg_match("~`~",$U)?"":" class='hidden'")."><option value=''>(".'ON DELETE'.")".optionlist(explode("|",driver()->onActions),$m["on_delete"])."</select> ":" ");}function
get_partitions_info($R){$id="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$I=connection()->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $id ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$J=array();list($J["partition_by"],$J["partition"],$J["partitions"])=$I->fetch_row();$fg=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $id AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$J["partition_names"]=array_keys($fg);$J["partition_values"]=array_values($fg);return$J;}function
process_length($y){$vc=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$vc(?:\\s*,\\s*$vc)*+\\s*\\)?\\s*\$~",$y)&&preg_match_all("~$vc~",$y,$He)?"(".implode(",",$He[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$y)));}function
process_type(array$m,$gb="COLLATE"){return" $m[type]".process_length($m["length"]).(preg_match(number_type(),$m["type"])&&in_array($m["unsigned"],driver()->unsigned)?" $m[unsigned]":"").(preg_match('~char|text|enum|set~',$m["type"])&&$m["collation"]?" $gb ".(JUSH=="mssql"?$m["collation"]:q($m["collation"])):"");}function
process_field(array$m,array$Gi){if($m["on_update"])$m["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$m["on_update"]);return
array(idf_escape(trim($m["field"])),process_type($Gi),($m["null"]?" NULL":" NOT NULL"),default_value($m),(preg_match('~timestamp|datetime~',$m["type"])&&$m["on_update"]?" ON UPDATE $m[on_update]":""),(support("comment")&&$m["comment"]!=""?" COMMENT ".q($m["comment"]):""),($m["auto_increment"]?auto_increment():null),);}function
default_value(array$m){$k=$m["default"];$ld=$m["generated"];return($k===null?"":(in_array($ld,driver()->generated)?(JUSH=="mssql"?" AS ($k)".($ld=="VIRTUAL"?"":" $ld")."":" GENERATED ALWAYS AS ($k) $ld"):" DEFAULT ".(!preg_match('~^GENERATED ~i',$k)&&(preg_match('~char|binary|text|json|enum|set~',$m["type"])||preg_match('~^(?![a-z])~i',$k))?(JUSH=="sql"&&preg_match('~text|json~',$m["type"])?"(".q($k).")":q($k)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($k)":$k)))));}function
type_class($U){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$x=>$X){if(preg_match("~$x|$X~",$U))return" class='$x'";}}function
edit_fields(array$n,array$hb,$U="TABLE",array$ed=array()){$n=array_values($n);$Ob=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$nb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?'Column name':'Parameter name'),"<td id='label-type'>".'Type'."<textarea id='enum-edit' rows='4' cols='12' wrap='off' style='display: none;'></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<td id='label-length'>".'Length',"<td>".'Options';if($U=="TABLE")echo"<td id='label-null'>NULL\n","<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".'Auto Increment'."'>AI</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",'sqlite'=>"autoinc.html",'pgsql'=>"datatype-numeric.html#DATATYPE-SERIAL",'mssql'=>"t-sql/statements/create-table-transact-sql-identity-property",)),"<td id='label-default'$Ob>".'Default value',(support("comment")?"<td id='label-comment'$nb>".'Comment':"");echo"<td>".icon("plus","add[".(support("move_col")?0:count($n))."]","+",'Add next'),"</thead>\n<tbody>\n",script("mixin(qsl('tbody'), {onclick: editingClick, onkeydown: editingKeydown, oninput: editingInput});");foreach($n
as$s=>$m){$s++;$Mf=$m[($_POST?"orig":"field")];$Xb=(isset($_POST["add"][$s-1])||(isset($m["field"])&&!idx($_POST["drop_col"],$s)))&&(support("drop_col")||$Mf=="");echo"<tr".($Xb?"":" style='display: none;'").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$s][inout]",explode("|",driver()->inout),$m["inout"]):"")."<th>";if($Xb)echo"<input name='fields[$s][field]' value='".h($m["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'>";echo
input_hidden("fields[$s][orig]",$Mf);edit_type("fields[$s]",$m,$hb,$ed);if($U=="TABLE")echo"<td>".checkbox("fields[$s][null]",1,$m["null"],"","","block","label-null"),"<td><label class='block'><input type='radio' name='auto_increment_col' value='$s'".($m["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$Ob>".(driver()->generated?html_select("fields[$s][generated]",array_merge(array("","DEFAULT"),driver()->generated),$m["generated"])." ":checkbox("fields[$s][generated]",1,$m["generated"],"","","","label-default")),"<input name='fields[$s][default]' value='".h($m["default"])."' aria-labelledby='label-default'>",(support("comment")?"<td$nb><input name='fields[$s][comment]' value='".h($m["comment"])."' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'>":"");echo"<td>",(support("move_col")?icon("plus","add[$s]","+",'Add next')." ".icon("up","up[$s]","↑",'Move up')." ".icon("down","down[$s]","↓",'Move down')." ":""),($Mf==""||support("drop_col")?icon("cross","drop_col[$s]","x",'Remove'):"");}}function
process_fields(array&$n){$D=0;if($_POST["up"]){$te=0;foreach($n
as$x=>$m){if(key($_POST["up"])==$x){unset($n[$x]);array_splice($n,$te,0,array($m));break;}if(isset($m["field"]))$te=$D;$D++;}}elseif($_POST["down"]){$gd=false;foreach($n
as$x=>$m){if(isset($m["field"])&&$gd){unset($n[key($_POST["down"])]);array_splice($n,$D,0,array($gd));break;}if(key($_POST["down"])==$x)$gd=$m;$D++;}}elseif($_POST["add"]){$n=array_values($n);array_splice($n,key($_POST["add"]),0,array(array()));}elseif(!$_POST["drop_col"])return
false;return
true;}function
normalize_enum(array$B){$X=$B[0];return"'".str_replace("'","''",addcslashes(stripcslashes(str_replace($X[0].$X[0],$X[0],substr($X,1,-1))),'\\'))."'";}function
grant($nd,array$zg,$e,$xf){if(!$zg)return
true;if($zg==array("ALL PRIVILEGES","GRANT OPTION"))return($nd=="GRANT"?queries("$nd ALL PRIVILEGES$xf WITH GRANT OPTION"):queries("$nd ALL PRIVILEGES$xf")&&queries("$nd GRANT OPTION$xf"));return
queries("$nd ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$e, ",$zg).$e).$xf);}function
drop_create($bc,$h,$dc,$ii,$fc,$A,$Ue,$Se,$Te,$uf,$hf){if($_POST["drop"])query_redirect($bc,$A,$Ue);elseif($uf=="")query_redirect($h,$A,$Te);elseif($uf!=$hf){$Ab=queries($h);queries_redirect($A,$Se,$Ab&&queries($bc));if($Ab)queries($dc);}else
queries_redirect($A,$Se,queries($ii)&&queries($fc)&&queries($bc)&&queries($h));}function
create_trigger($xf,array$K){$oi=" $K[Timing] $K[Event]".(preg_match('~ OF~',$K["Event"])?" $K[Of]":"");return"CREATE TRIGGER ".idf_escape($K["Trigger"]).(JUSH=="mssql"?$xf.$oi:$oi.$xf).rtrim(" $K[Type]\n$K[Statement]",";").";";}function
create_routine($Zg,array$K){$O=array();$n=(array)$K["fields"];ksort($n);foreach($n
as$m){if($m["field"]!="")$O[]=(preg_match("~^(driver()->inout)\$~",$m["inout"])?"$m[inout] ":"").idf_escape($m["field"]).process_type($m,"CHARACTER SET");}$Pb=rtrim($K["definition"],";");return"CREATE $Zg ".idf_escape(trim($K["name"]))." (".implode(", ",$O).")".($Zg=="FUNCTION"?" RETURNS".process_type($K["returns"],"CHARACTER SET"):"").($K["language"]?" LANGUAGE $K[language]":"").(JUSH=="pgsql"?" AS ".q($Pb):"\n$Pb;");}function
remove_definer($H){return
preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~','`@`(%|\1)',logged_user()).'`~','\1',$H);}function
format_foreign_key(array$p){$j=$p["db"];$mf=$p["ns"];return" FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$p["source"])).") REFERENCES ".($j!=""&&$j!=$_GET["db"]?idf_escape($j).".":"").($mf!=""&&$mf!=$_GET["ns"]?idf_escape($mf).".":"").idf_escape($p["table"])." (".implode(", ",array_map('Adminer\idf_escape',$p["target"])).")".(preg_match("~^(driver()->onActions)\$~",$p["on_delete"])?" ON DELETE $p[on_delete]":"").(preg_match("~^(driver()->onActions)\$~",$p["on_update"])?" ON UPDATE $p[on_update]":"");}function
tar_file($o,$ti){$J=pack("a100a8a8a8a12a12",$o,644,0,0,decoct($ti->size),decoct(time()));$ab=8*32;for($s=0;$s<strlen($J);$s++)$ab+=ord($J[$s]);$J
.=sprintf("%06o",$ab)."\0 ";echo$J,str_repeat("\0",512-strlen($J));$ti->send();echo
str_repeat("\0",511-($ti->size+511)%512);}function
ini_bytes($Wd){$X=ini_get($Wd);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
doc_link(array$hg,$ji="<sup>?</sup>"){$th=connection()->server_info;$fj=preg_replace('~^(\d\.?\d).*~s','\1',$th);$Ui=array('sql'=>"https://dev.mysql.com/doc/refman/$fj/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$fj)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://www.oracle.com/pls/topic/lookup?ctx=db".preg_replace('~^.* (\d+)\.(\d+)\.\d+\.\d+\.\d+.*~s','\1\2',$th)."&id=",);if(connection()->flavor=='maria'){$Ui['sql']="https://mariadb.com/kb/en/";$hg['sql']=(isset($hg['mariadb'])?$hg['mariadb']:str_replace(".html","/",$hg['sql']));}return($hg[JUSH]?"<a href='".h($Ui[JUSH].$hg[JUSH].(JUSH=='mssql'?"?view=sql-server-ver$fj":""))."'".target_blank().">$ji</a>":"");}function
db_size($j){if(!connection()->select_db($j))return"?";$J=0;foreach(table_status()as$S)$J+=$S["Data_length"]+$S["Index_length"];return
format_number($J);}function
set_utf8mb4($h){static$O=false;if(!$O&&preg_match('~\butf8mb4~i',$h)){$O=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!=""){header("HTTP/1.1 404 Not Found");page_header('Database'.": ".h(DB),'Invalid database.',true);}else{if($_POST["db"]&&!$l)queries_redirect(substr(ME,0,-1),'Databases have been dropped.',drop_databases($_POST["db"]));page_header('Select database',$l,false);echo"<p class='links'>\n";foreach(array('database'=>'Create database','privileges'=>'Privileges','processlist'=>'Process list','variables'=>'Variables','status'=>'Status',)as$x=>$X){if(support($x))echo"<a href='".h(ME)."$x='>$X</a>\n";}echo"<p>".sprintf('%s version: %s through PHP extension %s',get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".sprintf('Logged as: %s',"<b>".h(logged_user())."</b>")."\n";if(isset(adminer()->plugins)&&is_array(adminer()->plugins)){echo"<p>".'Loaded plugins'.":\n<ul>\n";foreach(adminer()->plugins
as$mg){$Ng=new
\ReflectionObject($mg);echo"<li><b>".get_class($mg)."</b>".h(preg_match('~^/[\s*]+(.+)~',$Ng->getDocComment(),$B)?": $B[1]":"")."\n";}echo"</ul>\n";}$i=adminer()->databases();if($i){$hh=support("scheme");$hb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),"<thead><tr>".(support("database")?"<td>":"")."<th>".'Database'.(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".'Refresh'."</a>":"")."<td>".'Collation'."<td>".'Tables'."<td>".'Size'." - <a href='".h(ME)."dbsize=1'>".'Compute'."</a>".script("qsl('a').onclick = partial(ajaxSetHtml, '".js_escape(ME)."script=connect');","")."</thead>\n";$i=($_GET["dbsize"]?count_tables($i):array_flip($i));foreach($i
as$j=>$T){$Yg=h(ME)."db=".urlencode($j);$t=h("Db-".$j);echo"<tr>".(support("database")?"<td>".checkbox("db[]",$j,in_array($j,(array)$_POST["db"]),"","","",$t):""),"<th><a href='$Yg' id='$t'>".h($j)."</a>";$c=h(db_collation($j,$hb));echo"<td>".(support("database")?"<a href='$Yg".($hh?"&amp;ns=":"")."&amp;database=' title='".'Alter database'."'>$c</a>":$c),"<td align='right'><a href='$Yg&amp;schema=' id='tables-".h($j)."' title='".'Database schema'."'>".($_GET["dbsize"]?$T:"?")."</a>","<td align='right' id='size-".h($j)."'>".($_GET["dbsize"]?db_size($j):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".'Selected'." <span id='selected'></span></legend><div>\n".input_hidden("all").script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^db/)); };")."<input type='submit' name='drop' value='".'Drop'."'>".confirm()."\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}}page_footer("db");exit;}if(support("scheme")){if(DB!=""&&$_GET["ns"]!==""){if(!isset($_GET["ns"]))redirect(preg_replace('~ns=[^&]*&~','',ME)."ns=".get_schema());if(!set_schema($_GET["ns"])){header("HTTP/1.1 404 Not Found");page_header('Schema'.": ".h($_GET["ns"]),'Invalid schema.',true);page_footer("ns");exit;}}}class
TmpFile{private$handler;var$size;function
__construct(){$this->handler=tmpfile();}function
write($wb){$this->size+=strlen($wb);fwrite($this->handler,$wb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$n=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$M=array(idf_escape($_GET["field"]));$I=driver()->select($a,$M,array(where($_GET,$n)),$M);$K=($I?$I->fetch_row():array());echo
driver()->value($K[0],$n[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$n=fields($a);if(!$n)$l=error()?:'No tables.';$S=table_status1($a);$C=adminer()->tableName($S);page_header(($n&&is_view($S)?$S['Engine']=='materialized view'?'Materialized view':'View':'Table').": ".($C!=""?$C:h($a)),$l);$Xg=array();foreach($n
as$x=>$m)$Xg+=$m["privileges"];adminer()->selectLinks($S,(isset($Xg["insert"])||!support("table")?"":null));$mb=$S["Comment"];if($mb!="")echo"<p class='nowrap'>".'Comment'.": ".h($mb)."\n";if($n)adminer()->tableStructurePrint($n,$S);if(support("indexes")&&driver()->supportsIndex($S)){echo"<h3 id='indexes'>".'Indexes'."</h3>\n";$w=indexes($a);if($w)adminer()->tableIndexesPrint($w);echo'<p class="links"><a href="'.h(ME).'indexes='.urlencode($a).'">'.'Alter indexes'."</a>\n";}if(!is_view($S)){if(fk_support($S)){echo"<h3 id='foreign-keys'>".'Foreign keys'."</h3>\n";$ed=foreign_keys($a);if($ed){echo"<table>\n","<thead><tr><th>".'Source'."<td>".'Target'."<td>".'ON DELETE'."<td>".'ON UPDATE'."<td></thead>\n";foreach($ed
as$C=>$p){echo"<tr title='".h($C)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$p["source"]))."</i>";$_=($p["db"]!=""?preg_replace('~db=[^&]*~',"db=".urlencode($p["db"]),ME):($p["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".urlencode($p["ns"]),ME):ME));echo"<td><a href='".h($_."table=".urlencode($p["table"]))."'>".($p["db"]!=""&&$p["db"]!=DB?"<b>".h($p["db"])."</b>.":"").($p["ns"]!=""&&$p["ns"]!=$_GET["ns"]?"<b>".h($p["ns"])."</b>.":"").h($p["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$p["target"]))."</i>)","<td>".h($p["on_delete"]),"<td>".h($p["on_update"]),'<td><a href="'.h(ME.'foreign='.urlencode($a).'&name='.urlencode($C)).'">'.'Alter'.'</a>',"\n";}echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'foreign='.urlencode($a).'">'.'Add foreign key'."</a>\n";}if(support("check")){echo"<h3 id='checks'>".'Checks'."</h3>\n";$Wa=driver()->checkConstraints($a);if($Wa){echo"<table>\n";foreach($Wa
as$x=>$X)echo"<tr title='".h($x)."'>","<td><code class='jush-".JUSH."'>".h($X),"<td><a href='".h(ME.'check='.urlencode($a).'&name='.urlencode($x))."'>".'Alter'."</a>","\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'check='.urlencode($a).'">'.'Create check'."</a>\n";}}if(support(is_view($S)?"view_trigger":"trigger")){echo"<h3 id='triggers'>".'Triggers'."</h3>\n";$Fi=triggers($a);if($Fi){echo"<table>\n";foreach($Fi
as$x=>$X)echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($x)."<td><a href='".h(ME.'trigger='.urlencode($a).'&name='.urlencode($x))."'>".'Alter'."</a>\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'trigger='.urlencode($a).'">'.'Add trigger'."</a>\n";}}elseif(isset($_GET["schema"])){page_header('Database schema',"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));$Zh=array();$ai=array();$ca=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$ca,$He,PREG_SET_ORDER);foreach($He
as$s=>$B){$Zh[$B[1]]=array($B[2],$B[3]);$ai[]="\n\t'".js_escape($B[1])."': [ $B[2], $B[3] ]";}$wi=0;$Ga=-1;$fh=array();$Mg=array();$xe=array();$sa=driver()->allFields();foreach(table_status('',true)as$R=>$S){if(is_view($S))continue;$pg=0;$fh[$R]["fields"]=array();foreach($sa[$R]as$m){$pg+=1.25;$m["pos"]=$pg;$fh[$R]["fields"][$m["field"]]=$m;}$fh[$R]["pos"]=($Zh[$R]?:array($wi,0));foreach(adminer()->foreignKeys($R)as$X){if(!$X["db"]){$ve=$Ga;if(idx($Zh[$R],1)||idx($Zh[$X["table"]],1))$ve=min(idx($Zh[$R],1,0),idx($Zh[$X["table"]],1,0))-1;else$Ga-=.1;while($xe[(string)$ve])$ve-=.0001;$fh[$R]["references"][$X["table"]][(string)$ve]=array($X["source"],$X["target"]);$Mg[$X["table"]][$R][(string)$ve]=$X["target"];$xe[(string)$ve]=true;}}$wi=max($wi,$fh[$R]["pos"][0]+2.5+$pg);}echo'<div id="schema" style="height: ',$wi,'em;">
<script',nonce(),'>
qs(\'#schema\').onselectstart = () => false;
const tablePos = {',implode(",",$ai)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$wi,';
document.onmousemove = schemaMousemove;
document.onmouseup = partialArg(schemaMouseup, \'',js_escape(DB),'\');
</script>
';foreach($fh
as$C=>$R){echo"<div class='table' style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em;'>",'<a href="'.h(ME).'table='.urlencode($C).'"><b>'.h($C)."</b></a>",script("qsl('div').onmousedown = schemaMousedown;");foreach($R["fields"]as$m){$X='<span'.type_class($m["type"]).' title="'.h($m["type"].($m["length"]?"($m[length])":"").($m["null"]?" NULL":'')).'">'.h($m["field"]).'</span>';echo"<br>".($m["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$gi=>$Og){foreach($Og
as$ve=>$Jg){$we=$ve-idx($Zh[$C],1);$s=0;foreach($Jg[0]as$Ch)echo"\n<div class='references' title='".h($gi)."' id='refs$ve-".($s++)."' style='left: $we"."em; top: ".$R["fields"][$Ch]["pos"]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: ".(-$we)."em;'></div></div>";}}foreach((array)$Mg[$C]as$gi=>$Og){foreach($Og
as$ve=>$e){$we=$ve-idx($Zh[$C],1);$s=0;foreach($e
as$fi)echo"\n<div class='references arrow' title='".h($gi)."' id='refd$ve-".($s++)."' style='left: $we"."em; top: ".$R["fields"][$fi]["pos"]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$we)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($fh
as$C=>$R){foreach((array)$R["references"]as$gi=>$Og){foreach($Og
as$ve=>$Jg){$Xe=$wi;$Le=-10;foreach($Jg[0]as$x=>$Ch){$qg=$R["pos"][0]+$R["fields"][$Ch]["pos"];$rg=$fh[$gi]["pos"][0]+$fh[$gi]["fields"][$Jg[1][$x]]["pos"];$Xe=min($Xe,$qg,$rg);$Le=max($Le,$qg,$rg);}echo"<div class='references' id='refl$ve' style='left: $ve"."em; top: $Xe"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($Le-$Xe)."em;'></div></div>\n";}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".urlencode($ca)),'" id="schema-link">Permanent link</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$l){save_settings(array_intersect_key($_POST,array_flip(array("output","format","db_style","types","routines","events","table_style","auto_increment","triggers","data_style"))),"adminer_export");$T=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$Hc=dump_headers((count($T)==1?key($T):DB),(DB==""||count($T)>1));$he=preg_match('~sql~',$_POST["format"]);if($he){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$Ph=$_POST["db_style"];$i=array(DB);if(DB==""){$i=$_POST["databases"];if(is_string($i))$i=explode("\n",rtrim(str_replace("\r","",$i),"\n"));}foreach((array)$i
as$j){adminer()->dumpDatabase($j);if(connection()->select_db($j)){if($he&&preg_match('~CREATE~',$Ph)&&($h=get_val("SHOW CREATE DATABASE ".idf_escape($j),1))){set_utf8mb4($h);if($Ph=="DROP+CREATE")echo"DROP DATABASE IF EXISTS ".idf_escape($j).";\n";echo"$h;\n";}if($he){if($Ph)echo
use_sql($j).";\n\n";$Tf="";if($_POST["types"]){foreach(types()as$t=>$U){$wc=type_values($t);if($wc)$Tf
.=($Ph!='DROP+CREATE'?"DROP TYPE IF EXISTS ".idf_escape($U).";;\n":"")."CREATE TYPE ".idf_escape($U)." AS ENUM ($wc);\n\n";else$Tf
.="-- Could not export type $U\n\n";}}if($_POST["routines"]){foreach(routines()as$K){$C=$K["ROUTINE_NAME"];$Zg=$K["ROUTINE_TYPE"];$h=create_routine($Zg,array("name"=>$C)+routine($K["SPECIFIC_NAME"],$Zg));set_utf8mb4($h);$Tf
.=($Ph!='DROP+CREATE'?"DROP $Zg IF EXISTS ".idf_escape($C).";;\n":"")."$h;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$K){$h=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($K["Name"]),3));set_utf8mb4($h);$Tf
.=($Ph!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($K["Name"]).";;\n":"")."$h;;\n\n";}}echo($Tf&&JUSH=='sql'?"DELIMITER ;;\n\n$Tf"."DELIMITER ;\n\n":$Tf);}if($_POST["table_style"]||$_POST["data_style"]){$hj=array();foreach(table_status('',true)as$C=>$S){$R=(DB==""||in_array($C,(array)$_POST["tables"]));$Hb=(DB==""||in_array($C,(array)$_POST["data"]));if($R||$Hb){$ti=null;if($Hc=="tar"){$ti=new
TmpFile;ob_start(array($ti,'write'),1e5);}adminer()->dumpTable($C,($R?$_POST["table_style"]:""),(is_view($S)?2:0));if(is_view($S))$hj[]=$C;elseif($Hb){$n=fields($C);adminer()->dumpData($C,$_POST["data_style"],"SELECT *".convert_fields($n,$n)." FROM ".table($C));}if($he&&$_POST["triggers"]&&$R&&($Fi=trigger_sql($C)))echo"\nDELIMITER ;;\n$Fi\nDELIMITER ;\n";if($Hc=="tar"){ob_end_flush();tar_file((DB!=""?"":"$j/")."$C.csv",$ti);}elseif($he)echo"\n";}}if(function_exists('Adminer\foreign_keys_sql')){foreach(table_status('',true)as$C=>$S){$R=(DB==""||in_array($C,(array)$_POST["tables"]));if($R&&!is_view($S))echo
foreign_keys_sql($C);}}foreach($hj
as$gj)adminer()->dumpTable($gj,$_POST["table_style"],1);if($Hc=="tar")echo
pack("x512");}}}adminer()->dumpFooter();exit;}page_header('Export',$l,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$Lb=array('','USE','DROP+CREATE','CREATE');$bi=array('','DROP+CREATE','CREATE');$Ib=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Ib[]='INSERT+UPDATE';$K=get_settings("adminer_export");if(!$K)$K=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"table_style"=>"DROP+CREATE","data_style"=>"INSERT");if(!isset($K["events"])){$K["routines"]=$K["events"]=($_GET["dump"]=="");$K["triggers"]=$K["table_style"];}echo"<tr><th>".'Output'."<td>".html_radios("output",adminer()->dumpOutput(),$K["output"])."\n","<tr><th>".'Format'."<td>".html_radios("format",adminer()->dumpFormat(),$K["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".'Database'."<td>".html_select('db_style',$Lb,$K["db_style"]).(support("type")?checkbox("types",1,$K["types"],'User types'):"").(support("routine")?checkbox("routines",1,$K["routines"],'Routines'):"").(support("event")?checkbox("events",1,$K["events"],'Events'):"")),"<tr><th>".'Tables'."<td>".html_select('table_style',$bi,$K["table_style"]).checkbox("auto_increment",1,$K["auto_increment"],'Auto Increment').(support("trigger")?checkbox("triggers",1,$K["triggers"],'Triggers'):""),"<tr><th>".'Data'."<td>".html_select('data_style',$Ib,$K["data_style"]),'</table>
<p><input type="submit" value="Export">
',input_token(),'
<table>
',script("qsl('table').onclick = dumpClick;");$ug=array();if(DB!=""){$Ya=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$Ya>".'Tables'."</label>".script("qs('#check-tables').onclick = partial(formCheck, /^tables\\[/);",""),"<th style='text-align: right;'><label class='block'>".'Data'."<input type='checkbox' id='check-data'$Ya></label>".script("qs('#check-data').onclick = partial(formCheck, /^data\\[/);",""),"</thead>\n";$hj="";$ci=tables_list();foreach($ci
as$C=>$U){$tg=preg_replace('~_.*~','',$C);$Ya=($a==""||$a==(substr($a,-1)=="%"?"$tg%":$C));$wg="<tr><td>".checkbox("tables[]",$C,$Ya,$C,"","block");if($U!==null&&!preg_match('~table~i',$U))$hj
.="$wg\n";else
echo"$wg<td align='right'><label class='block'><span id='Rows-".h($C)."'></span>".checkbox("data[]",$C,$Ya)."</label>\n";$ug[$tg]++;}echo$hj;if($ci)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-databases'".($a==""?" checked":"").">".'Database'."</label>",script("qs('#check-databases').onclick = partial(formCheck, /^databases\\[/);",""),"</thead>\n";$i=adminer()->databases();if($i){foreach($i
as$j){if(!information_schema($j)){$tg=preg_replace('~_.*~','',$j);echo"<tr><td>".checkbox("databases[]",$j,$a==""||$a=="$tg%",$j,"","block")."\n";$ug[$tg]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$Uc=true;foreach($ug
as$x=>$X){if($x!=""&&$X>1){echo($Uc?"<p>":" ")."<a href='".h(ME)."dump=".urlencode("$x%")."'>".h($x)."</a>";$Uc=false;}}}elseif(isset($_GET["privileges"])){page_header('Privileges');echo'<p class="links"><a href="'.h(ME).'user=">'.'Create user'."</a>";$I=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$nd=$I;if(!$I)$I=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($nd?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".'Username'."<th>".'Server'."<th></thead>\n";while($K=$I->fetch_assoc())echo'<tr><td>'.h($K["User"])."<td>".h($K["Host"]).'<td><a href="'.h(ME.'user='.urlencode($K["User"]).'&host='.urlencode($K["Host"])).'">'.'Edit'."</a>\n";if(!$nd||DB!="")echo"<tr><td><input name='user' autocapitalize='off'><td><input name='host' value='localhost' autocapitalize='off'><td><input type='submit' value='".'Edit'."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$l&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();exit;}restart_session();$Dd=&get_session("queries");$Cd=&$Dd[DB];if(!$l&&$_POST["clear"]){$Cd=array();redirect(remove_from_uri("history"));}stop_session();page_header((isset($_GET["import"])?'Import':'SQL command'),$l);if(!$l&&$_POST){$q=false;if(!isset($_GET["import"]))$H=$_POST["query"];elseif($_POST["webfile"]){$Gh=adminer()->importServerPath();$q=@fopen((file_exists($Gh)?$Gh:"compress.zlib://$Gh.gz"),"rb");$H=($q?fread($q,1e6):false);}else$H=get_file("sql_file",true,";");if(is_string($H)){if(function_exists('memory_get_usage')&&($Qe=ini_bytes("memory_limit"))!="-1")@ini_set("memory_limit",max($Qe,strval(2*strlen($H)+memory_get_usage()+8e6)));if($H!=""&&strlen($H)<1e6){$Cg=$H.(preg_match("~;[ \t\r\n]*\$~",$H)?"":";");if(!$Cd||first(end($Cd))!=$Cg){restart_session();$Cd[]=array($Cg,time());set_session("queries",$Dd);stop_session();}}$Dh="(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";$Rb=";";$D=0;$qc=true;$g=connect(adminer()->credentials());if($g&&DB!=""){$g->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$g);}$lb=0;$yc=array();$ag='[\'"'.(JUSH=="sql"?'`#':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|-- |$'.(JUSH=="pgsql"?'|\$[^$]*\$':'');$xi=microtime(true);$ma=get_settings("adminer_import");$hc=adminer()->dumpFormat();unset($hc["sql"]);while($H!=""){if(!$D&&preg_match("~^$Dh*+DELIMITER\\s+(\\S+)~i",$H,$B)){$Rb=$B[1];$H=substr($H,strlen($B[0]));}else{preg_match('('.preg_quote($Rb)."\\s*|$ag)",$H,$B,PREG_OFFSET_CAPTURE,$D);list($gd,$pg)=$B[0];if(!$gd&&$q&&!feof($q))$H
.=fread($q,1e5);else{if(!$gd&&rtrim($H)=="")break;$D=$pg+strlen($gd);if($gd&&rtrim($gd)!=$Rb){$Qa=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($pg>0&&strtolower($H[$pg-1])=="e"));$ig=($gd=='/*'?'\*/':($gd=='['?']':(preg_match('~^-- |^#~',$gd)?"\n":preg_quote($gd).($Qa?"|\\\\.":""))));while(preg_match("($ig|\$)s",$H,$B,PREG_OFFSET_CAPTURE,$D)){$dh=$B[0][0];if(!$dh&&$q&&!feof($q))$H
.=fread($q,1e5);else{$D=$B[0][1]+strlen($dh);if(!$dh||$dh[0]!="\\")break;}}}else{$qc=false;$Cg=substr($H,0,$pg);$lb++;$wg="<pre id='sql-$lb'><code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($Cg)."</code></pre>\n";if(JUSH=="sqlite"&&preg_match("~^$Dh*+ATTACH\\b~i",$Cg,$B)){echo$wg,"<p class='error'>".'ATTACH queries are not supported.'."\n";$yc[]=" <a href='#sql-$lb'>$lb</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$wg;ob_flush();flush();}$Lh=microtime(true);if(connection()->multi_query($Cg)&&$g&&preg_match("~^$Dh*+USE\\b~i",$Cg))$g->query($Cg);do{$I=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$wg:""),"<p class='error'>".'Error in query'.(connection()->errno?" (".connection()->errno.")":"").": ".error()."\n";$yc[]=" <a href='#sql-$lb'>$lb</a>";if($_POST["error_stops"])break
2;}else{$mi=" <span class='time'>(".format_time($Lh).")</span>".(strlen($Cg)<1000?" <a href='".h(ME)."sql=".urlencode(trim($Cg))."'>".'Edit'."</a>":"");$oa=connection()->affected_rows;$kj=($_POST["only_errors"]?"":driver()->warnings());$lj="warnings-$lb";if($kj)$mi
.=", <a href='#$lj'>".'Warnings'."</a>".script("qsl('a').onclick = partial(toggle, '$lj');","");$Fc=null;$Lf=null;$Gc="explain-$lb";if(is_object($I)){$z=$_POST["limit"];$Lf=print_select_result($I,$g,array(),$z);if(!$_POST["only_errors"]){echo"<form action='' method='post'>\n";$nf=$I->num_rows;echo"<p class='sql-footer'>".($nf?($z&&$nf>$z?sprintf('%d / ',$z):"").lang(array('%d row','%d rows'),$nf):""),$mi;if($g&&preg_match("~^($Dh|\\()*+SELECT\\b~i",$Cg)&&($Fc=explain($g,$Cg)))echo", <a href='#$Gc'>Explain</a>".script("qsl('a').onclick = partial(toggle, '$Gc');","");$t="export-$lb";echo", <a href='#$t'>".'Export'."</a>".script("qsl('a').onclick = partial(toggle, '$t');","")."<span id='$t' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ma["output"])." ".html_select("format",$hc,$ma["format"]).input_hidden("query",$Cg)."<input type='submit' name='export' value='".'Export'."'>".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$Dh*+(CREATE|DROP|ALTER)$Dh++(DATABASE|SCHEMA)\\b~i",$Cg)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang(array('Query executed OK, %d row affected.','Query executed OK, %d rows affected.'),$oa)."$mi\n";}echo($kj?"<div id='$lj' class='hidden'>\n$kj</div>\n":"");if($Fc){echo"<div id='$Gc' class='hidden explain'>\n";print_select_result($Fc,$g,$Lf);echo"</div>\n";}}$Lh=microtime(true);}while(connection()->next_result());}$H=substr($H,$D);$D=0;}}}}if($qc)echo"<p class='message'>".'No commands to execute.'."\n";elseif($_POST["only_errors"])echo"<p class='message'>".lang(array('%d query executed OK.','%d queries executed OK.'),$lb-count($yc))," <span class='time'>(".format_time($xi).")</span>\n";elseif($yc&&$lb>1)echo"<p class='error'>".'Error in query'.": ".implode("",$yc)."\n";}else
echo"<p class='error'>".upload_error($H)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form">
';$Dc="<input type='submit' value='".'Execute'."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$Cg=$_GET["sql"];if($_POST)$Cg=$_POST["query"];elseif($_GET["history"]=="all")$Cg=$Cd;elseif($_GET["history"]!="")$Cg=idx($Cd[$_GET["history"]],0);echo"<p>";textarea("query",$Cg,20);echo
script(($_POST?"":"qs('textarea').focus();\n")."qs('#form').onsubmit = partial(sqlSubmit, qs('#form'), '".js_escape(remove_from_uri("sql|limit|error_stops|only_errors|history"))."');"),"<p>";adminer()->sqlPrintAfter();echo"$Dc\n",'Limit rows'.": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{echo"<fieldset><legend>".'File upload'."</legend><div>";$td=(extension_loaded("zlib")?"[.gz]":"");echo(ini_bool("file_uploads")?"SQL$td (&lt; ".ini_get("upload_max_filesize")."B): <input type='file' name='sql_file[]' multiple>\n$Dc":'File uploads are disabled.'),"</div></fieldset>\n";$Nd=adminer()->importServerPath();if($Nd)echo"<fieldset><legend>".'From server'."</legend><div>",sprintf('Webserver file %s',"<code>".h($Nd)."$td</code>"),' <input type="submit" name="webfile" value="'.'Run file'.'">',"</div></fieldset>\n";echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),'Stop on error')."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),'Show only errors')."\n",input_token();if(!isset($_GET["import"])&&$Cd){print_fieldset("history",'History',$_GET["history"]!="");for($X=end($Cd);$X;$X=prev($Cd)){$x=key($Cd);list($Cg,$mi,$lc)=$X;echo'<a href="'.h(ME."sql=&history=$x").'">'.'Edit'."</a>"." <span class='time' title='".@date('Y-m-d',$mi)."'>".@date("H:i:s",$mi)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(ltrim(str_replace("\n"," ",str_replace("\r","",preg_replace('~^(#|-- ).*~m','',$Cg)))),80,"</code>").($lc?" <span class='time'>($lc)</span>":"")."<br>\n";}echo"<input type='submit' name='clear' value='".'Clear'."'>\n","<a href='".h(ME."sql=&history=all")."'>".'Edit all'."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$n=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$n):""):where($_GET,$n));$Ri=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($n
as$C=>$m){if(!isset($m["privileges"][$Ri?"update":"insert"])||adminer()->fieldName($m)==""||$m["generated"])unset($n[$C]);}if($_POST&&!$l&&!isset($_GET["select"])){$A=$_POST["referer"];if($_POST["insert"])$A=($Ri?null:$_SERVER["REQUEST_URI"]);elseif(!preg_match('~^.+&select=.+$~',$A))$A=ME."select=".urlencode($a);$w=indexes($a);$Mi=unique_array($_GET["where"],$w);$Fg="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($A,'Item has been deleted.',driver()->delete($a,$Fg,$Mi?0:1));else{$O=array();foreach($n
as$C=>$m){$X=process_input($m);if($X!==false&&$X!==null)$O[idf_escape($C)]=$X;}if($Ri){if(!$O)redirect($A);queries_redirect($A,'Item has been updated.',driver()->update($a,$O,$Fg,$Mi?0:1));if(is_ajax()){page_headers();page_messages($l);exit;}}else{$I=driver()->insert($a,$O);$ue=($I?last_id($I):0);queries_redirect($A,sprintf('Item%s has been inserted.',($ue?" $ue":"")),$I);}}}$K=null;if($_POST["save"])$K=(array)$_POST["fields"];elseif($Z){$M=array();foreach($n
as$C=>$m){if(isset($m["privileges"]["select"])){$wa=($_POST["clone"]&&$m["auto_increment"]?"''":convert_field($m));$M[]=($wa?"$wa AS ":"").idf_escape($C);}}$K=array();if(!support("table"))$M=array("*");if($M){$I=driver()->select($a,$M,array($Z),$M,array(),(isset($_GET["select"])?2:1));if(!$I)$l=error();else{$K=$I->fetch_assoc();if(!$K)$K=false;}if(isset($_GET["select"])&&(!$K||$I->fetch_assoc()))$K=null;}}if(!support("table")&&!$n){if(!$Z){$I=driver()->select($a,array("*"),array(),array("*"));$K=($I?$I->fetch_assoc():false);if(!$K)$K=array(driver()->primary=>"");}if($K){foreach($K
as$x=>$X){if(!$Z)$K[$x]=null;$n[$x]=array("field"=>$x,"null"=>($x!=driver()->primary),"auto_increment"=>($x==driver()->primary));}}}edit_form($a,$n,$K,$Ri,$l);}elseif(isset($_GET["create"])){$a=$_GET["create"];$cg=array();foreach(array('HASH','LINEAR HASH','KEY','LINEAR KEY','RANGE','LIST')as$x)$cg[$x]=$x;$Lg=referencable_primary($a);$ed=array();foreach($Lg
as$Xh=>$m)$ed[str_replace("`","``",$Xh)."`".str_replace("`","``",$m["field"])]=$Xh;$Of=array();$S=array();if($a!=""){$Of=fields($a);$S=table_status1($a);if(count($S)<2)$l='No tables.';}$K=$_POST;$K["fields"]=(array)$K["fields"];if($K["auto_increment_col"])$K["fields"][$K["auto_increment_col"]]["auto_increment"]=true;if($_POST)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($K["fields"])&&!$l){if($_POST["drop"])queries_redirect(substr(ME,0,-1),'Table has been dropped.',drop_tables(array($a)));else{$n=array();$sa=array();$Vi=false;$cd=array();$Nf=reset($Of);$qa=" FIRST";foreach($K["fields"]as$x=>$m){$p=$ed[$m["type"]];$Gi=($p!==null?$Lg[$p]:$m);if($m["field"]!=""){if(!$m["generated"])$m["default"]=null;$Ag=process_field($m,$Gi);$sa[]=array($m["orig"],$Ag,$qa);if(!$Nf||$Ag!==process_field($Nf,$Nf)){$n[]=array($m["orig"],$Ag,$qa);if($m["orig"]!=""||$qa)$Vi=true;}if($p!==null)$cd[idf_escape($m["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$ed[$m["type"]],'source'=>array($m["field"]),'target'=>array($Gi["field"]),'on_delete'=>$m["on_delete"],));$qa=" AFTER ".idf_escape($m["field"]);}elseif($m["orig"]!=""){$Vi=true;$n[]=array($m["orig"]);}if($m["orig"]!=""){$Nf=next($Of);if(!$Nf)$qa="";}}$eg="";if(support("partitioning")){if(isset($cg[$K["partition_by"]])){$Zf=array();foreach($K
as$x=>$X){if(preg_match('~^partition~',$x))$Zf[$x]=$X;}foreach($Zf["partition_names"]as$x=>$C){if($C==""){unset($Zf["partition_names"][$x]);unset($Zf["partition_values"][$x]);}}if($Zf!=get_partitions_info($a)){$fg=array();if($Zf["partition_by"]=='RANGE'||$Zf["partition_by"]=='LIST'){foreach($Zf["partition_names"]as$x=>$C){$Y=$Zf["partition_values"][$x];$fg[]="\n  PARTITION ".idf_escape($C)." VALUES ".($Zf["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$eg
.="\nPARTITION BY $Zf[partition_by]($Zf[partition])";if($fg)$eg
.=" (".implode(",",$fg)."\n)";elseif($Zf["partitions"])$eg
.=" PARTITIONS ".(+$Zf["partitions"]);}}elseif(preg_match("~partitioned~",$S["Create_options"]))$eg
.="\nREMOVE PARTITIONING";}$Re='Table has been altered.';if($a==""){cookie("adminer_engine",$K["Engine"]);$Re='Table has been created.';}$C=trim($K["name"]);queries_redirect(ME.(support("table")?"table=":"select=").urlencode($C),$Re,alter_table($a,$C,(JUSH=="sqlite"&&($Vi||$cd)?$sa:$n),$cd,($K["Comment"]!=$S["Comment"]?$K["Comment"]:null),($K["Engine"]&&$K["Engine"]!=$S["Engine"]?$K["Engine"]:""),($K["Collation"]&&$K["Collation"]!=$S["Collation"]?$K["Collation"]:""),($K["Auto_increment"]!=""?number($K["Auto_increment"]):""),$eg));}}page_header(($a!=""?'Alter table':'Create table'),$l,array("table"=>$a),h($a));if(!$_POST){$Ii=driver()->types();$K=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($Ii["int"])?"int":(isset($Ii["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$K=$S;$K["name"]=$a;$K["fields"]=array();if(!$_GET["auto_increment"])$K["Auto_increment"]="";foreach($Of
as$m){$m["generated"]=$m["generated"]?:(isset($m["default"])?"DEFAULT":"");$K["fields"][]=$m;}if(support("partitioning")){$K+=get_partitions_info($a);$K["partition_names"][]="";$K["partition_values"][]="";}}}$hb=collations();if(is_array(reset($hb)))$hb=call_user_func_array('array_merge',array_values($hb));$sc=driver()->engines();foreach($sc
as$rc){if(!strcasecmp($rc,$K["Engine"])){$K["Engine"]=$rc;break;}}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo'Table name'.": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($K["name"])."' autocapitalize='off'>\n",($sc?html_select("Engine",array(""=>"(".'engine'.")")+$sc,$K["Engine"]).on_help("event.target.value",1).script("qsl('select').onchange = helpClose;")."\n":"");if($hb)echo"<datalist id='collations'>".optionlist($hb)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($K["Collation"])."' placeholder='(".'collation'.")'>");echo"<input type='submit' value='".'Save'."'>\n";}if(support("columns")){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($K["fields"],$hb,"TABLE",$ed);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",'Auto Increment'.": <input type='number' name='Auto_increment' class='size' value='".h($K["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),'Default values',"columnShow(this.checked, 5)","jsonly");$ob=($_POST?$_POST["comments"]:get_setting("comments"));echo(support("comment")?checkbox("comments",1,$ob,'Comment',"editingCommentsClick(this, true);","jsonly").' '.(preg_match('~\n~',$K["Comment"])?"<textarea name='Comment' rows='2' cols='20'".($ob?"":" class='hidden'").">".h($K["Comment"])."</textarea>":'<input name="Comment" value="'.h($K["Comment"]).'" data-maxlength="'.(min_version(5.5)?2048:60).'"'.($ob?"":" class='hidden'").'>'):''),'<p>
<input type="submit" value="Save">
';}echo'
';if($a!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$a));if(support("partitioning")){$dg=preg_match('~RANGE|LIST~',$K["partition_by"]);print_fieldset("partition",'Partition by',$K["partition_by"]);echo"<p>".html_select("partition_by",array(""=>"")+$cg,$K["partition_by"]).on_help("event.target.value.replace(/./, 'PARTITION BY \$&')",1).script("qsl('select').onchange = partitionByChange;"),"(<input name='partition' value='".h($K["partition"])."'>)\n",'Partitions'.": <input type='number' name='partitions' class='size".($dg||!$K["partition_by"]?" hidden":"")."' value='".h($K["partitions"])."'>\n","<table id='partition-table'".($dg?"":" class='hidden'").">\n","<thead><tr><th>".'Partition name'."<th>".'Values'."</thead>\n";foreach($K["partition_names"]as$x=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off">',($x==count($K["partition_names"])-1?script("qsl('input').oninput = partitionNameChange;"):''),'<td><input name="partition_values[]" value="'.h(idx($K["partition_values"],$x)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Sd=array("PRIMARY","UNIQUE","INDEX");$S=table_status1($a,true);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$Sd[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$Sd[]="SPATIAL";$w=indexes($a);$G=array();if(JUSH=="mongo"){$G=$w["_id_"];unset($Sd[0]);unset($w["_id_"]);}$K=$_POST;if($K)save_settings(array("index_options"=>$K["options"]));if($_POST&&!$l&&!$_POST["add"]&&!$_POST["drop_col"]){$b=array();foreach($K["indexes"]as$v){$C=$v["name"];if(in_array($v["type"],$Sd)){$e=array();$ze=array();$Tb=array();$O=array();ksort($v["columns"]);foreach($v["columns"]as$x=>$d){if($d!=""){$y=idx($v["lengths"],$x);$Sb=idx($v["descs"],$x);$O[]=idf_escape($d).($y?"(".(+$y).")":"").($Sb?" DESC":"");$e[]=$d;$ze[]=($y?:null);$Tb[]=$Sb;}}$Ec=$w[$C];if($Ec){ksort($Ec["columns"]);ksort($Ec["lengths"]);ksort($Ec["descs"]);if($v["type"]==$Ec["type"]&&array_values($Ec["columns"])===$e&&(!$Ec["lengths"]||array_values($Ec["lengths"])===$ze)&&array_values($Ec["descs"])===$Tb){unset($w[$C]);continue;}}if($e)$b[]=array($v["type"],$C,$O);}}foreach($w
as$C=>$Ec)$b[]=array($Ec["type"],$C,"DROP");if(!$b)redirect(ME."table=".urlencode($a));queries_redirect(ME."table=".urlencode($a),'Indexes have been altered.',alter_indexes($a,$b));}page_header('Indexes',$l,array("table"=>$a),h($a));$n=array_keys(fields($a));if($_POST["add"]){foreach($K["indexes"]as$x=>$v){if($v["columns"][count($v["columns"])]!="")$K["indexes"][$x]["columns"][]="";}$v=end($K["indexes"]);if($v["type"]||array_filter($v["columns"],'strlen'))$K["indexes"][]=array("columns"=>array(1=>""));}if(!$K){foreach($w
as$x=>$v){$w[$x]["name"]=$x;$w[$x]["columns"][]="";}$w[]=array("columns"=>array(1=>""));$K["indexes"]=$w;}$ze=(JUSH=="sql"||JUSH=="mssql");$yh=($_POST?$_POST["options"]:get_setting("index_options"));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap">
<thead><tr>
<th id="label-type">Index Type
<th><input type="submit" class="wayoff">','Column'.($ze?"<span class='idxopts".($yh?"":" hidden")."'> (".'length'.")</span>":"");if($ze||support("descidx"))echo
checkbox("options",1,$yh,'Options',"indexOptionsShow(this.checked)","jsonly")."\n";echo'<th id="label-name">Name
<th><noscript>',icon("plus","add[0]","+",'Add next'),'</noscript>
</thead>
';if($G){echo"<tr><td>PRIMARY<td>";foreach($G["columns"]as$x=>$d)echo
select_input(" disabled",$n,$d),"<label><input disabled type='checkbox'>".'descending'."</label> ";echo"<td><td>\n";}$ke=1;foreach($K["indexes"]as$v){if(!$_POST["drop_col"]||$ke!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$ke][type]",array(-1=>"")+$Sd,$v["type"],($ke==count($K["indexes"])?"indexesAddRow.call(this);":""),"label-type"),"<td>";ksort($v["columns"]);$s=1;foreach($v["columns"]as$x=>$d){echo"<span>".select_input(" name='indexes[$ke][columns][$s]' title='".'Column'."'",($n?array_combine($n,$n):$n),$d,"partial(".($s==count($v["columns"])?"indexesAddColumn":"indexesChangeColumn").", '".js_escape(JUSH=="sql"?"":$_GET["indexes"]."_")."')"),"<span class='idxopts".($yh?"":" hidden")."'>",($ze?"<input type='number' name='indexes[$ke][lengths][$s]' class='size' value='".h(idx($v["lengths"],$x))."' title='".'Length'."'>":""),(support("descidx")?checkbox("indexes[$ke][descs][$s]",1,idx($v["descs"],$x),'descending'):""),"</span> </span>";$s++;}echo"<td><input name='indexes[$ke][name]' value='".h($v["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n","<td>".icon("cross","drop_col[$ke]","x",'Remove').script("qsl('button').onclick = partial(editingRemoveRow, 'indexes\$1[type]');");}$ke++;}echo'</table>
</div>
<p>
<input type="submit" value="Save">
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$K=$_POST;if($_POST&&!$l&&!$_POST["add"]){$C=trim($K["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),'Database has been dropped.',drop_databases(array(DB)));}elseif(DB!==$C){if(DB!=""){$_GET["db"]=$C;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".urlencode($C),'Database has been renamed.',rename_database($C,$K["collation"]));}else{$i=explode("\n",str_replace("\r","",$C));$Qh=true;$te="";foreach($i
as$j){if(count($i)==1||$j!=""){if(!create_database($j,$K["collation"]))$Qh=false;$te=$j;}}restart_session();set_session("dbs",null);queries_redirect(ME."db=".urlencode($te),'Database has been created.',$Qh);}}else{if(!$K["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($C).(preg_match('~^[a-z0-9_]+$~i',$K["collation"])?" COLLATE $K[collation]":""),substr(ME,0,-1),'Database has been altered.');}}page_header(DB!=""?'Alter database':'Create database',$l,array(),h(DB));$hb=collations();$C=DB;if($_POST)$C=$K["name"];elseif(DB!="")$K["collation"]=db_collation(DB,$hb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$nd){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$nd,$B)&&$B[1]){$C=stripcslashes(idf_unescape("`$B[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($C,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($C).'</textarea><br>':'<input name="name" autofocus value="'.h($C).'" data-maxlength="64" autocapitalize="off">')."\n".($hb?html_select("collation",array(""=>"(".'collation'.")")+$hb,$K["collation"]).doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",'mssql'=>"relational-databases/system-functions/sys-fn-helpcollations-transact-sql",)):""),'<input type="submit" value="Save">
';if(DB!="")echo"<input type='submit' name='drop' value='".'Drop'."'>".confirm(sprintf('Drop %s?',DB))."\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",'Add next')."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["scheme"])){$K=$_POST;if($_POST&&!$l){$_=preg_replace('~ns=[^&]*&~','',ME)."ns=";if($_POST["drop"])query_redirect("DROP SCHEMA ".idf_escape($_GET["ns"]),$_,'Schema has been dropped.');else{$C=trim($K["name"]);$_
.=urlencode($C);if($_GET["ns"]=="")query_redirect("CREATE SCHEMA ".idf_escape($C),$_,'Schema has been created.');elseif($_GET["ns"]!=$C)query_redirect("ALTER SCHEMA ".idf_escape($_GET["ns"])." RENAME TO ".idf_escape($C),$_,'Schema has been altered.');else
redirect($_);}}page_header($_GET["ns"]!=""?'Alter schema':'Create schema',$l);if(!$K)$K["name"]=$_GET["ns"];echo'
<form action="" method="post">
<p><input name="name" autofocus value="',h($K["name"]),'" autocapitalize="off">
<input type="submit" value="Save">
';if($_GET["ns"]!="")echo"<input type='submit' name='drop' value='".'Drop'."'>".confirm(sprintf('Drop %s?',$_GET["ns"]))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ba=($_GET["name"]?:$_GET["call"]);page_header('Call'.": ".h($ba),$l);$Zg=routine($_GET["call"],(isset($_GET["callf"])?"FUNCTION":"PROCEDURE"));$Od=array();$Tf=array();foreach($Zg["fields"]as$s=>$m){if(substr($m["inout"],-3)=="OUT")$Tf[$s]="@".idf_escape($m["field"])." AS ".idf_escape($m["field"]);if(!$m["inout"]||substr($m["inout"],0,2)=="IN")$Od[]=$s;}if(!$l&&$_POST){$Ra=array();foreach($Zg["fields"]as$x=>$m){$X="";if(in_array($x,$Od)){$X=process_input($m);if($X===false)$X="''";if(isset($Tf[$x]))connection()->query("SET @".idf_escape($m["field"])." = $X");}$Ra[]=(isset($Tf[$x])?"@".idf_escape($m["field"]):$X);}$H=(isset($_GET["callf"])?"SELECT":"CALL")." ".table($ba)."(".implode(", ",$Ra).")";$Lh=microtime(true);$I=connection()->multi_query($H);$oa=connection()->affected_rows;echo
adminer()->selectQuery($H,$Lh,!$I);if(!$I)echo"<p class='error'>".error()."\n";else{$g=connect(adminer()->credentials());if($g)$g->select_db(DB);do{$I=connection()->store_result();if(is_object($I))print_select_result($I,$g);else
echo"<p class='message'>".lang(array('Routine has been called, %d row affected.','Routine has been called, %d rows affected.'),$oa)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($Tf)print_select_result(connection()->query("SELECT ".implode(", ",$Tf)));}}echo'
<form action="" method="post">
';if($Od){echo"<table class='layout'>\n";foreach($Od
as$x){$m=$Zg["fields"][$x];$C=$m["field"];echo"<tr><th>".adminer()->fieldName($m);$Y=idx($_POST["fields"],$C);if($Y!=""){if($m["type"]=="set")$Y=implode(",",$Y);}input($m,$Y,idx($_POST["function"],$C,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type="submit" value="Call">
',input_token(),'</form>

<pre>
';function
pre_tr($dh){return
preg_replace('~^~m','<tr>',preg_replace('~\|~','<td>',preg_replace('~\|$~m',"",rtrim($dh))));}$R='(\+--[-+]+\+\n)';$K='(\| .* \|\n)';echo
preg_replace_callback("~^$R?$K$R?($K*)$R?~m",function($B){$Vc=pre_tr($B[2]);return"<table>\n".($B[1]?"<thead>$Vc</thead>\n":$Vc).pre_tr($B[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($Zg['comment']))));echo'</pre>
';}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$C=$_GET["name"];$K=$_POST;if($_POST&&!$l&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$K["source"]=array_filter($K["source"],'strlen');ksort($K["source"]);$fi=array();foreach($K["source"]as$x=>$X)$fi[$x]=$K["target"][$x];$K["target"]=$fi;}if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(" $C"=>($K["drop"]?"":" ".format_foreign_key($K))));else{$b="ALTER TABLE ".table($a);$I=($C==""||queries("$b DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($C)));if(!$K["drop"])$I=queries("$b ADD".format_foreign_key($K));}queries_redirect(ME."table=".urlencode($a),($K["drop"]?'Foreign key has been dropped.':($C!=""?'Foreign key has been altered.':'Foreign key has been created.')),$I);if(!$K["drop"])$l="$l<br>".'Source and target columns must have the same data type, there must be an index on the target columns and referenced data must exist.';}page_header('Foreign key',$l,array("table"=>$a),h($a));if($_POST){ksort($K["source"]);if($_POST["add"])$K["source"][]="";elseif($_POST["change"]||$_POST["change-js"])$K["target"]=array();}elseif($C!=""){$ed=foreign_keys($a);$K=$ed[$C];$K["source"][]="";}else{$K["table"]=$a;$K["source"]=array("");}echo'
<form action="" method="post">
';$Ch=array_keys(fields($a));if($K["db"]!="")connection()->select_db($K["db"]);if($K["ns"]!=""){$Pf=get_schema();set_schema($K["ns"]);}$Kg=array_keys(array_filter(table_status('',true),'Adminer\fk_support'));$fi=array_keys(fields(in_array($K["table"],$Kg)?$K["table"]:reset($Kg)));$_f="this.form['change-js'].value = '1'; this.form.submit();";echo"<p>".'Target table'.": ".html_select("table",$Kg,$K["table"],$_f)."\n";if(support("scheme")){$gh=array_filter(adminer()->schemas(),function($fh){return!preg_match('~^information_schema$~i',$fh);});echo'Schema'.": ".html_select("ns",$gh,$K["ns"]!=""?$K["ns"]:$_GET["ns"],$_f);if($K["ns"]!="")set_schema($Pf);}elseif(JUSH!="sqlite"){$Mb=array();foreach(adminer()->databases()as$j){if(!information_schema($j))$Mb[]=$j;}echo'DB'.": ".html_select("db",$Mb,$K["db"]!=""?$K["db"]:$_GET["db"],$_f);}echo
input_hidden("change-js"),'<noscript><p><input type="submit" name="change" value="Change"></noscript>
<table>
<thead><tr><th id="label-source">Source<th id="label-target">Target</thead>
';$ke=0;foreach($K["source"]as$x=>$X){echo"<tr>","<td>".html_select("source[".(+$x)."]",array(-1=>"")+$Ch,$X,($ke==count($K["source"])-1?"foreignAddRow.call(this);":""),"label-source"),"<td>".html_select("target[".(+$x)."]",$fi,idx($K["target"],$x),"","label-target");$ke++;}echo'</table>
<p>
ON DELETE: ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$K["on_delete"]),' ON UPDATE: ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$K["on_update"]),doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",'pgsql'=>"sql-createtable.html#SQL-CREATETABLE-REFERENCES",'mssql'=>"t-sql/statements/create-table-transact-sql",'oracle'=>"SQLRF01111",)),'<p>
<input type="submit" value="Save">
<noscript><p><input type="submit" name="add" value="Add column"></noscript>
';if($C!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$C));echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$K=$_POST;$Qf="VIEW";if(JUSH=="pgsql"&&$a!=""){$P=table_status1($a);$Qf=strtoupper($P["Engine"]);}if($_POST&&!$l){$C=trim($K["name"]);$wa=" AS\n$K[select]";$A=ME."table=".urlencode($C);$Re='View has been altered.';$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$C&&JUSH!="sqlite"&&$U=="VIEW"&&$Qf=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($C).$wa,$A,$Re);else{$hi=$C."_adminer_".uniqid();drop_create("DROP $Qf ".table($a),"CREATE $U ".table($C).$wa,"DROP $U ".table($C),"CREATE $U ".table($hi).$wa,"DROP $U ".table($hi),($_POST["drop"]?substr(ME,0,-1):$A),'View has been dropped.',$Re,'View has been created.',$a,$C);}}if(!$_POST&&$a!=""){$K=view($a);$K["name"]=$a;$K["materialized"]=($Qf!="VIEW");if(!$l)$l=error();}page_header(($a!=""?'Alter view':'Create view'),$l,array("table"=>$a),h($a));echo'
<form action="" method="post">
<p>Name: <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$K["materialized"],'Materialized view'):""),'<p>';textarea("select",$K["select"]);echo'<p>
<input type="submit" value="Save">
';if($a!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$a));echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$ce=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$Mh=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$K=$_POST;if($_POST&&!$l){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),'Event has been dropped.');elseif(in_array($K["INTERVAL_FIELD"],$ce)&&isset($Mh[$K["STATUS"]])){$eh="\nON SCHEDULE ".($K["INTERVAL_VALUE"]?"EVERY ".q($K["INTERVAL_VALUE"])." $K[INTERVAL_FIELD]".($K["STARTS"]?" STARTS ".q($K["STARTS"]):"").($K["ENDS"]?" ENDS ".q($K["ENDS"]):""):"AT ".q($K["STARTS"]))." ON COMPLETION".($K["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?'Event has been altered.':'Event has been created.'),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$eh.($aa!=$K["EVENT_NAME"]?"\nRENAME TO ".idf_escape($K["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($K["EVENT_NAME"]).$eh)."\n".$Mh[$K["STATUS"]]." COMMENT ".q($K["EVENT_COMMENT"]).rtrim(" DO\n$K[EVENT_DEFINITION]",";").";"));}}page_header(($aa!=""?'Alter event'.": ".h($aa):'Create event'),$l);if(!$K&&$aa!=""){$L=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$K=reset($L);}echo'
<form action="" method="post">
<table class="layout">
<tr><th>Name<td><input name="EVENT_NAME" value="',h($K["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">Start<td><input name="STARTS" value="',h("$K[EXECUTE_AT]$K[STARTS]"),'">
<tr><th title="datetime">End<td><input name="ENDS" value="',h($K["ENDS"]),'">
<tr><th>Every<td><input type="number" name="INTERVAL_VALUE" value="',h($K["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$ce,$K["INTERVAL_FIELD"]),'<tr><th>Status<td>',html_select("STATUS",$Mh,$K["STATUS"]),'<tr><th>Comment<td><input name="EVENT_COMMENT" value="',h($K["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$K["ON_COMPLETION"]=="PRESERVE",'On completion preserve'),'</table>
<p>';textarea("EVENT_DEFINITION",$K["EVENT_DEFINITION"]);echo'<p>
<input type="submit" value="Save">
';if($aa!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$aa));echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ba=($_GET["name"]?:$_GET["procedure"]);$Zg=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$K=$_POST;$K["fields"]=(array)$K["fields"];if($_POST&&!process_fields($K["fields"])&&!$l){$Mf=routine($_GET["procedure"],$Zg);$hi="$K[name]_adminer_".uniqid();foreach($K["fields"]as$x=>$m){if($m["field"]=="")unset($K["fields"][$x]);}drop_create("DROP $Zg ".routine_id($ba,$Mf),create_routine($Zg,$K),"DROP $Zg ".routine_id($K["name"],$K),create_routine($Zg,array("name"=>$hi)+$K),"DROP $Zg ".routine_id($hi,$K),substr(ME,0,-1),'Routine has been dropped.','Routine has been altered.','Routine has been created.',$ba,$K["name"]);}page_header(($ba!=""?(isset($_GET["function"])?'Alter function':'Alter procedure').": ".h($ba):(isset($_GET["function"])?'Create function':'Create procedure')),$l);if(!$_POST){if($ba=="")$K["language"]="sql";else{$K=routine($_GET["procedure"],$Zg);$K["name"]=$ba;}}$hb=get_vals("SHOW CHARACTER SET");sort($hb);$ah=routine_languages();echo($hb?"<datalist id='collations'>".optionlist($hb)."</datalist>":""),'
<form action="" method="post" id="form">
<p>Name: <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',($ah?'Language'.": ".html_select("language",$ah,$K["language"])."\n":""),'<input type="submit" value="Save">
<div class="scrollable">
<table class="nowrap">
';edit_fields($K["fields"],$hb,$Zg);if(isset($_GET["function"])){echo"<tr><td>".'Return type';edit_type("returns",$K["returns"],$hb,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$K["definition"]);echo'<p>
<input type="submit" value="Save">
';if($ba!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$ba));echo
input_token(),'</form>
';}elseif(isset($_GET["sequence"])){$da=$_GET["sequence"];$K=$_POST;if($_POST&&!$l){$_=substr(ME,0,-1);$C=trim($K["name"]);if($_POST["drop"])query_redirect("DROP SEQUENCE ".idf_escape($da),$_,'Sequence has been dropped.');elseif($da=="")query_redirect("CREATE SEQUENCE ".idf_escape($C),$_,'Sequence has been created.');elseif($da!=$C)query_redirect("ALTER SEQUENCE ".idf_escape($da)." RENAME TO ".idf_escape($C),$_,'Sequence has been altered.');else
redirect($_);}page_header($da!=""?'Alter sequence'.": ".h($da):'Create sequence',$l);if(!$K)$K["name"]=$da;echo'
<form action="" method="post">
<p><input name="name" value="',h($K["name"]),'" autocapitalize="off">
<input type="submit" value="Save">
';if($da!="")echo"<input type='submit' name='drop' value='".'Drop'."'>".confirm(sprintf('Drop %s?',$da))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["type"])){$ea=$_GET["type"];$K=$_POST;if($_POST&&!$l){$_=substr(ME,0,-1);if($_POST["drop"])query_redirect("DROP TYPE ".idf_escape($ea),$_,'Type has been dropped.');else
query_redirect("CREATE TYPE ".idf_escape(trim($K["name"]))." $K[as]",$_,'Type has been created.');}page_header($ea!=""?'Alter type'.": ".h($ea):'Create type',$l);if(!$K)$K["as"]="AS ";echo'
<form action="" method="post">
<p>
';if($ea!=""){$Ii=driver()->types();$wc=type_values($Ii[$ea]);if($wc)echo"<code class='jush-".JUSH."'>ENUM (".h($wc).")</code>\n<p>";echo"<input type='submit' name='drop' value='".'Drop'."'>".confirm(sprintf('Drop %s?',$ea))."\n";}else{echo'Name'.": <input name='name' value='".h($K['name'])."' autocapitalize='off'>\n",doc_link(array('pgsql'=>"datatype-enum.html",),"?");textarea("as",$K["as"]);echo"<p><input type='submit' value='".'Save'."'>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$C=$_GET["name"];$K=$_POST;if($K&&!$l){if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(),"",array(),"$C",($K["drop"]?"":$K["clause"]));else{$I=($C==""||queries("ALTER TABLE ".table($a)." DROP CONSTRAINT ".idf_escape($C)));if(!$K["drop"])$I=queries("ALTER TABLE ".table($a)." ADD".($K["name"]!=""?" CONSTRAINT ".idf_escape($K["name"]):"")." CHECK ($K[clause])");}queries_redirect(ME."table=".urlencode($a),($K["drop"]?'Check has been dropped.':($C!=""?'Check has been altered.':'Check has been created.')),$I);}page_header(($C!=""?'Alter check'.": ".h($C):'Create check'),$l,array("table"=>$a));if(!$K){$Za=driver()->checkConstraints($a);$K=array("name"=>$C,"clause"=>$Za[$C]);}echo'
<form action="" method="post">
<p>';if(JUSH!="sqlite")echo'Name'.': <input name="name" value="'.h($K["name"]).'" data-maxlength="64" autocapitalize="off"> ';echo
doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",'pgsql'=>"ddl-constraints.html#DDL-CONSTRAINTS-CHECK-CONSTRAINTS",'mssql'=>"relational-databases/tables/create-check-constraints",'sqlite'=>"lang_createtable.html#check_constraints",),"?"),'<p>';textarea("clause",$K["clause"]);echo'<p><input type="submit" value="Save">
';if($C!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$C));echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$C="$_GET[name]";$Ei=trigger_options();$K=(array)trigger($C,$a)+array("Trigger"=>$a."_bi");if($_POST){if(!$l&&in_array($_POST["Timing"],$Ei["Timing"])&&in_array($_POST["Event"],$Ei["Event"])&&in_array($_POST["Type"],$Ei["Type"])){$xf=" ON ".table($a);$bc="DROP TRIGGER ".idf_escape($C).(JUSH=="pgsql"?$xf:"");$A=ME."table=".urlencode($a);if($_POST["drop"])query_redirect($bc,$A,'Trigger has been dropped.');else{if($C!="")queries($bc);queries_redirect($A,($C!=""?'Trigger has been altered.':'Trigger has been created.'),queries(create_trigger($xf,$_POST)));if($C!="")queries(create_trigger($xf,$K+array("Type"=>reset($Ei["Type"]))));}}$K=$_POST;}page_header(($C!=""?'Alter trigger'.": ".h($C):'Create trigger'),$l,array("table"=>$a));echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>Time<td>',html_select("Timing",$Ei["Timing"],$K["Timing"],"triggerChange(/^".preg_quote($a,"/")."_[ba][iud]$/, '".js_escape($a)."', this.form);"),'<tr><th>Event<td>',html_select("Event",$Ei["Event"],$K["Event"],"this.form['Timing'].onchange();"),(in_array("UPDATE OF",$Ei["Event"])?" <input name='Of' value='".h($K["Of"])."' class='hidden'>":""),'<tr><th>Type<td>',html_select("Type",$Ei["Type"],$K["Type"]),'</table>
<p>Name: <input name="Trigger" value="',h($K["Trigger"]),'" data-maxlength="64" autocapitalize="off">
',script("qs('#form')['Timing'].onchange();"),'<p>';textarea("Statement",$K["Statement"]);echo'<p>
<input type="submit" value="Save">
';if($C!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$C));echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){$fa=$_GET["user"];$zg=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$K){foreach(explode(",",($K["Privilege"]=="Grant option"?"":$K["Context"]))as$xb)$zg[$xb][$K["Privilege"]]=$K["Comment"];}$zg["Server Admin"]+=$zg["File access on server"];$zg["Databases"]["Create routine"]=$zg["Procedures"]["Create routine"];unset($zg["Procedures"]["Create routine"]);$zg["Columns"]=array();foreach(array("Select","Insert","Update","References")as$X)$zg["Columns"][$X]=$zg["Tables"][$X];unset($zg["Server Admin"]["Usage"]);foreach($zg["Tables"]as$x=>$X)unset($zg["Databases"][$x]);$gf=array();if($_POST){foreach($_POST["objects"]as$x=>$X)$gf[$X]=(array)$gf[$X]+idx($_POST["grants"],$x,array());}$od=array();$vf="";if(isset($_GET["host"])&&($I=connection()->query("SHOW GRANTS FOR ".q($fa)."@".q($_GET["host"])))){while($K=$I->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$K[0],$B)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$B[1],$He,PREG_SET_ORDER)){foreach($He
as$X){if($X[1]!="USAGE")$od["$B[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$K[0]))$od["$B[2]$X[2]"]["GRANT OPTION"]=true;}}if(preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~",$K[0],$B))$vf=$B[1];}}if($_POST&&!$l){$wf=(isset($_GET["host"])?q($fa)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $wf",ME."privileges=",'User has been dropped.');else{$if=q($_POST["user"])."@".q($_POST["host"]);$gg=$_POST["pass"];if($gg!=''&&!$_POST["hashed"]&&!min_version(8)){$gg=get_val("SELECT PASSWORD(".q($gg).")");$l=!$gg;}$Ab=false;if(!$l){if($wf!=$if){$Ab=queries((min_version(5)?"CREATE USER":"GRANT USAGE ON *.* TO")." $if IDENTIFIED BY ".(min_version(8)?"":"PASSWORD ").q($gg));$l=!$Ab;}elseif($gg!=$vf)queries("SET PASSWORD FOR $if = ".q($gg));}if(!$l){$Wg=array();foreach($gf
as$pf=>$nd){if(isset($_GET["grant"]))$nd=array_filter($nd);$nd=array_keys($nd);if(isset($_GET["grant"]))$Wg=array_diff(array_keys(array_filter($gf[$pf],'strlen')),$nd);elseif($wf==$if){$tf=array_keys((array)$od[$pf]);$Wg=array_diff($tf,$nd);$nd=array_diff($nd,$tf);unset($od[$pf]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$pf,$B)&&(!grant("REVOKE",$Wg,$B[2]," ON $B[1] FROM $if")||!grant("GRANT",$nd,$B[2]," ON $B[1] TO $if"))){$l=true;break;}}}if(!$l&&isset($_GET["host"])){if($wf!=$if)queries("DROP USER $wf");elseif(!isset($_GET["grant"])){foreach($od
as$pf=>$Wg){if(preg_match('~^(.+)(\(.*\))?$~U',$pf,$B))grant("REVOKE",array_keys($Wg),$B[2]," ON $B[1] FROM $if");}}}queries_redirect(ME."privileges=",(isset($_GET["host"])?'User has been altered.':'User has been created.'),!$l);if($Ab)connection()->query("DROP USER $if");}}page_header((isset($_GET["host"])?'Username'.": ".h("$fa@$_GET[host]"):'Create user'),$l,array("privileges"=>array('','Privileges')));$K=$_POST;if($K)$od=$gf;else{$K=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$K["pass"]=$vf;if($vf!="")$K["hashed"]=true;$od[(DB==""||$od?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>Server<td><input name="host" data-maxlength="60" value="',h($K["host"]),'" autocapitalize="off">
<tr><th>Username<td><input name="user" data-maxlength="80" value="',h($K["user"]),'" autocapitalize="off">
<tr><th>Password<td><input name="pass" id="pass" value="',h($K["pass"]),'" autocomplete="new-password">
',($K["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8)?"":checkbox("hashed",1,$K["hashed"],'Hashed',"typePassword(this.form['pass'], this.checked);")),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".'Privileges'.doc_link(array('sql'=>"grant.html#priv_level"));$s=0;foreach($od
as$pf=>$nd){echo'<th>'.($pf!="*.*"?"<input name='objects[$s]' value='".h($pf)."' size='10' autocapitalize='off'>":input_hidden("objects[$s]","*.*")."*.*");$s++;}echo"</thead>\n";foreach(array(""=>"","Server Admin"=>'Server',"Databases"=>'Database',"Tables"=>'Table',"Columns"=>'Column',"Procedures"=>'Routine',)as$xb=>$Sb){foreach((array)$zg[$xb]as$yg=>$mb){echo"<tr><td".($Sb?">$Sb<td":" colspan='2'").' lang="en" title="'.h($mb).'">'.h($yg);$s=0;foreach($od
as$pf=>$nd){$C="'grants[$s][".h(strtoupper($yg))."]'";$Y=$nd[strtoupper($yg)];if($xb=="Server Admin"&&$pf!=(isset($od["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$C><option><option value='1'".($Y?" selected":"").">".'Grant'."<option value='0'".($Y=="0"?" selected":"").">".'Revoke'."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$C value='1'".($Y?" checked":"").($yg=="All privileges"?" id='grants-$s-all'>":">".($yg=="Grant option"?"":script("qsl('input').onclick = function () { if (this.checked) formUncheck('grants-$s-all'); };"))),"</label>";$s++;}}}echo"</table>\n",'<p>
<input type="submit" value="Save">
';if(isset($_GET["host"]))echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',"$fa@$_GET[host]"));echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$l){$qe=0;foreach((array)$_POST["kill"]as$X){if(kill_process($X))$qe++;}queries_redirect(ME."processlist=",lang(array('%d process has been killed.','%d processes have been killed.'),$qe),$qe||!$_POST["kill"]);}}page_header('Process list',$l);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds">
',script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});");$s=-1;foreach(process_list()as$s=>$K){if(!$s){echo"<thead><tr lang='en'>".(support("kill")?"<th>":"");foreach($K
as$x=>$X)echo"<th>$x".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($x),'pgsql'=>"monitoring-stats.html#PG-STAT-ACTIVITY-VIEW",'oracle'=>"REFRN30223",));echo"</thead>\n";}echo"<tr>".(support("kill")?"<td>".checkbox("kill[]",$K[JUSH=="sql"?"Id":"pid"],0):"");foreach($K
as$x=>$X)echo"<td>".((JUSH=="sql"&&$x=="Info"&&preg_match("~Query|Killed~",$K["Command"])&&$X!="")||(JUSH=="pgsql"&&$x=="current_query"&&$X!="<IDLE>")||(JUSH=="oracle"&&$x=="sql_text"&&$X!="")?"<code class='jush-".JUSH."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(ME.($K["db"]!=""?"db=".urlencode($K["db"])."&":"")."sql=".urlencode($X)).'">'.'Clone'.'</a>':h($X));echo"\n";}echo'</table>
</div>
<p>
';if(support("kill"))echo($s+1)."/".sprintf('%d in total',max_connections()),"<p><input type='submit' value='".'Kill'."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif(isset($_GET["select"])){$a=$_GET["select"];$S=table_status1($a);$w=indexes($a);$n=fields($a);$ed=column_foreign_keys($a);$rf=$S["Oid"];$na=get_settings("adminer_import");$Xg=array();$e=array();$jh=array();$If=array();$li="";foreach($n
as$x=>$m){$C=adminer()->fieldName($m);$ef=html_entity_decode(strip_tags($C),ENT_QUOTES);if(isset($m["privileges"]["select"])&&$C!=""){$e[$x]=$ef;if(is_shortable($m))$li=adminer()->selectLengthProcess();}if(isset($m["privileges"]["where"])&&$C!="")$jh[$x]=$ef;if(isset($m["privileges"]["order"])&&$C!="")$If[$x]=$ef;$Xg+=$m["privileges"];}list($M,$pd)=adminer()->selectColumnsProcess($e,$w);$M=array_unique($M);$pd=array_unique($pd);$ge=count($pd)<count($M);$Z=adminer()->selectSearchProcess($n,$w);$Hf=adminer()->selectOrderProcess($n,$w);$z=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$Ni=>$K){$wa=convert_field($n[key($K)]);$M=array($wa?:idf_escape(key($K)));$Z[]=where_check($Ni,$n);$J=driver()->select($a,$M,$Z,$M);if($J)echo
first($J->fetch_row());}exit;}$G=$Pi=null;foreach($w
as$v){if($v["type"]=="PRIMARY"){$G=array_flip($v["columns"]);$Pi=($M?$G:array());foreach($Pi
as$x=>$X){if(in_array(idf_escape($x),$M))unset($Pi[$x]);}break;}}if($rf&&!$G){$G=$Pi=array($rf=>0);$w[]=array("type"=>"PRIMARY","columns"=>array($rf));}if($_POST&&!$l){$nj=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$Za=array();foreach($_POST["check"]as$Va)$Za[]=where_check($Va,$n);$nj[]="((".implode(") OR (",$Za)."))";}$nj=($nj?"\nWHERE ".implode(" AND ",$nj):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$id=($M?implode(", ",$M):"*").convert_fields($e,$n,$M)."\nFROM ".table($a);$rd=($pd&&$ge?"\nGROUP BY ".implode(", ",$pd):"").($Hf?"\nORDER BY ".implode(", ",$Hf):"");$H="SELECT $id$nj$rd";if(is_array($_POST["check"])&&!$G){$Li=array();foreach($_POST["check"]as$X)$Li[]="(SELECT".limit($id,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$n).$rd,1).")";$H=implode(" UNION ALL ",$Li);}adminer()->dumpData($a,"table",$H);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$ed)){if($_POST["save"]||$_POST["delete"]){$I=true;$oa=0;$O=array();if(!$_POST["delete"]){foreach($_POST["fields"]as$C=>$X){$X=process_input($n[$C]);if($X!==null&&($_POST["clone"]||$X!==false))$O[idf_escape($C)]=($X!==false?$X:idf_escape($C));}}if($_POST["delete"]||$O){$H=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($O)).")\nSELECT ".implode(", ",$O)."\nFROM ".table($a):"");if($_POST["all"]||($G&&is_array($_POST["check"]))||$ge){$I=($_POST["delete"]?driver()->delete($a,$nj):($_POST["clone"]?queries("INSERT $H$nj".driver()->insertReturning($a)):driver()->update($a,$O,$nj)));$oa=connection()->affected_rows;if(is_object($I))$oa+=$I->num_rows;}else{foreach((array)$_POST["check"]as$X){$mj="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$n);$I=($_POST["delete"]?driver()->delete($a,$mj,1):($_POST["clone"]?queries("INSERT".limit1($a,$H,$mj)):driver()->update($a,$O,$mj,1)));if(!$I)break;$oa+=connection()->affected_rows;}}}$Re=lang(array('%d item has been affected.','%d items have been affected.'),$oa);if($_POST["clone"]&&$I&&$oa==1){$ue=last_id($I);if($ue)$Re=sprintf('Item%s has been inserted.'," $ue");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page":""),$Re,$I);if(!$_POST["delete"]){$sg=(array)$_POST["fields"];edit_form($a,array_intersect_key($n,$sg),$sg,!$_POST["clone"],$l);page_footer();exit;}}elseif(!$_POST["import"]){if(!$_POST["val"])$l='Ctrl+click on a value to modify it.';else{$I=true;$oa=0;foreach($_POST["val"]as$Ni=>$K){$O=array();foreach($K
as$x=>$X){$x=bracket_escape($x,true);$O[idf_escape($x)]=(preg_match('~char|text~',$n[$x]["type"])||$X!=""?adminer()->processInput($n[$x],$X):"NULL");}$I=driver()->update($a,$O," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($Ni,$n),($ge||$G?0:1)," ");if(!$I)break;$oa+=connection()->affected_rows;}queries_redirect(remove_from_uri(),lang(array('%d item has been affected.','%d items have been affected.'),$oa),$I);}}elseif(!is_string($Sc=get_file("csv_file",true)))$l=upload_error($Sc);elseif(!preg_match('~~u',$Sc))$l='File must be in UTF-8 encoding.';else{save_settings(array("output"=>$na["output"],"format"=>$_POST["separator"]),"adminer_import");$I=true;$ib=array_keys($n);preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Sc,$He);$oa=count($He[0]);driver()->begin();$ph=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$L=array();foreach($He[0]as$x=>$X){preg_match_all("~((?>\"[^\"]*\")+|[^$ph]*)$ph~",$X.$ph,$Ie);if(!$x&&!array_diff($Ie[1],$ib)){$ib=$Ie[1];$oa--;}else{$O=array();foreach($Ie[1]as$s=>$fb)$O[idf_escape($ib[$s])]=($fb==""&&$n[$ib[$s]]["null"]?"NULL":q(preg_match('~^".*"$~s',$fb)?str_replace('""','"',substr($fb,1,-1)):$fb));$L[]=$O;}}$I=(!$L||driver()->insertUpdate($a,$L,$G));if($I)driver()->commit();queries_redirect(remove_from_uri("page"),lang(array('%d row has been imported.','%d rows have been imported.'),$oa),$I);driver()->rollback();}}}$Xh=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header('Select'.": $Xh",$l);$O=null;if(isset($Xg["insert"])||!support("table")){$Zf=array();foreach((array)$_GET["where"]as$X){if(isset($ed[$X["col"]])&&count($ed[$X["col"]])==1&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$X["val"])))))$Zf["set"."[".bracket_escape($X["col"])."]"]=$X["val"];}$O=$Zf?"&".http_build_query($Zf):"";}adminer()->selectLinks($S,$O);if(!$e&&support("table"))echo"<p class='error'>".'Unable to select the table'.($n?".":": ".error())."\n";else{echo"<form action='' id='form'>\n","<div style='display: none;'>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($M,$e);adminer()->selectSearchPrint($Z,$jh,$w);adminer()->selectOrderPrint($Hf,$If,$w);adminer()->selectLimitPrint($z);adminer()->selectLengthPrint($li);adminer()->selectActionPrint($w);echo"</form>\n";$E=$_GET["page"];if($E=="last"){$hd=get_val(count_rows($a,$Z,$ge,$pd));$E=floor(max(0,intval($hd)-1)/$z);}$kh=$M;$qd=$pd;if(!$kh){$kh[]="*";$yb=convert_fields($e,$n,$M);if($yb)$kh[]=substr($yb,2);}foreach($M
as$x=>$X){$m=$n[idf_unescape($X)];if($m&&($wa=convert_field($m)))$kh[$x]="$wa AS $X";}if(!$ge&&$Pi){foreach($Pi
as$x=>$X){$kh[]=idf_escape($x);if($qd)$qd[]=idf_escape($x);}}$I=driver()->select($a,$kh,$Z,$qd,$Hf,$z,$E,true);if(!$I)echo"<p class='error'>".error()."\n";else{if(JUSH=="mssql"&&$E)$I->seek($z*$E);$pc=array();echo"<form action='' method='post' enctype='multipart/form-data'>\n";$L=array();while($K=$I->fetch_assoc()){if($E&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}if($_GET["page"]!="last"&&$z&&$pd&&$ge&&JUSH=="sql")$hd=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".'No rows.'."\n";else{$Ea=adminer()->backwardKeys($a,$Xh);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'>",script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"),"<thead><tr>".(!$pd&&$M?"":"<td><input type='checkbox' id='all-page' class='jsonly'>".script("qs('#all-page').onclick = partial(formCheck, /check/);","")." <a href='".h($_GET["modify"]?remove_from_uri("modify"):$_SERVER["REQUEST_URI"]."&modify=1")."'>".'Modify'."</a>");$ff=array();$kd=array();reset($M);$Hg=1;foreach($L[0]as$x=>$X){if(!isset($Pi[$x])){$X=idx($_GET["columns"],key($M))?:array();$m=$n[$M?($X?$X["col"]:current($M)):$x];$C=($m?adminer()->fieldName($m,$Hg):($X["fun"]?"*":h($x)));if($C!=""){$Hg++;$ff[$x]=$C;$d=idf_escape($x);$Gd=remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($x);$Sb="&desc%5B0%5D=1";echo"<th id='th[".h(bracket_escape($x))."]'>".script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});","");$jd=apply_sql_function($X["fun"],$C);$Bh=isset($m["privileges"]["order"])||$jd;echo($Bh?'<a href="'.h($Gd.($Hf[0]==$d||$Hf[0]==$x||(!$Hf&&$ge&&$pd[0]==$d)?$Sb:'')).'">'."$jd</a>":$jd),"<span class='column hidden'>";if($Bh)echo"<a href='".h($Gd.$Sb)."' title='".'descending'."' class='text'> ↓</a>";if(!$X["fun"]&&isset($m["privileges"]["where"]))echo'<a href="#fieldset-search" title="'.'Search'.'" class="text jsonly"> =</a>',script("qsl('a').onclick = partial(selectSearch, '".js_escape($x)."');");echo"</span>";}$kd[$x]=$X["fun"];next($M);}}$ze=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$x=>$X)$ze[$x]=max($ze[$x],min(40,strlen(utf8_decode($X))));}}echo($Ea?"<th>".'Relations':"")."</thead>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($L,$ed)as$df=>$K){$Mi=unique_array($L[$df],$w);if(!$Mi){$Mi=array();foreach($L[$df]as$x=>$X){if(!preg_match('~^(COUNT\((\*|(DISTINCT )?`(?:[^`]|``)+`)\)|(AVG|GROUP_CONCAT|MAX|MIN|SUM)\(`(?:[^`]|``)+`\))$~',$x))$Mi[$x]=$X;}}$Ni="";foreach($Mi
as$x=>$X){$m=(array)$n[$x];if((JUSH=="sql"||JUSH=="pgsql")&&preg_match('~char|text|enum|set~',$m["type"])&&strlen($X)>64){$x=(strpos($x,'(')?$x:idf_escape($x));$x="MD5(".(JUSH!='sql'||preg_match("~^utf8~",$m["collation"])?$x:"CONVERT($x USING ".charset(connection()).")").")";$X=md5($X);}$Ni
.="&".($X!==null?urlencode("where[".bracket_escape($x)."]")."=".urlencode($X===false?"f":$X):"null%5B%5D=".urlencode($x));}echo"<tr>".(!$pd&&$M?"":"<td>".checkbox("check[]",substr($Ni,1),in_array(substr($Ni,1),(array)$_POST["check"])).($ge||information_schema(DB)?"":" <a href='".h(ME."edit=".urlencode($a).$Ni)."' class='edit'>".'edit'."</a>"));foreach($K
as$x=>$X){if(isset($ff[$x])){$m=(array)$n[$x];$X=driver()->value($X,$m);if($X!=""&&(!isset($pc[$x])||$pc[$x]!=""))$pc[$x]=(is_mail($X)?$ff[$x]:"");$_="";if(preg_match('~blob|bytea|raw|file~',$m["type"])&&$X!="")$_=ME.'download='.urlencode($a).'&field='.urlencode($x).$Ni;if(!$_&&$X!==null){foreach((array)$ed[$x]as$p){if(count($ed[$x])==1||end($p["source"])==$x){$_="";foreach($p["source"]as$s=>$Ch)$_
.=where_link($s,$p["target"][$s],$L[$df][$Ch]);$_=($p["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.urlencode($p["db"]),ME):ME).'select='.urlencode($p["table"]).$_;if($p["ns"])$_=preg_replace('~([?&]ns=)[^&]+~','\1'.urlencode($p["ns"]),$_);if(count($p["source"])==1)break;}}}if($x=="COUNT(*)"){$_=ME."select=".urlencode($a);$s=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$Mi))$_
.=where_link($s++,$W["col"],$W["val"],$W["op"]);}foreach($Mi
as$me=>$W)$_
.=where_link($s++,$me,$W);}$X=select_value($X,$_,$m,$li);$t=h("val[$Ni][".bracket_escape($x)."]");$Y=idx(idx($_POST["val"],$Ni),bracket_escape($x));$kc=!is_array($K[$x])&&is_utf8($X)&&$L[$df][$x]==$K[$x]&&!$kd[$x]&&!$m["generated"];$ji=preg_match('~text|json|lob~',$m["type"]);echo"<td id='$t'".(preg_match(number_type(),$m["type"])&&($X=='<i>NULL</i>'||is_numeric(strip_tags($X)))?" class='number'":"");if(($_GET["modify"]&&$kc)||$Y!==null){$ud=h($Y!==null?$Y:$K[$x]);echo">".($ji?"<textarea name='$t' cols='30' rows='".(substr_count($K[$x],"\n")+1)."'>$ud</textarea>":"<input name='$t' value='$ud' size='$ze[$x]'>");}else{$Ce=strpos($X,"<i>…</i>");echo" data-text='".($Ce?2:($ji?1:0))."'".($kc?"":" data-warning='".h('Use edit link to modify this value.')."'").">$X";}}}if($Ea)echo"<td>";adminer()->backwardKeysPrint($Ea,$L[$df]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){if($L||$E){$Cc=true;$hd=null;if($_GET["page"]!="last"){if(!$z||(count($L)<$z&&($L||!$E)))$hd=($E?$E*$z:0)+count($L);elseif(JUSH!="sql"||!$ge){$hd=($ge?false:found_rows($S,$Z));if(intval($hd)<max(1e4,2*($E+1)*$z))$hd=first(slow_query(count_rows($a,$Z,$ge,$pd)));else$Cc=false;}}$Xf=($z&&($hd===false||$hd>$z||$E));if($Xf)echo(($hd===false?count($L)+1:$hd-$E*$z)>$z?'<p><a href="'.h(remove_from_uri("page")."&page=".($E+1)).'" class="loadmore">'.'Load more data'.'</a>'.script("qsl('a').onclick = partial(selectLoadMore, $z, '".'Loading'."…');",""):''),"\n";echo"<div class='footer'><div>\n";if($Xf){$Ke=($hd===false?$E+(count($L)>=$z?2:1):floor(($hd-1)/$z));echo"<fieldset>";if(JUSH!="simpledb"){echo"<legend><a href='".h(remove_from_uri("page"))."'>".'Page'."</a></legend>",script("qsl('a').onclick = function () { pageClick(this.href, +prompt('".'Page'."', '".($E+1)."')); return false; };"),pagination(0,$E).($E>5?" …":"");for($s=max(1,$E-4);$s<min($Ke,$E+5);$s++)echo
pagination($s,$E);if($Ke>0)echo($E+5<$Ke?" …":""),($Cc&&$hd!==false?pagination($Ke,$E):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Ke'>".'last'."</a>");}else
echo"<legend>".'Page'."</legend>",pagination(0,$E).($E>1?" …":""),($E?pagination($E,$E):""),($Ke>$E?pagination($E+1,$E).($Ke>$E+1?" …":""):"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".'Whole result'."</legend>";$Yb=($Cc?"":"~ ").$hd;$Af="const checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$Yb' : checked); selectCount('selected2', this.checked || !checked ? '$Yb' : checked);";echo
checkbox("all",1,0,($hd!==false?($Cc?"":"~ ").lang(array('%d row','%d rows'),$hd):""),$Af)."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':' class="jsonly"'),'><legend>Modify</legend><div>
<input type="submit" value="Save"',($_GET["modify"]?'':' title="'.'Ctrl+click on a value to modify it.'.'"'),'>
</div></fieldset>
<fieldset><legend>Selected <span id="selected"></span></legend><div>
<input type="submit" name="edit" value="Edit">
<input type="submit" name="clone" value="Clone">
<input type="submit" name="delete" value="Delete">',confirm(),'</div></fieldset>
';$fd=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$d){if($d["fun"]){unset($fd['sql']);break;}}if($fd){print_fieldset("export",'Export'." <span id='selected2'></span>");$Uf=adminer()->dumpOutput();echo($Uf?html_select("output",$Uf,$na["output"])." ":""),html_select("format",$fd,$na["format"])," <input type='submit' name='export' value='".'Export'."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($pc,'strlen'),$e);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<div>","<a href='#import'>".'Import'."</a>",script("qsl('a').onclick = partial(toggle, 'import');",""),"<span id='import'".($_POST["import"]?"":" class='hidden'").">: ","<input type='file' name='csv_file'> ",html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$na["format"])," <input type='submit' name='import' value='".'Import'."'>","</span>","</div>";echo
input_token(),"</form>\n",(!$pd&&$M?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$P=isset($_GET["status"]);page_header($P?'Status':'Variables');$dj=($P?show_status():show_variables());if(!$dj)echo"<p class='message'>".'No rows.'."\n";else{echo"<table>\n";foreach($dj
as$K){echo"<tr>";$x=array_shift($K);echo"<th><code class='jush-".JUSH.($P?"status":"set")."'>".h($x)."</code>";foreach($K
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: text/javascript; charset=utf-8");if($_GET["script"]=="db"){$Th=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$C=>$S){json_row("Comment-$C",h($S["Comment"]));if(!is_view($S)){foreach(array("Engine","Collation")as$x)json_row("$x-$C",h($S[$x]));foreach($Th+array("Auto_increment"=>0,"Rows"=>0)as$x=>$X){if($S[$x]!=""){$X=format_number($S[$x]);if($X>=0)json_row("$x-$C",($x=="Rows"&&$X&&$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")?"~ $X":$X));if(isset($Th[$x]))$Th[$x]+=($S["Engine"]!="InnoDB"||$x!="Data_free"?$S[$x]:0);}elseif(array_key_exists($x,$S))json_row("$x-$C","?");}}}foreach($Th
as$x=>$X)json_row("sum-$x",format_number($X));json_row("");}elseif($_GET["script"]=="kill")connection()->query("KILL ".number($_POST["kill"]));else{foreach(count_tables(adminer()->databases())as$j=>$X){json_row("tables-$j",$X);json_row("size-$j",db_size($j));}json_row("");}exit;}else{$di=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($di&&!$l&&!$_POST["search"]){$I=true;$Re="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$I=truncate_tables($_POST["tables"]);$Re='Tables have been truncated.';}elseif($_POST["move"]){$I=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$Re='Tables have been moved.';}elseif($_POST["copy"]){$I=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$Re='Tables have been copied.';}elseif($_POST["drop"]){if($_POST["views"])$I=drop_views($_POST["views"]);if($I&&$_POST["tables"])$I=drop_tables($_POST["tables"]);$Re='Tables have been dropped.';}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$K)$Re
.="<b>".h($R)."</b>: ".h($K["integrity_check"])."<br>";}}elseif(JUSH!="sql"){$I=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?"":" ANALYZE"),$_POST["tables"]));$Re='Tables have been optimized.';}elseif(!$_POST["tables"])$Re='No tables.';elseif($I=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($K=$I->fetch_assoc())$Re
.="<b>".h($K["Table"])."</b>: ".h($K["Msg_text"])."<br>";}queries_redirect(substr(ME,0,-1),$Re,$I);}page_header(($_GET["ns"]==""?'Database'.": ".h(DB):'Schema'.": ".h($_GET["ns"])),$l,true);if(adminer()->homepage()){if($_GET["ns"]!==""){echo"<h3 id='tables-views'>".'Tables and views'."</h3>\n";$ci=tables_list();if(!$ci)echo"<p class='message'>".'No tables.'."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".'Search data in tables'." <span id='selected2'></span></legend><div>","<input type='search' name='query' value='".h($_POST["query"])."'>",script("qsl('input').onkeydown = partialArg(bodyKeydown, 'search');","")," <input type='submit' name='search' value='".'Search'."'>\n","</div></fieldset>\n";if($_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=driver()->convertOperator("LIKE %%");search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr class="wrap">','<td><input id="check-all" type="checkbox" class="jsonly">'.script("qs('#check-all').onclick = partial(formCheck, /^(tables|views)\[/);",""),'<th>'.'Table','<td>'.'Engine'.doc_link(array('sql'=>'storage-engines.html')),'<td>'.'Collation'.doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')),'<td>'.'Data Length'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT','oracle'=>'REFRN20286')),'<td>'.'Index Length'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT')),'<td>'.'Data Free'.doc_link(array('sql'=>'show-table-status.html')),'<td>'.'Auto Increment'.doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),'<td>'.'Rows'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'catalog-pg-class.html#CATALOG-PG-CLASS','oracle'=>'REFRN20286')),(support("comment")?'<td>'.'Comment'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-info.html#FUNCTIONS-INFO-COMMENT-TABLE')):''),"</thead>\n";$T=0;foreach($ci
as$C=>$U){$gj=($U!==null&&!preg_match('~table|sequence~i',$U));$t=h("Table-".$C);echo'<tr><td>'.checkbox(($gj?"views[]":"tables[]"),$C,in_array("$C",$di,true),"","","",$t),'<th>'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".urlencode($C)."' title='".'Show structure'."' id='$t'>".h($C).'</a>':h($C));if($gj)echo'<td colspan="6"><a href="'.h(ME)."view=".urlencode($C).'" title="'.'Alter view'.'">'.(preg_match('~materialized~i',$U)?'Materialized view':'View').'</a>','<td align="right"><a href="'.h(ME)."select=".urlencode($C).'" title="'.'Select data'.'">?</a>';else{foreach(array("Engine"=>array(),"Collation"=>array(),"Data_length"=>array("create",'Alter table'),"Index_length"=>array("indexes",'Alter indexes'),"Data_free"=>array("edit",'New item'),"Auto_increment"=>array("auto_increment=1&create",'Alter table'),"Rows"=>array("select",'Select data'),)as$x=>$_){$t=" id='$x-".h($C)."'";echo($_?"<td align='right'>".(support("table")||$x=="Rows"||(support("indexes")&&$x!="Data_length")?"<a href='".h(ME."$_[0]=").urlencode($C)."'$t title='$_[1]'>?</a>":"<span$t>?</span>"):"<td id='$x-".h($C)."'>");}$T++;}echo(support("comment")?"<td id='Comment-".h($C)."'>":""),"\n";}echo"<tr><td><th>".sprintf('%d in total',count($ci)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),"<td>".h(db_collation(DB,collations()));foreach(array("Data_length","Index_length","Data_free")as$x)echo"<td align='right' id='sum-$x'>";echo"\n","</table>\n","</div>\n";if(!information_schema(DB)){echo"<div class='footer'><div>\n";$aj="<input type='submit' value='".'Vacuum'."'> ".on_help("'VACUUM'");$Df="<input type='submit' name='optimize' value='".'Optimize'."'> ".on_help(JUSH=="sql"?"'OPTIMIZE TABLE'":"'VACUUM OPTIMIZE'");echo"<fieldset><legend>".'Selected'." <span id='selected'></span></legend><div>".(JUSH=="sqlite"?$aj."<input type='submit' name='check' value='".'Check'."'> ".on_help("'PRAGMA integrity_check'"):(JUSH=="pgsql"?$aj.$Df:(JUSH=="sql"?"<input type='submit' value='".'Analyze'."'> ".on_help("'ANALYZE TABLE'").$Df."<input type='submit' name='check' value='".'Check'."'> ".on_help("'CHECK TABLE'")."<input type='submit' name='repair' value='".'Repair'."'> ".on_help("'REPAIR TABLE'"):"")))."<input type='submit' name='truncate' value='".'Truncate'."'> ".on_help(JUSH=="sqlite"?"'DELETE'":"'TRUNCATE".(JUSH=="pgsql"?"'":" TABLE'")).confirm()."<input type='submit' name='drop' value='".'Drop'."'>".on_help("'DROP TABLE'").confirm()."\n";$i=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($i)!=1&&JUSH!="sqlite"){$j=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo"<p>".'Move to other database'.": ",($i?html_select("target",$i,$j):'<input name="target" value="'.h($j).'" autocapitalize="off">')," <input type='submit' name='move' value='".'Move'."'>",(support("copy")?" <input type='submit' name='copy' value='".'Copy'."'> ".checkbox("overwrite",1,$_POST["overwrite"],'overwrite'):""),"\n";}echo"<input type='hidden' name='all' value=''>",script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^(tables|views)\[/));".(support("table")?" selectCount('selected2', formChecked(this, /^tables\[/) || $T);":"")." }"),input_token(),"</div></fieldset>\n","</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo"<p class='links'><a href='".h(ME)."create='>".'Create table'."</a>\n",(support("view")?"<a href='".h(ME)."view='>".'Create view'."</a>\n":"");if(support("routine")){echo"<h3 id='routines'>".'Routines'."</h3>\n";$bh=routines();if($bh){echo"<table class='odds'>\n",'<thead><tr><th>'.'Name'.'<td>'.'Type'.'<td>'.'Return type'."<td></thead>\n";foreach($bh
as$K){$C=($K["SPECIFIC_NAME"]==$K["ROUTINE_NAME"]?"":"&name=".urlencode($K["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').urlencode($K["SPECIFIC_NAME"]).$C).'">'.h($K["ROUTINE_NAME"]).'</a>','<td>'.h($K["ROUTINE_TYPE"]),'<td>'.h($K["DTD_IDENTIFIER"]),'<td><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').urlencode($K["SPECIFIC_NAME"]).$C).'">'.'Alter'."</a>";}echo"</table>\n";}echo'<p class="links">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.'Create procedure'.'</a>':'').'<a href="'.h(ME).'function=">'.'Create function'."</a>\n";}if(support("sequence")){echo"<h3 id='sequences'>".'Sequences'."</h3>\n";$sh=get_vals("SELECT sequence_name FROM information_schema.sequences WHERE sequence_schema = current_schema() ORDER BY sequence_name");if($sh){echo"<table class='odds'>\n","<thead><tr><th>".'Name'."</thead>\n";foreach($sh
as$X)echo"<tr><th><a href='".h(ME)."sequence=".urlencode($X)."'>".h($X)."</a>\n";echo"</table>\n";}echo"<p class='links'><a href='".h(ME)."sequence='>".'Create sequence'."</a>\n";}if(support("type")){echo"<h3 id='user-types'>".'User types'."</h3>\n";$Yi=types();if($Yi){echo"<table class='odds'>\n","<thead><tr><th>".'Name'."</thead>\n";foreach($Yi
as$X)echo"<tr><th><a href='".h(ME)."type=".urlencode($X)."'>".h($X)."</a>\n";echo"</table>\n";}echo"<p class='links'><a href='".h(ME)."type='>".'Create type'."</a>\n";}if(support("event")){echo"<h3 id='events'>".'Events'."</h3>\n";$L=get_rows("SHOW EVENTS");if($L){echo"<table>\n","<thead><tr><th>".'Name'."<td>".'Schedule'."<td>".'Start'."<td>".'End'."<td></thead>\n";foreach($L
as$K)echo"<tr>","<th>".h($K["Name"]),"<td>".($K["Execute at"]?'At given time'."<td>".$K["Execute at"]:'Every'." ".$K["Interval value"]." ".$K["Interval field"]."<td>$K[Starts]"),"<td>$K[Ends]",'<td><a href="'.h(ME).'event='.urlencode($K["Name"]).'">'.'Alter'.'</a>';echo"</table>\n";$Ac=get_val("SELECT @@event_scheduler");if($Ac&&$Ac!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($Ac)."\n";}echo'<p class="links"><a href="'.h(ME).'event=">'.'Create event'."</a>\n";}if($ci)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}}}page_footer();