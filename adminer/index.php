<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 5.1.0
*/namespace
Adminer;$ia="5.1.0";error_reporting(24575);set_error_handler(function($xc,$zc){return!!preg_match('~^(Trying to access array offset on( value of type)? null|Undefined (array key|offset|index))~',$zc);},E_WARNING|E_NOTICE);$Tc=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($Tc||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$Pi=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($Pi)$$X=$Pi;}}if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection(){global$g;return$g;}function
adminer(){global$b;return$b;}function
driver(){global$m;return$m;}function
version(){global$ia;return$ia;}function
idf_unescape($w){if(!preg_match('~^[`\'"[]~',$w))return$w;$qe=substr($w,-1);return
str_replace($qe.$qe,$qe,substr($w,1,-1));}function
q($Q){global$g;return$g->quote($Q);}function
escape_string($X){return
substr(q($X),1,-1);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
number_type(){return'((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';}function
remove_slashes($yg,$Tc=false){if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){while(list($z,$X)=each($yg)){foreach($X
as$ie=>$W){unset($yg[$z][$ie]);if(is_array($W)){$yg[$z][stripslashes($ie)]=$W;$yg[]=&$yg[$z][stripslashes($ie)];}else$yg[$z][stripslashes($ie)]=($Tc?$W:stripslashes($W));}}}}function
bracket_escape($w,$Ca=false){static$zi=array(':'=>':1',']'=>':2','['=>':3','"'=>':4');return
strtr($w,($Ca?array_flip($zi):$zi));}function
min_version($gj,$De="",$h=null){global$g;if(!$h)$h=$g;$rh=$h->server_info;if($De&&preg_match('~([\d.]+)-MariaDB~',$rh,$B)){$rh=$B[1];$gj=$De;}return$gj&&version_compare($rh,$gj)>=0;}function
charset($g){return(min_version("5.5.3",0,$g)?"utf8mb4":"utf8");}function
ini_bool($Ud){$X=ini_get($Ud);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($fj,$N,$V,$F){$_SESSION["pwds"][$fj][$N][$V]=($_COOKIE["adminer_key"]&&is_string($F)?array(encrypt_string($F,$_COOKIE["adminer_key"])):$F);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$o=0){global$g;return$g->result($H,$o);}function
get_vals($H,$e=0){global$g;$J=array();$I=$g->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$e];}return$J;}function
get_key_vals($H,$h=null,$uh=true){global$g;if(!is_object($h))$h=$g;$J=array();$I=$h->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($uh)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$h=null,$n="<p class='error'>"){global$g;$qb=(is_object($h)?$h:$g);$J=array();$I=$qb->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!is_object($h)&&$n&&(defined('Adminer\PAGE_HEADER')||$n=="-- "))echo$n.error()."\n";return$J;}function
unique_array($K,$y){foreach($y
as$x){if(preg_match("~PRIMARY|UNIQUE~",$x["type"])){$J=array();foreach($x["columns"]as$z){if(!isset($K[$z]))continue
2;$J[$z]=$K[$z];}return$J;}}}function
escape_key($z){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$z,$B))return$B[1].idf_escape(idf_unescape($B[2])).$B[3];return
idf_escape($z);}function
where($Z,$p=array()){global$g;$J=array();foreach((array)$Z["where"]as$z=>$X){$z=bracket_escape($z,1);$e=escape_key($z);$Rc=$p[$z]["type"];$J[]=$e.(JUSH=="sql"&&$Rc=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($Rc,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($p[$z],q($X)))));if(JUSH=="sql"&&preg_match('~char|text~',$Rc)&&preg_match("~[^ -@]~",$X))$J[]="$e = ".q($X)." COLLATE ".charset($g)."_bin";}foreach((array)$Z["null"]as$z)$J[]=escape_key($z)." IS NULL";return
implode(" AND ",$J);}function
where_check($X,$p=array()){parse_str($X,$Ua);remove_slashes(array(&$Ua));return
where($Ua,$p);}function
where_link($u,$e,$Y,$zf="="){return"&where%5B$u%5D%5Bcol%5D=".urlencode($e)."&where%5B$u%5D%5Bop%5D=".urlencode(($Y!==null?$zf:"IS NULL"))."&where%5B$u%5D%5Bval%5D=".urlencode($Y);}function
convert_fields($f,$p,$M=array()){$J="";foreach($f
as$z=>$X){if($M&&!in_array(idf_escape($z),$M))continue;$wa=convert_field($p[$z]);if($wa)$J.=", $wa AS ".idf_escape($z);}return$J;}function
cookie($C,$Y,$ye=2592000){global$ba;return
header("Set-Cookie: $C=".urlencode($Y).($ye?"; expires=".gmdate("D, d M Y H:i:s",time()+$ye)." GMT":"")."; path=".preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]).($ba?"; secure":"")."; HttpOnly; SameSite=lax",false);}function
get_settings($zb){parse_str($_COOKIE[$zb],$vh);return$vh;}function
get_setting($z,$zb="adminer_settings"){$vh=get_settings($zb);return$vh[$z];}function
save_settings($vh,$zb="adminer_settings"){return
cookie($zb,http_build_query($vh+get_settings($zb)));}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==1))session_start();}function
stop_session($bd=false){$Xi=ini_bool("session.use_cookies");if(!$Xi||$bd){session_write_close();if($Xi&&@ini_set("session.use_cookies",false)===false)session_start();}}function&get_session($z){return$_SESSION[$z][DRIVER][SERVER][$_GET["username"]];}function
set_session($z,$X){$_SESSION[$z][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($fj,$N,$V,$k=null){global$ac;$Ti=remove_from_uri(implode("|",array_keys($ac))."|username|ext|".($k!==null?"db|":"").($fj=='mssql'||$fj=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Ti,$B);return"$B[1]?".(sid()?SID."&":"").($fj!="server"||$N!=""?urlencode($fj)."=".urlencode($N)."&":"").($_GET["ext"]?"ext=".urlencode($_GET["ext"])."&":"")."username=".urlencode($V).($k!=""?"&db=".urlencode($k):"").($B[2]?"&$B[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($_e,$Qe=null){if($Qe!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($_e!==null?$_e:$_SERVER["REQUEST_URI"]))][]=$Qe;}if($_e!==null){if($_e=="")$_e=".";header("Location: $_e");exit;}}function
query_redirect($H,$_e,$Qe,$Gg=true,$Dc=true,$Mc=false,$mi=""){global$g,$n,$b;if($Dc){$Lh=microtime(true);$Mc=!$g->query($H);$mi=format_time($Lh);}$Fh="";if($H)$Fh=$b->messageQuery($H,$mi,$Mc);if($Mc){$n=error().$Fh.script("messagesPrint();");return
false;}if($Gg)redirect($_e,$Qe.$Fh);return
true;}function
queries($H){global$g;static$Bg=array();static$Lh;if(!$Lh)$Lh=microtime(true);if($H===null)return
array(implode("\n",$Bg),format_time($Lh));$Bg[]=(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";";return$g->query($H);}function
apply_queries($H,$T,$_c='Adminer\table'){foreach($T
as$R){if(!queries("$H ".$_c($R)))return
false;}return
true;}function
queries_redirect($_e,$Qe,$Gg){list($Bg,$mi)=queries(null);return
query_redirect($Bg,$_e,$Qe,$Gg,false,!$Gg,$mi);}function
format_time($Lh){return
sprintf('%.3f s',max(0,microtime(true)-$Lh));}function
relative_uri(){return
str_replace(":","%3a",preg_replace('~^[^?]*/([^?]*)~','\1',$_SERVER["REQUEST_URI"]));}function
remove_from_uri($Vf=""){return
substr(preg_replace("~(?<=[?&])($Vf".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_file($z,$Nb=false,$Rb=""){$Sc=$_FILES[$z];if(!$Sc)return
null;foreach($Sc
as$z=>$X)$Sc[$z]=(array)$X;$J='';foreach($Sc["error"]as$z=>$n){if($n)return$n;$C=$Sc["name"][$z];$ui=$Sc["tmp_name"][$z];$vb=file_get_contents($Nb&&preg_match('~\.gz$~',$C)?"compress.zlib://$ui":$ui);if($Nb){$Lh=substr($vb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$Lh))$vb=iconv("utf-16","utf-8",$vb);elseif($Lh=="\xEF\xBB\xBF")$vb=substr($vb,3);}$J.=$vb;if($Rb)$J.=(preg_match("($Rb\\s*\$)",$vb)?"":$Rb)."\n\n";}return$J;}function
upload_error($n){$Le=($n==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($n?'Unable to upload a file.'.($Le?" ".sprintf('Maximum allowed file size is %sB.',$Le):""):'File does not exist.');}function
repeat_pattern($fg,$we){return
str_repeat("$fg{0,65535}",$we/65535)."$fg{0,".($we%65535)."}";}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
shorten_utf8($Q,$we=80,$Rh=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$we).")($)?)u",$Q,$B))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$we).")($)?)",$Q,$B);return
h($B[1]).$Rh.(isset($B[2])?"":"<i>…</i>");}function
format_number($X){return
strtr(number_format($X,0,".",','),preg_split('~~u','0123456789',-1,PREG_SPLIT_NO_EMPTY));}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$Nc=false){$J=table_status($R,$Nc);return($J?:array("Name"=>$R));}function
column_foreign_keys($R){global$b;$J=array();foreach($b->foreignKeys($R)as$r){foreach($r["source"]as$X)$J[$X][]=$r;}return$J;}function
fields_from_edit(){global$m;$J=array();foreach((array)$_POST["field_keys"]as$z=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$z];$_POST["fields"][$X]=$_POST["field_vals"][$z];}}foreach((array)$_POST["fields"]as$z=>$X){$C=bracket_escape($z,1);$J[$C]=array("field"=>$C,"privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>1,"auto_increment"=>($z==$m->primary),);}return$J;}function
dump_headers($Hd,$Ye=false){global$b;$J=$b->dumpHeaders($Hd,$Ye);$Rf=$_POST["output"];if($Rf!="text")header("Content-Disposition: attachment; filename=".$b->dumpFilename($Hd).".$J".($Rf!="file"&&preg_match('~^[0-9a-z]+$~',$Rf)?".$Rf":""));session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv($K){foreach($K
as$z=>$X){if(preg_match('~["\n,;\t]|^0|\.\d*0$~',$X)||$X==="")$K[$z]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($_POST["format"]=="tsv"?"\t":";")),$K)."\r\n";}function
apply_sql_function($t,$e){return($t?($t=="unixepoch"?"DATETIME($e, '$t')":($t=="count distinct"?"COUNT(DISTINCT ":strtoupper("$t("))."$e)"):$e);}function
get_temp_dir(){$J=ini_get("upload_tmp_dir");if(!$J){if(function_exists('sys_get_temp_dir'))$J=sys_get_temp_dir();else{$q=@tempnam("","");if(!$q)return
false;$J=dirname($q);unlink($q);}}return$J;}function
file_open_lock($q){if(is_link($q))return;$s=@fopen($q,"c+");if(!$s)return;chmod($q,0660);if(!flock($s,LOCK_EX)){fclose($s);return;}return$s;}function
file_write_unlock($s,$Hb){rewind($s);fwrite($s,$Hb);ftruncate($s,strlen($Hb));file_unlock($s);}function
file_unlock($s){flock($s,LOCK_UN);fclose($s);}function
first($va){return
reset($va);}function
password_file($i){$q=get_temp_dir()."/adminer.key";if(!$i&&!file_exists($q))return
false;$s=file_open_lock($q);if(!$s)return
false;$J=stream_get_contents($s);if(!$J){$J=rand_string();file_write_unlock($s,$J);}else
file_unlock($s);return$J;}function
rand_string(){return
md5(uniqid(mt_rand(),true));}function
select_value($X,$A,$o,$li){global$b;if(is_array($X)){$J="";foreach($X
as$ie=>$W)$J.="<tr>".($X!=array_values($X)?"<th>".h($ie):"")."<td>".select_value($W,$A,$o,$li);return"<table>$J</table>";}if(!$A)$A=$b->selectLink($X,$o);if($A===null){if(is_mail($X))$A="mailto:$X";if(is_url($X))$A=$X;}$J=$b->editVal($X,$o);if($J!==null){if(!is_utf8($J))$J="\0";elseif($li!=""&&is_shortable($o))$J=shorten_utf8($J,max(0,+$li));else$J=h($J);}return$b->selectVal($J,$A,$o,$X);}function
is_mail($nc){$xa='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$Zb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$fg="$xa+(\\.$xa+)*@($Zb?\\.)+$Zb";return
is_string($nc)&&preg_match("(^$fg(,\\s*$fg)*\$)i",$nc);}function
is_url($Q){$Zb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^(https?)://($Zb?\\.)+$Zb(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$Q);}function
is_shortable($o){return
preg_match('~char|text|json|lob|geometry|point|linestring|polygon|string|bytea~',$o["type"]);}function
count_rows($R,$Z,$ce,$pd){$H=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($ce&&(JUSH=="sql"||count($pd)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$pd).")$H":"SELECT COUNT(*)".($ce?" FROM (SELECT 1$H GROUP BY ".implode(", ",$pd).") x":$H));}function
slow_query($H){global$b,$vi,$m;$k=$b->database();$ni=$b->queryTimeout();$zh=$m->slowQuery($H,$ni);$h=null;if(!$zh&&support("kill")&&is_object($h=connect($b->credentials()))&&($k==""||$h->select_db($k))){$le=$h->result(connection_id());echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$le&token=$vi'); }, 1000 * $ni);");}ob_flush();flush();$J=@get_key_vals(($zh?:$H),$h,false);if($h){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$Eg=rand(1,1e6);return($Eg^$_SESSION["token"]).":$Eg";}function
verify_token(){list($vi,$Eg)=explode(":",$_POST["token"]);return($Eg^$_SESSION["token"])==$vi;}function
lzw_decompress($Ia){$Vb=256;$Ja=8;$db=array();$Rg=0;$Sg=0;for($u=0;$u<strlen($Ia);$u++){$Rg=($Rg<<8)+ord($Ia[$u]);$Sg+=8;if($Sg>=$Ja){$Sg-=$Ja;$db[]=$Rg>>$Sg;$Rg&=(1<<$Sg)-1;$Vb++;if($Vb>>$Ja)$Ja++;}}$Ub=range("\0","\xFF");$J="";foreach($db
as$u=>$cb){$mc=$Ub[$cb];if(!isset($mc))$mc=$pj.$pj[0];$J.=$mc;if($u)$Ub[]=$pj.$mc[0];$pj=$mc;}return$J;}function
script($Bh,$yi="\n"){return"<script".nonce().">$Bh</script>$yi";}function
script_src($Ui){return"<script src='".h($Ui)."'".nonce()."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
input_hidden($C,$Y=""){return"<input type='hidden' name='".h($C)."' value='".h($Y)."'>\n";}function
input_token($Dh=""){global$vi;return
input_hidden("token",($Dh?:$vi));}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace("\0","&#0;",htmlspecialchars($Q,ENT_QUOTES,'utf-8'));}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($C,$Y,$Xa,$ne="",$yf="",$bb="",$oe=""){$J="<input type='checkbox' name='$C' value='".h($Y)."'".($Xa?" checked":"").($oe?" aria-labelledby='$oe'":"").">".($yf?script("qsl('input').onclick = function () { $yf };",""):"");return($ne!=""||$bb?"<label".($bb?" class='$bb'":"").">$J".h($ne)."</label>":$J);}function
optionlist($Cf,$jh=null,$Yi=false){$J="";foreach($Cf
as$ie=>$W){$Df=array($ie=>$W);if(is_array($W)){$J.='<optgroup label="'.h($ie).'">';$Df=$W;}foreach($Df
as$z=>$X)$J.='<option'.($Yi||is_string($z)?' value="'.h($z).'"':'').($jh!==null&&($Yi||is_string($z)?(string)$z:$X)===$jh?' selected':'').'>'.h($X);if(is_array($W))$J.='</optgroup>';}return$J;}function
html_select($C,$Cf,$Y="",$xf="",$oe=""){return"<select name='".h($C)."'".($oe?" aria-labelledby='$oe'":"").">".optionlist($Cf,$Y)."</select>".($xf?script("qsl('select').onchange = function () { $xf };",""):"");}function
html_radios($C,$Cf,$Y=""){$J="";foreach($Cf
as$z=>$X)$J.="<label><input type='radio' name='".h($C)."' value='".h($z)."'".($z==$Y?" checked":"").">".h($X)."</label>";return$J;}function
confirm($Qe="",$kh="qsl('input')"){return
script("$kh.onclick = () => confirm('".($Qe?js_escape($Qe):'Are you sure?')."');","");}function
print_fieldset($v,$ve,$jj=false){echo"<fieldset><legend>","<a href='#fieldset-$v'>$ve</a>",script("qsl('a').onclick = partial(toggle, 'fieldset-$v');",""),"</legend>","<div id='fieldset-$v'".($jj?"":" class='hidden'").">\n";}function
bold($La,$bb=""){return($La?" class='active $bb'":($bb?" class='$bb'":""));}function
js_escape($Q){return
addcslashes($Q,"\r\n'\\/");}function
pagination($E,$Eb){return" ".($E==$Eb?$E+1:'<a href="'.h(remove_from_uri("page").($E?"&page=$E".($_GET["next"]?"&next=".urlencode($_GET["next"]):""):"")).'">'.($E+1)."</a>");}function
hidden_fields($yg,$Kd=array(),$rg=''){$J=false;foreach($yg
as$z=>$X){if(!in_array($z,$Kd)){if(is_array($X))hidden_fields($X,array(),$z);else{$J=true;echo
input_hidden(($rg?$rg."[$z]":$z),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),(SERVER!==null?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
enum_input($U,$ya,$o,$Y,$qc=null){global$b;preg_match_all("~'((?:[^']|'')*)'~",$o["length"],$Ge);$J=($qc!==null?"<label><input type='$U'$ya value='$qc'".((is_array($Y)?in_array($qc,$Y):$Y===$qc)?" checked":"")."><i>".'empty'."</i></label>":"");foreach($Ge[1]as$u=>$X){$X=stripcslashes(str_replace("''","'",$X));$Xa=(is_array($Y)?in_array($X,$Y):$Y===$X);$J.=" <label><input type='$U'$ya value='".h($X)."'".($Xa?' checked':'').'>'.h($b->editVal($X,$o)).'</label>';}return$J;}function
input($o,$Y,$t,$Ba=false){global$m,$b;$C=h(bracket_escape($o["field"]));echo"<td class='function'>";if(is_array($Y)&&!$t){$Y=json_encode($Y,128|64|256);$t="json";}$Qg=(JUSH=="mssql"&&$o["auto_increment"]);if($Qg&&!$_POST["save"])$t=null;$kd=(isset($_GET["select"])||$Qg?array("orig"=>'original'):array())+$b->editFunctions($o);$Wb=stripos($o["default"],"GENERATED ALWAYS AS ")===0?" disabled=''":"";$ya=" name='fields[$C]'$Wb".($Ba?" autofocus":"");$wc=$m->enumLength($o);if($wc){$o["type"]="enum";$o["length"]=$wc;}echo$m->unconvertFunction($o)." ";if($o["type"]=="enum")echo
h($kd[""])."<td>".$b->editInput($_GET["edit"],$o,$ya,$Y);else{$xd=(in_array($t,$kd)||isset($kd[$t]));echo(count($kd)>1?"<select name='function[$C]'$Wb>".optionlist($kd,$t===null||$xd?$t:"")."</select>".on_help("event.target.value.replace(/^SQL\$/, '')",1).script("qsl('select').onchange = functionChange;",""):h(reset($kd))).'<td>';$Wd=$b->editInput($_GET["edit"],$o,$ya,$Y);if($Wd!="")echo$Wd;elseif(preg_match('~bool~',$o["type"]))echo"<input type='hidden'$ya value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked='checked'":"")."$ya value='1'>";elseif($o["type"]=="set"){preg_match_all("~'((?:[^']|'')*)'~",$o["length"],$Ge);foreach($Ge[1]as$u=>$X){$X=stripcslashes(str_replace("''","'",$X));$Xa=in_array($X,explode(",",$Y),true);echo" <label><input type='checkbox' name='fields[$C][$u]' value='".h($X)."'".($Xa?' checked':'').">".h($b->editVal($X,$o)).'</label>';}}elseif(preg_match('~blob|bytea|raw|file~',$o["type"])&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$C'>";elseif($t=="json"||preg_match('~^jsonb?$~',$o["type"]))echo"<textarea$ya cols='50' rows='12' class='jush-js'>".h($Y).'</textarea>';elseif(($ji=preg_match('~text|lob|memo~i',$o["type"]))||preg_match("~\n~",$Y)){if($ji&&JUSH!="sqlite")$ya.=" cols='50' rows='12'";else{$L=min(12,substr_count($Y,"\n")+1);$ya.=" cols='30' rows='$L'";}echo"<textarea$ya>".h($Y).'</textarea>';}else{$Ji=$m->types();$Ne=(!preg_match('~int~',$o["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$o["length"],$B)?((preg_match("~binary~",$o["type"])?2:1)*$B[1]+($B[3]?1:0)+($B[2]&&!$o["unsigned"]?1:0)):($Ji[$o["type"]]?$Ji[$o["type"]]+($o["unsigned"]?0:1):0));if(JUSH=='sql'&&min_version(5.6)&&preg_match('~time~',$o["type"]))$Ne+=7;echo"<input".((!$xd||$t==="")&&preg_match('~(?<!o)int(?!er)~',$o["type"])&&!preg_match('~\[\]~',$o["full_type"])?" type='number'":"")." value='".h($Y)."'".($Ne?" data-maxlength='$Ne'":"").(preg_match('~char|binary~',$o["type"])&&$Ne>20?" size='".($Ne>99?60:40)."'":"")."$ya>";}echo$b->editHint($_GET["edit"],$o,$Y);$Uc=0;foreach($kd
as$z=>$X){if($z===""||!$X)break;$Uc++;}if($Uc&&count($kd)>1)echo
script("qsl('td').oninput = partial(skipOriginal, $Uc);");}}function
process_input($o){global$b,$m;if(stripos($o["default"],"GENERATED ALWAYS AS ")===0)return
null;$w=bracket_escape($o["field"]);$t=$_POST["function"][$w];$Y=$_POST["fields"][$w];if($o["type"]=="enum"||$m->enumLength($o)){if($Y==-1)return
false;if($Y=="")return"NULL";}if($o["auto_increment"]&&$Y=="")return
null;if($t=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$o["on_update"])?idf_escape($o["field"]):false);if($t=="NULL")return"NULL";if($o["type"]=="set")$Y=implode(",",(array)$Y);if($t=="json"){$t="";$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}if(preg_match('~blob|bytea|raw|file~',$o["type"])&&ini_bool("file_uploads")){$Sc=get_file("fields-$w");if(!is_string($Sc))return
false;return$m->quoteBinary($Sc);}return$b->processInput($o,$Y,$t);}function
search_tables(){global$b,$g;$_GET["where"][0]["val"]=$_POST["query"];$mh="<ul>\n";foreach(table_status('',true)as$R=>$S){$C=$b->tableName($S);if(isset($S["Engine"])&&$C!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$I=$g->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",$b->selectSearchProcess(fields($R),array())),1));if(!$I||$I->fetch_row()){$ug="<a href='".h(ME."select=".urlencode($R)."&where[0][op]=".urlencode($_GET["where"][0]["op"])."&where[0][val]=".urlencode($_GET["where"][0]["val"]))."'>$C</a>";echo"$mh<li>".($I?$ug:"<p class='error'>$ug: ".error())."\n";$mh="";}}}echo($mh?"<p class='message'>".'No tables.':"</ul>")."\n";}function
on_help($kb,$xh=0){return
script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $kb, $xh) }, onmouseout: helpMouseout});","");}function
edit_form($R,$p,$K,$Si){global$b,$n;$Xh=$b->tableName(table_status1($R,true));page_header(($Si?'Edit':'Insert'),$n,array("select"=>array($R,$Xh)),$Xh);$b->editRowPrint($R,$p,$K,$Si);if($K===false){echo"<p class='error'>".'No rows.'."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";if(!$p)echo"<p class='error'>".'You have no privileges to update this table.'."\n";else{echo"<table class='layout'>".script("qsl('table').onkeydown = editingKeydown;");$Ba=!$_POST;foreach($p
as$C=>$o){echo"<tr><th>".$b->fieldName($o);$l=$_GET["set"][bracket_escape($C)];if($l===null){$l=$o["default"];if($o["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$l,$Ng))$l=$Ng[1];if(JUSH=="sql"&&preg_match('~binary~',$o["type"]))$l=bin2hex($l);}$Y=($K!==null?($K[$C]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$o["type"])&&is_array($K[$C])?implode(",",$K[$C]):(is_bool($K[$C])?+$K[$C]:$K[$C])):(!$Si&&$o["auto_increment"]?"":(isset($_GET["select"])?false:$l)));if(!$_POST["save"]&&is_string($Y))$Y=$b->editVal($Y,$o);$t=($_POST["save"]?(string)$_POST["function"][$C]:($Si&&preg_match('~^CURRENT_TIMESTAMP~i',$o["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$Si&&$Y==$o["default"]&&preg_match('~^[\w.]+\(~',$Y))$t="SQL";if(preg_match("~time~",$o["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$t="now";}if($o["type"]=="uuid"&&$Y=="uuid()"){$Y="";$t="uuid";}if($Ba!==false)$Ba=($o["auto_increment"]||$t=="now"||$t=="uuid"?null:true);input($o,$Y,$t,$Ba);if($Ba)$Ba=false;echo"\n";}if(!support("table")&&!fields($R))echo"<tr>"."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;")."<td class='function'>".html_select("field_funs[]",$b->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>"."\n";echo"</table>\n";}echo"<p>\n";if($p){echo"<input type='submit' value='".'Save'."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($Si?'Save and continue edit':'Save and insert next')."' title='Ctrl+Shift+Enter'>\n",($Si?script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".'Saving'."…', this); };"):"");}echo($Si?"<input type='submit' name='delete' value='".'Delete'."'>".confirm()."\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}if(isset($_GET["file"])){if(substr($ia,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}if($_GET["file"]=="favicon.ico"){header("Content-Type: image/x-icon");echo
lzw_decompress("\0\0\0` \0�\0\n @\0�C��\"\0`E�Q����?�tvM'�Jd�d\\�b0\0�\"��fӈ��s5����A�XPaJ�0���8�#R�T��z`�#.��c�X��Ȁ?�-\0�Im?�.�M��\0ȯ(̉��/(%�\0");}elseif($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("b7�'���o9�c`��a1���#y��d��C�1��tFQx�\\2�\n�S���n0�'#I��,\$M�c)��c����1i�Xi3ͦ���n)T�i��d:FcI�[��c��	��Fé�vt2�+�C,�a�G�F����:;Nu�)����Ǜ!�tl���F�|��,�`pw�S-����������oQk�� n�E��O+,=�4�mM���Ƌ�GS��Zh�6��. uO�M�C@����M'�(�b5�ҩ��H�a2)�qиpe6�?t#Z-���ox�<���s���;��H�4\$�䥍�ۚ��a�4�\"�(�!C,D�N��;����Jj����@�@�!����K�����6��jX�\r����@ 2@�b��(Z�Apl��8��h�.�=*H�4q3�AЂ�.��K���!�f�qr�!�1�Ȏ�c���*+ �(�\n�2�j���(dYA���D�t�ϑ�m*R�P�Qb#J+�1��N���̙>�A�wK�C`�P���=��<��@�̊��c��]Rr�C����H���Z�մ\0�=P��\$�@o�(����0C�G-��Z�Aʅt3:t���c��͵J����lc��R����\n�r��l�Ψk�9G��p�!pj�_X]h�Xfu��0̳�ҝ�0�=�C��6#˞75�s,�(f#z�X>3[�n?��1m�\\X��PX�eggJ4>��� \\T��?�H�3�y�~�hx�t6��%u��8L��m\nX���i���M��[����,�Y��P��9|~O�1���*�5+ӣ��1d{o=��g���x,��E�P��K��sк�E^4�f�;�*J��#j=�k�t�8�W1v�h��������ԃ�3�B��t�ȧ����.^̀os�`�?��7}?Z�]y���[iPn���W�������\"�z�x\"���4ؽr�?'�\"@g���f!�637�QCA�\r��Y��T�P�}@���M�}ͽ+����{�!��ղ������ݜ��4׹ G	I��\0^�RN}E���DY��{��/>����#Ɓ*��n��:��Fp��hn%����w#�����p΍�\\�\0��P����+�E�\r��)sL�ӱ�d\r��@�������-\$���V]Ã����;��d���0�\0X%ܰQ��*� w]�d��X�y�a&3M�M8�rXE�lQ%ֽB2�t�!\0�1,�ʈ�TI�-�4��hee3�ņD�n�.���P�'��3Ն���Z�R�sx�i�8��.�3�FI֒Ԛb�Si�i2�)�+\r+p�Cc9..i�paI�xn�԰�Rja�O)�\raĘ�@Y�\rrNP��jBC`d\0�7�̻'@#�֩�9�\"�	u\rtqՒ\"ae�0��Y*�`r�BM�D�l�59�������7<1�3g��e��D�>Ң{\\Xyr�3m�̨��*.3(7�����.��Y[/!�3�(���sC��(�)f���Q'���N��@*L�aŜ\"�dA�8���ݴ\":�M��I	(��*1C���)�L�y�f��4S(�ƻ\"�Qۄ������TW�A{&.�+6o1��:�\\Gȣ[�Z�3����CuH	Ɯ��qؼ���	�0S�s-�&���&�eCb@(\r(�R��5��bH�\r�Nl��ҘH��e�|a�È1.!�x蓂�YCH�)>JI����� =Aڧɥ�C���T���p�r� +���;IOe]�.5�<[��vS|̔���4݅�9�U�ܩQsⴓ��-ߘ\n�n\$\n[1���ʊ'z҂9�L�Vz*X�g�x�T�~�'�E�+�Y�,A�K�G��xs��a]5�ƨ�A>Ti����RLj�q�=|Y58o<���j�\r�LP0N����&|f�9b#��,[TY����u֚�c�`��ֈ�d�O���v & �VO-��j<�B���7��p#�x�&��A�Ch��Q�5����%�<�y+�\$�~�Tm9?�ck�!j�o� cV\ng�]�4����SJI�PT�_	>��~�;ś��O���f��Q�\nw�M#�uե���X��o8��TK%e��\\d-��,mS�F��K���E�g\0�\$�4L�ٖ㈑���M�\0��P���-�ꧼ�Á��A�)k�z�5Q��z�[�4�d���e������}/ݟ���h���\r�WWs<���k��I�*[���@�ics�B��@5Gj�7	��_/-�������A�6\0ЉzZ7��ΏhO��e�4��Q]G߾����tJyQN	��S����\\���� P�\nJ����O�nb������O�Oΐ����ތ��nNdNT��c�o��F��/��cΞ�]��G\nP9ƴ)�����2���!�n��\ng��O��M!O���B���#D���0���#���l��^J\0PoF�Q�t\0pp\n��F�O�_�,��L��.,Y#R��-�Ԓ��h\0v�00��\0AP�O����p�/P�'�c��ڜ��\0�-\\����{��PSY��.W`�PN��\njԢh�n%p}I{͟b��6����QQ�Z�)z�1|��pP邗�Z���ɪ�Ǣ`Z� ������C\n��\r\0��\0fq�Y1S��4M�`��Z�L�@`*]@Z\r`� �>Ѽ�K��be�R��G��&��g�Fn-ў�QXKҤ����/�\r ċq�.���?�\"`�J#q�i�\$Q��4��Q�%�Hz�M&1�&k�&�H��'�8c��R@Q��J�S��8��\r�� �ڏ�����*����{R�)��+��`��r�'2�*R�,@�+�)��-�a,��,���#)������6\0�/�Ư� Xr�(���R��0�e��1J�+2؆��,�-3\0�\r��Wr�/r���,�{2�>(�x�K,�0�T_�Y5334��Dfs)���2R�-^\r�7Q���7�O2�6�0S�)�b\r���.�٣�Q.�2r�-�J�3)/�-:s�;��/Ҋ�N�Y�\$���,hR��K1�s(ѩ\$c�3�1���4��sH?T�r�^t	6t#;қA�W4�%T\r+4 ��8s�)J���%2W24=7����@`�E+A�3tUE�8�!6%@�At	C�B�YCS�3�C��D [\$4IHtL�r�\0�:S�H���TKF�9@��:R1F4SKF\$ �\0�3A3#i�J�uEtB�	B4�9�3H�1!4�+0	N���.\0���.�J�f��P�4����Oԕ4��4F�Hԩ\"[9-��P�4�;/�?=��9�Z.A<aT�R�W,t�;��\0�T�G,u4\0�V�qV�OT�ڞkyV �Ws�Xu;r�Vu�,cPeuXՑ-��ZuMQ��LӉ12wI3�;C�]s�\\�N��c4��55�:3]2��? 2%\\3\"z\r�G[�7\" u'*��Q�3J�z-�4�d ���&��S��@�j{M�6�x0�/S�oX�'+5�\n��Z�ac�0`�!��`i�KKn ��\\S�Yuy2�7Xi8�,�[3֚���voE��hth_֍Q7i3�iMf��i�6�e`e�:�_#e@�Y\$��c14���C40�\rg��g�+j��:\"�\\MJ��,");}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("b7�'���o9�c����b�F��r7�M�HP�`2\r\"'�\r�\rF#s1�p;��Ɠ���e2I���Y.�GF�I��:4��S���3��㔚Y�u(Ìc(�`h��#%0[��L�����h���C!���E����b5�Ú�������y�fb��w	�z#���1�P��6�����l2��MQ�d�e#�Q��m�>�5ً�n�1��S��e0��o#����G��y��GxA�׍��]��]Ի�LƓ)��s2�5��]��6�8��-�ļ����o�� NZ�)�[Y�#��)Pk��p���8C(\\5��w\0C-��&\n(�:��rʖ��c�lL��t��A HR @��	x�FtB8��LB�I���0�\r@�3��|s��� �s\$�)�r����s��I2TF9�r�:��t0�;A����r��*͔�=�!ʹ�.*���r�\r.U��534�h@@1?��R�QnC�4��(�9�#8�7�a��Ւ�QO�P�2�LB�H���K%��YTUUe\\��E`�!ȅn��(�@�M#�0Ьb0����4��\noX���tӃt�X��j1�sHĢۈʭ+�����\\	�H��,�@�����6�0,�9ζ�ʊ٣.2㢩�c�@5��65�P�Q\\�!�fޯ+@0��\r��X�O�d�Zq\n*�y��:B�£�È�-�gC�>W��c��[�o��cJ=e������;ۢ)��S���#��W��7�麭G�à�8n���3�a@`a�Xq�pn׭�4��tt��Cʡ��C��/fp�O���p`AHYX��a�o[���\0�4�ەskvɀGT��\0�٠�:�9�m.~9�m���C��y�{��㳠��zv�0�ū���F1D.H�9O\r0	�,��]��Ma�6�\0d��7x�͍%t���s�1G�����V	��m֛Q!A��Ew�haw�\0A<����=<p��Aq�W�Vf�x�����{X\nA,�4㘢��t�ǰ�'H��[Mi�ܾ��2M�#D����n\"��q��P��*+є8�	����͂�X�yC(6W��>��Z`a(��@�0Z��w1Ċxʥa�;��t�d�Ahv!�:�Y ��\r���``V�握�Jh}#(j`�7�d\r�\0cUr�?��DH�����^�Pj��t���8�ɉf9�&\0");}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("f:��gCI��\n8��3)��7���81��x:\nOg#)��r7\n\"��`�|2�gSi�H)N�S��\r��\"0��@�)�`(\$s6O!��V/=��g@T4�=��iS��6IO�G#�X�VC��s��Z1.�hp8,�[�H�~Cz���2�l�c3���s���I�b�4\n�F8NPC\r\$�n�T���=\\�r9O\"�	��l<�\r�\\x�I,�s\nA��el1����E�Ư�L��]�[M'=��K��e�\0��lӝә���\n�i��v�c��������]EHפ���H�o��ֽΈ�麮��쨋�6�#�Ґ��\":cz>ߣC2v�CX�<�P���*5���0X97�N����\"�3���!���!���#��\nZ%�ć#CH�!��rX�&:c��7JHp~�# ��A:86������&���|ˠ���3��@�2<�sΆ�PN��]�!�0M-˲4�0���\0��\\ԗ響33�d!>H��5�.,4��pX25Mڢ�*��,��(��\n,(��!���a��pΎ�n\0Rփ�v�m�I.\r�][H�������<J8�Ѷ5N��C��<�ɠ��%mCr#�`X�D�ooS�L)^�B��{���`��wJ�JZ�/���XC(��>a@O��Ӛ��%�Dpӣ��8a�^���X2`�\\���~�����\$�`W��!�Qc�ذ�!\"#\"i�iZnJ\r�\$.\$���:j�]�1�C{��`�7ܻ�<��p�A��`n��S��#F�3��`bA��6�:u�:n8���VԃNa��2��I0*iri�%�8�g�/5��ܭ,t���3���Y����g����r9u�=C[�ʦ�:c@{�^˨z�XR�Ⱥ�7!ut0��⼸&E�B���o�4k�\"�#P�<��9�H�g�6!b�H��k�\\����MAG��R�PK\"o�7*�K� CAa�2(&�z2\$�+�f^\n��0����zE� I\r�H<��Y!`t��R��!�e�����]C�[���2.\\P((��2����p��p�M�p!�YV�@\"�l&`�4�(qБ�\n��,/�2�)�#��_\0��C8E]�/2 W� )�A��	\rϺ��qL�R�TˣO�=���nc��I1܍x6p\$�!�Q�Q�0R��`=Kr|���Ԋ\n,���`���c\rq�<�I~e�t�1�y#�	4H�X� N�Hz&Mi@6%Ƀ6�̘DA�6�\$�8�h[M=:Ni�:'Q�\r�ѝ� �o�\\���u�0�=R��X�*8hr��\$PA�����B@t\r�%A��EB[���3��O!��\\�X�9~\nx\\@�����3�\0�<�ο�Ba��2nwV��*��\"ETZ�Q�Vb��E�\n)\r��,����9ZduN,�Y�2e�RՂ[�䪕нT�N21Q��C���ŗ�t�,BydR�Q���� �\rM�d�Q\n#WU�Uj�t���z,�ܫgV�LZ��Â�1Y��8\r�zl׫!i&Br���+8l�He)�[[0�Hr����J�VY�n3baW�@ :˨��׊`� ��=�ɾ������N��@���x\nXԅ�� ;R�~�@�Mu�2Y�����o��j�|��q&�T��6X�Z����n��{fd��Kw�wZ��To`d`�K�j���V�{��L����!̙����0Y��f܉��O��!I�nB�#�+����;���`��CZ\\�GF�ci!�z��:dI��ʜ�A���ѓHܐ�X�5׼�I�s�a���3NK�'�м��By�!��(��&Q�tr��	T�z�����:�W(��#��>[��^��e:U+���A,��7��~�a��Ձ�\"O��c�fV��@��|t5_�2ֲ��B�sI��[}�����[�aȩ�9S3d���c�ݷz��͘Wqe\n]^d�k\0����=��3��z�a�޼~��2�����0��hw7ap-���	����܆ۿ�\0��G�ቃa��	/g�wl�^�ָ����Ci���/�t����0t	h��I}�!'��f�n������w����ic7O�9om��~L�8�\r����z�'�UF��b�V�S����cZ�4���]�s꒿[-iW�om�����5��.�t�=���K����bc��X͞����=�82��i�E�G��Q*)C��uE�AJ�t�\"�p_�B�\"����p� &T+��H���{�(�2�^O�,�t�+���hLe��͐�C7~�4��d���J3kjA@7�s�Sq�p��SF(�,����)�i��b4�@��ir�\0A\0K�;�FĊ\r8�X��\\�-I��\$%%/<%E�.�]�����W� bL�MF��-�RM�:�0��	��z�^�cЖGZ�B:��.?i�Ӏ�3(+@L&v��Y �`�p���#��n��М����`�-���^�q�v^ �\r��e�^�j��lBb](�X��\rF�X��&��%:Pf���� �M@�ΐ�D��!���&�f��zK�(�c�U�8�o��b�1>�@Ч����^�<�1N-m��垠l�hr��vdHrb.�����?C�e������� Z@��^`^���)�h^�Hd���(NƷ��J��`Y�������ёQ�Ѡ�Q�����N�֣p��MNݍdժ�ﮚ�*�;Hx�@��j	P�X���f��;���n3G��`�% �\$-t�-F�jGd f\$�FD�K!`z a\r#���#G����EP�22G\0�'���T����&r\"�m��l����\"��(�@�\r���C�R����n�0������b!*bX\r�\"�\"	D��#R�\r��R�6�F�-%N��d�N�kb��I#\rE'�m\n��qH���k'�R��!q�D�®��2�β#�RV`��ķ@�l y3(�-B�\r��b53&14h�\r\"���Dk>�`r���4@�)\$�(K!I�vNb�MD�z��e5B�%�[63F٭�䭢�\"fW�J[�)S����(*�7FK7�Dr8\r�8B`DBV!ED9��,���\$(���@�#�Tu�ZC29	��u��#�Հ�29#�@t�X��t`L�\nbtx�LqF���D�d4�@��n\n���'���N�N�SEtZc�h�\0�s�C�C�]\$��\$�R�V2[\$�bRP�DI��-?F�Dӫ���DĦ#�ut0 �_Fi�K��ޭ�2��4��C̮\n�K��)�&��\r+62���Jޮ4����>��R�t������BD�E�D�VPTwG�u@�����DO	���YO*�����D隺q�N��Q`N䦧��\\��\n��%�<�rqF޲UT��Y&2t;h�N��Bl�B�r�ԁHRL�h([u�%�f�?tG�WQtID�++��땓%��Y�`L�z#�Jl�M���BEU���}3�Q�\"2�U�-b���{���eB\$��!��]U���B�,jҔ~ً�����/������^�B��R��+��6'7J�b�� ãD�;\r�O�)P��\rJ��qS\0�R@CB\$D�)C�JP�h�q�f9\0����&p�H5<��o�����S���kseC_��L��������\"%`\n��d &��ezP�T�V��ԭ����n�^	��\nb�n���l����dL�k �5����M�p���}�\r������E6����n�'o6�!v�u�Iln��5�|a�ήœrL��E��r�!u%au�b-�� �zp�`t���(&�\r��H��<��eX\0�u�.3�;;,V3�� e�_,��]�M]�}_h����\\1q������D �|(u)��׭�W�%1����1/0�W�8�c����J�b��~�������2Ϲ��A����`A1\"�Q\"�Ox�V��'�}��@B&�@� �Vwv�B�e��b8&L\"��~��&^�����Ç�f*lW���C��EW�Qx�*x�k�Çp?�݊\$2#ȶ� ��o��S4`A+B�X�Y�D�̒�x���_38�()�Hd�?x��Xę�=� 8ބ��(�%Ȥz��\n\0�\n`�����*%�@I�z�m�7�#�����k��s�N��\$�[t�AK����p����%�(jD�e�˔\\�F�9LjKt���s����q�������ᙶ���� �9B8�\"ǋ<ǖ'��9*q��T#�R\\��`e-i/����4U\0ǆ�m�������@�/�1��}�E@]���yy�qx��]o�hX#ۍ �d��hl��%��������4x�}��HI�H���:�\nS������\\�`Q�֌8���#�\"�xb�\n8��1˟��u31w`z	@�	��\r�8&��*!�.�R�<�h�@�у��Li�2GDjONE4����O0Cы����ƾ�u��B�C+�4��݂,��\$�L\$I���pKg�kknN˖!� \r)*��Mt�Kx6�֏P(T�bE���Rq���=N����]��B�I���S%M��;�vT�67v�tT\r� J�\n��	*\r�*7.&��h�y����;��V\0�Qz(�lHz��u��{�5�'�F��B�D�U�E�׬#���_\0@��@�K%@�rP�5����*Mj��\\Q��j8]�v[�U\0ښ_����D�����|?�1��Z�M���Uڡ�(<�ˬ)©��D�6�S��Xq���>	yf@P�R=F��#Tj\rBk��p\0���ϗ�o�xœU|�`�^J���(:�;tX{\"��i�����,�\$�[1&�5F5��-�Z���TX�M�\"�~�T�#]`m;��R�0���a�\\����c;h<�̼���P��D��{ib�:�\0��l~/bN�2շ`Q����5�\n�cT[Htl�D\"�>��/d��8��MowZ\r�iB�h�R槫Z'd@o��iR�9�G�A�x�G���Wj���Gy�b�KV�}F��\\3��\"�\0@z�cϣX�\"	�Y v�6yl�<R(R]��2�5�_��>�b��B,\$��Ŕ8q�{Ȍ�˛�� ��%����N�P�nP���-�����#s��;���XL��R r��\nnr-�3^_�+6�i�m\0�k�2��|@`\0u�I^\\����v,=4.*�bx7�k�RԦ�o��	y�΂���s�Qv���&.#]���D\rϭԒ�8I\"r��(�(���\nt甼��̩��>�UKd�/}������#�0MT�&��۠\r��r�{�	��YÝ�J��	<a�X2����:��,uQ��Z�G��]��}���k�_{�=� ؤ3i����~,� ����}48�8�Hr	��M2�#�P����bq�'���N�@Z��ԞPI	��r������Щ��?��N�w)��9�mMs��H��Ł�gN� �]p1�]����:\$t��7�����1�y�FAg����!�Y�8`,�oDh��\\\0x��:?�\"+��	���S�:Q��#A*޸��h�@X�@V� �I	��>�A\0��	�JPꏋBse�t\r��e�rR.���	�rr\n���Q��}h\0t�\r�mt0� �F�#}�h�`�.��L�`=�*z| ��5����<���2�A��F�J��@����ȳ�nh�ymt�bF\0��2�Bޓ5�K\\aoZ��Q�	���.�3T�iHV�e�!��^�\\�T� �K�!�F���P�`���W	�����L��3V�����i\$�C�oav[��\rn\0d�0�!���C4�0�t�4��MHj�&��A�l��XlC)�eM>;��mSQ���CXT\\��n���2#����V��R�7\"�eЕ�t�n|�������)�5�`}�z~r�B�t��o�]@X��28'����N9ǆ�~%��yO\"bB���zD���T�\0� qE*'0TT\\�\"#,�Hz����}3h�!�WK�\"H�EhQ!Z��D�\$Q���	��Ҁy�@�5��r��ظ&�.0i(���w�y�n��\"���E�# q뀻���c�?< �ЧHI��C�B�p����fV�ATU��n�qK�2��dUܮ!w`-]�u��1�I�'��Rȣ6\r�J5q�A�\\��(��.Am�p�Z���\$���f���\n\0�sA)����(;�\$�!�#u�@��5����1���`\0�#������\0�mp[�HF�����8�XAܒ�~���\0o�{ y��t忐`�j��M���܅b� ���l\"W��T�X5�y��/�Mi޲���69i�Jl\$;\"�H~Bɶ��I�N��0�\$+�FDd�ӦB:B5q3@Zd�S�jG�W\r�KE�0��6rQz5\0b���H�/=�o���-� rY��@��1/l&�-C�\0�32fɕ�u8��['2I�N�J�@ZC���rC	�՗�!xɠ�����i)9�X\"҆�@w�(�)������Ɯ\$)E�2T`d�,�e(p�Jx!ҩ�l�^�R��-�2�m���y&���|\r���\0R��\0�K�� ��+p��U҈�C�e�0#X�����\"�H�;����	�(�y�k�6�����r�\$d���!n��k�@u.Q��H�'�x��.�!L�#�Z�ABpȔR�0�ʌ��j`5��b%C0@��,i\$�HJ�R���;�r+�D��a[H>�:ly�/��&e��\n��	7'9@���+(�}�;��s�\0�S;�P�>p7nIl(VRt��m.(>_D�NnIY�M\\�+�K�#�/X�BP�(�`עt\"�M'!~ҹ����K������#�a�xO1%1��m���\n���I��r���&�4G�O��[W_�u]����_(�	ͼ�hxs1%��!r��%�llD�D)�7��!�j>��\r�Ob| 3A�\nH��hי~60\0A��!�෩�u�䃰2O<&��6���L�覎(!C��^����6dx��'�E���f��d^,c�LBK�\$����Mf^�����3��e@��P�nTJUH��>���~� 1ī�K���˫�)�H�י�ʱ*�*(�gf�FdwK��\0�\0*%�\0\"|�H�@��\0��J>*d�(&�mj�eV܂1�ʆ��)���Y�\0���\0\n5�h-#�����G,���U�WqjJx��P(�����utR`���AJ���������\"-����8\"M��A*\"Q���%\rh�D���.nD`-W�_W��*+m\"�9S�\na��'DF!��2�Ik��՟hX���Ӏ}�Y�f��5:Ra�i�8�_\0ou�Ł/)U\n������He|�/�>�4b���9o��ǎHRS�X��M���2#�b��]��L���8�<����w�y��	o�9\0�Mи�=��}����i�\\�	i��@^�\$�,�|�Y.�(MZoum5�YO�}Dy!\$�ё\0P�\0��\n�������!3�2�\\��*j��h���@R3ݏD�F(#�;��|	%L�0˄(�|�h����P�����h�EMY*h���+�8\"JrO�9ܩ\r7���p�D3��J���|6aI%Nb�p��ޒ�S���&8�Bi>u�(�E0(���kJY���������0�9�UM���eWP�Ԓ�H��<�O:ZUE<�.�V���']]��*����%D\n	�.W���΅(uP5(	�2^�BCl�zP�`3���\0�(�1Tj�b]pN\\:���!�,�Lg`P�HH�Ү(�\n�<��Etb�0G���\nؗD[�p���00��Sq� ��Ⱦ6iI�	j{\0����PKq�\"ɯS�ƺ���t�;>Mp�Sc��ɦЖ�x6�E�����4�N�'��ΰ�u��}\0�_e����H�!�7B���\\k!^��W�\r��lq,��oXh�6����=_cS؈#V\$o��lQb��!�䋤��)��1\"�<�+VZ�I`K��ND��_���<e�Moaa9���3����e��*�Vb� Ukc�;���j��b]�V�n���*��JVHH�bjd�AV��#��NB6@��n���0�c�8dՎ�g��N�����@����Ӯ����\0Εq9Z�¶�g�E�6�KU�JնU�z mde;I�~��e��T�r��]����m~k���n[5�Ei��j�q+�MYx�.���\0lÿD�+bOŭ��m�HP�#_�:1��\n,��(1r���F�p����*:�W�א�0��P|hh�9��G�[K�5kL��ty��BH��X����.*YK����A_�7J��a}t��2�D��Y�tB��Հ��P�֏c{��/�S���~F`-�T#M�����\n��KE�C���Z+�\n��ȍ&�U��'�8�wD��0F-qx���Ɣ����t�x���>�CӓL^��la�9�\$3��E��;+��B��%�t�뭋��[�(�k�L�P�J�V�����W�ɐ��l5`i�å���U�^� b2�n�eP�t�C	�!����y%�b��^�U�>a��P�TVʶ�I���UׄV��!4��kj�㥤_���F;J�XW`i�`+�+��<X�/]=��a4L�h��V��!{:;P\"1Ы�]HG��h�d�.���R�hP������ǈ>�\0 �����iқs��`,��o���tk<=��`q3�DK�*)��H��GG��\"��to������1i-����#]�F�u�p��e��B�(�Z�u@,!�j�O��\\�*.����E��gi�X轄�-���]�����]�o87�f���aRpF��N��!7���D�R�ip�x���\n�h�ὕ�B���C�y@Q�l\$a*K�M�X�Z:�O�J��m�����y�a�4�Q��~��bT�@�E	�E��kx����	�L+%G\n�\0'��j2[��C6\$�E��t�K<���}��v�/O���P�7>�V2�����	\"�W�[*ٖ�Dq�mʉ*r����K'E����[A\0�\0[�ORdi�P��X�X�h[g��4(:B�g�8���\\r y��Eˎs_�m��r#�\0��8�1�60ȕ�K��Dw�*`�E��,a���@�>2_��q~��Xø�JW��K�<#>X@�v;���^YB�E�.]�\\�d���N�R͢�q,��g_�b>�5ee���ƈ�%-,n��]\nx�>����t%ˀ��1�F,�d��62�f-�՛�����gF,���BWA+���Wm�\\�\$������m���kC5��@�@��#uA.ڇ�6��#��8�d�I�\\�f�#9�8�ggmI��o�w!1Ӛd�9Vfp\"=q�����	�+��I���C-�o�.�7<�60�'��@�mkzi i��.b�iL���:u_4r-��ܓQ`,�\0\0C��Sɽ��Niٙ״�pLvΠ@E�+!g�}��Z!�۞�����oX��\nXlp���J�4\r�l\rD�ۼ�%-���ƫ0��r�y�X�\\�g:���/;���Y� �{�_���7H%�ƉB0ި~�<z,��Ghy�wO�����=y�|�=Y�zA7>W��̱CG=-2`s�D&C݀&r3�Ԣ3-&�@k3Up^^ϓ\"��S%YUO�+�sKWX�c1e\"Ri�儳5Y�\$�T��e����No\0Ja+�V����uL��3a�\nmO�\rM�w:D�DA�,8��~Ȁ� \$\\�\0�my/V��C�-TEP\r�%+]L��@VJ��[�v٨\0�3RQG��߉�j7��`%�{�9�8�\$�2[��#�T1���Jr)S�����%���T���\$4���\nV��� �� �8�Kz����)����ZMs�W\0�Š5٭�V�h�)��#D���57�\$'�BY4�\r<��|Ln����h�y�:��]���R�/gSY\nc&k�Q��\$I	��@E2��޼�㽼���0B����,�ԝ�z{�d��N�=�bL�\0�fȣE���N9\"��еz�Cf�6���ʥJ� S)��Zu�V��	�҉���5՗�b�)t����_�?�\"Z@��Mh�&ۿx�*��I�l���\$��\$Um��)�`d��K���K'�L��5r�'��Y����N�9��x�R�.�|2SM��NB�G-�rjQ|� '��ֹ���N�;���HC��Z|+F�d�/6�C�d�n���p]w�B\r)!�J���B\rse�d�#�>H�m�఼Wd��Y��P���{��a3\n�c&q����y�stC棵D���>�'snsy2����b��b��.{���B�\n=�O�!;�\\*�+���W���X�D\\���X��Oh�F������X��]����4.#M<����j��Mt�S����&�G�UP�&td���B���0!{�>�2�:��Q�����af^��B��`��eF*0�Z�\\|��'�&hd����gx(;����G]�@�\"��_�%���n,�jPf�Cr��0�o�i�--��K�5a{��9�я���ܒ���Z=\0M-��a~DMF�w�!��S�����k��1�|�G�=_��@�[������A�˗V��HW�\\��e�t:��0y��],M�p�\\�ﶲ��),\$w�\$�����j8�%)��-*Rq*�SH�n��2Vj�U��h��[*���8y��!�jw;�Ĵ	���f�>�]͇3�*�}���\n��\\/�\n;�6m�@�b�;�h��-Ȓ�U�:I�NP����}�V�lnU��%.�9�B���@Ip\0��\nK�7\$�`E�xD]�\nF|�!n��Ԅ�ߺ�\\����Ȼy�?����,W�ͭ�����:�u\\q�&4g�5H��;�Q�Ǡ�\"˒s֝�vhInfU�(�V��V��#��If;�:{�~�r󭜨;��O1F�\$�\\�C��cV_���}��I9'�V�O�l�tn\n��B�R�1�m�|�wK6O� ֝�鑘\0j�����O�=/i�>F�v���n��;_��؁�|�A9?+cܰ����#F\0P\n��|�����!:�>/�N{�Ū;�0���%�>w@N`����6�;����\"/X�g	D:�g�i�##���kK�rR���M����:@��\$�w@��\0�we��<ݕַ�{�w��P��2���:<⪇\$�&:~�륽�>n�D�w��83��<�V�q�Z�LdS��\0,�k��?A�\rfΫ=I\n��h��<�M�I:E��0�@�\r�2�S���+\"�chI\0�d���(\n�[�ϔ:�6���KA(���8�#��g�@ZCP���� �^U���C� mx{IBn6\n[DA33JL�N�Id��dR���q�2(��7�uA�fp`z�מ��@|��Y�G���ko�O����y�>%ԄH���b��N��Г�0�2j�nr��9���j���Ш����������]�����c��������6 ԛs�p\\�k\r��j��'D*<��>�y����`��p���rW��\$��!�l�G��||(q�B�R��0{����⏸���T*\0���2���G�O�EI�)�\r�^E�op��������4����\"���^P/\0��^�7��h�4��[.>.];��Q:�؂ߤ�[8-�+�>��綽F4���6~�����8]_����\0002_�Jp���K����P�7�<�XNJ���%�ţ�t�\n���c�!P������א�3���������_���L_?2EO4�a~8X���7��P�T6z3� '�����M�)%a�>�f|���'Q�nF#�_���O�}��]؀q-�_HzHp�����Å���>�\r�p.�|�B@���Q�R���\0�F��h\n��J�����0\n�`�������K!�������:���)s'���������*��;��a��^�\"�-b�+���f����N���n��d#�\n��~\0z�Sg����U���p���hG�@T`g,�HM�M�\\�x!�8\0��O'\$���\$���C�A�&s��+?�1��'�7�^���a��� ����>�2�N,��%��ʴH��+1	�2���i� ��h@�\0���M���朽�	�FE7�f�m��\rj\rȏ���\"a����\$���w��(-��Y�la����`�ZP�K!{����P ��Bɔ����ށM퀆�z0y�,�TPW4�z��PL>l��N��\r�4�B���@����\r��X@PT��Yd.A`�d�.Ajz{w��?m<+:�j��\r\"���=��5K���b��e[�U(`P+�p Y�G,�e��?� �\r�M7����\0��");}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("v0��F����==��FS	��_6MƳ���r:�E�CI��o:�C��Xc��\r�؄J(:=�E���a28�x�?�'�i�SANN���xs�NB��Vl0���S	��Ul�(D|҄��P��>�E�㩶yHch��-3Eb�� �b��pE�p�9.����~\n�?Kb�iw|�`��d.�x8EN��!��2��3���\r���Y���y6GFmY�8o7\n\r�0�<d4�E'�\n#�\r���.�C!�^t�(��bqH��.���s���2�N�q٤�9��#{�c�����3nӸ2��r�:<�+�9�CȨ���\n<�\r`��/b�\\���!�H�2SڙF#8Ј�I�78�K��*ں�!���鎑��+��:+���&�2|�:��9���:��N���pA/#�� �0D�\\�'�1����2�a@��+J�.�c,�����1��@^.B��ь�`OK=�`B��P�6����>(�eK%! ^!Ϭ�B��HS�s8^9�3�O1��.Xj+���M	#+�F�:�7�S�\$0�V(�FQ�\r!I��*�X�/̊���67=�۪X3݆؇���^��gf#W��g��8ߋ�h�7��E�k\r�ŹG�)��t�We4�V؝����&7�\0R��N!0�1W���y�CP��!��i|�gn��.\r�0�9�Aݸ���۶�^�8v�l\"�b�|�yHY�2�9�0�߅�.��:y���6�:�ؿ�n�\0Q�7��bk�<\0��湸�-�B�{��;�����W����&�/n�w��2A׵�����A�0yu)���kLƹtk�\0�;�d�=%m.��ŏc5�f���*�@4�� ���c�Ƹ܆|�\"맳�h�\\�f�P�N��q����s�f�~P��pHp\n~���>T_��QOQ�\$�V��S�pn1�ʚ��}=���L��Jeuc�����aA|;��ȓN��-��Z�@R��ͳ� �	��.��2�����`RE���^iP1&��ވ(���\$�C�Y�5�؃��axh@��=Ʋ�+>`��ע���\r!�b���r��2p�(=����!�es�X4G�Hhc �M�S.��|YjH��zB�SV��0�j�\nf\r�����D�o��%��\\1���MI`(�:�!�-�3=0������S���gW�e5��z�(h��d�r�ӫ�Ki�@Y.�����\$@�s�ѱEI&��Df�SR}��rڽ?�x\"�@ng����PI\\U��<�5X\"E0��t8��Y�=�`=��>�Q�4B�k���+p`�(8/N�qSK�r����i�O*[J��RJY�&u���7������#�>���Xû�?AP���CD�D���\$�����Y��<���X[�d�d��:��a\$�����Π��W�/ɂ�!+eYIw=9���i�;q\r\n���1��x�0]Q�<�zI9~W��9RD�KI6��L���C�z�\"0NW�WzH4��x�g�ת�x&�F�aӃ��\\�x��=�^ԓ���KH��x��ٓ0�EÝ҂ɚ�X�k,��R���~	��̛�Ny��Sz���6\0D	���؏�hs|.��=I�x}/�uN���'�[�R��`�N��95\0��C������X�ْ�6w1P���u�L\0V��ʲO�9[��O�>��PK�tÈu\r�|�̮R��pO��U��Drf�9�L�cSvn��Qo���@o��(��ްàp��a*�^�O>Oɹ<���e�������\"�ٓ��P>��H^���	psTO\r�0d�{�Z\$	2�,7�C���!u��}B�^����?�D��ڃF�ݱ����H�Ι`���'�@J��3��|O�ܹ�B�Mb�f1�n��@�1���(ղ����!�oow��f���)I�L\\[�����8[1)��!)���u��~�c�-�6-���y*	���>\"�m�61��ӕ�.��~�*�x��諍q��ǚG |��rl��O*%����݅�A�bRAx�g��D�f�V\\��R5l��ޤ`��5`��w�|���Sg��O���B;�Ϯ^LÖ��W?�5 ��ac}��s�ݏ�I��A��r��ݺO0�;w�x���P(�b�m�L'~�wh\0c�¨pE�߲:C�{g&ܾ/Ƒ>[����ۜ)	a}�n͡��wN�˼�x�]V^ye&�@A	�P\"� �E?P>@�|�!8 �Њ�H	�\\�`��@E	�Â�4�\0D�a!�������nr쯜\\���8�o`�H�f�����&���̒<�r��(jN�eN�)�6EO��4�.��n0�������6\r�� �\$����\$�� �N�<��|αN���j�OY\0�R�n��`�o���mkH����*�-Ϙ�w	Oz�NZ*ʛn�O�\n�#�n�⏓p[P_�b�������jP��P��Г\0�}\n/��Ӑ�������П	o}��S'��`b����\nPd�p ?Po0sq\n�:b�L���Uu\r.L`��SP���1mq���~�]%&ʚ�Q��� �\r�D�pq��pV|��f�8\$�p�&��ׂ�F��&����m�O�w��G	��1/elր��D\0�`~��`K���\\�b&�Q�Q�`ʾ�A����V�E�W�n: ؓBƌ�\r�*��l\0N��D��r뭦���[&G��h�r�H4A'�bP>�VƱ��M~�R�%2��r�m��\$�\0��2�c�����Mhʇvc���}cjg�s%l�DȺ�2�D�+�A�9#\$\0�\$RH�l��@Q!��%���\$R�FV�Ny+F\n��	 �%fz���*�ֿ��Mɾ�R�%@ڝ6\"�TN� kփ~@�F@��LQBv����6OD^hhm|6�n��L7`zr֍�Z@ր@܇3h��\$��@ѫ���t7zI��� P\rkf D�\"�b`�E@�\$\0�RZ1�&�\"~0��`��\nb�G�)	c>�[>ήe\"�6��N4�@d���n��9����ɴD4&2��\"/��|�7�u:ӱ;T3 �ԓi<TO`�Z�����B�؃�9�0�S>Qh�r\0A2�8\0W!�t��twH�OA��\0e�I��F��JT�4x�sA�AG�J2�i%:�=��#�^ ��g�7cr7s���%Ms�D v�sZ5\rb��\$�@����P��\r�\$=�%4��nX\\Xd��,l��pO��x�9b�m\"�&��g4�O�\\�(ൔ�5&rs� M�8���.I�Y5U5�IP3d�b/M��\0��3�y��^u^\"UbI�gT�?U4�N�h`�5�t���\r2}5-2�����W��(�f7@��e�/�\rJ�Kd7�- Sli3qU����z�\0�)�\$�c��oF?@]LJb�Dҿ�0��s?[gʜ�%��\rj�Un���^��R5,֪�t�FE\"��xzm��\n`�-�W#S(�l	p��%CU��辚�F�&T|jb�Z����8	��/4L�*nɦyB�:(�8�^9�8U� K���{`Z���\nF�\0Cl\r�'(`m�eR�6��M���B���C���6��v�����n%#nv�D��jGo,^:`�`s�l\r�_���X5CoV-��8RZ�@y��13q GSBt�v�Ѣt���#��bB������]��#�p���fZC�Ĳ����OZ����N��]�����sl�Ԃ���EL,+Q�@Yw�~9�I\"�8!մV5�&r�\\�7��W�&�ܼ�[\r\ri\r��~L|��d���ܷ�,��|i��@,\0�\"g�\$B�~��!)5v0�V ���b|M\$������D�f\r��8;���}�f��f�����icԄV0,Fx\rR��`�a&nȧ�QB.# Y��>w�g�����E��[�Ɨ�X���~RO��Y]8�]rK}�-��?�8�v�L�@�~�A*��f���J�M��tג���-v�[#�xL'L��>�l�8�Pg\n��\r�Q���ѱ\r�M��\":xw����\$b��-������=�kRXoQ乇9;��ˈ過��sՃ�͋�)���~�geB�Bt���,����,����K���y����-,mӀ���+��07yC��˃�Iz�ƍ�Y��^GGW��u�v0#kX��RJ\$JP+�6x��1�8���Y�g����{��?�\0�X�\r�	XF��W��ה��V/��̓dIg9߆�і�y��1��-�G�X����@O��R�y����!�GuY�5�ZF\r�㕵-�\$�O�e�u-��ZF��Zd��i�9+�쵘`M�z��\r�ҫI��y��A�Vp�:��O�J��:�V:�#:��:c��{��k�l��Zs��W����P0����#�9g@Mc�zw���[9U�\\k�����6��9Ӆ� ���y�,�����f6n-Zu���f�ً�c�,����[o�[g�d� �:w#��!W\\@�n�`�߱�\r��ɡ\$۟������\$��%��ߡ۷�z#��\$�imY��c�ɂ�k�I_������y��L���Ϲ�\$�`V��[����F�2C�8�\$��������ؼ�����G�[����¼���=�U��υ[q����K����Y���݋�Q��?�8���aX���m*G����\\��?�U�\0Ϣ���KĤ��|CR�͓�-����|ɜa��e��RY�ƺ饘�ܒ������������PJE��=��u�����\$�{�8�X��{����ŏ����ٓ�ٗ��ՙ��\r�������Ͱ٬&���Y�ҹ�(ټ�M2)��V u7\0S Z_��o]\\�|٩Ec7��S��΄[���<��<����;��-��i�� �}����l���!�,�}%����-۬��=����Ӭ��=��Y�8���PV|���zE.����\r�����bLfƸ��h*;�	ַ�;�؇�Q{��9\n_b\$5��l�UzXn�z\0xb�k�M	�2�� Z\r��c�|�ג/��}%��`�N�A�\0�*=`�F���^Q3�W�X��<���tR>r�`u�ģ>i��zN���اÝi����\$\0r���s����^C���>U�5���^a�)��	��J+>�uB��@?�J�-H���OJ'�-Tʀ�T��oUh�F��{��ԏJ[��N��V�oJ&S�B\"I^5�I�2���T���龽�]\0��\rk�L%�}�t�۷~I0�H|Pk�L5�_T�<�w��=<�x\"esa�K�\"���JH��+�U�a��'Y�~���7�)W��<6�=_�N�h�?6ܘ��y�,����a���w�\rİ�#�-V@�k��?i�b*%�޺��p?����yЀΆ�p��-p��|�n���Ca�f�8A�8�+#\r�R�@n����p��m�~ۈ{`�H?�v�*%�Ǽ�v%��G�`�`�Z��.���,�6�z��U8��|�y��V�����/�p��^��פ�m��]zcӞ���\$�IB0�|����@���pR�\n�j�9 ��G�7���읤#p߭�?����'���=�6H�lψ.�Y�OY��_V�G����O]I����=��x��\$���=�|Ϫ{��\n��<;�{:f^L'S�A1%�8*�^��p75���W��\n��\0��S⟕\02\nX(�u[��rp��B�0ڭ�x���:n	�ZI3�C����{�[��&�C(@}�r���w2�闌�nt����{C�ɆY!\0�He>��P\"�9t5�o���!�\$@\\7SS\r��C� P㄄@��I���nhG����	I�S�`x�7�0b+v5�^g�r%b�p�U��%)<+�S/Z@ �4!��j��8��\0�vN-6a[>�X�,�e\ned/�PX�`�}kOR�N���+�1O\$�π�F6B-�:wڨ�N��T�D>��x�����Y)��n�1��&�7��}�&xZ�\nޖ������W��:U@��a�⺃@��.�R�hbcT\"�����x\n� E���|߈�\r�-\0��\"�QA�Ih�\0�	 F��P\0MH�F�SB؎@�\0*��9���s\0�0'�	@Et�O�����Cx@\"G�81�`ϾP(G�=1ˏ\0��\"f>Qꎸ@�`'�>;���l������82>�zI� IG�\n�R�H	��c\"�\0�;1ێ�n�)���8�B`���(�V@Q�8c\"2���E�4r\0�9��\r�ԑ��� \0'GzH��5E!#���\rA�JЉJ�(��FC��&�d� I�\"I�V솣���G�SAX��Z~`'UA���@�����+A�\n�p��i%��ѿ�G�Z`\$��������>~?�E�\0�}� �<Q����'����E�w�ئ��#\rɂ7rQ� }�'iMI�O�0dm% ��Hʰ\"-h#��XF��M��t\$�!���R���t�,(�H8�8�!J�5I�x��r\n�Thړ~Pe@&eg\"[hؖ��4����|�2�z�D��lw#9	v{lb��/~\0���&I8%�,�IKA��\0�����/GYK�*�>���O/���2�t�eھف�P93=\$�X�d��-�&��|��#154LU���G.�i�2`����M.B���\00036�ISJ�-�~�쩦�jF\\3	o4�u	(@a3�A\0�c��`�P( ��0\$���\\}/d������\0�-�3�%b0\nc�z`��))%*��6\"����ٖ��E4��F�q���J����d��(�Ӏ����1�iLm�2�A��.)&q@\$�`L���2Lrse�� �.�vss�\r����i�KQ�󤙬 �0()�|�Mb�tU�9!�ED	�(	�`8*pa<�����80��s�\r� N���8O0�Ξ���d0��OVx��@'�<�Ol��J)�	�~}���\0U=��O�'Ňd�~\0�Of��X�H�	�L��Ҡ(]'�@�EP�LW��E'=��\0�'�\n��N�\$iI��Zy�	���>i�OH6f��'�߁x�.\"}@��-�wa2vӅ��A��L>����<0/����P��B�����͢��T���\n���<sSQ~|�ӂ��P�f�i�O�φ�lq���9T\r�����ѕgÄ���Fӧ�%O�(1�h⺶n�m�v�;�|���g���SaF��R��Ȥ�Nr��9z�%&�X��\0007\"�2t�-\rh%fŦֽ���3!�\"(�7I�\$s/ �-�7*J\rΕC�Lxw���֗�铴���(Ҫ�B,+�h\n���f\r�F�7Rf���*�:�\"�Δ4t�P�i�X�����*�\0P.(#��+H�oJAG���q�.57�+N	:-m`���&��HJO�Uvi��\0�\nGN:gR�n��2i�)}#���	F駩�>d�`�q������H���ƕe�5J);HQ�����\nHϓGRW�Ԟ��/�Jj�)K*UR���i�b8za�.�����RG��!4ͣ��@9����c: E.F|��T*��s�<Z]_O�i����\r@�2��qTlVUk�CQ\rOe��\"�\n�.�T�EUZ�Ԡ@i��^�ܪ��L��aMUB��V������'�U�+Q �V���W�m�G��Ժ�u0��*�P�T+�!u�\\�kV�y@Ƥ�j+��H��䁐�\"E��P��,�`<�H��Ք�p�ğ%	l\n�K ���\0�\$T!8@�@�2����h��4L��ŝ+��&����,�|��\"�T��Q霋�b#w)umŵ[�ޒ��)E}��[���Exd�)p����	n��-AK��1}W\\IU�nF^�\n��` \$��m)�oZ��	P�D�P�V��D �r%�R)��bұ�l�^�w�)JB���-K�D.1��8����\0��;� le�,L(\"m�N\n�Z��K�����gH���e��\0��\0t7�]��Kk\$�yN����X\0�6�(Y�������f�\\\r�K1y�,�`0��qo����\0�h\$��\n�_����dR��zE���C�h�<Y���p!�\0ro;����'g'*�!��Y�Xv��%�K4R�V�\r����Z�}Z�\r�o��mpN]N��5��xUay��\r�j��W��k�b�~��+m���edyٯʰZ�ksO�4;T���a�l@4[��]�M�7n 7�>�6���ϓ��=�h�*�0HΫj\$��[`���,����y	>��7p��D\$��u9�H ;�������R��~�0[�D��H��삕6�ܐ>-Lxj�Z�k�NȢ����n���dg�;�C\\\n�Pb[�h)3M�c�D4�0uR�#bP��5�:�a��EqH: ���:�.X��?�c�9�%n�K����a�5��J�`�7X�\n�q=ȿvr�E�<�(~���CȷPQxH�bK�ܪ�-]����\"�Q��C�U�.a��Q��v&�� ��7�]Ĩ媻�>�.9\0�=K=)���T�� ���_OX��5�!�b�U��h���AP�-����\r��%zPޔ߀<�x�����c7�|��4q�����p�C<�N���Y�5ь��)�澈��}AN_�RCTx�F�*�3���g���.�`*��B��`&�T�:**�7ƷE�W�R�\\�c�W��[���Kb��\r�o�Hr�����u 2~/խ�	@����aI� ,%b �\0�¡+{��[�,`_6�7��.�@̆�)?�m�m�b�a\n�v�������]`��W�8��!���W`��:�Fpo-`7	�\re��XXzK�I:���bD�_�5�>���ŗ��f+<Y��vg��,�%�H\\  d\$@��q�\n��A \n��6�8F�'|�I��R���T�{s�m3��8b)��	@���Lc�M���F@�#Y`��N���DX��CxzYc�0y���3hDZ��6\"�t\\7�SE;���U#�R^��ީ�s\0Cfb�ܚ��rrI\"Y�	�tå�8ZB/.�`�E��K�|����b��\n|_�}��KC�.��� p�1:����#Y\nTC	%,,��\r#�@�+��dqŁ�\$���{�D	\\J\0񒫇-`m!�|�g��dz�VI��vv&��A�`���MH\\I�����|E������j�B0ۊ@ѡnU��K��ތ��>����]ݸ�h��i�X9upr����a�\$7�v��Q��CA�>1����xif�R���7*�;8%���\"��Ʉ���w�P��TB���yH��'\n攏bظ���v��T5xcH\$�\\��ۏ����X�l��K���a�`���#t�Ew�gh�1�� �z��p���4:�\n�C��2��H�K<X	(!J��;�㏨���,��u�3�y�s�M�C9p��wz\0��ՠ9���ǈ�x�ǃ��1��B�������ي��`r�)=hLƂ�`���?z9�E�?���J���1�����Q��R�<\r�L\n8(#��r���p>��L�Q������|���\"4�(��*���8�fpiWaQ\n�Q��*���\\0@H�;�V��Y�Ά�����OZx�<F��'�I�A\n<�]�dP��_N�T!�\r˧���@*~І�B��=�%�z������;��:���AB}��&�l��c��h�`T��O�))�\0�y���I��ۦ�8��Ny��ј�G�\r\0�T�\"hn�5W@}�����Ն�B�}ZkV���Ф�y=s�	z�Ӕ����;\r쌚��,�hT��i|jza&�ր\$�i�S°�Hi��>I�B{Z*U�Ә�I�n���O�}��XMs��Q��8��I��Њ�	��v&! �k�@���#��<���T�Z�.����j�Z:�	^�B�}Y�����v�O3BTC���6=��k�eS��~���?]ij�O�Ѧ����m,\0}�!���mF!�[J�.��g��Ul�ZP٦����O[;&����]��Oht	`aILA��k�bki�N�vY���m:��v�v���k�g7���)���>��b&�؞��p�\0�5�I����]dp=�+:;��)� ���Dx@^o��ѸA���L�'�����t w�&U�g��3��B`/�=����'d>�/�dbF�\0�w\0y����9���n�Z[��6Tu���b�Z���~��~��\nzd'�@�Ra\n\n@�G���0�;vS����={��~��\0@_c0�ov�1�~�x����������e�\0p�o��>�83�|�pp�<�Il�o˄��O�;� ����%8Gx.>�o��O=^uLG��\r�N7��ݶq8~&n�5��l��]ڀ�������I..��4ओ_ۼ=��x���P������I�����5�]���[\0_�\0̓��  �<:��e��o������� �B�y�/��Eq瑻���f'�J�w��#7���N�x�F��(y��D�7�\\��'��Y2�˕�?߯)9e�nGr�vQ	�.�/�.Y��ܪ�<�zkMޠ�c��M��B+�\"ہ�\r�g�l�\0^\0��B@-	T���6�1����\n�����P�@ \"\"��@���F���0��t����U�\04��!��_|��(B\0Oc<��'����t�\"m)�TW����F ��P?f9�����C��M�mk����D���ސ�|���	�&��3�`�dΞ���\0O8�y�@��\n\0I?@@�@�/��O��\n��0���<d�\r\n�\0���H��C>�k���nm_:Gb�\$�\0�ђ��|�(v�I6�0�\"KB� �J��rK�`|6����F�T�'�9Y9>r�@y��@�%�ʄ��d�7<��\$p>t�\r\0|�yr�́��k9+����6���#��\"97�� N�ڮ���ͪ��Enp{s^�_;�\"��I�\0�J <w6��e�jc%���8�5�ր�����L&F{�2/w;����&CD���+p��%�#��BYo:d4�#H�!�A�,݃\nsα�8#=g�jl:�U��B�YX\0�eտtmd�(v��@k\\9vQ2��-{&/¶A��<%N����`�EKJ��Pպ,s&���8+-�1�T@W���8�l����D��x76@�\$�v�\"���t�X���vj��@t�H��'Ey@5�ك<ɏ��{��v�OY{LW���r:�(�,̗��\n�+�:(�5䏤�����02�%�D�Q�B��{�x-�(�*�~.����C�J�\n������S���ў#K��|䆮��ɨ2C@��a�B���bCq��y�L�7�K��4���O��fQ=�'���<!ٙ�fP+�`���gND��U���ҡ��!�\$�\$��-�/��3�Az_�@d~Q3��'��>�\n�\0�11�>���J�5���T���k8;���d�Y��^��ƥ���\0�Ӈ���(���F왕���`k���Q�+�I}Z�g0>�0MW{�z_BkП;`�(��-�wJ�e&ؤ;�FA%L\r?!��̋��\"�V�_�5G3���s?-eتQ�,�Y�s?24�~l\$߱eؤ޷�G\r�rH�����A~��O�,�G@l���dϲY���l�bЂ�?���#��:�Sߒ��k�n��ü�,�3Jy�\rg�fπ������v��/�4ݒk��d��A}�OY|t�������K�A���ޗ��?|���ށ-�����&���W`������_�\0S�����������\"��os~���G��r\$�Dr��{#�'���Eͽg���/�?����<������?��:��0�'�����Zn�7���9h@�?����b@(�3�o(�.������,���o>�{���I\"���䑂\"�`9ډ^����-�F7��%��h�Ұ��*֬�@|	\0i����@�@~C��\0�X����X\r,���3��\0����ZT ���6�.<;�C;2b��\0���K=1��#�!��� 5�:T�\nꙪMtᵀ��i�l@����9���S�b�@��(��81���i�A� �@�\r�+�8���K�B�6�~�\r�8-R���L\n�*�`6��1w�B�[�Oٻ�:���t�� A�\n�@�J\"���A8k�l[�������Co��<_�#AF��Xn�l��(��W��,�ꮈZ6���ȭXn\0���J3�Pu������>>��d�!=V�{KGe�c�F龪�Ɍ����m/��0�L��XOi*��˻�\0B/�3z���(��������}�0����+I�BPp\nB����ש���Iui�,�)0���%f	S��h�����Ϝ{���:�P�#�_���'T��k2h� �Ⱦ����i¸B����\r� 0k�ΐOn#>�l�	�\n��B���\n��2����̐�������VOiа��Y��b�s�\0����d�I�ſ	�1�6B�[�,\\���+2��(&��\0���\0�\r��p��^�Z)@<AL�zɐ��U\r�\r���tdH��\rl0D�V1� ��9�d0Lt������@[�5�P	P/��+��<Bz�zn;�f� \"�\n��xg�j���`T�2�4���X� @;���7������\"��ț9h�ۮ��>c<����C������-a\nD\np��9�bZ�����k �����*2�Bʡ���\\1���XC��'��Ɂ����D��D6�; 9;�+Ȯ`���ʃ�J���C������\0002���o���PH�>�\rc�`2A����@F��`ۂ%\$�\"D8����+A�\\`ս��y�&7�4�����x��\0ºt��Ѣp�� i��ZHe�HR�����D#LZ���p)�����.�bɀ,��pB�\$�%xB�&�TɈ`�E(�R��b���\0�;F�1i��o�TⲀ��4/��k<U�*\0�K���\r�Q�Z�e���]\0��ɑLEK����:),X�c(�?N���,W���V�GBʯ�Rqhŀ�ih�<S�oŗ�Y��EM���_�Y�YE��]Q]ų�W�KŻ45qv�����zEB��^�r�4��.���9����\n�al*�+,`�S�U�b/QE���kQ5�Xc��mTP��T�{�`����%�=	P\n\0���x{Hq��B��!R�5�P`��]��	����i�>��¤���h��F�\nN��<<| ��h�Oj��ᝐtڝ��C��)�F��88(�1�8�NR�i����\0߯�����i��蓀-�@'�2!����K@��%X\0����Dk��(Z��\0���\0���룆#���ii������(/-��\$���ػ�`t\$�����[�;^�� ׃���;O/:Θӽ��]\n�Ja��L���9F��RS劣\$�T��d����Ճ~`6��2�	����j��D�2\\OG�Q8����� XE�����4�nl��CfA�\0@�bX	b�Xd��4bk#V\r�t�~�W5�ћFEN`�m���#H��F�OX���\0�8��\$%\n;���(���)���0�\n�:D����@@��)���p	�r����)�0�jM�\n\0�8�\0�(\n��#�!�`���QQ�\r(�8��J5R?��M�(��X�)(�<~Q�G졀Rѹ6�䀑� dmǴ]\"b�����\rȵ��ʁ �&>�A��\$h?��c��(\n�\0�>�	�����}R��~\rhH��{�,G�<�m�(VN��\"�\0_�h�7:،�2A��_�>R\$�1\"\\��27\"z�#�G�l~rDG��m��l��[��I-#Srr@u ;d* I/\"1�����'�]�<���\nH���w�AI �������8#��	[v\0001�^l�#27\\��}��ɒ3#���7E&|�i9����l��&�v���\r��9�'zC./�3'�@�j+�h农�*r@��hY��;'��2~��(96{�A(9��HC�T�D��[�҅�](���,0��u(���}�3Q����)<R�2(RL����\rd�'�\n��F2{J���|�u((SA��ȱ(o%�(� °\0[�.��ʐ3�򙆚��J1(T�2��\"j��ʫ*�7ү�]*���I�:0.!H\n+�C��`����(P?Ҹ���L�aF��+��2�ʀ9�� �+�σ�*A��F�L6��0�\0�+�c�\$@cP?R���# �R��Xy:6p�D�� �,����G�5(�QQԤcP\r��+į�'J�B�8�,�m�8������-��P��pM���x�̥B�V��}�|�G,�< 6\n�\r��ҲJ�S� 9�Z������Ļ2��.��E����1K��8:ՌG*A� �&5-ĸ!jK������Ae-�9�'#/�������U'�s0��'�\n����LUJN.m��Ķ�\nK�04��9Lc��p�\0�<���L0t�2��B\$�<LBL�sLJ�xhs��1l�n'�|���W�d�����Lm,�\"��w*t���Lo-Y�hߤ�\"Z�1�ȥx��焨Ĥ� /�1�U�9̤ʒ�K�2��s.��'(̂�vI���|��������̇.cS\r�\$�����a3�r3\r��J#�i�<\r�� �1�+�΀�J�4\$�N�#���-4j�jM��\n�o/��34t��HʘlȒ��8L�/��4��SN�0�Q���4�ҳRM0]����K����3>%0�')L?*T�s���|�3`̋6���|��R�ͅ3��a�J&�r�M�xs9�2<�s+̅6�(�l͑1�>�9͟5ۉ�T��6<�x\0�\\�slM���/}GJ���\0006M�7j7�;��3��gM�7C����+\"�K�7��s�#~<���ˑ8d�i\"���\$������+��,� ���0�8Y&6��7xb/}#3���\0�8����L��	2��9��Mu9K1*��-/�䲟\n54��q�K��œ��wD栏�o1She�~#��s��l�r��:��ӜN|����\"�4���L79�?O}\0[KӉ�7��eE���(\ra�N)3�ܳJ�.k�2��BF��K���L�)�I2o9�%�|2f����sI�'D̒u��'pSBy���>/|��-\0���s�ʖ�r|�O8�DH-N�<�u�Jm:������=X%)��0�Y3�2��o\nդt	���M�,l�D�ͣ=�K����=�+�ق�6���OU>���I�>\0���MR\n�г�OY'�����A�SOM=D�S�ϫ=��r�;s�sO�=��2��?����N[.D�3�ɣ?���O�=�\0\"LO[?u\0���7@T�4v+p+\$��9L�.��1,H�J̎G����P7��F��5>U���'A5�P?A\\���%?���Y@��M��C4LAh�d���<��P�'�TN�?��4%̢��\r�������oB�E����\nҁ�qA��L��L�a�PDT�	T.��B�\n��Я.��422�؈��)�\r��P�?UT1P�@D���5�4\0��Զ�L9��I�I}'�M��*3\$�`6ɫ'H�rv9��\nP�P�?l���P���<QUC��_QGB����悌P��4���J�2|����q����,}�菦>�0��\$f��`)�PY��(�+\0��0���� �ޕ��bWQ�0�p\0�\ne�\$��rP�s��\n�Q�Q�F��n0(�@#�J@�&ў3\0*��FZ9�\"�����#��>�	�(Q����n�	Fm�h�EF�\n`(�N?r;��\0��\\��R&>��`'\0�x	cꎮ(\n�@��F���&\0���n���\n�Ə��R�/���rD�#�đ(c�Q�G����\n>ďT���FRG�ќ�%	�ѥGxtjѮ�kT��JpAr�GJ�,-�Ү(ԁ#�!e+�H�H�*4�R�K04Ar��>�t�G��R�J}�'Q�G	�rQ�GE0�\0��H���\0�e�F�����6ҍJ�9���Km)�n��P�G��J8t���K�,�R� �.t�SH��T�\0�L�+�n�(�(��1Gu�|��G�\"���H5t����!@>S?M5\"4�R�N�4��H�#`��#Ԑ�I5c�#�I=%4��IIl����?6��RL%0Ԃ�IL�Q����3��S@�(\nT�ұN`0�k���M��\0�I�&�'�qI���T\rI�0N�R��52�r��E7  ��G�, �RoI���{Pe(5Ҋe5�����%�#�>�2`\"�UKe?h��eK\\���\0���	���X*7kTH(�#�ѻKM2�#��	���R\n�%*�-!T�Q�= �UT�?T���1O�\r�.T\\�% ,�UR]K!�Q%+��MQp\ni[\0�J�J�!SQT���^�}4�7���J�T�S5H���MS�O�9�KQ`\\��WS�+\0+%MPa�Q�M`����G�G���?�.���Q㨉@#p*=�'���Rt�Ӭ>���USP�PrR��\$�\0%��U�C��0?�\\�.UuL����(�u7�(�����\0�U�7d�N�If�ME\$5K�?쎃���?�0�j�J\rT@\"�H�x�5oUV�U����W)yS)M�]T���S�\$��p>�Fc������O�Z�U.?�S5mU8%<�(Q�F���uF��V\n�MT���K�_��U@=\\5q�L?\rbus��Y\r4�w�gY!1�#�eX�a@�U�>�d4�\0��\0�#��p	�>\0��=��� � h��?�	��?������L�.՜Ԩ��	@'�nX	5`\$J�4e�K@���V-n�ֱK�u�V�]Wի���D�U�Z���m�6���h�VX[��\rV����M-Dվ��Yui;�uU��)BU�[�\$�ģsTMG4kH�!]uWR}o��H�OoI\$�?Eq��H; �\nT�ԙG�:#�\0���t�TMnc�T�-D�VJ�u�ق�?����T�%vC��ʏeG2;y]hh�\$�W�:)CWs^wuu��V�`�M��^E\\��W�^�*ՙW�R�R��W�V�z�Nן_Jt�א>����׿Wg���V5w�G\0�S�}��F�ZU�V)Zuh���WK�	4��qHU��U7X�hUD��_�y6��F�\\��T�`M�V\n�`}�4�XS݃���e`H\n�G���p���GU&#�%�}r	����e��W\"?=1I�Ze�*֞饄�ܣ�T������,���Xd�t����	�����\0&��kT���bM��P��-T��N`�%�^�BU\0�!����\0�a�<�&��G��H�?�D�%�eM9�=��L��e��}Q6=֤�k@�R\ne(�AWWu�� WB]o��Y']�8��U��@є��VԢ��-L5y��b kH�Wh�\r�VO\0Vj?��UP�Oh�ӫQ�	�#��\rm�W�cb}�\$�Le?4jVk!�Q`'U%^h��R��EN\0Tn휂u\rT��_�*\0�-��\$]�76mٻY��4TmfU&8;p?5RU\"���F�*?�g-��x����4�X쏅IuSRf�i[RSb8	4�ٽg5�6���g�*���Y������b͠V��UE n���6t��}O5��l#�M+�����\"�i5+t�#yV��� �] �QԆ��QM��ZoFե�=Zl魥6'Z�i͇YZgQu����c�U��Q�/5�sZ� �T�0>�&c��U@���Q�!ZM��U��\0�.�\$Y�P8R�?}kiցNM��IT�D��K#�x�'T�RH��7��G卵�Tގ-������p\n�i��Ul�t�U�|�V��V�0�����l����\0���D�[+lݎc�[ ���π�c�M5|\0�l�:�ҤfG6�і\r1�=��m] ���\\�Tm�Qg�1��ہX���᣺>�fu���e����b���k�am �ݣkm�Q�:\0�>���##sn}�'���g�\0�ñ��Z�U���\"�X�uk��T�>�2UR�O �%�\\��b��\$\0�`%7�8[:�����mm�7�mH��\\H=��v�KL�\$�p�KFm\$�SH�Z=���W%c�0�>�c�t���o%���X�}L\0\"��S��%Z�o�7\0#H����w�\n�{�*��i�	n��h?]�����\rq�HT`�V��meU�ꀿK�i#��v�	 \"\0��Ű��#�PM�7�Ih��ԝ��\n?�g���T7PEAT�R�PrM5`S\n5x�����@69�h�E!�6��x�T�Z4����\r;Qr��(��-K�;���` �t��UK�/V���N@��S��� �PV�m@���n��v���bT����t>�E5�;jC�?#rLc�����T�[` �yT���\0�p-�W3��������8�-I��S+T���]\"����:�������:�=�N���)XOo�:�9\0��q6�ݯr��@!��� Waۑ]e#@/��?�2tT]wU�v%�mܒQ�'����o\\շ֑��H<�4�\\Yx�SaYU\$�0XqHŔ�Sb�� W)!� �>Yyb-�\0>UY�K�G\0�k�wדSEy-�n�ck-�	؟P@��\0���WY`�\rgt��UD����1=��M޳!u�<Ħ�C�ר\$t`d�9���́\0��z}�cJD�@b�;��\$.�{���i���TP#����\\ɑ���ȍxT������k��|&e�<<D,��B'|8W�B�zk�-�^�p!�P��f�%:�\r�\r.\\_1z�\r��\$�=�0��G|�B��Ţ��{z|Շ#='����ڭ�*Rź�}��.�_nF��7�C�}k�P�1��0��ZJ���/�_eJ� 7��� <�n?-!X],\n`+UQy]�6�Tr�8�UfӏNM��DR�O�0�&ӑm=��5����i6׍]�;@�=K����Tj]�5Y�����Y]�\rwh�ԑRP0����]u�2Ӏ#��_��iG�*?�	\n_�Q�n�̔}4�0�m �0�\0�t��*:� �,��7.�;��� ���UX��*\0004��9e�.���� J�	%\nM�X��>;�!�Bz@���MtHa>�1[��?\0�N\\�<,�+�ЖAv8�D	D�v\r�(���u�jƔ2(�܃n�Ij�H\$���/^�!s�@�a\nv�&d���/A��{l�N�Ơ`�'���T�n�,!<k�:݄�S@��]�c�`،hT�T`�^ T�?;{�p5x4Dx=XkA����\n�A�� M��������\$�S� �N�ìo&������� ȕ�:��k��N�[��	��n���ҙB����߮�/�H����z����:�,t0+��2;�����a)��vPL�z)	{��#�ڂ��6������3b/�}��;)��� *��Qb,�p�b&5�p��P�ΕY���1��\rX\r!%a����<�O\$h����\0006/o�i{�)����[���*��'�4G��p�a!Vh@-��b�H?� ���Jx����Jc-��>*���f��b�&���A_��\"�%��-��=�W{�J�Yb�~%��;���%X/ ���\$�Qb��G8����f,����\rx�c(\ra��:�v1`>c��&a�����a%b@�qL�HkW����t\n���	����7�ɤ�+V|���?���N��cQ`� cg�h 6����F0�86xߝ��A]�9\0�88��J����Ճc���η�1@ 0���ab��7x�\$?8�2�NS�\$�J'D�\\�5��A%�1�v3��O�3�!7N��rh�#�;7�����{��&%��Aw\$�:���;��������pK8�c��5�ܘL���n,Ȕ�Ȁ��#����	�\0��@:�R�NEB�3˯���.h�S�=�.3�\"��ELs�cR�v)��ǭ�\$�����i�O��FImљn��!���Jb�\r�T��d�|`O����n�;(h�5���w�d�;�kN�ʪ��73�T-��78�\n�UY7D���s�7@�\n�5.���	Tsf~�k�n��)	�mA7B��N��d�ͦ�>@E��&�P@� �ツb�ҝ�:��Ҝ�AE\0�<\"�Q�k�������7X����:\0��at�l��;\r�q\0���)��|\\S;(���Y��s��_^�c��&(�|Yj^��~Z�DƸ�K���+�\0܄��;�=�ї +A�(�6\\i�Bz2mXB_��}�6߉.}���_���ӛe� [�B2e�|�(��fz�Z�����c��f}�ن\0�P@2Ad��by�f��bY�Nm��A�2×��d93f\rvd����e9���dY�f�na���c��e���/��fٓf9��f�e�~4?��_{����f�-�l�~7ں�}�bY��vM���LL������v����eш\n9E����u�U�Y\\���	�#�\$��n�g�B�<� �~����w�\r�uC�����W-d|��Ǭ��y���Tz�	1�,k�9�Q�VpRO��,hCB���~�nY˸Q��p�j��Y#��NX��Wum��Z�(��g3V��L�^oy�gq�!�gz!]�p.:�q�)	��gtJa|��u�܃�a6	�/燃���4d\$�6\n����2#1.g���s�ž���\\�&u����+�,g������wy�Y�K1�� 0�9��:מۭf6�˞�xY�9��Qb�\$��~tX'���6z���.�m�`�1�9s�@4�̓hD��y2�☾vqζ�VD.�\0�6��<���\"\0�綊k���>P9�1�vzϏ�\r����N՟�FY���V}\$:���6��`��::';�O�Od\$yF~��8���\"�턚.�5y�6O�����,Q�!=�t%��e���\0�\0yf6��}���R\n�A�`�P�r,�C\0���k@��S�zB�QCX!�I\0�.v�N����\$��@�Tc�F��Hi�Z�2֑K�\n������)]��i>�77�߀MbŸ��?����ŽC;�C���ޓc��I��4������#�0�hT�M��D=zM��X����CY�i�@`�,����y�Cݑ�i��c;�zV%������,M������%~�:ENY����.��NY�N����/�N��7h�<�A j�\\\n�aW-x`ډ��d���i~KP0�M��*i��\$�Fz|�QAV�I�=�j!�,:tB0�-�z����N���V?@K��AzxDb�V��K\0��8KD�����^��;��Gg�je�Ý�F|��oC9���u��n��(��\0���*4�A1�����j�\n��B�f�=n����Q���zxb܂D47i,!v�JP�!�XΎ��xP�{�Zv��U�Ӏj�B^!dj�\r��������K:4��z��4��bp�l����C�Cܢy����Ao\$��)6�z��Q��?A\r`���\\zEיִ\r�݃s���:Eh�e�>�Ќn�f�nڥ;����B��管��j�n~����w�Tho��M�[(�KKɮ���t!���ˤTx�4���o��y�Ɲ�EKR�6:KG��#�.\$t&��7c��-���@�]�Q�Q:ʊ߾�Ҩi-�,lQné��qO�+G�H�:�f�:�ꓯ�ID��_��Bo��M��Aj9���\n�W�3���F��~�/���f9	�0>����G��d����D��\\�A��]bK�\"\r��F~���[��c�\r�˸BOs�1�d!�y/Ѕ��n���\r�0�7�\r���	�%����h\n�2�l����Jב��ց8\"� h�Bh��j�J7�-b*�K�����!�FCV4��SK�ًF-����~�2�;�F�KÛ4������n�Z��1�vR9��\"L��:.�ν�dQh����k�a�n�k#9N�9��Ʋd��U��\0N��6�O��V��5+�iǢd��]{ج�����c	��g�AM^=����U�{vl�\$�P��5��/�(�\r):`F_:Ɨ��=�	�!y�V��9�ϟE�Q��5�>���:5�<c����Ɠ���z���	�M1�[�n��dn/����F�9�F�#`��v�X�<B�Fj�dN`Q�5�󞾴�K��5o���	�h;�������#���BZ�>����o@ck*��@����֓���D\\�S��)��pۭ���sC���6��pU[��G4�����?�.�e\na	��>W@��{�.��£��훭̵�\\9ژ>���CA�����ץ�`�0���d�]�f��M�1���I7�[����\n�]��,�q�VJ���ۑ?�tz��]����um*�p�+틽���.���\0H��W���;+���Bzo���x;^nE�tK��hq�����ꟓ�E!�+n=��T��瓗��xkj�6�{������#�h��#�[�o}��q���P�DղÝ��������o�1��xc��8D�\0�񲆜�J	������v=�W�Fzz�mk���hOޓ5j\$��X��}�<A>�n�{~h]��\"�\r��GD��x�Q�)=:�5����G:�P��D8�p	�sH2pzt�������\\ڀ����k�|)�Yt	���P�E\\D�0����¾�|p�1�Ɛs=&��`�h���IO��\n�,�M틂>Ae\\}���\\>�գ�G��7�N��l\\��L4!�5c,�T������!p}Ĭ��<�Q�H艞�89����!=�F�1j��ː�A�@��o�6�ۏ�U���9�������Ĺ���q���\nM��<_�}����3q��\0���\$n��o�>\$�z/	��+��q}����1�o\0�F8�?��P�����r�������;<�NG���E�c��\$*��qU����}��s�F�����8��b�C6��\rk��G�m� 4K<~4H!��j��m8Nkr	f.U����z��h�#�S�rU(	Zs���n�z!�/%\0����/&�}����ں6rxW`5�cG���O��b�W\$�b�M]��\$�?��z���\rޭ\"q�����J��Θn�ـ�A���&}���#[%�ɸ-�'gt\$ƕ�j��L�wN�re�\0\$8Z�#��:;�s\0M��\\������s\n�D�M�eA�������f��4I�BԾ��p`��@%Z�\0004�0�}�O.�\"���L4����]\"�'��H���f�י1��n�ыRet�Fޮ�.MY6���ȏ�lc>h�5�ӂ}<�Ɍ���(��7FL�r��m2(�%����b7��C\0[͸�M�s��#V�6�Χ5M	&v�79��7�����@�!�\0�|�N6\$ݔ��v���n�!�T�Ƞ���<��WD�@M؀_�(;���'h���L�d���+��r��Q�ˤHi�ʱ3,�)t]+��p=<�tq1o3	F���e�����}�%\0001R�,��S�O�_Iͥҍ)lt�8�LI�t�:&��\0�Ҥ�!?�_�^}0d�\0i\r'��g�A��)4�?���/Lt���θI�E�|���4W�?mi7���g�	Уu��/��C1�I��yI?C��{SZM�e�m�K��P \0��~�\0��A5�#�.\$s��Y)���|�ҊM9yd]ϫA =9	�h�^���rE@SO�#>0L�HK��HE�%t��.�m��O���f�ѸR{�~��F�%�8�sK�B���Y�w�]/#�Q����cc�)HT_GX\\�p�r>�Օ���F���lX�c�V�nu�����@u�d85��lB� �-hE����TV\0�h�=`-Tuv�rTg^5��Q��=b4l��ZMU�Yx�u��'vC^M�c�ٓUES��U1#�d�&v�en@�R�n%�����?d�_vOeŗW��iT�wf[)�?a=��_/iVM�X��]��Vod���eڏf���EI'j�,���mp��Rcj͍�8�?^����V�g5�Z�c�+}��sk�\n�W��ueV�Z�۽�v�����TlU�^UU����[�S=÷kٝ\\ݛ�;W7guxҿU�8�6����v�v��(�v�U��Os��է۽ow_U�?�i�Y׳\\utyQ���u��VM�^]��ck�n���W5e��YG^�%��]P�_�[cW�s�|V�o=���X�wu��Y�\$ݕX�Yq:w��]f�����d=��CU�d=�v���=�Va�]�H����`\n]�w�?wi���QlOj����z��g���u��I����{Y�x4�ViH���FVl���+�{F�Õ���>����\\�sErVrܟ��wY�}\\u���u��Ů�y��d<�c��p��t�q]9]��!j=Uc;yb��GS�RE�הT��?s�'ׇQ̅T�wF�}=��Um����w��-6����S�C.a��g&x{����-;�߁�i^1��|\0�u	Z^(I7�������c�;V���U%h͜��Y�g\r��t\0Qh��v9�cP����H�y������?8axD��g�-�!�3Y�g�\$��Y�ݯj7��P>���ee�Xb���s��h�a���Y�D/f��n����n�=�	^μ�ﳞ:���V��[�L���N�a����x+������w�9/x�>�+���a\$��L;(���SF�t����o�;��ly��xs�\"�	E�����ߍ�-��@׿�5��>��~=�!�\0�1B�US�b���\0O�8L}��ѫ��4q�8L:��.�6��3�.�Yr�oɀ��Yz[���_+�Q�p��?���62�/x�b�2ځ����~-0+���r~�mC�X!��b���\0���A8�9��&Rh�	H?ɖ���^��W��d���E梾�bϟ���z?���\\<j.� Jc;��\$�)�;N[�����yj	_��H�I���:�B*���ļ��3�:S�������.lf�P�Qö�hF[����6Ý@p\r{����ӝ�e����;|���V�s��FN��P+��k��o�g��̝6�[���>����֘�{l�+7�{��+�f����\n���cl=y����py;��B��\n�������ìm��ǒ��y��%�h�@�L4``�{�cnF��{��k��z���^�������[��O�U|\0�����.�d��w�y(�g�nJ��d�ϼ�AOQ�F_:�b�PP�h����a����,�	1������:']P���g�}�6��6XЗ�Ř/P��/-�I���>�M��x1�b޷� �U�#`��d3����z�Ŕ?�6�C�tx���ǻ��:L���׻�#,��?0|���S�mw��T��i���6����8���/˰�%��*h���wç���,��@�`���2���M}���E����� �%�o�a)�_���Q�NM�׿�\"�Yά�)�������P�w�RMƇ�?ա.B\r�5�TbX��\$X/t���!)�	)�I7�Ľ[1}�n��`�����o��`��~�AΪbt�oʒ�wڟh���n�/{Iԟ��}<v� �b���(>8����	�\r3���\"���(\rp��\r7ޟ{l���:������o�^.}��~ݯ����/�.m�7�\0s?T~?����><�|��o�M�N�:Ơ�yJq�\0��o�\r�,<�}2	PJ�L~?;W�-�i�_ݼ\\}���:\"�PA��;5�������\r�� @���+�8�~��fDߤr\r��ٟ���,t_\"����ƿY���?����'ߣ��������}�cٯ4�\"�l]ef��Ȑy�����[�I�L��N���a2�����!f�P����S��#	4��_���J��?�߽��Ġ���[��~����EN箒4*ÂU�\0%���8ʇ�Q�`��S�����H??�h\\��@�P2 J[xL�G�?�����\0�ȁ�>ü��/�R�\"3��HB{����<�.~܄l}}�<�|����_�^��w�/_J�:�ަ�&����w����h����k�lN[�T��@(�z�~M�0�#�h+ܓ6GETh�ck�ѝ tS2�(�q�[ŠZ��_�>��Y\n�TTE\r\";(�X�s�������-��@�D k�S�J{(�p��� �a���^\0��bZf{���#di�����D�L<��2�l�Ĉ_��v��P擯�	�\0%�S���0��*D��!ֽgЅ;��v4dP'1���q�ZXb.Y�f���մ[<�c��S����['�+����Ђ|^�p����� �V�b���n�1(p��\n\0�2�*ge G}� �-/;��1^��\n��tqz��P��[� �	����p\"%�Z\0d���\"�9�+��.FO�L1�o}�jO����P�hCDE\\d_j��9L�c&��9��xV�7�5��|te�16�P5B��\0�}*�2J�n�=f���BQ�'�rR	}���RɎB�8>�K�ưMC>Qɪ`P3inկ�wP���a��	#�c�3��Y�H���E�h1��_���k0\n��pe�Gǟ�1eh�=\n29t*���\0h(���!sQV��\0�{j&���+@D��[ַ0ul�a�#��M;\r�tXǁ��j��hQε4�CM�3S�M_w6�;A0n{l֠�Xx��z	�zf�HB�rl	K!dO�# n~��ps]�.1��jh�0�!!r�0���p�p�d�9iD�%r�������f��\0�P4	3���g��7���>J�\r�L�M����2k���+�8*��Z��h����Fߌ�ґ1Z����hdFٌ.�A�й. mNY\0փ�K��X��Ax�6Q|��h8f��c�/��%�}��帠q�c�nWA`���`PB�L����惁ɂj`+����\\f����;������g�ݘ,<�C���;>g���S��:��8�\n,�۳�XA���	c}H?ò��S=*��8@���7R�(���č�^ˁ�7�gj��߀W�8�z�8�Y��|Cܰ�A��FD�}�#PxE\n#8�P��5�n�M��FX�� ���6��r�ݟ�O�z�B_`L�Ԑ���bE��NM�Zȁ�������\nP>Am���7�PG��Gx�9��1���\09B^kt��97�P<7�V�q���JN)_u-�d�a���G`�<�o�ĳ\$'�JM�����M�	�yp�܍B4��i��(��@�8Uhb~�<(�\"�Y��w4�X�7fzPA \"�ā�A�b��T�Tm�T!����9�.�PB�L��h.�U�M�_ĕ#Vp���B�(�����[e^	zG-� �9g�tE�d�?�C� 2����V�ɈSO�'<Z�u��(�ҍ{��e�=��C������\0����v�p�O&��Ki���� Cಷ4n�|�,/�'MP�U��~�lxv����(֛�(NQP۰d��\\�TsΑ�ڨȢ���ˀ@\0HN�\$x��No_�)wYx�q�<8��\\�9�sN͖���'�HC\"����b !��RIN�� \"KG8��	�\$�s��K�D�F�!�������&���i �@�b7�;h�C��{��H��Q(�=�5q�0�TO��K��4+{pO��%\n��	m>JW�l�CR��r��\$5)�V�Lp��� JE\r��ؐԤ�B�8�i\\��6���nb���&�\r�2<8�����m�ۇ%\$ࣧ�_f�!��_7�\r�+�63��������pǴ:V��#�d'��d�M�t9�j��J#CYr䔾L:�u��~�=�:t!��)A]i��f�%���Up)V�.�J9nyGn�n�{�ȇ�����W�\n�U�;�w���^���G*��\n��\$ޣ�Lr�g�i�xdt�e:��b�ݎ�>\0��K�u%�S��*�x���ݫ�7^� ^%)�V\\��Lb��r�T��6T\$��M\n��D�<�,cS죉L�A?Ka�DT2�� �@�!���.U\$�}#ۮ���UT.6v��j�巎��C��vⵍp�֕WK[	��\\������'p.ߖ�;�Zb��iR����KV�-�_��i���n���Q����#�}�nU|��Z���frG������]��v˶Հ����U[�Yoj��8��V�*�w\"��y*�E�+YH��Z��9R����e�� p#��aZ8}Ek���+�xh�Mx1��L'P	�:v��_��e��Aփ�u=Qx�@h�+�ܝ�\\���I\"�\$�n��C&\0��t��4@b p[��\"��K��D�V��MM���K����Y�^A�?d)�X�!lI�D�k~����?���K�g7�\n�F� �(��,�,��l��9���'�Q8��DoX ���j`մ����h���r���y��M�n\0�<���ǵsF�6�;Bug������s׶�\0yl|�2���\r]�s��j�2B+у��=���p �DO~���2�++���!^�H{���_���li\\ˆ��`\n�K�&�/���j 9�����ݢ�cd���D'��o@���cD�/?P�\n.Y����\r�%�\0����(�LED�G������әҹ|�x�kA�!Ic�4Aeo��q� '�9X���Xx�CsW���ґ\"{�Ӏ\rY!����u��)��\"5fFN����E���P������H���H��l	&���Ӭ\"�m�Q�tZ�ʑW�+Ų���\$ ���.Ǌ-`a	��F8�o��X�#���ឺ�&R��>��> ��}�\\���X�9v~��.�����o�/#�x����S�,����4���c>��pC4�����hg��\rE�1@O|4(e�\\���6*��	��d�!�ҋ�x�Mp`\0007�D��4)cd��P��ZV\n�ɸ)���@\0001\0n���a��\0�4\0g��a�\0����5���P@\r�F\0l\0��XƱ���#�w��xƥ���,��\0��dƱ�@FH��\0�1dd(���8��Zx����@F.:�1Xh�ш��6\0a�2�a�@\rӂ�`\0g�2\\a���c(F7�w���ep�c�5���3Lb��Q���7\0sV2\\b`1�cF8\0d\0�2<e����F\0aB4\$b`эM [\0l\0�3�f8����Z�:��hXȱ��OF���4��ɑ��F�\0ir5�e��Q�@\0001\0m�0�i��q��`�+���g���@\0005�20�k��Q��PF;\0o�4dk \0\rcbFna��3|kH�Q�ciF0�{�1�e��#(Fj��|�\"�q��Fe�pdj7�d��q��GF��7�nh�Q��9���B2\\k�1�#OF���M>3Lj����5���\0�5�g��q��=�݌T\0�2�g�1ǣ(FP�!�5Hh�ѯ#^��<\0�1\$p�@\r@�Fb�I�8�c���cF�����Hۑ��C�G���1H�Ѻ�\r��\0i.2;��Q�clƂ�I^9Td�� \r�FFe���2\$b��q��7�[��f8\\l�ߑ��qG����e��񇣧����3,exő��GA���oX� \rc�F��P�a�Ϡ#�Ƅ���5<q Q���F����6�l��ѡc�H���<,h`��ck�2�/��g������a����d�ȱ�c��;�q�3�l������F8�j44{��q�c8�O���<�c�����-Ƈ�~8�s�ь��F1��F�8lf���iǌ9��2lx�q�c��]\0g8�a����ʀ5���3�l��Q��G\$A�?m��q����L�NZz6�u���c=�܍G68�sı���G���@D~0Q�XfGs��=|g��q�\$G}�oz?d��C��F��SF6�o���c��<�9*9�hh���vGG�]�4�e���\0001G�\0c�3�Yэ�H.�!9����q�IH=�U�;�hb�Qˣ�G�W�A�q(����\\�� �B,s(���\$Ɓ����l��qҤ�Y�]�2�x��/�%��д�p���a���M��&7�m��1��G��NB�t����&�֏��4<e1�#��O���8���Q�OF��CR9�{�1�dF~��25����c��,�E�=Ll��Q���+�E\"�2�|�ȱ�#��A�G��I�a��HČ��D�dXб�c��ƍc=4���Q�cL�3�= �9Tj���#*�C��\"�F�fx�ѡ#3G#��\"?���͑��VG��#28�}X��c�B���;fy��#1GZ�e�2\$�����^��{�9����c(G���C�oH�Dc&�7�3R=b9�ң�Hy���=�x��#v�@�O R:�|�Ѳ#&�\$�\"�3܅���L#�F���#�3L��ñ��,G�/�3e�Nc=ȭ�I v4,q(�1��%HБ�*F<|��1�c�IQ���?�l��Q��.I��\$3<��\ncvGu��\"*G��Y�������<Ԍ(ױ�dG����J�(���YFS���A\$��1�d��S�5#�6ܒH���(Ix�\"Z8�q���#\$ǯ�; Z6Lt��ģ�GJ\0e\$�34n��1���I��\"�G�Hݑ�#^�q�Y�3|bY3��#nH-<�>�i��1�#��ג��F�Y\0Q��FFD��Md����c?H��LJB�bI�T�3��I�@T|(�U�5��\0bLJBs	4��>ǌ�m:�b@r ��HA�W1̇��pc��ˑu'�BTa�.��#3Gz�W�4��Ĳ��G���#>>�u��4���&�?\\����dF����K���#�c�I2�K�J}�r`��Ɉ�#�=�bi?q�#5�m��(^:k�#R6dVI���'3<yҒ_�.G4��&�:x��2G\$�G�{r:�p���Z��Hm��v?�c9Cq�c\"H���!v3�w�q�\$�H���(�KL�Y	�3#�4���?�1)\$���ǣ��'�7k�*d\nH��Wr2�X�����#�E�x�23e!�k�(b98�8���<��v44u�뒓�A���*6O����I%G���H�	<���GI���'RKl�h���c��W�)<d�	?�i�Rǌ��%�L�1)Kq��Z�b�?fGtz9R�c��Г�F=�}�RK�nI�I!F?<��Gq��j�~��%\"3Č(�;�\$Jō�>0�9*�3��I؍e\"&St�(Ų#��ܓM!6Bԡ�01УYH���VAtp�Z���]ʤ�w&\"G���2�jG��#�5�k�Nҥd�ƹ� X	,��`Rd�GC�3\"�;�z�O2�#b�\r�'�>�m����kI�_'�1<9��1xc��\\��t�\"�%j�V,�Σb�C���@')�\n�g��V���݇�\$ڻQJ�͉hk\rU�*�`M-�<�EdBc���MUU-<B��i���Y�(w���ؚ�娋Ge��o���J�ŕ����^��B���Q�KZ��\"[���b��^>(�Y`��LM?%�?% -f����T��Z<��[��p Ľ�]v�-�J��mr�ѫ�v�-an��` �,�p����qs��:��%���P���א��Wb\0���h��G�c��%�˷%|���z��0Gސ�ya�)4�p#����\n�T�O0}�2��/p?������e�;�W�&0�ĶE^�nT�3�z��c[��v��%�<���]Q4A�}��ԁ��V���T�}�R<.\$�4�쿷����Fܗ#0N�������Y�\ri��\0kGZI�k\$�k��Nm�s\n���5�!KB%�K``\0����'��\n}��D��f����\0֢<,���-�@��ǍiK�_�,�f�e�/�����Z�u�`���S��0�jX5@�W�D���Qgp��\nubZ��x=-\"a:�\0J��\$��x�1m`�� \\��@!-Z��HJ��)Ց�	4M\n�e���k��e�5zb��|@�P0�9ZF��f\0��\n��/�=˞����dR�����C�K��-at��l�J-iT\0GD��U��Ƭ�\n�]Gjŕ�\n;fGKW�!2�eX}��j�%�L��_2�\$����+c&U+�X���d\nƕ�\n�_��\$N�]\$��0��%�z��-�^2��s�\0VIK�Y\$�D?Iv��?Lt,���ε�R��U�mJ�f\\(�P#�֖L�\$c�w�j��g<~bPi>Գ\$s �<<�fg�%~�p�Z���f��@kKʁ�,%Q�0d,M���T�\0(^j�vh�ϐ*ȘVJ�WY�\"hB&�k���)�v������.�]��YC-�g��U\\�C�\$��4�]d�Yu�%�W�+w&��>��[����M7v�-sR�)��K�\$0��4��Z�ɇ˴\"��S�8�!P\n@!��\0���tD�We��#)Kv��e[��E����<�C��1�j����\n5�MW�O.�k9#�����)YR.fk4��+D�f/��3Fl��+*���lR�6%�Z�E23	��i� ��l�Їr�f��͙%�-a��y��Z��MqQ��j^���seՍ�Κ�-Ze���k���x	5��s��{�c温v`1�^�Թ�J�WL��x��2^�%�A��̳RZ��]����_UĪ�^V��MY�����_���k�Y�+�UUMj�m7)ZB��uZ�D�m�6��:��j�xf��`�7�d���`\n�M,�H��Y��9�J���[�ȯfm�ܥ��r��M}����X����F�{��W��	L�&	���f�;Η�ﰍ�q1�L���8q!T�U&O�����2Zo���d ����\\5qb�9�'�����ef���JV�N'w�a��)Ug�V��M^����r3�٭�f��y��.�?t�����Ms��Jk���k�&�-���5�\$�\0\n�f����8�m,�ɱ�&�;�~w���l��9��Q'3͞%6mO���������E:	Xz���'3�A�]:![��I�3���\$Xֲ����Ն�nW�ͻT�:A`��a���MM^V,��s<܉ԫCf�<K��2�]t�)�Ө&ꉌ���Qed�wa3��UN���:�o�	���� ��ҪRo4���s�'.;��^��s:��fӴf��m��:Rvt���s��c\0Y�.�s:�8{���PN���8.v��Y���'��V�0�u&�˯g<2�x�q�����z;Y�h6i�����Nf�NM�y1wº�b3\\��4RQ8�r4�ߪ�*KG����<�5��S�c;Y��5br\\���˖g(OU)9Nx�����À*�윳;-e�wjS��0NI��4��:���S�g9���<	T��I�K�>O)S�Bl��y��ѝ�Nx���nt3\09�\n�O<~v{6�y2����՝�M��=UT�i��<'�Y�|�Va<�Y������I=�X��b��'�Ϊ��7-e���S��\0N�\0�=�u��uT�W�����!Qt�i�ve��R��=�q,���s�g�+\"��<�o<�Y��yg�Nʚ�9nt����s��Nϖ��&t��y���%�Cʈ)=M��Wd����+;�[i��gy;����&p��M,\nC���G����D��Ӭ�U%?(�:�ũ��(\0_V��H?x�	3�'G�ќS<I���}*G��K�T³6Y���	��g�KF�J:z�C�ʤ�+O�~��O��9r�\0&�&y\r�Z��/*�f�K1<���0�+��W,˖`H�- ��Y�j_g��H�,�*��dr��-����B[�ө�M��_#p��<�Eɓ�ԥ�\nT�@�%�g*�@)�·�.]��j�hP\$\$��S����%�͟Y�8����h ��i,ʂz�%'�����I\0��(r�gF�W��]aYiE\0�)q֣�Q����wDD�|3I)�[,�ft�P�S�(7;/H��uY��ݢ��9�l��:3�ԯ�T����*��P�_�A�b�:���!O֡BQj�����Zg�Nc#�Bl��ZS�V�О�>G�ʦ��'gP��IB�qtCJS��W)ZҐq!Tղ8�Yg5�o���^�#uA*��eNk�Un����-�,Ь��C.�E04(R�=���y ��q�4����L��O�\r���m(��W�?YRB��}�8�\"�k��C*�`���hnP�]�C*lb�9kR�*�'C�b����?�Q\0X�����gt%�vLX������TDf,H�ȵ�r��*�?h_�!�1Dvl��9Ћ(��D�)C�zr�)�tI���X��>��'���.S��J6a�&k29�u�د�S��5�b&,�#YPa*�i4RrP�YE5e��KJV<-QT�EM�H��Zh�)|`\$!kH}J*�d(0�;X,���,�E�Q�����}A>�奡�ԃLCS5?��:��/.�h�,^Tn��i��Z/�y,,��1Ew���a!���Q�\0�Ef�9��I4��1]��˪S��ћ��Dq���碼:�,��\$�Vm�d�&6p��jf����[Q@iKZ�e����ͼ^BB�\n ֒���wG߼�@�Tf����<�`-E�*\\X*\0\$�Gv��* �t���]��Hp�z0�h?�{Vv�u����q-fH�N]�r��ȴL*���#\\�BJ��U&4	��O�]������@�J�0\rTu=b�ʣu�*�N����<���\"EF�V\\ϛ�\n@�E�/W�R��\rh�I\"� �qb?�T(�YZB�Ջ2Ω�N������B;�{�kL�W�Je��JFJ�Ծ)���H�JҺ�G���h��Qr%ZS/�+�1����bʛ\n64wh��ݢ\n�_�%��='��'vI�~r���S�i/.��ĩab��E��@ ��-��Y�2��?Q螰��RZ�U�J��R^:�3`�K�U����ѐT�H���jQ?��f\0���RXY�jl'Y�~�,�Y}�Z\n�(�R��8��Y�)��Td�\0�Q��s�@�H\n��\"-DT�J�J��JU4|?O�\\]IyS�����U�Ƣe;���ɩ\nh��-�[��ʖ(�!��&�/'�6JV�j��Vk4gخHv��Q�#I��(Ι�:�}�%u1Dy	��n�������ԙ�~Ҝ�7Jf�*����1>�G\\\r�!�t��R��K�Qe/�4�YXRo\0P�p(*��)�\"�#����\$S��i�����)ra�\\���/(O�\$jF3f挀��(t\0�`��d�U	>h��e�c����H�\rp�`gP��c�[=�L�f������\0002\0/\0��5\0b�!�`�&\0]*px)�g�CZu�d-�<�\$���k%A��z��d����Ҿ�Y\0֛5�k��\0006��Қ�c@�@\r��x����7Zn4�@�z��M�5�\no��i�STtF5\"�U8@Ɣ�d�]��M��]5�pg�)��b�Mڜ\\�r`����n�/M7Js��)�Sy�GN*��7�t��\$ρ\$�6ʜ�ju��i�ӍsN&�d�Zvt�#PF��vҝ�;jq���G��}N���<jxT�)�SƦ�Nޞ%<�\$�\r)߀_��M��E;\nx���S�����=�ot��S�!O�U>jx��i��G�O2��tzyt�)�Ӓ��Mf:==J~��#��ا�NR:==�xt��S��PP�@ju���F~�eP0�@�~t�i�Ff��O���Z�t��SϨM?���*S��?NB��@*~	)�Sը-O��=:����jӔ�oN\n���z}��*Gq�]N�̕J��j!�*�yMf0�Az���*\$���Q&��B������-�wPR���ډ��\$��Q��M�ImB*�ui�T�[Q��5�%��j1�j�Q�BEZ}�*�#��P���E�5*!Tt�MQJ��k�Q��A�0�\0�C�CJ���T8��Q�K�G(˵\$�=Sސ#RJ��E���*ITl� \"�5I��0j+�ޑ�Qn�D�����`�b�#��G�ƒ0j4T��[PBF\rJ�U(�8Ը��R>��kڔ�+\$���-%'~�5Gژ/*\"T��S:��J*��.�[F���&~��L:r`�%T���R���Mʕu6��Ӗ��Sr��=ꛕ-i��ܩ�\0�3�A�m�<c3��K�QR�uOJ�5=*4T���Sҥ�OJ�I��T���Sƣ�Pz��)�U��T\"�uPDő��{%��[TV�-HJ�5B���S§�M��uE�H�#�RN�5Q8��F*�U�AT���R��Hj�!�;T���0\r1�cCF�9�S�2��Z�Q�cƿ�WS�5R��ruj��sU4\rܭJ�\0�}US��UZ3}U���E�*�X�?S�<\rUh�5X�	�`�S�:UV*�5E�[�b��T2MV*��=�?UV�eUګ=Qhĵ]����	Uګ}Q8��]�����UZ:�X���=�OՈ�]S�1�X��ua���K�oV\"�-PX��U�6U��TZ2�Yʱ�=�k՜�'Vj��T��ug*����UZ0�[\n��Acհ�GTZ1%[\n��k���Q�eV­mT��UU�E�īgTN4=\\J��Ac��īwW\n��UH�q*��UL	%Et�r�*�T��R��	%\\�Œ�*��ϫ�֮�St�@I*ꁻC\rR:�R_�0�v������W��E]�����TͪW֦}_��UF�U�9W��lb��U�j��%�W��E`���J��U�'Wb6ma��Յ���D�1X&N�_��}d���a�X���^��5�����YX��Ub��Xk\n�)��X֚�]��Վ��U���X��cjõ\\kGO��Uֱ�sJ��}��GX��mbڼ_�\$�l\0F�|��Euv��B��2��k\n˕��/����%���c�u�*��8�Y��e^걵��(Vr��WZ4�]��5��5Vx�AVn��Y��5�c�V)����ZZ�5�cV�WZ�q��mkGۭ'Yޯtc��u�j��8��Zz�%4���kQ֘��Z2��i��4�*�ɟ�	&~�2c93��CQFۭ[2��]3��@�ɟ�WP.��kڡ5����Z�[W�j*���d�V���YK�k����*kV̬W�1eھ��k��LYv����˵�#.�ΌgZ�3]n�ׁ����֖T b��<�jKl>+a�+<�������]��7\0�>��<���s�+�����=LR�5S���+�]��\rH2�Ȅ���������[:�%cs��*O�v��iL<�5V@ב�w��C��2�5�S�'���v��|�i�\0����^�n{��j�f�V��J��Hd�uBs��;W��8��Y�t�'���y6x�B�Dʤ'WN��|�ً.���P-�q@	���fsr��͟��8��*����í�`1C�=o��5��Y�%\r5idE���&ѻ8�B���\$�5m������:k�+iƋI&�W\r�,Jڸ�q%<��k���\"�!�q��U�f�� ��^\n�rY�U�V^��#�6�Uz9�S�+�)��W7*eD�)�f��2v�\\�f:����)��O����n3� \n�k���\$�;�p�i��Ք��ˮ�8�+����*���\n�ث�}��9�Q��S��zh\"���Uا��B�o;r��i����pP�w�]�vj�U���k�L�ҥ�������ߧ;;�eL�pz��U�����&�]ed5��W�0���=������U�F��@}W�ә��\$;��^�uZ�:�t����j������݇���&��(��\\�?�{��\rk�NН�_p��S�UX�͝�_>�=�'�ik�P�w�;��Ey:j�a񬮉Y0A�E\n浾r�����h��5�3h�W�w�\ri:�i�+��R@�F�Vx|�W�1^Njx\\I�f��Gpa�,�T��N|z��cs0�EL�xm5*�5��ά@Dg(�����Q���������\0\"H�X�:�Ɍ����4�\"1u(.���`Ӂ屑y,0��R�`��5�A�-	�~v	�+�X�qM��s��;�[0�Bf��((&h�q�_�Fڃ�8����~6Ob��8'�	���X�dfu`��42\nؿ|.Ku�P�H3:^/�G|<�Y�(��<�\n�b�>�	�Z;'z�c�\r�3b2\r�N��L���2�xU:\r6X��-�b\0�t��T�%P������X�SX!�k���ćQ�u�a�?�v�g�.���S�S:l����d�t��H�\0=/�`_3��m�F���%�l�bуB0 ڦ�k��5�ň�(�PO?�?Ί�<��\nЋS�=5j�\n��{*\0��3��b!eT��F���3����<bʃ*�	���5F�c�	�N�	H��=�ga6�e�\r�� �6��;\0�&Ě��a��Qe�4�Ђ��h��YadL�	\n�դl�*�G_��ׅ�	y��H�1�e.X�j��tY�2Mw�4�6�J�]��MȐ�Ͻș\n,��jxF�G@��*g\0_���� �XY��	f\r�m�9y�à������߇�>��o��(jG;8\"yA�3׃f�9��	L�����mgQ�[{�ds(Y�~��~@�@:	���Y�������6Fa\$l�)Ob�=��<Vx�Yuvx����č5�Y���������Yjuh��\r�/���c^�x� \r���A,m�*��yw�\0�٫��hu��7U̫HA{���#S��{>L�h�]��Ђ�&~�f�ÞѸx��m�]�.���B���&e�m��e�lH��+6ZĿ�(�\0ǅ�ٝ,:YPZak���Q�.������~��	[��-a_�:�ɜbPcA����/\rh���e���h��'���ui�Ӭ\0�=�m�\0��i���\0JPh-6�`rfi��=���mC�R\"�'^õ�Tq�lS����U�ݐ���^��Q3��T�.A�=&gv�lM�3@�-T�+P��� ƏQA.!\0�j�D[\"�W�,Z'�QRݫ�U&v�YX�[i0\"՗{Y �l��{�\"��{P�\"a�W�Z�d�\0B�PV.��mm=0�kv\r5�5�Z���ൾh2�4����lOZܵ�Oɖ��.,���:��F�Z('��`-N��B�څ�խ6��,�§a����a�l����<6�ܽ\0000�����@�lM����4����Zc�R�Օ��aloڝ|&��G�I�b3��\n��\r0�(��5[/�fH�\rŮZ`���L�^�d\$��LΐU(5-�[;��(��8*��v̓��~|�a6����4�d�l����\n���/�L��y�*>�2���?�������d!|�'O�(k��P6!i��t�x\"��I��\0A�� ��,�����7�b��z����J2E��C�\nB5�@!��F��h���+-�:�\0NMC�s��H�ہ=nA��;s�o�*���:q��B��\0�ۨN�n��n�V܄��4}���k6��Zʗ�_�tv�����3>w�9\n��L(�Yy-B{�����G�\$6ye̋t�d]�2�");}else{header("Content-Type: image/gif");switch($_GET["file"]){case"plus.gif":echo"GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0!�����M��*)�o��) q��e���#��L�\0;";break;case"cross.gif":echo"GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0#�����#\na�Fo~y�.�_wa��1�J�G�L�6]\0\0;";break;case"up.gif":echo"GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0 �����MQN\n�}��a8�y�aŶ�\0��\0;";break;case"down.gif":echo"GIF89a\0\0�\0001���\0\0����\0\0\0!�\0\0\0,\0\0\0\0\0\0 �����M��*)�[W�\\��L&ٜƶ�\0��\0;";break;case"arrow.gif":echo"GIF89a\0\n\0�\0\0������!�\0\0\0,\0\0\0\0\0\n\0\0�i������Ӳ޻\0\0;";break;}}exit;}if($_GET["script"]=="version"){$q=get_temp_dir()."/adminer.version";@unlink($q);$s=file_open_lock($q);if($s)file_write_unlock($s,serialize(array("signature"=>$_POST["signature"],"version"=>$_POST["version"])));exit;}global$b,$g,$m,$ac,$n,$ba,$ca,$pe,$hg,$_d,$vi,$Ai,$ia;if(!$_SERVER["REQUEST_URI"])$_SERVER["REQUEST_URI"]=$_SERVER["ORIG_PATH_INFO"];if(!strpos($_SERVER["REQUEST_URI"],'?')&&$_SERVER["QUERY_STRING"]!="")$_SERVER["REQUEST_URI"].="?$_SERVER[QUERY_STRING]";if($_SERVER["HTTP_X_FORWARDED_PREFIX"])$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];$ba=($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure");@ini_set("session.use_trans_sid",false);if(!defined("SID")){session_cache_limiter("");session_name("adminer_sid");session_set_cookie_params(0,preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),"",$ba,true);session_start();}remove_slashes(array(&$_GET,&$_POST,&$_COOKIE),$Tc);if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);@set_time_limit(0);@ini_set("precision",15);function
get_lang(){return'en';}function
lang($_i,$lf=null){if(is_array($_i)){$mg=($lf==1?0:1);$_i=$_i[$mg];}$_i=str_replace("%d","%s",$_i);$lf=format_number($lf);return
sprintf($_i,$lf);}if(extension_loaded('pdo')){abstract
class
PdoDb{var$flavor='',$server_info,$affected_rows,$errno,$error;protected$pdo;private$result;function
dsn($gc,$V,$F,$Cf=array()){$Cf[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$Cf[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoDbStatement');try{$this->pdo=new
\PDO($gc,$V,$F,$Cf);}catch(\Exception$Bc){auth_error(h($Bc->getMessage()));}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);}abstract
function
select_db($Jb);function
quote($Q){return$this->pdo->quote($Q);}function
query($H,$Ki=false){$I=$this->pdo->query($H);$this->error="";if(!$I){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error='Unknown error.';return
false;}$this->store_result($I);return$I;}function
multi_query($H){return$this->result=$this->query($H);}function
store_result($I=null){if(!$I){$I=$this->result;if(!$I)return
false;}if($I->columnCount()){$I->num_rows=$I->rowCount();return$I;}$this->affected_rows=$I->rowCount();return
true;}function
next_result(){if(!$this->result)return
false;$this->result->_offset=0;return@$this->result->nextRowset();}function
result($H,$o=0){$I=$this->query($H);if(!$I)return
false;$K=$I->fetch();return$K?$K[$o]:false;}}class
PdoDbStatement
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch(\PDO::FETCH_NUM);}function
fetch_column($o){return$this->fetchColumn($o);}function
fetch_field(){$K=(object)$this->getColumnMeta($this->_offset++);$U=$K->pdo_type;$K->type=($U==\PDO::PARAM_INT?0:15);$K->charsetnr=($U==\PDO::PARAM_LOB||(isset($K->flags)&&in_array("blob",(array)$K->flags))?63:0);return$K;}function
seek($D){for($u=0;$u<$D;$u++)$this->fetch();}}}$ac=array();function
add_driver($v,$C){global$ac;$ac[$v]=$C;}function
get_driver($v){global$ac;return$ac[$v];}abstract
class
SqlDriver{static$pg=array();static$he;protected$conn;protected$types=array();var$editFunctions=array();var$unsigned=array();var$operators=array();var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();function
__construct($g){$this->conn=$g;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength($o){}function
unconvertFunction($o){}function
select($R,$M,$Z,$pd,$Ef=array(),$_=1,$E=0,$ug=false){global$b;$ce=(count($pd)<count($M));$H=$b->selectQueryBuild($M,$Z,$pd,$Ef,$_,$E);if(!$H)$H="SELECT".limit(($_GET["page"]!="last"&&$_!=""&&$pd&&$ce&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$M)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($pd&&$ce?"\nGROUP BY ".implode(", ",$pd):"").($Ef?"\nORDER BY ".implode(", ",$Ef):""),($_!=""?+$_:null),($E?$_*$E:0),"\n");$Lh=microtime(true);$J=$this->conn->query($H);if($ug)echo$b->selectQuery($H,$Lh,!$J);return$J;}function
delete($R,$Cg,$_=0){$H="FROM ".table($R);return
queries("DELETE".($_?limit1($R,$H,$Cg):" $H$Cg"));}function
update($R,$O,$Cg,$_=0,$nh="\n"){$dj=array();foreach($O
as$z=>$X)$dj[]="$z = $X";$H=table($R)." SET$nh".implode(",$nh",$dj);return
queries("UPDATE".($_?limit1($R,$H,$Cg,$nh):" $H$Cg"));}function
insert($R,$O){return
queries("INSERT INTO ".table($R).($O?" (".implode(", ",array_keys($O)).")\nVALUES (".implode(", ",$O).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,$L,$G){return
false;}function
begin(){return
queries("BEGIN");}function
commit(){return
queries("COMMIT");}function
rollback(){return
queries("ROLLBACK");}function
slowQuery($H,$ni){}function
convertSearch($w,$X,$o){return$w;}function
convertOperator($zf){return$zf;}function
value($X,$o){return(method_exists($this->conn,'value')?$this->conn->value($X,$o):(is_resource($X)?stream_get_contents($X):$X));}function
quoteBinary($bh){return
q($bh);}function
warnings(){return'';}function
tableHelp($C,$fe=false){}function
hasCStyleEscapes(){return
false;}function
engines(){return
array();}function
supportsIndex($S){return!is_view($S);}function
checkConstraints($R){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($R)."
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'");}}$ac["sqlite"]="SQLite";if(isset($_GET["sqlite"])){define('Adminer\DRIVER',"sqlite");if(class_exists("SQLite3")&&$_GET["ext"]!="pdo"){class
SqliteDb{var$extension="SQLite3",$server_info,$affected_rows,$errno,$error;private$link;function
__construct($q){$this->link=new
\SQLite3($q);$gj=$this->link->version();$this->server_info=$gj["versionString"];}function
query($H){$I=@$this->link->query($H);$this->error="";if(!$I){$this->errno=$this->link->lastErrorCode();$this->error=$this->link->lastErrorMsg();return
false;}elseif($I->numColumns())return
new
Result($I);$this->affected_rows=$this->link->changes();return
true;}function
quote($Q){return(is_utf8($Q)?"'".$this->link->escapeString($Q)."'":"x'".first(unpack('H*',$Q))."'");}function
store_result(){return$this->result;}function
result($H,$o=0){$I=$this->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return$K?$K[$o]:false;}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($I){$this->result=$I;}function
fetch_assoc(){return$this->result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$e=$this->offset++;$U=$this->result->columnType($e);return(object)array("name"=>$this->result->columnName($e),"type"=>($U==SQLITE3_TEXT?15:0),"charsetnr"=>($U==SQLITE3_BLOB?63:0),);}function
__destruct(){return$this->result->finalize();}}}elseif(extension_loaded("pdo_sqlite")){class
SqliteDb
extends
PdoDb{var$extension="PDO_SQLite";function
__construct($q){$this->dsn(DRIVER.":$q","","");}function
select_db($k){return
false;}}}if(class_exists('Adminer\SqliteDb')){class
Db
extends
SqliteDb{var$flavor='';function
__construct(){parent::__construct(":memory:");$this->query("PRAGMA foreign_keys = 1");}function
select_db($q){if(is_readable($q)&&$this->query("ATTACH ".$this->quote(preg_match("~(^[/\\\\]|:)~",$q)?$q:dirname($_SERVER["SCRIPT_FILENAME"])."/$q")." AS a")){parent::__construct($q);$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return
true;}return
false;}}}class
Driver
extends
SqlDriver{static$pg=array("SQLite3","PDO_SQLite");static$he="sqlite";protected$types=array(array("integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0));var$editFunctions=array(array(),array("integer|real|numeric"=>"+/-","text"=>"||",));var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");var$functions=array("hex","length","lower","round","unixepoch","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");function
__construct($g){parent::__construct($g);if(min_version(3.31,0,$g))$this->generated=array("STORED","VIRTUAL");}function
structuredTypes(){return
array_keys($this->types[0]);}function
insertUpdate($R,$L,$G){$dj=array();foreach($L
as$O)$dj[]="(".implode(", ",$O).")";return
queries("REPLACE INTO ".table($R)." (".implode(", ",array_keys(reset($L))).") VALUES\n".implode(",\n",$dj));}function
tableHelp($C,$fe=false){if($C=="sqlite_sequence")return"fileformat2.html#seqtab";if($C=="sqlite_master")return"fileformat2.html#$C";}function
checkConstraints($R){preg_match_all('~ CHECK *(\( *(((?>[^()]*[^() ])|(?1))*) *\))~',$this->conn->result("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R)),$Ge);return
array_combine($Ge[2],$Ge[2]);}}function
idf_escape($w){return'"'.str_replace('"','""',$w).'"';}function
table($w){return
idf_escape($w);}function
connect($Bb){list(,,$F)=$Bb;if($F!="")return'Database does not support password.';return
new
Db;}function
get_databases(){return
array();}function
limit($H,$Z,$_,$D=0,$nh=" "){return" $H$Z".($_!==null?$nh."LIMIT $_".($D?" OFFSET $D":""):"");}function
limit1($R,$H,$Z,$nh="\n"){return(preg_match('~^INTO~',$H)||get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($H,$Z,1,0,$nh):" $H WHERE rowid = (SELECT rowid FROM ".table($R).$Z.$nh."LIMIT 1)");}function
db_collation($k,$hb){return
get_val("PRAGMA encoding");}function
logged_user(){return
get_current_user();}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view') ORDER BY (name = 'sqlite_sequence'), name");}function
count_tables($j){return
array();}function
table_status($C=""){$J=array();foreach(get_rows("SELECT name AS Name, type AS Engine, 'rowid' AS Oid, '' AS Auto_increment FROM sqlite_master WHERE type IN ('table', 'view') ".($C!=""?"AND name = ".q($C):"ORDER BY name"))as$K){$K["Rows"]=get_val("SELECT COUNT(*) FROM ".idf_escape($K["Name"]));$J[$K["Name"]]=$K;}foreach(get_rows("SELECT * FROM sqlite_sequence",null,"")as$K)$J[$K["name"]]["Auto_increment"]=$K["seq"];return($C!=""?$J[$C]:$J);}function
is_view($S){return$S["Engine"]=="view";}function
fk_support($S){return!get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
fields($R){$J=array();$G="";foreach(get_rows("PRAGMA table_".(min_version(3.31)?"x":"")."info(".table($R).")")as$K){$C=$K["name"];$U=strtolower($K["type"]);$l=$K["dflt_value"];$J[$C]=array("field"=>$C,"type"=>(preg_match('~int~i',$U)?"integer":(preg_match('~char|clob|text~i',$U)?"text":(preg_match('~blob~i',$U)?"blob":(preg_match('~real|floa|doub~i',$U)?"real":"numeric")))),"full_type"=>$U,"default"=>(preg_match("~^'(.*)'$~",$l,$B)?str_replace("''","'",$B[1]):($l=="NULL"?null:$l)),"null"=>!$K["notnull"],"privileges"=>array("select"=>1,"insert"=>1,"update"=>1,"where"=>1,"order"=>1),"primary"=>$K["pk"],);if($K["pk"]){if($G!="")$J[$G]["auto_increment"]=false;elseif(preg_match('~^integer$~i',$U))$J[$C]["auto_increment"]=true;$G=$C;}}$Fh=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R));$w='(("[^"]*+")+|[a-z0-9_]+)';preg_match_all('~'.$w.'\s+text\s+COLLATE\s+(\'[^\']+\'|\S+)~i',$Fh,$Ge,PREG_SET_ORDER);foreach($Ge
as$B){$C=str_replace('""','"',preg_replace('~^"|"$~','',$B[1]));if($J[$C])$J[$C]["collation"]=trim($B[3],"'");}preg_match_all('~'.$w.'\s.*GENERATED ALWAYS AS \((.+)\) (STORED|VIRTUAL)~i',$Fh,$Ge,PREG_SET_ORDER);foreach($Ge
as$B){$C=str_replace('""','"',preg_replace('~^"|"$~','',$B[1]));$J[$C]["default"]=$B[3];$J[$C]["generated"]=strtoupper($B[4]);}return$J;}function
indexes($R,$h=null){global$g;if(!is_object($h))$h=$g;$J=array();$Fh=$h->result("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R));if(preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i',$Fh,$B)){$J[""]=array("type"=>"PRIMARY","columns"=>array(),"lengths"=>array(),"descs"=>array());preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i',$B[1],$Ge,PREG_SET_ORDER);foreach($Ge
as$B){$J[""]["columns"][]=idf_unescape($B[2]).$B[4];$J[""]["descs"][]=(preg_match('~DESC~i',$B[5])?'1':null);}}if(!$J){foreach(fields($R)as$C=>$o){if($o["primary"])$J[""]=array("type"=>"PRIMARY","columns"=>array($C),"lengths"=>array(),"descs"=>array(null));}}$Jh=get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($R),$h);foreach(get_rows("PRAGMA index_list(".table($R).")",$h)as$K){$C=$K["name"];$x=array("type"=>($K["unique"]?"UNIQUE":"INDEX"));$x["lengths"]=array();$x["descs"]=array();foreach(get_rows("PRAGMA index_info(".idf_escape($C).")",$h)as$ah){$x["columns"][]=$ah["name"];$x["descs"][]=null;}if(preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($C).' ON '.idf_escape($R),'~').' \((.*)\)$~i',$Jh[$C],$Ng)){preg_match_all('/("[^"]*+")+( DESC)?/',$Ng[2],$Ge);foreach($Ge[2]as$z=>$X){if($X)$x["descs"][$z]='1';}}if(!$J[""]||$x["type"]!="UNIQUE"||$x["columns"]!=$J[""]["columns"]||$x["descs"]!=$J[""]["descs"]||!preg_match("~^sqlite_~",$C))$J[$C]=$x;}return$J;}function
foreign_keys($R){$J=array();foreach(get_rows("PRAGMA foreign_key_list(".table($R).")")as$K){$r=&$J[$K["id"]];if(!$r)$r=$K;$r["source"][]=$K["from"];$r["target"][]=$K["to"];}return$J;}function
view($C){return
array("select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+~iU','',get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($C))));}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):array());}function
information_schema($k){return
false;}function
error(){global$g;return
h($g->error);}function
check_sqlite_name($C){global$g;$Jc="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($Jc)\$~",$C)){$g->error=sprintf('Please use one of the extensions %s.',str_replace("|",", ",$Jc));return
false;}return
true;}function
create_database($k,$gb){global$g;if(file_exists($k)){$g->error='File exists.';return
false;}if(!check_sqlite_name($k))return
false;try{$A=new
SqliteDb($k);}catch(\Exception$Bc){$g->error=$Bc->getMessage();return
false;}$A->query('PRAGMA encoding = "UTF-8"');$A->query('CREATE TABLE adminer (i)');$A->query('DROP TABLE adminer');return
true;}function
drop_databases($j){global$g;$g->__construct(":memory:");foreach($j
as$k){if(!@unlink($k)){$g->error='File exists.';return
false;}}return
true;}function
rename_database($C,$gb){global$g;if(!check_sqlite_name($C))return
false;$g->__construct(":memory:");$g->error='File exists.';return@rename(DB,$C);}function
auto_increment(){return" PRIMARY KEY AUTOINCREMENT";}function
alter_table($R,$C,$p,$cd,$mb,$rc,$gb,$_a,$bg){global$g;$Wi=($R==""||$cd);foreach($p
as$o){if($o[0]!=""||!$o[1]||$o[2]){$Wi=true;break;}}$c=array();$Pf=array();foreach($p
as$o){if($o[1]){$c[]=($Wi?$o[1]:"ADD ".implode($o[1]));if($o[0]!="")$Pf[$o[0]]=$o[1][0];}}if(!$Wi){foreach($c
as$X){if(!queries("ALTER TABLE ".table($R)." $X"))return
false;}if($R!=$C&&!queries("ALTER TABLE ".table($R)." RENAME TO ".table($C)))return
false;}elseif(!recreate_table($R,$C,$c,$Pf,$cd,$_a))return
false;if($_a){queries("BEGIN");queries("UPDATE sqlite_sequence SET seq = $_a WHERE name = ".q($C));if(!$g->affected_rows)queries("INSERT INTO sqlite_sequence (name, seq) VALUES (".q($C).", $_a)");queries("COMMIT");}return
true;}function
recreate_table($R,$C,$p,$Pf,$cd,$_a=0,$y=array(),$cc="",$ma=""){global$m;if($R!=""){if(!$p){foreach(fields($R)as$z=>$o){if($y)$o["auto_increment"]=0;$p[]=process_field($o,$o);$Pf[$z]=idf_escape($z);}}$tg=false;foreach($p
as$o){if($o[6])$tg=true;}$ec=array();foreach($y
as$z=>$X){if($X[2]=="DROP"){$ec[$X[1]]=true;unset($y[$z]);}}foreach(indexes($R)as$je=>$x){$f=array();foreach($x["columns"]as$z=>$e){if(!$Pf[$e])continue
2;$f[]=$Pf[$e].($x["descs"][$z]?" DESC":"");}if(!$ec[$je]){if($x["type"]!="PRIMARY"||!$tg)$y[]=array($x["type"],$je,$f);}}foreach($y
as$z=>$X){if($X[0]=="PRIMARY"){unset($y[$z]);$cd[]="  PRIMARY KEY (".implode(", ",$X[2]).")";}}foreach(foreign_keys($R)as$je=>$r){foreach($r["source"]as$z=>$e){if(!$Pf[$e])continue
2;$r["source"][$z]=idf_unescape($Pf[$e]);}if(!isset($cd[" $je"]))$cd[]=" ".format_foreign_key($r);}queries("BEGIN");}foreach($p
as$z=>$o){if(preg_match('~GENERATED~',$o[3]))unset($Pf[array_search($o[0],$Pf)]);$p[$z]="  ".implode($o);}$p=array_merge($p,array_filter($cd));foreach($m->checkConstraints($R)as$Ua){if($Ua!=$cc)$p[]="  CHECK ($Ua)";}if($ma)$p[]="  CHECK ($ma)";$hi=($R==$C?"adminer_$C":$C);if(!queries("CREATE TABLE ".table($hi)." (\n".implode(",\n",$p)."\n)"))return
false;if($R!=""){if($Pf&&!queries("INSERT INTO ".table($hi)." (".implode(", ",$Pf).") SELECT ".implode(", ",array_map('Adminer\idf_escape',array_keys($Pf)))." FROM ".table($R)))return
false;$Gi=array();foreach(triggers($R)as$Ei=>$oi){$Di=trigger($Ei);$Gi[]="CREATE TRIGGER ".idf_escape($Ei)." ".implode(" ",$oi)." ON ".table($C)."\n$Di[Statement]";}$_a=$_a?0:get_val("SELECT seq FROM sqlite_sequence WHERE name = ".q($R));if(!queries("DROP TABLE ".table($R))||($R==$C&&!queries("ALTER TABLE ".table($hi)." RENAME TO ".table($C)))||!alter_indexes($C,$y))return
false;if($_a)queries("UPDATE sqlite_sequence SET seq = $_a WHERE name = ".q($C));foreach($Gi
as$Di){if(!queries($Di))return
false;}queries("COMMIT");}return
true;}function
index_sql($R,$U,$C,$f){return"CREATE $U ".($U!="INDEX"?"INDEX ":"").idf_escape($C!=""?$C:uniqid($R."_"))." ON ".table($R)." $f";}function
alter_indexes($R,$c){foreach($c
as$G){if($G[0]=="PRIMARY")return
recreate_table($R,$R,array(),array(),array(),0,$c);}foreach(array_reverse($c)as$X){if(!queries($X[2]=="DROP"?"DROP INDEX ".idf_escape($X[1]):index_sql($R,$X[0],$X[1],"(".implode(", ",$X[2]).")")))return
false;}return
true;}function
truncate_tables($T){return
apply_queries("DELETE FROM",$T);}function
drop_views($ij){return
apply_queries("DROP VIEW",$ij);}function
drop_tables($T){return
apply_queries("DROP TABLE",$T);}function
move_tables($T,$ij,$fi){return
false;}function
trigger($C){if($C=="")return
array("Statement"=>"BEGIN\n\t;\nEND");$w='(?:[^`"\s]+|`[^`]*`|"[^"]*")+';$Fi=trigger_options();preg_match("~^CREATE\\s+TRIGGER\\s*$w\\s*(".implode("|",$Fi["Timing"]).")\\s+([a-z]+)(?:\\s+OF\\s+($w))?\\s+ON\\s*$w\\s*(?:FOR\\s+EACH\\s+ROW\\s)?(.*)~is",get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($C)),$B);$nf=$B[3];return
array("Timing"=>strtoupper($B[1]),"Event"=>strtoupper($B[2]).($nf?" OF":""),"Of"=>idf_unescape($nf),"Trigger"=>$C,"Statement"=>$B[4],);}function
triggers($R){$J=array();$Fi=trigger_options();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($R))as$K){preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode("|",$Fi["Timing"]).')\s*(.*?)\s+ON\b~i',$K["sql"],$B);$J[$K["name"]]=array($B[1],$B[2]);}return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
begin(){return
queries("BEGIN");}function
last_id($I){return
get_val("SELECT LAST_INSERT_ROWID()");}function
explain($g,$H){return$g->query("EXPLAIN QUERY PLAN $H");}function
found_rows($S,$Z){}function
types(){return
array();}function
create_sql($R,$_a,$Ph){$J=get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($R));foreach(indexes($R)as$C=>$x){if($C=='')continue;$J.=";\n\n".index_sql($R,$x['type'],$C,"(".implode(", ",array_map('Adminer\idf_escape',$x['columns'])).")");}return$J;}function
truncate_sql($R){return"DELETE FROM ".table($R);}function
use_sql($Jb){}function
trigger_sql($R){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($R)));}function
show_variables(){$J=array();foreach(get_rows("PRAGMA pragma_list")as$K){$C=$K["name"];if($C!="pragma_list"&&$C!="compile_options"){$J[$C]=array($C,'');foreach(get_rows("PRAGMA $C")as$K)$J[$C][1].=implode(", ",$K)."\n";}}return$J;}function
show_status(){$J=array();foreach(get_vals("PRAGMA compile_options")as$Bf)$J[]=explode("=",$Bf,2);return$J;}function
convert_field($o){}function
unconvert_field($o,$J){return$J;}function
support($Oc){return
preg_match('~^(check|columns|database|drop_col|dump|indexes|descidx|move_col|sql|status|table|trigger|variables|view|view_trigger)$~',$Oc);}}$ac["pgsql"]="PostgreSQL";if(isset($_GET["pgsql"])){define('Adminer\DRIVER',"pgsql");if(extension_loaded("pgsql")&&$_GET["ext"]!="pdo"){class
Db{var$extension="PgSQL",$flavor='',$server_info,$affected_rows,$error,$timeout;private$link,$result,$string,$database=true;function
_error($xc,$n){if(ini_bool("html_errors"))$n=html_entity_decode(strip_tags($n));$n=preg_replace('~^[^:]*: ~','',$n);$this->error=$n;}function
connect($N,$V,$F){global$b;$k=$b->database();set_error_handler(array($this,'_error'));$this->string="host='".str_replace(":","' port='",addcslashes($N,"'\\"))."' user='".addcslashes($V,"'\\")."' password='".addcslashes($F,"'\\")."'";$Kh=$b->connectSsl();if(isset($Kh["mode"]))$this->string.=" sslmode='".$Kh["mode"]."'";$this->link=@pg_connect("$this->string dbname='".($k!=""?addcslashes($k,"'\\"):"postgres")."'",PGSQL_CONNECT_FORCE_NEW);if(!$this->link&&$k!=""){$this->database=false;$this->link=@pg_connect("$this->string dbname='postgres'",PGSQL_CONNECT_FORCE_NEW);}restore_error_handler();if($this->link)pg_set_client_encoding($this->link,"UTF8");return(bool)$this->link;}function
quote($Q){return(function_exists('pg_escape_literal')?pg_escape_literal($this->link,$Q):"'".pg_escape_string($this->link,$Q)."'");}function
value($X,$o){return($o["type"]=="bytea"&&$X!==null?pg_unescape_bytea($X):$X);}function
select_db($Jb){global$b;if($Jb==$b->database())return$this->database;$J=@pg_connect("$this->string dbname='".addcslashes($Jb,"'\\")."'",PGSQL_CONNECT_FORCE_NEW);if($J)$this->link=$J;return$J;}function
close(){$this->link=@pg_connect("$this->string dbname='postgres'");}function
query($H,$Ki=false){$I=@pg_query($this->link,$H);$this->error="";if(!$I){$this->error=pg_last_error($this->link);$J=false;}elseif(!pg_num_fields($I)){$this->affected_rows=pg_affected_rows($I);$J=true;}else$J=new
Result($I);if($this->timeout){$this->timeout=0;$this->query("RESET statement_timeout");}return$J;}function
multi_query($H){return$this->result=$this->query($H);}function
store_result(){return$this->result;}function
next_result(){return
false;}function
result($H,$o=0){$I=$this->query($H);return($I?$I->fetch_column($o):false);}function
warnings(){return
h(pg_last_notice($this->link));}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($I){$this->result=$I;$this->num_rows=pg_num_rows($I);}function
fetch_assoc(){return
pg_fetch_assoc($this->result);}function
fetch_row(){return
pg_fetch_row($this->result);}function
fetch_column($o){return($this->num_rows?pg_fetch_result($this->result,0,$o):false);}function
fetch_field(){$e=$this->offset++;$J=new
\stdClass;$J->orgtable=pg_field_table($this->result,$e);$J->name=pg_field_name($this->result,$e);$J->type=pg_field_type($this->result,$e);$J->charsetnr=($J->type=="bytea"?63:0);return$J;}function
__destruct(){pg_free_result($this->result);}}}elseif(extension_loaded("pdo_pgsql")){class
Db
extends
PdoDb{var$extension="PDO_PgSQL",$timeout;function
connect($N,$V,$F){global$b;$k=$b->database();$gc="pgsql:host='".str_replace(":","' port='",addcslashes($N,"'\\"))."' client_encoding=utf8 dbname='".($k!=""?addcslashes($k,"'\\"):"postgres")."'";$Kh=$b->connectSsl();if(isset($Kh["mode"]))$gc.=" sslmode='".$Kh["mode"]."'";$this->dsn($gc,$V,$F);return
true;}function
select_db($Jb){global$b;return($b->database()==$Jb);}function
query($H,$Ki=false){$J=parent::query($H,$Ki);if($this->timeout){$this->timeout=0;parent::query("RESET statement_timeout");}return$J;}function
warnings(){return'';}function
close(){}}}class
Driver
extends
SqlDriver{static$pg=array("PgSQL","PDO_PgSQL");static$he="pgsql";var$operators=array("=","<",">","<=",">=","!=","~","!~","LIKE","LIKE %%","ILIKE","ILIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");var$functions=array("char_length","lower","round","to_hex","to_timestamp","upper");var$grouping=array("avg","count","count distinct","max","min","sum");function
__construct($g){parent::__construct($g);$this->types=array('Numbers'=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),'Date and time'=>array("date"=>13,"time"=>17,"timestamp"=>20,"timestamptz"=>21,"interval"=>0),'Strings'=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),'Binary'=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),'Network'=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"macaddr8"=>23,"txid_snapshot"=>0),'Geometry'=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),);if(min_version(9.2,0,$g)){$this->types['Strings']["json"]=4294967295;if(min_version(9.4,0,$g))$this->types['Strings']["jsonb"]=4294967295;}$this->editFunctions=array(array("char"=>"md5","date|time"=>"now",),array(number_type()=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",));if(min_version(12,0,$g))$this->generated=array("STORED");}function
enumLength($o){$tc=$this->types['User types'][$o["type"]];return($tc?type_values($tc):"");}function
setUserTypes($Ji){$this->types['User types']=array_flip($Ji);}function
insertReturning($R){$_a=array_filter(fields($R),function($o){return$o['auto_increment'];});return(count($_a)==1?" RETURNING ".idf_escape(key($_a)):"");}function
insertUpdate($R,$L,$G){global$g;foreach($L
as$O){$Si=array();$Z=array();foreach($O
as$z=>$X){$Si[]="$z = $X";if(isset($G[idf_unescape($z)]))$Z[]="$z = $X";}if(!(($Z&&queries("UPDATE ".table($R)." SET ".implode(", ",$Si)." WHERE ".implode(" AND ",$Z))&&$g->affected_rows)||queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($O)).") VALUES (".implode(", ",$O).")")))return
false;}return
true;}function
slowQuery($H,$ni){$this->conn->query("SET statement_timeout = ".(1000*$ni));$this->conn->timeout=1000*$ni;return$H;}function
convertSearch($w,$X,$o){$ki="char|text";if(strpos($X["op"],"LIKE")===false)$ki.="|date|time(stamp)?|boolean|uuid|inet|cidr|macaddr|".number_type();return(preg_match("~$ki~",$o["type"])?$w:"CAST($w AS text)");}function
quoteBinary($bh){return"'\\x".bin2hex($bh)."'";}function
warnings(){return$this->conn->warnings();}function
tableHelp($C,$fe=false){$ze=array("information_schema"=>"infoschema","pg_catalog"=>($fe?"view":"catalog"),);$A=$ze[$_GET["ns"]];if($A)return"$A-".str_replace("_","-",$C).".html";}function
supportsIndex($S){return$S["Engine"]!="view";}function
hasCStyleEscapes(){static$Pa;if($Pa===null)$Pa=($this->conn->result("SHOW standard_conforming_strings")=="off");return$Pa;}}function
idf_escape($w){return'"'.str_replace('"','""',$w).'"';}function
table($w){return
idf_escape($w);}function
connect($Bb){global$ac;$g=new
Db;if($g->connect($Bb[0],$Bb[1],$Bb[2])){if(min_version(9,0,$g))$g->query("SET application_name = 'Adminer'");$gj=$g->result("SELECT version()");$g->flavor=(preg_match('~CockroachDB~',$gj)?'cockroach':'');$g->server_info=preg_replace('~^\D*([\d.]+[-\w]*).*~','\1',$gj);if($g->flavor=='cockroach')$ac[DRIVER]="CockroachDB";return$g;}return$g->error;}function
get_databases(){return
get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");}function
limit($H,$Z,$_,$D=0,$nh=" "){return" $H$Z".($_!==null?$nh."LIMIT $_".($D?" OFFSET $D":""):"");}function
limit1($R,$H,$Z,$nh="\n"){return(preg_match('~^INTO~',$H)?limit($H,$Z,1,0,$nh):" $H".(is_view(table_status1($R))?$Z:$nh."WHERE ctid = (SELECT ctid FROM ".table($R).$Z.$nh."LIMIT 1)"));}function
db_collation($k,$hb){return
get_val("SELECT datcollate FROM pg_database WHERE datname = ".q($k));}function
logged_user(){return
get_val("SELECT user");}function
tables_list(){$H="SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = current_schema()";if(support("materializedview"))$H.="
UNION ALL
SELECT matviewname, 'MATERIALIZED VIEW'
FROM pg_matviews
WHERE schemaname = current_schema()";$H.="
ORDER BY 1";return
get_key_vals($H);}function
count_tables($j){global$g;$J=array();foreach($j
as$k){if($g->select_db($k))$J[$k]=count(tables_list());}return$J;}function
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
".($C!=""?"AND relname = ".q($C):"ORDER BY relname"))as$K)$J[$K["Name"]]=$K;return($C!=""?$J[$C]:$J);}function
is_view($S){return
in_array($S["Engine"],array("view","materialized view"));}function
fk_support($S){return
true;}function
fields($R){$J=array();$ta=array('timestamp without time zone'=>'timestamp','timestamp with time zone'=>'timestamptz',);foreach(get_rows("SELECT
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
ORDER BY a.attnum")as$K){preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~',$K["full_type"],$B);list(,$U,$we,$K["length"],$na,$va)=$B;$K["length"].=$va;$Wa=$U.$na;if(isset($ta[$Wa])){$K["type"]=$ta[$Wa];$K["full_type"]=$K["type"].$we.$va;}else{$K["type"]=$U;$K["full_type"]=$K["type"].$we.$na.$va;}if(in_array($K['attidentity'],array('a','d')))$K['default']='GENERATED '.($K['attidentity']=='d'?'BY DEFAULT':'ALWAYS').' AS IDENTITY';$K["generated"]=($K["attgenerated"]=="s"?"STORED":"");$K["null"]=!$K["attnotnull"];$K["auto_increment"]=$K['attidentity']||preg_match('~^nextval\(~i',$K["default"])||preg_match('~^unique_rowid\(~',$K["default"]);$K["privileges"]=array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1);if(preg_match('~(.+)::[^,)]+(.*)~',$K["default"],$B))$K["default"]=($B[1]=="NULL"?null:idf_unescape($B[1]).$B[2]);$J[$K["field"]]=$K;}return$J;}function
indexes($R,$h=null){global$g;if(!is_object($h))$h=$g;$J=array();$Yh=$h->result("SELECT oid FROM pg_class WHERE relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema()) AND relname = ".q($R));$f=get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $Yh AND attnum > 0",$h);foreach(get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey, indoption, (indpred IS NOT NULL)::int as indispartial
FROM pg_index i, pg_class ci
WHERE i.indrelid = $Yh AND ci.oid = i.indexrelid
ORDER BY indisprimary DESC, indisunique DESC",$h)as$K){$Og=$K["relname"];$J[$Og]["type"]=($K["indispartial"]?"INDEX":($K["indisprimary"]?"PRIMARY":($K["indisunique"]?"UNIQUE":"INDEX")));$J[$Og]["columns"]=array();$J[$Og]["descs"]=array();if($K["indkey"]){foreach(explode(" ",$K["indkey"])as$Rd)$J[$Og]["columns"][]=$f[$Rd];foreach(explode(" ",$K["indoption"])as$Sd)$J[$Og]["descs"][]=($Sd&1?'1':null);}$J[$Og]["lengths"]=array();}return$J;}function
foreign_keys($R){global$m;$J=array();foreach(get_rows("SELECT conname, condeferrable::int AS deferrable, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = (SELECT pc.oid FROM pg_class AS pc INNER JOIN pg_namespace AS pn ON (pn.oid = pc.relnamespace) WHERE pc.relname = ".q($R)." AND pn.nspname = current_schema())
AND contype = 'f'::char
ORDER BY conkey, conname")as$K){if(preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA',$K['definition'],$B)){$K['source']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$B[1])));if(preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~',$B[2],$Ee)){$K['ns']=idf_unescape($Ee[2]);$K['table']=idf_unescape($Ee[4]);}$K['target']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$B[3])));$K['on_delete']=(preg_match("~ON DELETE ($m->onActions)~",$B[4],$Ee)?$Ee[1]:'NO ACTION');$K['on_update']=(preg_match("~ON UPDATE ($m->onActions)~",$B[4],$Ee)?$Ee[1]:'NO ACTION');$J[$K['conname']]=$K;}}return$J;}function
view($C){return
array("select"=>trim(get_val("SELECT pg_get_viewdef(".get_val("SELECT oid FROM pg_class WHERE relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema()) AND relname = ".q($C)).")")));}function
collations(){return
array();}function
information_schema($k){return
get_schema()=="information_schema";}function
error(){global$g;$J=h($g->error);if(preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s',$J,$B))$J=$B[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($B[3]).'})(.*)~','\1<b>\2</b>',$B[2]).$B[4];return
nl_br($J);}function
create_database($k,$gb){return
queries("CREATE DATABASE ".idf_escape($k).($gb?" ENCODING ".idf_escape($gb):""));}function
drop_databases($j){global$g;$g->close();return
apply_queries("DROP DATABASE",$j,'Adminer\idf_escape');}function
rename_database($C,$gb){global$g;$g->close();return
queries("ALTER DATABASE ".idf_escape(DB)." RENAME TO ".idf_escape($C));}function
auto_increment(){return"";}function
alter_table($R,$C,$p,$cd,$mb,$rc,$gb,$_a,$bg){$c=array();$Bg=array();if($R!=""&&$R!=$C)$Bg[]="ALTER TABLE ".table($R)." RENAME TO ".table($C);$oh="";foreach($p
as$o){$e=idf_escape($o[0]);$X=$o[1];if(!$X)$c[]="DROP $e";else{$cj=$X[5];unset($X[5]);if($o[0]==""){if(isset($X[6]))$X[1]=($X[1]==" bigint"?" big":($X[1]==" smallint"?" small":" "))."serial";$c[]=($R!=""?"ADD ":"  ").implode($X);if(isset($X[6]))$c[]=($R!=""?"ADD":" ")." PRIMARY KEY ($X[0])";}else{if($e!=$X[0])$Bg[]="ALTER TABLE ".table($C)." RENAME $e TO $X[0]";$c[]="ALTER $e TYPE$X[1]";$ph=$R."_".idf_unescape($X[0])."_seq";$c[]="ALTER $e ".($X[3]?"SET".preg_replace('~GENERATED ALWAYS(.*) STORED~','EXPRESSION\1',$X[3]):(isset($X[6])?"SET DEFAULT nextval(".q($ph).")":"DROP DEFAULT"));if(isset($X[6]))$oh="CREATE SEQUENCE IF NOT EXISTS ".idf_escape($ph)." OWNED BY ".idf_escape($R).".$X[0]";$c[]="ALTER $e ".($X[2]==" NULL"?"DROP NOT":"SET").$X[2];}if($o[0]!=""||$cj!="")$Bg[]="COMMENT ON COLUMN ".table($C).".$X[0] IS ".($cj!=""?substr($cj,9):"''");}}$c=array_merge($c,$cd);if($R=="")array_unshift($Bg,"CREATE TABLE ".table($C)." (\n".implode(",\n",$c)."\n)");elseif($c)array_unshift($Bg,"ALTER TABLE ".table($R)."\n".implode(",\n",$c));if($oh)array_unshift($Bg,$oh);if($mb!==null)$Bg[]="COMMENT ON TABLE ".table($C)." IS ".q($mb);foreach($Bg
as$H){if(!queries($H))return
false;}return
true;}function
alter_indexes($R,$c){$i=array();$bc=array();$Bg=array();foreach($c
as$X){if($X[0]!="INDEX")$i[]=($X[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($X[1]):"\nADD".($X[1]!=""?" CONSTRAINT ".idf_escape($X[1]):"")." $X[0] ".($X[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$X[2]).")");elseif($X[2]=="DROP")$bc[]=idf_escape($X[1]);else$Bg[]="CREATE INDEX ".idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R)." (".implode(", ",$X[2]).")";}if($i)array_unshift($Bg,"ALTER TABLE ".table($R).implode(",",$i));if($bc)array_unshift($Bg,"DROP INDEX ".implode(", ",$bc));foreach($Bg
as$H){if(!queries($H))return
false;}return
true;}function
truncate_tables($T){return
queries("TRUNCATE ".implode(", ",array_map('Adminer\table',$T)));}function
drop_views($ij){return
drop_tables($ij);}function
drop_tables($T){foreach($T
as$R){$P=table_status($R);if(!queries("DROP ".strtoupper($P["Engine"])." ".table($R)))return
false;}return
true;}function
move_tables($T,$ij,$fi){foreach(array_merge($T,$ij)as$R){$P=table_status($R);if(!queries("ALTER ".strtoupper($P["Engine"])." ".table($R)." SET SCHEMA ".idf_escape($fi)))return
false;}return
true;}function
trigger($C,$R){if($C=="")return
array("Statement"=>"EXECUTE PROCEDURE ()");$f=array();$Z="WHERE trigger_schema = current_schema() AND event_object_table = ".q($R)." AND trigger_name = ".q($C);foreach(get_rows("SELECT * FROM information_schema.triggered_update_columns $Z")as$K)$f[]=$K["event_object_column"];$J=array();foreach(get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers'."
$Z
ORDER BY event_manipulation DESC")as$K){if($f&&$K["Event"]=="UPDATE")$K["Event"].=" OF";$K["Of"]=implode(", ",$f);if($J)$K["Event"].=" OR $J[Event]";$J=$K;}return$J;}function
triggers($R){$J=array();foreach(get_rows("SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = ".q($R))as$K){$Di=trigger($K["trigger_name"],$R);$J[$Di["Trigger"]]=array($Di["Timing"],$Di["Event"]);}return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF"),"Type"=>array("FOR EACH ROW","FOR EACH STATEMENT"),);}function
routine($C,$U){$L=get_rows('SELECT routine_definition AS definition, LOWER(external_language) AS language, *
FROM information_schema.routines
WHERE routine_schema = current_schema() AND specific_name = '.q($C));$J=$L[0];$J["returns"]=array("type"=>$J["type_udt_name"]);$J["fields"]=get_rows('SELECT parameter_name AS field, data_type AS type, character_maximum_length AS length, parameter_mode AS inout
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
routine_id($C,$K){$J=array();foreach($K["fields"]as$o){$we=$o["length"];$J[]=$o["type"].($we?"($we)":"");}return
idf_escape($C)."(".implode(", ",$J).")";}function
last_id($I){return(is_object($I)&&$I->num_rows?$I->fetch_column(0):0);}function
explain($g,$H){return$g->query("EXPLAIN $H");}function
found_rows($S,$Z){if(preg_match("~ rows=([0-9]+)~",get_val("EXPLAIN SELECT * FROM ".idf_escape($S["Name"]).($Z?" WHERE ".implode(" AND ",$Z):"")),$Ng))return$Ng[1];return
false;}function
types(){return
get_key_vals("SELECT oid, typname
FROM pg_type
WHERE typnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema())
AND typtype IN ('b','d','e')
AND typelem = 0");}function
type_values($v){$wc=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $v ORDER BY enumsortorder");return($wc?"'".implode("', '",array_map('addslashes',$wc))."'":"");}function
schemas(){return
get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");}function
get_schema(){return
get_val("SELECT current_schema()");}function
set_schema($dh,$h=null){global$g,$m;if(!$h)$h=$g;$J=$h->query("SET search_path TO ".idf_escape($dh));$m->setUserTypes(types());return$J;}function
foreign_keys_sql($R){$J="";$P=table_status($R);$Yc=foreign_keys($R);ksort($Yc);foreach($Yc
as$Xc=>$Wc)$J.="ALTER TABLE ONLY ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." ADD CONSTRAINT ".idf_escape($Xc)." $Wc[definition] ".($Wc['deferrable']?'DEFERRABLE':'NOT DEFERRABLE').";\n";return($J?"$J\n":$J);}function
create_sql($R,$_a,$Ph){global$m;$Tg=array();$qh=array();$P=table_status($R);if(is_view($P)){$hj=view($R);return
rtrim("CREATE VIEW ".idf_escape($R)." AS $hj[select]",";");}$p=fields($R);if(!$P||empty($p))return
false;$J="CREATE TABLE ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." (\n    ";foreach($p
as$o){$Yf=idf_escape($o['field']).' '.$o['full_type'].default_value($o).($o['attnotnull']?" NOT NULL":"");$Tg[]=$Yf;if(preg_match('~nextval\(\'([^\']+)\'\)~',$o['default'],$Ge)){$ph=$Ge[1];$Eh=first(get_rows((min_version(10)?"SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = ".q(idf_unescape($ph)):"SELECT * FROM $ph"),null,"-- "));$qh[]=($Ph=="DROP+CREATE"?"DROP SEQUENCE IF EXISTS $ph;\n":"")."CREATE SEQUENCE $ph INCREMENT $Eh[increment_by] MINVALUE $Eh[min_value] MAXVALUE $Eh[max_value]".($_a&&$Eh['last_value']?" START ".($Eh["last_value"]+1):"")." CACHE $Eh[cache_value];";}}if(!empty($qh))$J=implode("\n\n",$qh)."\n\n$J";$G="";foreach(indexes($R)as$Pd=>$x){if($x['type']=='PRIMARY'){$G=$Pd;$Tg[]="CONSTRAINT ".idf_escape($Pd)." PRIMARY KEY (".implode(', ',array_map('Adminer\idf_escape',$x['columns'])).")";}}foreach($m->checkConstraints($R)as$rb=>$tb)$Tg[]="CONSTRAINT ".idf_escape($rb)." CHECK $tb";$J.=implode(",\n    ",$Tg)."\n) WITH (oids = ".($P['Oid']?'true':'false').");";if($P['Comment'])$J.="\n\nCOMMENT ON TABLE ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." IS ".q($P['Comment']).";";foreach($p
as$Qc=>$o){if($o['comment'])$J.="\n\nCOMMENT ON COLUMN ".idf_escape($P['nspname']).".".idf_escape($P['Name']).".".idf_escape($Qc)." IS ".q($o['comment']).";";}foreach(get_rows("SELECT indexdef FROM pg_catalog.pg_indexes WHERE schemaname = current_schema() AND tablename = ".q($R).($G?" AND indexname != ".q($G):""),null,"-- ")as$K)$J.="\n\n$K[indexdef];";return
rtrim($J,';');}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
trigger_sql($R){$P=table_status($R);$J="";foreach(triggers($R)as$Ci=>$Bi){$Di=trigger($Ci,$P['Name']);$J.="\nCREATE TRIGGER ".idf_escape($Di['Trigger'])." $Di[Timing] $Di[Event] ON ".idf_escape($P["nspname"]).".".idf_escape($P['Name'])." $Di[Type] $Di[Statement];;\n";}return$J;}function
use_sql($Jb){return"\connect ".idf_escape($Jb);}function
show_variables(){return
get_rows("SHOW ALL");}function
process_list(){return
get_rows("SELECT * FROM pg_stat_activity ORDER BY ".(min_version(9.2)?"pid":"procpid"));}function
convert_field($o){}function
unconvert_field($o,$J){return$J;}function
support($Oc){global$g;return
preg_match('~^(check|database|table|columns|sql|indexes|descidx|comment|view|'.(min_version(9.3)?'materializedview|':'').'scheme|'.(min_version(11)?'procedure|':'').'routine|sequence|trigger|type|variables|drop_col'.($g->flavor=='cockroach'?'':'|processlist').'|kill|dump)$~',$Oc);}function
kill_process($X){return
queries("SELECT pg_terminate_backend(".number($X).")");}function
connection_id(){return"SELECT pg_backend_pid()";}function
max_connections(){return
get_val("SHOW max_connections");}}$ac["oracle"]="Oracle (beta)";if(isset($_GET["oracle"])){define('Adminer\DRIVER',"oracle");if(extension_loaded("oci8")&&$_GET["ext"]!="pdo"){class
Db{var$extension="oci8",$flavor='',$server_info,$affected_rows,$errno,$error;var$_current_db;private$link,$result;function
_error($xc,$n){if(ini_bool("html_errors"))$n=html_entity_decode(strip_tags($n));$n=preg_replace('~^[^:]*: ~','',$n);$this->error=$n;}function
connect($N,$V,$F){$this->link=@oci_new_connect($V,$F,$N,"AL32UTF8");if($this->link){$this->server_info=oci_server_version($this->link);return
true;}$n=oci_error();$this->error=$n["message"];return
false;}function
quote($Q){return"'".str_replace("'","''",$Q)."'";}function
select_db($Jb){$this->_current_db=$Jb;return
true;}function
query($H,$Ki=false){$I=oci_parse($this->link,$H);$this->error="";if(!$I){$n=oci_error($this->link);$this->errno=$n["code"];$this->error=$n["message"];return
false;}set_error_handler(array($this,'_error'));$J=@oci_execute($I);restore_error_handler();if($J){if(oci_num_fields($I))return
new
Result($I);$this->affected_rows=oci_num_rows($I);oci_free_statement($I);}return$J;}function
multi_query($H){return$this->result=$this->query($H);}function
store_result(){return$this->result;}function
next_result(){return
false;}function
result($H,$o=0){$I=$this->query($H);return(is_object($I)?$I->fetch_column($o):false);}}class
Result{var$num_rows;private$result,$offset=1;function
__construct($I){$this->result=$I;}private
function
convert($K){foreach((array)$K
as$z=>$X){if(is_a($X,'OCI-Lob'))$K[$z]=$X->load();}return$K;}function
fetch_assoc(){return$this->convert(oci_fetch_assoc($this->result));}function
fetch_row(){return$this->convert(oci_fetch_row($this->result));}function
fetch_column($o){return(oci_fetch($this->result)?oci_result($this->result,$o+1):false);}function
fetch_field(){$e=$this->offset++;$J=new
\stdClass;$J->name=oci_field_name($this->result,$e);$J->type=oci_field_type($this->result,$e);$J->charsetnr=(preg_match("~raw|blob|bfile~",$J->type)?63:0);return$J;}function
__destruct(){oci_free_statement($this->result);}}}elseif(extension_loaded("pdo_oci")){class
Db
extends
PdoDb{var$extension="PDO_OCI";var$_current_db;function
connect($N,$V,$F){$this->dsn("oci:dbname=//$N;charset=AL32UTF8",$V,$F);return
true;}function
select_db($Jb){$this->_current_db=$Jb;return
true;}}}class
Driver
extends
SqlDriver{static$pg=array("OCI8","PDO_OCI");static$he="oracle";var$editFunctions=array(array("date"=>"current_date","timestamp"=>"current_timestamp",),array("number|float|double"=>"+/-","date|timestamp"=>"+ interval/- interval","char|clob"=>"||",));var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");var$functions=array("length","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");function
__construct($g){parent::__construct($g);$this->types=array('Numbers'=>array("number"=>38,"binary_float"=>12,"binary_double"=>21),'Date and time'=>array("date"=>10,"timestamp"=>29,"interval year"=>12,"interval day"=>28),'Strings'=>array("char"=>2000,"varchar2"=>4000,"nchar"=>2000,"nvarchar2"=>4000,"clob"=>4294967295,"nclob"=>4294967295),'Binary'=>array("raw"=>2000,"long raw"=>2147483648,"blob"=>4294967295,"bfile"=>4294967296),);}function
begin(){return
true;}function
insertUpdate($R,$L,$G){global$g;foreach($L
as$O){$Si=array();$Z=array();foreach($O
as$z=>$X){$Si[]="$z = $X";if(isset($G[idf_unescape($z)]))$Z[]="$z = $X";}if(!(($Z&&queries("UPDATE ".table($R)." SET ".implode(", ",$Si)." WHERE ".implode(" AND ",$Z))&&$g->affected_rows)||queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($O)).") VALUES (".implode(", ",$O).")")))return
false;}return
true;}function
hasCStyleEscapes(){return
true;}}function
idf_escape($w){return'"'.str_replace('"','""',$w).'"';}function
table($w){return
idf_escape($w);}function
connect($Bb){$g=new
Db;if($g->connect($Bb[0],$Bb[1],$Bb[2]))return$g;return$g->error;}function
get_databases(){return
get_vals("SELECT DISTINCT tablespace_name FROM (
SELECT tablespace_name FROM user_tablespaces
UNION SELECT tablespace_name FROM all_tables WHERE tablespace_name IS NOT NULL
)
ORDER BY 1");}function
limit($H,$Z,$_,$D=0,$nh=" "){return($D?" * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $H$Z) t WHERE rownum <= ".($_+$D).") WHERE rnum > $D":($_!==null?" * FROM (SELECT $H$Z) WHERE rownum <= ".($_+$D):" $H$Z"));}function
limit1($R,$H,$Z,$nh="\n"){return" $H$Z";}function
db_collation($k,$hb){return
get_val("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");}function
logged_user(){return
get_val("SELECT USER FROM DUAL");}function
get_current_db(){global$g;$k=$g->_current_db?:DB;unset($g->_current_db);return$k;}function
where_owner($rg,$Sf="owner"){if(!$_GET["ns"])return'';return"$rg$Sf = sys_context('USERENV', 'CURRENT_SCHEMA')";}function
views_table($f){$Sf=where_owner('');return"(SELECT $f FROM all_views WHERE ".($Sf?:"rownum < 0").")";}function
tables_list(){$hj=views_table("view_name");$Sf=where_owner(" AND ");return
get_key_vals("SELECT table_name, 'table' FROM all_tables WHERE tablespace_name = ".q(DB)."$Sf
UNION SELECT view_name, 'view' FROM $hj
ORDER BY 1");}function
count_tables($j){$J=array();foreach($j
as$k)$J[$k]=get_val("SELECT COUNT(*) FROM all_tables WHERE tablespace_name = ".q($k));return$J;}function
table_status($C=""){$J=array();$gh=q($C);$k=get_current_db();$hj=views_table("view_name");$Sf=where_owner(" AND ");foreach(get_rows('SELECT table_name "Name", \'table\' "Engine", avg_row_len * num_rows "Data_length", num_rows "Rows" FROM all_tables WHERE tablespace_name = '.q($k).$Sf.($C!=""?" AND table_name = $gh":"")."
UNION SELECT view_name, 'view', 0, 0 FROM $hj".($C!=""?" WHERE view_name = $gh":"")."
ORDER BY 1")as$K){if($C!="")return$K;$J[$K["Name"]]=$K;}return$J;}function
is_view($S){return$S["Engine"]=="view";}function
fk_support($S){return
true;}function
fields($R){$J=array();$Sf=where_owner(" AND ");foreach(get_rows("SELECT * FROM all_tab_columns WHERE table_name = ".q($R)."$Sf ORDER BY column_id")as$K){$U=$K["DATA_TYPE"];$we="$K[DATA_PRECISION],$K[DATA_SCALE]";if($we==",")$we=$K["CHAR_COL_DECL_LENGTH"];$J[$K["COLUMN_NAME"]]=array("field"=>$K["COLUMN_NAME"],"full_type"=>$U.($we?"($we)":""),"type"=>strtolower($U),"length"=>$we,"default"=>$K["DATA_DEFAULT"],"null"=>($K["NULLABLE"]=="Y"),"privileges"=>array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1),);}return$J;}function
indexes($R,$h=null){$J=array();$Sf=where_owner(" AND ","aic.table_owner");foreach(get_rows("SELECT aic.*, ac.constraint_type, atc.data_default
FROM all_ind_columns aic
LEFT JOIN all_constraints ac ON aic.index_name = ac.constraint_name AND aic.table_name = ac.table_name AND aic.index_owner = ac.owner
LEFT JOIN all_tab_cols atc ON aic.column_name = atc.column_name AND aic.table_name = atc.table_name AND aic.index_owner = atc.owner
WHERE aic.table_name = ".q($R)."$Sf
ORDER BY ac.constraint_type, aic.column_position",$h)as$K){$Pd=$K["INDEX_NAME"];$jb=$K["DATA_DEFAULT"];$jb=($jb?trim($jb,'"'):$K["COLUMN_NAME"]);$J[$Pd]["type"]=($K["CONSTRAINT_TYPE"]=="P"?"PRIMARY":($K["CONSTRAINT_TYPE"]=="U"?"UNIQUE":"INDEX"));$J[$Pd]["columns"][]=$jb;$J[$Pd]["lengths"][]=($K["CHAR_LENGTH"]&&$K["CHAR_LENGTH"]!=$K["COLUMN_LENGTH"]?$K["CHAR_LENGTH"]:null);$J[$Pd]["descs"][]=($K["DESCEND"]&&$K["DESCEND"]=="DESC"?'1':null);}return$J;}function
view($C){$hj=views_table("view_name, text");$L=get_rows('SELECT text "select" FROM '.$hj.' WHERE view_name = '.q($C));return
reset($L);}function
collations(){return
array();}function
information_schema($k){return
get_schema()=="INFORMATION_SCHEMA";}function
error(){global$g;return
h($g->error);}function
explain($g,$H){$g->query("EXPLAIN PLAN FOR $H");return$g->query("SELECT * FROM plan_table");}function
found_rows($S,$Z){}function
auto_increment(){return"";}function
alter_table($R,$C,$p,$cd,$mb,$rc,$gb,$_a,$bg){$c=$bc=array();$Lf=($R?fields($R):array());foreach($p
as$o){$X=$o[1];if($X&&$o[0]!=""&&idf_escape($o[0])!=$X[0])queries("ALTER TABLE ".table($R)." RENAME COLUMN ".idf_escape($o[0])." TO $X[0]");$Kf=$Lf[$o[0]];if($X&&$Kf){$pf=process_field($Kf,$Kf);if($X[2]==$pf[2])$X[2]="";}if($X)$c[]=($R!=""?($o[0]!=""?"MODIFY (":"ADD ("):"  ").implode($X).($R!=""?")":"");else$bc[]=idf_escape($o[0]);}if($R=="")return
queries("CREATE TABLE ".table($C)." (\n".implode(",\n",$c)."\n)");return(!$c||queries("ALTER TABLE ".table($R)."\n".implode("\n",$c)))&&(!$bc||queries("ALTER TABLE ".table($R)." DROP (".implode(", ",$bc).")"))&&($R==$C||queries("ALTER TABLE ".table($R)." RENAME TO ".table($C)));}function
alter_indexes($R,$c){$bc=array();$Bg=array();foreach($c
as$X){if($X[0]!="INDEX"){$X[2]=preg_replace('~ DESC$~','',$X[2]);$i=($X[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($X[1]):"\nADD".($X[1]!=""?" CONSTRAINT ".idf_escape($X[1]):"")." $X[0] ".($X[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$X[2]).")");array_unshift($Bg,"ALTER TABLE ".table($R).$i);}elseif($X[2]=="DROP")$bc[]=idf_escape($X[1]);else$Bg[]="CREATE INDEX ".idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R)." (".implode(", ",$X[2]).")";}if($bc)array_unshift($Bg,"DROP INDEX ".implode(", ",$bc));foreach($Bg
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
drop_views($ij){return
apply_queries("DROP VIEW",$ij);}function
drop_tables($T){return
apply_queries("DROP TABLE",$T);}function
last_id($I){return
0;}function
schemas(){$J=get_vals("SELECT DISTINCT owner FROM dba_segments WHERE owner IN (SELECT username FROM dba_users WHERE default_tablespace NOT IN ('SYSTEM','SYSAUX')) ORDER BY 1");return($J?:get_vals("SELECT DISTINCT owner FROM all_tables WHERE tablespace_name = ".q(DB)." ORDER BY 1"));}function
get_schema(){return
get_val("SELECT sys_context('USERENV', 'SESSION_USER') FROM dual");}function
set_schema($fh,$h=null){global$g;if(!$h)$h=$g;return$h->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($fh));}function
show_variables(){return
get_rows('SELECT name, display_value FROM v$parameter');}function
show_status(){$J=array();$L=get_rows('SELECT * FROM v$instance');foreach(reset($L)as$z=>$X)$J[]=array($z,$X);return$J;}function
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
convert_field($o){}function
unconvert_field($o,$J){return$J;}function
support($Oc){return
preg_match('~^(columns|database|drop_col|indexes|descidx|processlist|scheme|sql|status|table|variables|view)$~',$Oc);}}$ac["mssql"]="MS SQL";if(isset($_GET["mssql"])){define('Adminer\DRIVER',"mssql");if(extension_loaded("sqlsrv")&&$_GET["ext"]!="pdo"){class
Db{var$extension="sqlsrv",$flavor='',$server_info,$affected_rows,$errno,$error;private$link,$result;private
function
get_error(){$this->error="";foreach(sqlsrv_errors()as$n){$this->errno=$n["code"];$this->error.="$n[message]\n";}$this->error=rtrim($this->error);}function
connect($N,$V,$F){global$b;$sb=array("UID"=>$V,"PWD"=>$F,"CharacterSet"=>"UTF-8");$Kh=$b->connectSsl();if(isset($Kh["Encrypt"]))$sb["Encrypt"]=$Kh["Encrypt"];if(isset($Kh["TrustServerCertificate"]))$sb["TrustServerCertificate"]=$Kh["TrustServerCertificate"];$k=$b->database();if($k!="")$sb["Database"]=$k;$this->link=@sqlsrv_connect(preg_replace('~:~',',',$N),$sb);if($this->link){$Td=sqlsrv_server_info($this->link);$this->server_info=$Td['SQLServerVersion'];}else$this->get_error();return(bool)$this->link;}function
quote($Q){$Li=strlen($Q)!=strlen(utf8_decode($Q));return($Li?"N":"")."'".str_replace("'","''",$Q)."'";}function
select_db($Jb){return$this->query(use_sql($Jb));}function
query($H,$Ki=false){$I=sqlsrv_query($this->link,$H);$this->error="";if(!$I){$this->get_error();return
false;}return$this->store_result($I);}function
multi_query($H){$this->result=sqlsrv_query($this->link,$H);$this->error="";if(!$this->result){$this->get_error();return
false;}return
true;}function
store_result($I=null){if(!$I)$I=$this->result;if(!$I)return
false;if(sqlsrv_field_metadata($I))return
new
Result($I);$this->affected_rows=sqlsrv_rows_affected($I);return
true;}function
next_result(){return$this->result?sqlsrv_next_result($this->result):null;}function
result($H,$o=0){$I=$this->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return$K[$o];}}class
Result{var$num_rows;private$result,$offset=0,$fields;function
__construct($I){$this->result=$I;}private
function
convert($K){foreach((array)$K
as$z=>$X){if(is_a($X,'DateTime'))$K[$z]=$X->format("Y-m-d H:i:s");}return$K;}function
fetch_assoc(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_ASSOC));}function
fetch_row(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_NUMERIC));}function
fetch_field(){if(!$this->fields)$this->fields=sqlsrv_field_metadata($this->result);$o=$this->fields[$this->offset++];$J=new
\stdClass;$J->name=$o["Name"];$J->type=($o["Type"]==1?254:15);$J->charsetnr=0;return$J;}function
seek($D){for($u=0;$u<$D;$u++)sqlsrv_fetch($this->result);}function
__destruct(){sqlsrv_free_stmt($this->result);}}function
last_id($I){return
get_val("SELECT SCOPE_IDENTITY()");}function
explain($g,$H){$g->query("SET SHOWPLAN_ALL ON");$J=$g->query($H);$g->query("SET SHOWPLAN_ALL OFF");return$J;}}else{class
MssqlDb
extends
PdoDb{function
select_db($Jb){return$this->query(use_sql($Jb));}function
lastInsertId(){return$this->pdo->lastInsertId();}}function
last_id($I){global$g;return$g->lastInsertId();}function
explain($g,$H){}if(extension_loaded("pdo_sqlsrv")){class
Db
extends
MssqlDb{var$extension="PDO_SQLSRV";function
connect($N,$V,$F){$this->dsn("sqlsrv:Server=".str_replace(":",",",$N),$V,$F);return
true;}}}elseif(extension_loaded("pdo_dblib")){class
Db
extends
MssqlDb{var$extension="PDO_DBLIB";function
connect($N,$V,$F){$this->dsn("dblib:charset=utf8;host=".str_replace(":",";unix_socket=",preg_replace('~:(\d)~',';port=\1',$N)),$V,$F);return
true;}}}}class
Driver
extends
SqlDriver{static$pg=array("SQLSRV","PDO_SQLSRV","PDO_DBLIB");static$he="mssql";var$editFunctions=array(array("date|time"=>"getdate",),array("int|decimal|real|float|money|datetime"=>"+/-","char|text"=>"+",));var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");var$functions=array("len","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$onActions="NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$generated=array("PERSISTED","VIRTUAL");function
__construct($g){parent::__construct($g);$this->types=array('Numbers'=>array("tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20),'Date and time'=>array("date"=>10,"smalldatetime"=>19,"datetime"=>19,"datetime2"=>19,"time"=>8,"datetimeoffset"=>10),'Strings'=>array("char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823),'Binary'=>array("binary"=>8000,"varbinary"=>8000,"image"=>2147483647),);}function
insertUpdate($R,$L,$G){$p=fields($R);$Si=array();$Z=array();$O=reset($L);$f="c".implode(", c",range(1,count($O)));$Oa=0;$Xd=array();foreach($O
as$z=>$X){$Oa++;$C=idf_unescape($z);if(!$p[$C]["auto_increment"])$Xd[$z]="c$Oa";if(isset($G[$C]))$Z[]="$z = c$Oa";else$Si[]="$z = c$Oa";}$dj=array();foreach($L
as$O)$dj[]="(".implode(", ",$O).")";if($Z){$Id=queries("SET IDENTITY_INSERT ".table($R)." ON");$J=queries("MERGE ".table($R)." USING (VALUES\n\t".implode(",\n\t",$dj)."\n) AS source ($f) ON ".implode(" AND ",$Z).($Si?"\nWHEN MATCHED THEN UPDATE SET ".implode(", ",$Si):"")."\nWHEN NOT MATCHED THEN INSERT (".implode(", ",array_keys($Id?$O:$Xd)).") VALUES (".($Id?$f:implode(", ",$Xd)).");");if($Id)queries("SET IDENTITY_INSERT ".table($R)." OFF");}else$J=queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($O)).") VALUES\n".implode(",\n",$dj));return$J;}function
begin(){return
queries("BEGIN TRANSACTION");}function
tableHelp($C,$fe=false){$ze=array("sys"=>"catalog-views/sys-","INFORMATION_SCHEMA"=>"information-schema-views/",);$A=$ze[get_schema()];if($A)return"relational-databases/system-$A".preg_replace('~_~','-',strtolower($C))."-transact-sql";}}function
idf_escape($w){return"[".str_replace("]","]]",$w)."]";}function
table($w){return($_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($w);}function
connect($Bb){$g=new
Db;if($Bb[0]=="")$Bb[0]="localhost:1433";if($g->connect($Bb[0],$Bb[1],$Bb[2]))return$g;return$g->error;}function
get_databases(){return
get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");}function
limit($H,$Z,$_,$D=0,$nh=" "){return($_!==null?" TOP (".($_+$D).")":"")." $H$Z";}function
limit1($R,$H,$Z,$nh="\n"){return
limit($H,$Z,1,0,$nh);}function
db_collation($k,$hb){return
get_val("SELECT collation_name FROM sys.databases WHERE name = ".q($k));}function
logged_user(){return
get_val("SELECT SUSER_NAME()");}function
tables_list(){return
get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");}function
count_tables($j){global$g;$J=array();foreach($j
as$k){$g->select_db($k);$J[$k]=get_val("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");}return$J;}function
table_status($C=""){$J=array();foreach(get_rows("SELECT ao.name AS Name, ao.type_desc AS Engine, (SELECT value FROM fn_listextendedproperty(default, 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ".($C!=""?"AND name = ".q($C):"ORDER BY name"))as$K){if($C!="")return$K;$J[$K["Name"]]=$K;}return$J;}function
is_view($S){return$S["Engine"]=="VIEW";}function
fk_support($S){return
true;}function
fields($R){$ob=get_key_vals("SELECT objname, cast(value as varchar(max)) FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', ".q(get_schema()).", 'table', ".q($R).", 'column', NULL)");$J=array();$Wh=get_val("SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') AND name = ".q($R));foreach(get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name, t.name type, d.definition [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = ".q($Wh))as$K){$U=$K["type"];$we=(preg_match("~char|binary~",$U)?$K["max_length"]/($U[0]=='n'?2:1):($U=="decimal"?"$K[precision],$K[scale]":""));$J[$K["name"]]=array("field"=>$K["name"],"full_type"=>$U.($we?"($we)":""),"type"=>$U,"length"=>$we,"default"=>(preg_match("~^\('(.*)'\)$~",$K["default"],$B)?str_replace("''","'",$B[1]):$K["default"]),"default_constraint"=>$K["default_constraint"],"null"=>$K["is_nullable"],"auto_increment"=>$K["is_identity"],"collation"=>$K["collation_name"],"privileges"=>array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1),"primary"=>$K["is_primary_key"],"comment"=>$ob[$K["name"]],);}foreach(get_rows("SELECT * FROM sys.computed_columns WHERE object_id = ".q($Wh))as$K){$J[$K["name"]]["generated"]=($K["is_persisted"]?"PERSISTED":"VIRTUAL");$J[$K["name"]]["default"]=$K["definition"];}return$J;}function
indexes($R,$h=null){$J=array();foreach(get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = ".q($R),$h)as$K){$C=$K["name"];$J[$C]["type"]=($K["is_primary_key"]?"PRIMARY":($K["is_unique"]?"UNIQUE":"INDEX"));$J[$C]["lengths"]=array();$J[$C]["columns"][$K["key_ordinal"]]=$K["column_name"];$J[$C]["descs"][$K["key_ordinal"]]=($K["is_descending_key"]?'1':null);}return$J;}function
view($C){return
array("select"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',get_val("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = ".q($C))));}function
collations(){$J=array();foreach(get_vals("SELECT name FROM fn_helpcollations()")as$gb)$J[preg_replace('~_.*~','',$gb)][]=$gb;return$J;}function
information_schema($k){return
get_schema()=="INFORMATION_SCHEMA";}function
error(){global$g;return
nl_br(h(preg_replace('~^(\[[^]]*])+~m','',$g->error)));}function
create_database($k,$gb){return
queries("CREATE DATABASE ".idf_escape($k).(preg_match('~^[a-z0-9_]+$~i',$gb)?" COLLATE $gb":""));}function
drop_databases($j){return
queries("DROP DATABASE ".implode(", ",array_map('Adminer\idf_escape',$j)));}function
rename_database($C,$gb){if(preg_match('~^[a-z0-9_]+$~i',$gb))queries("ALTER DATABASE ".idf_escape(DB)." COLLATE $gb");queries("ALTER DATABASE ".idf_escape(DB)." MODIFY NAME = ".idf_escape($C));return
true;}function
auto_increment(){return" IDENTITY".($_POST["Auto_increment"]!=""?"(".number($_POST["Auto_increment"]).",1)":"")." PRIMARY KEY";}function
alter_table($R,$C,$p,$cd,$mb,$rc,$gb,$_a,$bg){$c=array();$ob=array();$Lf=fields($R);foreach($p
as$o){$e=idf_escape($o[0]);$X=$o[1];if(!$X)$c["DROP"][]=" COLUMN $e";else{$X[1]=preg_replace("~( COLLATE )'(\\w+)'~",'\1\2',$X[1]);$ob[$o[0]]=$X[5];unset($X[5]);if(preg_match('~ AS ~',$X[3]))unset($X[1],$X[2]);if($o[0]=="")$c["ADD"][]="\n  ".implode("",$X).($R==""?substr($cd[$X[0]],16+strlen($X[0])):"");else{$l=$X[3];unset($X[3]);unset($X[6]);if($e!=$X[0])queries("EXEC sp_rename ".q(table($R).".$e").", ".q(idf_unescape($X[0])).", 'COLUMN'");$c["ALTER COLUMN ".implode("",$X)][]="";$Kf=$Lf[$o[0]];if(default_value($Kf)!=$l){if($Kf["default"]!==null)$c["DROP"][]=" ".idf_escape($Kf["default_constraint"]);if($l)$c["ADD"][]="\n $l FOR $e";}}}}if($R=="")return
queries("CREATE TABLE ".table($C)." (".implode(",",(array)$c["ADD"])."\n)");if($R!=$C)queries("EXEC sp_rename ".q(table($R)).", ".q($C));if($cd)$c[""]=$cd;foreach($c
as$z=>$X){if(!queries("ALTER TABLE ".table($C)." $z".implode(",",$X)))return
false;}foreach($ob
as$z=>$X){$mb=substr($X,9);queries("EXEC sp_dropextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($C).", @level2type = N'Column', @level2name = ".q($z));queries("EXEC sp_addextendedproperty
@name = N'MS_Description',
@value = $mb,
@level0type = N'Schema',
@level0name = ".q(get_schema()).",
@level1type = N'Table',
@level1name = ".q($C).",
@level2type = N'Column',
@level2name = ".q($z));}return
true;}function
alter_indexes($R,$c){$x=array();$bc=array();foreach($c
as$X){if($X[2]=="DROP"){if($X[0]=="PRIMARY")$bc[]=idf_escape($X[1]);else$x[]=idf_escape($X[1])." ON ".table($R);}elseif(!queries(($X[0]!="PRIMARY"?"CREATE $X[0] ".($X[0]!="INDEX"?"INDEX ":"").idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R):"ALTER TABLE ".table($R)." ADD PRIMARY KEY")." (".implode(", ",$X[2]).")"))return
false;}return(!$x||queries("DROP INDEX ".implode(", ",$x)))&&(!$bc||queries("ALTER TABLE ".table($R)." DROP ".implode(", ",$bc)));}function
found_rows($S,$Z){}function
foreign_keys($R){$J=array();$wf=array("CASCADE","NO ACTION","SET NULL","SET DEFAULT");foreach(get_rows("EXEC sp_fkeys @fktable_name = ".q($R).", @fktable_owner = ".q(get_schema()))as$K){$r=&$J[$K["FK_NAME"]];$r["db"]=$K["PKTABLE_QUALIFIER"];$r["ns"]=$K["PKTABLE_OWNER"];$r["table"]=$K["PKTABLE_NAME"];$r["on_update"]=$wf[$K["UPDATE_RULE"]];$r["on_delete"]=$wf[$K["DELETE_RULE"]];$r["source"][]=$K["FKCOLUMN_NAME"];$r["target"][]=$K["PKCOLUMN_NAME"];}return$J;}function
truncate_tables($T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views($ij){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$ij)));}function
drop_tables($T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables($T,$ij,$fi){return
apply_queries("ALTER SCHEMA ".idf_escape($fi)." TRANSFER",array_merge($T,$ij));}function
trigger($C){if($C=="")return
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
set_schema($dh){$_GET["ns"]=$dh;return
true;}function
create_sql($R,$_a,$Ph){global$m;if(is_view(table_status($R))){$hj=view($R);return"CREATE VIEW ".table($R)." AS $hj[select]";}$p=array();$G=false;foreach(fields($R)as$C=>$o){$X=process_field($o,$o);if($X[6])$G=true;$p[]=implode("",$X);}foreach(indexes($R)as$C=>$x){if(!$G||$x["type"]!="PRIMARY"){$f=array();foreach($x["columns"]as$z=>$X)$f[]=idf_escape($X).($x["descs"][$z]?" DESC":"");$C=idf_escape($C);$p[]=($x["type"]=="INDEX"?"INDEX $C":"CONSTRAINT $C ".($x["type"]=="UNIQUE"?"UNIQUE":"PRIMARY KEY"))." (".implode(", ",$f).")";}}foreach($m->checkConstraints($R)as$C=>$Ua)$p[]="CONSTRAINT ".idf_escape($C)." CHECK ($Ua)";return"CREATE TABLE ".table($R)." (\n\t".implode(",\n\t",$p)."\n)";}function
foreign_keys_sql($R){$p=array();foreach(foreign_keys($R)as$cd)$p[]=ltrim(format_foreign_key($cd));return($p?"ALTER TABLE ".table($R)." ADD\n\t".implode(",\n\t",$p).";\n\n":"");}function
truncate_sql($R){return"TRUNCATE TABLE ".table($R);}function
use_sql($Jb){return"USE ".idf_escape($Jb);}function
trigger_sql($R){$J="";foreach(triggers($R)as$C=>$Di)$J.=create_trigger(" ON ".table($R),trigger($C)).";";return$J;}function
convert_field($o){}function
unconvert_field($o,$J){return$J;}function
support($Oc){return
preg_match('~^(check|comment|columns|database|drop_col|dump|indexes|descidx|scheme|sql|table|trigger|view|view_trigger)$~',$Oc);}}class
Adminer{var$operators;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($i=false){return
password_file($i);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
serverName($N){return
h($N);}function
database(){return
DB;}function
databases($ad=true){return
get_databases($ad);}function
schemas(){return
schemas();}function
queryTimeout(){return
2;}function
headers(){}function
csp(){return
csp();}function
head($Gb=null){return
true;}function
css(){$J=array();foreach(array("","-dark")as$Xe){$q="adminer$Xe.css";if(file_exists($q))$J[]="$q?v=".crc32(file_get_contents($q));}return$J;}function
loginForm(){global$ac;echo"<table class='layout'>\n",$this->loginFormField('driver','<tr><th>'.'System'.'<td>',html_select("auth[driver]",$ac,DRIVER,"loginDriver(this);")),$this->loginFormField('server','<tr><th>'.'Server'.'<td>','<input name="auth[server]" value="'.h(SERVER).'" title="hostname[:port]" placeholder="localhost" autocapitalize="off">'),$this->loginFormField('username','<tr><th>'.'Username'.'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'.script("qs('#username').form['auth[driver]'].onchange();")),$this->loginFormField('password','<tr><th>'.'Password'.'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),$this->loginFormField('db','<tr><th>'.'Database'.'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".'Login'."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],'Permanent login')."\n";}function
loginFormField($C,$Bd,$Y){return$Bd.$Y."\n";}function
login($Ae,$F){if($F=="")return
sprintf('Adminer does not support accessing a database without a password, <a href="https://www.adminer.org/en/password/"%s>more information</a>.',target_blank());return
true;}function
tableName($Vh){return
h($Vh["Name"]);}function
fieldName($o,$Ef=0){$U=$o["full_type"];$mb=$o["comment"];return'<span title="'.h($U.($mb!=""?($U?": ":"").$mb:'')).'">'.h($o["field"]).'</span>';}function
selectLinks($Vh,$O=""){global$m;echo'<p class="links">';$ze=array("select"=>'Select data');if(support("table")||support("indexes"))$ze["table"]='Show structure';$fe=false;if(support("table")){$fe=is_view($Vh);if($fe)$ze["view"]='Alter view';else$ze["create"]='Alter table';}if($O!==null)$ze["edit"]='New item';$C=$Vh["Name"];foreach($ze
as$z=>$X)echo" <a href='".h(ME)."$z=".urlencode($C).($z=="edit"?$O:"")."'".bold(isset($_GET[$z])).">$X</a>";echo
doc_link(array(JUSH=>$m->tableHelp($C,$fe)),"?"),"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$Uh){return
array();}function
backwardKeysPrint($Da,$K){}function
selectQuery($H,$Lh,$Mc=false){global$m;$J="</p>\n";if(!$Mc&&($lj=$m->warnings())){$v="warnings";$J=", <a href='#$v'>".'Warnings'."</a>".script("qsl('a').onclick = partial(toggle, '$v');","")."$J<div id='$v' class='hidden'>\n$lj</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>(".format_time($Lh).")</span>".(support("sql")?" <a href='".h(ME)."sql=".urlencode($H)."'>".'Edit'."</a>":"").$J;}function
sqlCommandQuery($H){return
shorten_utf8(trim($H),1000);}function
sqlPrintAfter(){}function
rowDescription($R){return"";}function
rowDescriptions($L,$dd){return$L;}function
selectLink($X,$o){}function
selectVal($X,$A,$o,$Of){$J=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$o["type"])&&!preg_match("~var~",$o["type"])?"<code>$X</code>":(preg_match('~json~',$o["type"])?"<code class='jush-js'>$X</code>":$X)));if(preg_match('~blob|bytea|raw|file~',$o["type"])&&!is_utf8($X))$J="<i>".lang(array('%d byte','%d bytes'),strlen($Of))."</i>";return($A?"<a href='".h($A)."'".(is_url($A)?target_blank():"").">$J</a>":$J);}function
editVal($X,$o){return$X;}function
tableStructurePrint($p,$Vh=null){global$m;echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".'Column'."<td>".'Type'.(support("comment")?"<td>".'Comment':"")."</thead>\n";$Oh=$m->structuredTypes();foreach($p
as$o){echo"<tr><th>".h($o["field"]);$U=h($o["full_type"]);$gb=h($o["collation"]);echo"<td><span title='$gb'>".(in_array($U,(array)$Oh['User types'])?"<a href='".h(ME.'type='.urlencode($U))."'>$U</a>":$U.($gb&&isset($Vh["Collation"])&&$gb!=$Vh["Collation"]?" $gb":""))."</span>",($o["null"]?" <i>NULL</i>":""),($o["auto_increment"]?" <i>".'Auto Increment'."</i>":"");$l=h($o["default"]);echo(isset($o["default"])?" <span title='".'Default value'."'>[<b>".($o["generated"]?"<code class='jush-".JUSH."'>$l</code>":$l)."</b>]</span>":""),(support("comment")?"<td>".h($o["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint($y){echo"<table>\n";foreach($y
as$C=>$x){ksort($x["columns"]);$ug=array();foreach($x["columns"]as$z=>$X)$ug[]="<i>".h($X)."</i>".($x["lengths"][$z]?"(".$x["lengths"][$z].")":"").($x["descs"][$z]?" DESC":"");echo"<tr title='".h($C)."'><th>$x[type]<td>".implode(", ",$ug)."\n";}echo"</table>\n";}function
selectColumnsPrint($M,$f){global$m;print_fieldset("select",'Select',$M);$u=0;$M[""]=array();foreach($M
as$z=>$X){$X=$_GET["columns"][$z];$e=select_input(" name='columns[$u][col]'",$f,$X["col"],($z!==""?"selectFieldChange":"selectAddRow"));echo"<div>".($m->functions||$m->grouping?html_select("columns[$u][fun]",array(-1=>"")+array_filter(array('Functions'=>$m->functions,'Aggregation'=>$m->grouping)),$X["fun"]).on_help("event.target.value && event.target.value.replace(/ |\$/, '(') + ')'",1).script("qsl('select').onchange = function () { helpClose();".($z!==""?"":" qsl('select, input', this.parentNode).onchange();")." };","")."($e)":$e)."</div>\n";$u++;}echo"</div></fieldset>\n";}function
selectSearchPrint($Z,$f,$y){print_fieldset("search",'Search',$Z);foreach($y
as$u=>$x){if($x["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$x["columns"]))."</i>) AGAINST"," <input type='search' name='fulltext[$u]' value='".h($_GET["fulltext"][$u])."'>",script("qsl('input').oninput = selectFieldChange;",""),checkbox("boolean[$u]",1,isset($_GET["boolean"][$u]),"BOOL"),"</div>\n";}$Sa="this.parentNode.firstChild.onchange();";foreach(array_merge((array)$_GET["where"],array(array()))as$u=>$X){if(!$X||("$X[col]$X[val]"!=""&&in_array($X["op"],$this->operators)))echo"<div>".select_input(" name='where[$u][col]'",$f,$X["col"],($X?"selectFieldChange":"selectAddRow"),"(".'anywhere'.")"),html_select("where[$u][op]",$this->operators,$X["op"],$Sa),"<input type='search' name='where[$u][val]' value='".h($X["val"])."'>",script("mixin(qsl('input'), {oninput: function () { $Sa }, onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});",""),"</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint($Ef,$f,$y){print_fieldset("sort",'Sort',$Ef);$u=0;foreach((array)$_GET["order"]as$z=>$X){if($X!=""){echo"<div>".select_input(" name='order[$u]'",$f,$X,"selectFieldChange"),checkbox("desc[$u]",1,isset($_GET["desc"][$z]),'descending')."</div>\n";$u++;}}echo"<div>".select_input(" name='order[$u]'",$f,"","selectAddRow"),checkbox("desc[$u]",1,false,'descending')."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($_){echo"<fieldset><legend>".'Limit'."</legend><div>","<input type='number' name='limit' class='size' value='".h($_)."'>",script("qsl('input').oninput = selectFieldChange;",""),"</div></fieldset>\n";}function
selectLengthPrint($li){if($li!==null)echo"<fieldset><legend>".'Text length'."</legend><div>","<input type='number' name='text_length' class='size' value='".h($li)."'>","</div></fieldset>\n";}function
selectActionPrint($y){echo"<fieldset><legend>".'Action'."</legend><div>","<input type='submit' value='".'Select'."'>"," <span id='noindex' title='".'Full table scan'."'></span>","<script".nonce().">\n","const indexColumns = ";$f=array();foreach($y
as$x){$Fb=reset($x["columns"]);if($x["type"]!="FULLTEXT"&&$Fb)$f[$Fb]=1;}$f[""]=1;foreach($f
as$z=>$X)json_row($z);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint($oc,$f){}function
selectColumnsProcess($f,$y){global$m;$M=array();$pd=array();foreach((array)$_GET["columns"]as$z=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],$m->functions)||in_array($X["fun"],$m->grouping)))){$M[$z]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],$m->grouping))$pd[]=$M[$z];}}return
array($M,$pd);}function
selectSearchProcess($p,$y){global$g,$m;$J=array();foreach($y
as$u=>$x){if($x["type"]=="FULLTEXT"&&$_GET["fulltext"][$u]!="")$J[]="MATCH (".implode(", ",array_map('Adminer\idf_escape',$x["columns"])).") AGAINST (".q($_GET["fulltext"][$u]).(isset($_GET["boolean"][$u])?" IN BOOLEAN MODE":"").")";}foreach((array)$_GET["where"]as$z=>$X){if("$X[col]$X[val]"!=""&&in_array($X["op"],$this->operators)){$rg="";$pb=" $X[op]";if(preg_match('~IN$~',$X["op"])){$Md=process_length($X["val"]);$pb.=" ".($Md!=""?$Md:"(NULL)");}elseif($X["op"]=="SQL")$pb=" $X[val]";elseif($X["op"]=="LIKE %%")$pb=" LIKE ".$this->processInput($p[$X["col"]],"%$X[val]%");elseif($X["op"]=="ILIKE %%")$pb=" ILIKE ".$this->processInput($p[$X["col"]],"%$X[val]%");elseif($X["op"]=="FIND_IN_SET"){$rg="$X[op](".q($X["val"]).", ";$pb=")";}elseif(!preg_match('~NULL$~',$X["op"]))$pb.=" ".$this->processInput($p[$X["col"]],$X["val"]);if($X["col"]!="")$J[]=$rg.$m->convertSearch(idf_escape($X["col"]),$X,$p[$X["col"]]).$pb;else{$ib=array();foreach($p
as$C=>$o){if(isset($o["privileges"]["where"])&&(preg_match('~^[-\d.'.(preg_match('~IN$~',$X["op"])?',':'').']+$~',$X["val"])||!preg_match('~'.number_type().'|bit~',$o["type"]))&&(!preg_match("~[\x80-\xFF]~",$X["val"])||preg_match('~char|text|enum|set~',$o["type"]))&&(!preg_match('~date|timestamp~',$o["type"])||preg_match('~^\d+-\d+-\d+~',$X["val"])))$ib[]=$rg.$m->convertSearch(idf_escape($C),$X,$o).$pb;}$J[]=($ib?"(".implode(" OR ",$ib).")":"1 = 0");}}}return$J;}function
selectOrderProcess($p,$y){$J=array();foreach((array)$_GET["order"]as$z=>$X){if($X!="")$J[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$z])?" DESC":"");}return$J;}function
selectLimitProcess(){return(isset($_GET["limit"])?$_GET["limit"]:"50");}function
selectLengthProcess(){return(isset($_GET["text_length"])?$_GET["text_length"]:"100");}function
selectEmailProcess($Z,$dd){return
false;}function
selectQueryBuild($M,$Z,$pd,$Ef,$_,$E){return"";}function
messageQuery($H,$mi,$Mc=false){global$m;restart_session();$Dd=&get_session("queries");if(!$Dd[$_GET["db"]])$Dd[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\n…";$Dd[$_GET["db"]][]=array($H,time(),$mi);$Hh="sql-".count($Dd[$_GET["db"]]);$J="<a href='#$Hh' class='toggle'>".'SQL command'."</a>\n";if(!$Mc&&($lj=$m->warnings())){$v="warnings-".count($Dd[$_GET["db"]]);$J="<a href='#$v' class='toggle'>".'Warnings'."</a>, $J<div id='$v' class='hidden'>\n$lj</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$Hh' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1000)."</code></pre>".($mi?" <span class='time'>($mi)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".urlencode(DB),"db=".urlencode($_GET["db"]),ME).'sql=&history='.(count($Dd[$_GET["db"]])-1)).'">'.'Edit'.'</a>':'').'</div>';}function
editRowPrint($R,$p,$K,$Si){}function
editFunctions($o){global$m;$J=($o["null"]?"NULL/":"");$Si=isset($_GET["select"])||where($_GET);foreach($m->editFunctions
as$z=>$kd){if(!$z||(!isset($_GET["call"])&&$Si)){foreach($kd
as$fg=>$X){if(!$fg||preg_match("~$fg~",$o["type"]))$J.="/$X";}}if($z&&!preg_match('~set|blob|bytea|raw|file|bool~',$o["type"]))$J.="/SQL";}if($o["auto_increment"]&&!$Si)$J='Auto Increment';return
explode("/",$J);}function
editInput($R,$o,$ya,$Y){if($o["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$ya value='-1' checked><i>".'original'."</i></label> ":"").($o["null"]?"<label><input type='radio'$ya value=''".($Y!==null||isset($_GET["select"])?"":" checked")."><i>NULL</i></label> ":"").enum_input("radio",$ya,$o,$Y,$Y===0?0:null);return"";}function
editHint($R,$o,$Y){return"";}function
processInput($o,$Y,$t=""){if($t=="SQL")return$Y;$C=$o["field"];$J=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$t))$J="$t()";elseif(preg_match('~^current_(date|timestamp)$~',$t))$J=$t;elseif(preg_match('~^([+-]|\|\|)$~',$t))$J=idf_escape($C)." $t $J";elseif(preg_match('~^[+-] interval$~',$t))$J=idf_escape($C)." $t ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)?$Y:$J);elseif(preg_match('~^(addtime|subtime|concat)$~',$t))$J="$t(".idf_escape($C).", $J)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$t))$J="$t($J)";return
unconvert_field($o,$J);}function
dumpOutput(){$J=array('text'=>'open','file'=>'save');if(function_exists('gzencode'))$J['gz']='gzip';return$J;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpDatabase($k){}function
dumpTable($R,$Ph,$fe=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Ph)dump_csv(array_keys(fields($R)));}else{if($fe==2){$p=array();foreach(fields($R)as$C=>$o)$p[]=idf_escape($C)." $o[full_type]";$i="CREATE TABLE ".table($R)." (".implode(", ",$p).")";}else$i=create_sql($R,$_POST["auto_increment"],$Ph);set_utf8mb4($i);if($Ph&&$i){if($Ph=="DROP+CREATE"||$fe==1)echo"DROP ".($fe==2?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($fe==1)$i=remove_definer($i);echo"$i;\n\n";}}}function
dumpData($R,$Ph,$H){global$g;if($Ph){$Ie=(JUSH=="sqlite"?0:1048576);$p=array();$Jd=false;if($_POST["format"]=="sql"){if($Ph=="TRUNCATE+INSERT")echo
truncate_sql($R).";\n";$p=fields($R);if(JUSH=="mssql"){foreach($p
as$o){if($o["auto_increment"]){echo"SET IDENTITY_INSERT ".table($R)." ON;\n";$Jd=true;break;}}}}$I=$g->query($H,1);if($I){$Xd="";$Na="";$ke=array();$ld=array();$Rh="";$Pc=($R!=''?'fetch_assoc':'fetch_row');while($K=$I->$Pc()){if(!$ke){$dj=array();foreach($K
as$X){$o=$I->fetch_field();if($p[$o->name]['generated']){$ld[$o->name]=true;continue;}$ke[]=$o->name;$z=idf_escape($o->name);$dj[]="$z = VALUES($z)";}$Rh=($Ph=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$dj):"").";\n";}if($_POST["format"]!="sql"){if($Ph=="table"){dump_csv($ke);$Ph="INSERT";}dump_csv($K);}else{if(!$Xd)$Xd="INSERT INTO ".table($R)." (".implode(", ",array_map('Adminer\idf_escape',$ke)).") VALUES";foreach($K
as$z=>$X){if($ld[$z]){unset($K[$z]);continue;}$o=$p[$z];$K[$z]=($X!==null?unconvert_field($o,preg_match(number_type(),$o["type"])&&!preg_match('~\[~',$o["full_type"])&&is_numeric($X)?$X:q(($X===false?0:$X))):"NULL");}$bh=($Ie?"\n":" ")."(".implode(",\t",$K).")";if(!$Na)$Na=$Xd.$bh;elseif(strlen($Na)+4+strlen($bh)+strlen($Rh)<$Ie)$Na.=",$bh";else{echo$Na.$Rh;$Na=$Xd.$bh;}}}if($Na)echo$Na.$Rh;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",$g->error)."\n";if($Jd)echo"SET IDENTITY_INSERT ".table($R)." OFF;\n";}}function
dumpFilename($Hd){return
friendly_url($Hd!=""?$Hd:(SERVER!=""?SERVER:"localhost"));}function
dumpHeaders($Hd,$Ye=false){$Rf=$_POST["output"];$Hc=(preg_match('~sql~',$_POST["format"])?"sql":($Ye?"tar":"csv"));header("Content-Type: ".($Rf=="gz"?"application/x-gzip":($Hc=="tar"?"application/x-tar":($Hc=="sql"||$Rf!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($Rf=="gz"){ob_start(function($Q){return
gzencode($Q);},1e6);}return$Hc;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.'Alter database'."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?'Alter schema':'Create schema')."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.'Database schema'."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".'Privileges'."</a>\n":"");return
true;}function
navigation($We){global$ia,$ac,$g;echo"<h1>".$this->name()." <span class='version'>$ia";$gf=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare($ia,$gf)<0?h($gf):"")."</a>","</span></h1>\n";if($We=="auth"){$Rf="";foreach((array)$_SESSION["pwds"]as$fj=>$sh){foreach($sh
as$N=>$aj){$C=h(get_setting("vendor-$fj-$N")?:$ac[$fj]);foreach($aj
as$V=>$F){if($F!==null){$Mb=$_SESSION["db"][$fj][$N][$V];foreach(($Mb?array_keys($Mb):array(""))as$k)$Rf.="<li><a href='".h(auth_url($fj,$N,$V,$k))."'>($C) ".h($V.($N!=""?"@".$this->serverName($N):"").($k!=""?" - $k":""))."</a>\n";}}}}if($Rf)echo"<ul id='logins'>\n$Rf</ul>\n".script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");}else{$T=array();if($_GET["ns"]!==""&&!$We&&DB!=""){$g->select_db(DB);$T=table_status('',true);}$this->syntaxHighlighting($T);$this->databasesPrint($We);$la=array();if(DB==""||!$We){if(support("sql")){$la[]="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".'SQL command'."</a>";$la[]="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".'Import'."</a>";}$la[]="<a href='".h(ME)."dump=".urlencode(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".'Export'."</a>";}$Nd=$_GET["ns"]!==""&&!$We&&DB!="";if($Nd)$la[]='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".'Create table'."</a>";echo($la?"<p class='links'>\n".implode("\n",$la)."\n":"");if($Nd){if($T)$this->tablesPrint($T);else
echo"<p class='message'>".'No tables.'."</p>\n";}}}function
syntaxHighlighting($T){global$g;echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=5.1.0");if(support("sql")){echo"<script".nonce().">\n";if($T){$ze=array();foreach($T
as$R=>$U)$ze[]=preg_quote($R,'/');echo"var jushLinks = { ".JUSH.": [ '".js_escape(ME).(support("table")?"table=":"select=")."\$&', /\\b(".implode("|",$ze).")\\b/g ] };\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";}echo"</script>\n";}echo
script("syntaxHighlighting('".(is_object($g)?preg_replace('~^(\d\.?\d).*~s','\1',$g->server_info):"")."'".($g->flavor=='maria'?", 'maria'":($g->flavor=='cockroach'?", 'cockroach'":"")).");");}function
databasesPrint($We){global$b,$g;$j=$this->databases();if(DB&&$j&&!in_array(DB,$j))array_unshift($j,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$Kb=script("mixin(qsl('select'), {onmousedown: dbMouseDown, onchange: dbChange});");echo"<span title='".'Database'."'>".'DB'.":</span> ".($j?html_select("db",array(""=>"")+$j,DB).$Kb:"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n"),"<input type='submit' value='".'Use'."'".($j?" class='hidden'":"").">\n";if(support("scheme")){if($We!="db"&&DB!=""&&$g->select_db(DB)){echo"<br><span>".'Schema'.":</span> ".html_select("ns",array(""=>"")+$b->schemas(),$_GET["ns"]).$Kb;if($_GET["ns"]!="")set_schema($_GET["ns"]);}}foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
tablesPrint($T){echo"<ul id='tables'>".script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");foreach($T
as$R=>$P){$C=$this->tableName($P);if($C!="")echo'<li><a href="'.h(ME).'select='.urlencode($R).'"'.bold($_GET["select"]==$R||$_GET["edit"]==$R,"select")." title='".'Select data'."'>".'select'."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.urlencode($R).'"'.bold(in_array($R,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"])),(is_view($P)?"view":"structure"))." title='".'Show structure'."'>$C</a>":"<span>$C</span>")."\n";}echo"</ul>\n";}}class
Plugins
extends
Adminer{var$plugins;function
__construct($kg){if($kg===null){$kg=array();$Ha="adminer-plugins";if(is_dir($Ha)){foreach(glob("$Ha/*.php")as$q)$Od=include_once"./$q";}$Cd=" href='https://www.adminer.org/plugins/#use'".target_blank();if(file_exists("$Ha.php")){$Od=include_once"./$Ha.php";if(is_array($Od)){foreach($Od
as$jg)$kg[get_class($jg)]=$jg;}else$this->error.=sprintf('%s must <a%s>return an array</a>.',"<b>$Ha.php</b>",$Cd)."<br>";}foreach(get_declared_classes()as$bb){if(!$kg[$bb]&&preg_match('~^Adminer\w~i',$bb)){$Lg=new
\ReflectionClass($bb);$ub=$Lg->getConstructor();if($ub&&$ub->getNumberOfRequiredParameters())$this->error.=sprintf('<a%s>Configure</a> %s in %s.',$Cd,"<b>$bb</b>","<b>$Ha.php</b>")."<br>";else$kg[$bb]=new$bb;}}}$this->plugins=$kg;}private
function
callParent($t,$d){return
call_user_func_array(array('parent',$t),$d);}private
function
applyPlugin($t,$Wf){$d=array();foreach($Wf
as$z=>$X)$d[]=&$Wf[$z];foreach($this->plugins
as$jg){if(method_exists($jg,$t)){$J=call_user_func_array(array($jg,$t),$d);if($J!==null)return$J;}}return$this->callParent($t,$d);}private
function
appendPlugin($t,$d){$J=$this->callParent($t,$d);foreach($this->plugins
as$jg){if(method_exists($jg,$t)){$Y=call_user_func_array(array($jg,$t),$d);if($Y)$J+=$Y;}}return$J;}function
dumpFormat(){$d=func_get_args();return$this->appendPlugin(__FUNCTION__,$d);}function
dumpOutput(){$d=func_get_args();return$this->appendPlugin(__FUNCTION__,$d);}function
editRowPrint($R,$p,$K,$Si){$d=func_get_args();return$this->appendPlugin(__FUNCTION__,$d);}function
editFunctions($o){$d=func_get_args();return$this->appendPlugin(__FUNCTION__,$d);}function
name(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
credentials(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
connectSsl(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
permanentLogin($i=false){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
bruteForceKey(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
serverName($N){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
database(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
schemas(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
databases($ad=true){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
queryTimeout(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
headers(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
csp(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
head($Gb=null){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
css(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
loginForm(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
loginFormField($C,$Bd,$Y){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
login($Ae,$F){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
tableName($Vh){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
fieldName($o,$Ef=0){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectLinks($Vh,$O=""){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
foreignKeys($R){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
backwardKeys($R,$Uh){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
backwardKeysPrint($Da,$K){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectQuery($H,$Lh,$Mc=false){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
sqlCommandQuery($H){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
sqlPrintAfter(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
rowDescription($R){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
rowDescriptions($L,$dd){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectLink($X,$o){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectVal($X,$A,$o,$Of){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
editVal($X,$o){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
tableStructurePrint($p,$Vh=null){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
tableIndexesPrint($y){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectColumnsPrint($M,$f){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectSearchPrint($Z,$f,$y){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectOrderPrint($Ef,$f,$y){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectLimitPrint($_){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectLengthPrint($li){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectActionPrint($y){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectCommandPrint(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectImportPrint(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectEmailPrint($oc,$f){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectColumnsProcess($f,$y){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectSearchProcess($p,$y){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectOrderProcess($p,$y){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectLimitProcess(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectLengthProcess(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectEmailProcess($Z,$dd){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
selectQueryBuild($M,$Z,$pd,$Ef,$_,$E){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
messageQuery($H,$mi,$Mc=false){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
editInput($R,$o,$ya,$Y){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
editHint($R,$o,$Y){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
processInput($o,$Y,$t=""){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
dumpDatabase($k){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
dumpTable($R,$Ph,$fe=0){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
dumpData($R,$Ph,$H){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
dumpFilename($Hd){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
dumpHeaders($Hd,$Ye=false){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
dumpFooter(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
importServerPath(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
homepage(){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
navigation($We){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
syntaxHighlighting($T){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
databasesPrint($We){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}function
tablesPrint($T){$d=func_get_args();return$this->applyPlugin(__FUNCTION__,$d);}}if(function_exists('adminer_object'))$b=adminer_object();elseif(is_dir("adminer-plugins")||file_exists("adminer-plugins.php"))$b=new
Plugins(null);else$b=new
Adminer;$ac=array("server"=>"MySQL / MariaDB")+$ac;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\MySQLi{var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
connect($N="",$V="",$F="",$Jb=null,$lg=null,$_h=null){global$b;mysqli_report(MYSQLI_REPORT_OFF);list($Fd,$lg)=explode(":",$N,2);$Kh=$b->connectSsl();if($Kh)$this->ssl_set($Kh['key'],$Kh['cert'],$Kh['ca'],'','');$J=@$this->real_connect(($N!=""?$Fd:ini_get("mysqli.default_host")),($N.$V!=""?$V:ini_get("mysqli.default_user")),($N.$V.$F!=""?$F:ini_get("mysqli.default_pw")),$Jb,(is_numeric($lg)?$lg:ini_get("mysqli.default_port")),(!is_numeric($lg)?$lg:$_h),($Kh?($Kh['verify']!==false?2048:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,false);return$J;}function
set_charset($Ta){if(parent::set_charset($Ta))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $Ta");}function
result($H,$o=0){$I=$this->query($H);if(!$I)return
false;$K=$I->fetch_array();return($K?$K[$o]:false);}function
quote($Q){return"'".$this->escape_string($Q)."'";}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db{var$extension="MySQL",$flavor='',$server_info,$affected_rows,$info,$errno,$error;private$link,$result;function
connect($N,$V,$F){if(ini_bool("mysql.allow_local_infile")){$this->error=sprintf('Disable %s or enable %s or %s extensions.',"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");return
false;}$this->link=@mysql_connect(($N!=""?$N:ini_get("mysql.default_host")),("$N$V"!=""?$V:ini_get("mysql.default_user")),("$N$V$F"!=""?$F:ini_get("mysql.default_password")),true,131072);if($this->link)$this->server_info=mysql_get_server_info($this->link);else$this->error=mysql_error();return(bool)$this->link;}function
set_charset($Ta){if(function_exists('mysql_set_charset')){if(mysql_set_charset($Ta,$this->link))return
true;mysql_set_charset('utf8',$this->link);}return$this->query("SET NAMES $Ta");}function
quote($Q){return"'".mysql_real_escape_string($Q,$this->link)."'";}function
select_db($Jb){return
mysql_select_db($Jb,$this->link);}function
query($H,$Ki=false){$I=@($Ki?mysql_unbuffered_query($H,$this->link):mysql_query($H,$this->link));$this->error="";if(!$I){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($I===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($I);}function
multi_query($H){return$this->result=$this->query($H);}function
store_result(){return$this->result;}function
next_result(){return
false;}function
result($H,$o=0){$I=$this->query($H);return($I?$I->fetch_column($o):false);}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($I){$this->result=$I;$this->num_rows=mysql_num_rows($I);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_column($o){return($this->num_rows?mysql_result($this->result,0,$o):false);}function
fetch_field(){$J=mysql_fetch_field($this->result,$this->offset++);$J->orgtable=$J->table;$J->charsetnr=($J->blob?63:0);return$J;}function
__destruct(){mysql_free_result($this->result);}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
connect($N,$V,$F){global$b;$Cf=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);$Kh=$b->connectSsl();if($Kh){if($Kh['key'])$Cf[\PDO::MYSQL_ATTR_SSL_KEY]=$Kh['key'];if($Kh['cert'])$Cf[\PDO::MYSQL_ATTR_SSL_CERT]=$Kh['cert'];if($Kh['ca'])$Cf[\PDO::MYSQL_ATTR_SSL_CA]=$Kh['ca'];if(isset($Kh['verify']))$Cf[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$Kh['verify'];}$this->dsn("mysql:charset=utf8;host=".str_replace(":",";unix_socket=",preg_replace('~:(\d)~',';port=\1',$N)),$V,$F,$Cf);return
true;}function
set_charset($Ta){$this->query("SET NAMES $Ta");}function
select_db($Jb){return$this->query("USE ".idf_escape($Jb));}function
query($H,$Ki=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$Ki);return
parent::query($H,$Ki);}}}class
Driver
extends
SqlDriver{static$pg=array("MySQLi","MySQL","PDO_MySQL");static$he="sql";var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");function
__construct($g){parent::__construct($g);$this->types=array('Numbers'=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),'Date and time'=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),'Strings'=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),'Lists'=>array("enum"=>65535,"set"=>64),'Binary'=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),'Geometry'=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->editFunctions=array(array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",),array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",));if(min_version('5.7.8',10.2,$g))$this->types['Strings']["json"]=4294967295;if(min_version('',10.7,$g)){$this->types['Strings']["uuid"]=128;$this->editFunctions[0]['uuid']='uuid';}if(min_version(9,'',$g)){$this->types['Numbers']["vector"]=16383;$this->editFunctions[0]['vector']='string_to_vector';}if(min_version(5.7,10.2,$g))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction($o){return(preg_match("~binary~",$o["type"])?"<code class='jush-sql'>UNHEX</code>":($o["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):(preg_match("~geometry|point|linestring|polygon~",$o["type"])?"<code class='jush-sql'>GeomFromText</code>":"")));}function
insert($R,$O){return($O?parent::insert($R,$O):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,$L,$G){$f=array_keys(reset($L));$rg="INSERT INTO ".table($R)." (".implode(", ",$f).") VALUES\n";$dj=array();foreach($f
as$z)$dj[$z]="$z = VALUES($z)";$Rh="\nON DUPLICATE KEY UPDATE ".implode(", ",$dj);$dj=array();$we=0;foreach($L
as$O){$Y="(".implode(", ",$O).")";if($dj&&(strlen($rg)+$we+strlen($Y)+strlen($Rh)>1e6)){if(!queries($rg.implode(",\n",$dj).$Rh))return
false;$dj=array();$we=0;}$dj[]=$Y;$we+=strlen($Y)+2;}return
queries($rg.implode(",\n",$dj).$Rh);}function
slowQuery($H,$ni){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$ni FOR $H";elseif(preg_match('~^(SELECT\b)(.+)~is',$H,$B))return"$B[1] /*+ MAX_EXECUTION_TIME(".($ni*1000).") */ $B[2]";}}function
convertSearch($w,$X,$o){return(preg_match('~char|text|enum|set~',$o["type"])&&!preg_match("~^utf8~",$o["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($w USING ".charset($this->conn).")":$w);}function
warnings(){$I=$this->conn->query("SHOW WARNINGS");if($I&&$I->num_rows){ob_start();select($I);return
ob_get_clean();}}function
tableHelp($C,$fe=false){$Ce=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower("information-schema-".($Ce?"$C-table/":str_replace("_","-",$C)."-table.html"));if(DB=="mysql")return($Ce?"mysql$C-table/":"system-schema.html");}function
hasCStyleEscapes(){static$Pa;if($Pa===null){$Ih=$this->conn->result("SHOW VARIABLES LIKE 'sql_mode'",1);$Pa=(strpos($Ih,'NO_BACKSLASH_ESCAPES')===false);}return$Pa;}function
engines(){$J=array();foreach(get_rows("SHOW ENGINES")as$K){if(preg_match("~YES|DEFAULT~",$K["Support"]))$J[]=$K["Engine"];}return$J;}}function
idf_escape($w){return"`".str_replace("`","``",$w)."`";}function
table($w){return
idf_escape($w);}function
connect($Bb){global$ac;$g=new
Db;if($g->connect($Bb[0],$Bb[1],$Bb[2])){$g->set_charset(charset($g));$g->query("SET sql_quote_show_create = 1, autocommit = 1");$g->flavor=(preg_match('~MariaDB~',$g->server_info)?'maria':'');$ac[DRIVER]=($g->flavor=='maria'?"MariaDB":"MySQL");return$g;}$J=$g->error;if(function_exists('iconv')&&!is_utf8($J)&&strlen($bh=iconv("windows-1250","utf-8",$J))>strlen($J))$J=$bh;return$J;}function
get_databases($ad){$J=get_session("dbs");if($J===null){$H="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$J=($ad?slow_query($H):get_vals($H));restart_session();set_session("dbs",$J);stop_session();}return$J;}function
limit($H,$Z,$_,$D=0,$nh=" "){return" $H$Z".($_!==null?$nh."LIMIT $_".($D?" OFFSET $D":""):"");}function
limit1($R,$H,$Z,$nh="\n"){return
limit($H,$Z,1,0,$nh);}function
db_collation($k,$hb){$J=null;$i=get_val("SHOW CREATE DATABASE ".idf_escape($k),1);if(preg_match('~ COLLATE ([^ ]+)~',$i,$B))$J=$B[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$i,$B))$J=$hb[$B[1]][-1];return$J;}function
logged_user(){return
get_val("SELECT USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables($j){$J=array();foreach($j
as$k)$J[$k]=count(get_vals("SHOW TABLES IN ".idf_escape($k)));return$J;}function
table_status($C="",$Nc=false){$J=array();foreach(get_rows($Nc?"SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($C!=""?"AND TABLE_NAME = ".q($C):"ORDER BY Name"):"SHOW TABLE STATUS".($C!=""?" LIKE ".q(addcslashes($C,"%_\\")):""))as$K){if($K["Engine"]=="InnoDB")$K["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$K["Comment"]);if(!isset($K["Engine"]))$K["Comment"]="";if($C!=""){$K["Name"]=$C;return$K;}$J[$K["Name"]]=$K;}return$J;}function
is_view($S){return$S["Engine"]===null;}function
fk_support($S){return
preg_match('~InnoDB|IBMDB2I~i',$S["Engine"])||(preg_match('~NDB~i',$S["Engine"])&&min_version(5.6));}function
fields($R){global$g;$Ce=($g->flavor=='maria');$J=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$K){$o=$K["COLUMN_NAME"];$U=$K["COLUMN_TYPE"];$md=$K["GENERATION_EXPRESSION"];$Kc=$K["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Kc,$ld);preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$U,$Fe);$l=$K["COLUMN_DEFAULT"];if($l!=""){$ee=preg_match('~text|json~',$Fe[1]);if(!$Ce&&$ee)$l=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($l));if($Ce||$ee){$l=($l=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($B){return
stripslashes(str_replace("''","'",$B[1]));},$l));}if(!$Ce&&preg_match('~binary~',$Fe[1])&&preg_match('~^0x(\w*)$~',$l,$B))$l=pack("H*",$B[1]);}$J[$o]=array("field"=>$o,"full_type"=>$U,"type"=>$Fe[1],"length"=>$Fe[2],"unsigned"=>ltrim($Fe[3].$Fe[4]),"default"=>($ld?($Ce?$md:stripslashes($md)):$l),"null"=>($K["IS_NULLABLE"]=="YES"),"auto_increment"=>($Kc=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$Kc,$B)?$B[1]:""),"collation"=>$K["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$K[PRIVILEGES],where,order")),"comment"=>$K["COLUMN_COMMENT"],"primary"=>($K["COLUMN_KEY"]=="PRI"),"generated"=>($ld[1]=="PERSISTENT"?"STORED":$ld[1]),);}return$J;}function
indexes($R,$h=null){$J=array();foreach(get_rows("SHOW INDEX FROM ".table($R),$h)as$K){$C=$K["Key_name"];$J[$C]["type"]=($C=="PRIMARY"?"PRIMARY":($K["Index_type"]=="FULLTEXT"?"FULLTEXT":($K["Non_unique"]?($K["Index_type"]=="SPATIAL"?"SPATIAL":"INDEX"):"UNIQUE")));$J[$C]["columns"][]=$K["Column_name"];$J[$C]["lengths"][]=($K["Index_type"]=="SPATIAL"?null:$K["Sub_part"]);$J[$C]["descs"][]=null;}return$J;}function
foreign_keys($R){global$m;static$fg='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$J=array();$_b=get_val("SHOW CREATE TABLE ".table($R),1);if($_b){preg_match_all("~CONSTRAINT ($fg) FOREIGN KEY ?\\(((?:$fg,? ?)+)\\) REFERENCES ($fg)(?:\\.($fg))? \\(((?:$fg,? ?)+)\\)(?: ON DELETE ($m->onActions))?(?: ON UPDATE ($m->onActions))?~",$_b,$Ge,PREG_SET_ORDER);foreach($Ge
as$B){preg_match_all("~$fg~",$B[2],$Bh);preg_match_all("~$fg~",$B[5],$fi);$J[idf_unescape($B[1])]=array("db"=>idf_unescape($B[4]!=""?$B[3]:$B[4]),"table"=>idf_unescape($B[4]!=""?$B[4]:$B[3]),"source"=>array_map('Adminer\idf_unescape',$Bh[0]),"target"=>array_map('Adminer\idf_unescape',$fi[0]),"on_delete"=>($B[6]?:"RESTRICT"),"on_update"=>($B[7]?:"RESTRICT"),);}}return$J;}function
view($C){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($C),1)));}function
collations(){$J=array();foreach(get_rows("SHOW COLLATION")as$K){if($K["Default"])$J[$K["Charset"]][-1]=$K["Collation"];else$J[$K["Charset"]][]=$K["Collation"];}ksort($J);foreach($J
as$z=>$X)asort($J[$z]);return$J;}function
information_schema($k){return($k=="information_schema")||(min_version(5.5)&&$k=="performance_schema");}function
error(){global$g;return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",$g->error));}function
create_database($k,$gb){return
queries("CREATE DATABASE ".idf_escape($k).($gb?" COLLATE ".q($gb):""));}function
drop_databases($j){$J=apply_queries("DROP DATABASE",$j,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$J;}function
rename_database($C,$gb){$J=false;if(create_database($C,$gb)){$T=array();$ij=array();foreach(tables_list()as$R=>$U){if($U=='VIEW')$ij[]=$R;else$T[]=$R;}$J=(!$T&&!$ij)||move_tables($T,$ij,$C);drop_databases($J?array(DB):array());}return$J;}function
auto_increment(){$Aa=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$x){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$x["columns"],true)){$Aa="";break;}if($x["type"]=="PRIMARY")$Aa=" UNIQUE";}}return" AUTO_INCREMENT$Aa";}function
alter_table($R,$C,$p,$cd,$mb,$rc,$gb,$_a,$bg){global$g;$c=array();foreach($p
as$o){if($o[1]){$l=$o[1][3];if(preg_match('~ GENERATED~',$l)){$o[1][3]=($g->flavor=='maria'?"":$o[1][2]);$o[1][2]=$l;}$c[]=($R!=""?($o[0]!=""?"CHANGE ".idf_escape($o[0]):"ADD"):" ")." ".implode($o[1]).($R!=""?$o[2]:"");}else$c[]="DROP ".idf_escape($o[0]);}$c=array_merge($c,$cd);$P=($mb!==null?" COMMENT=".q($mb):"").($rc?" ENGINE=".q($rc):"").($gb?" COLLATE ".q($gb):"").($_a!=""?" AUTO_INCREMENT=$_a":"");if($R=="")return
queries("CREATE TABLE ".table($C)." (\n".implode(",\n",$c)."\n)$P$bg");if($R!=$C)$c[]="RENAME TO ".table($C);if($P)$c[]=ltrim($P);return($c||$bg?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$c).$bg):true);}function
alter_indexes($R,$c){foreach($c
as$z=>$X)$c[$z]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$c));}function
truncate_tables($T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views($ij){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$ij)));}function
drop_tables($T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables($T,$ij,$fi){global$g;$Pg=array();foreach($T
as$R)$Pg[]=table($R)." TO ".idf_escape($fi).".".table($R);if(!$Pg||queries("RENAME TABLE ".implode(", ",$Pg))){$Qb=array();foreach($ij
as$R)$Qb[table($R)]=view($R);$g->select_db($fi);$k=idf_escape(DB);foreach($Qb
as$C=>$hj){if(!queries("CREATE VIEW $C AS ".str_replace(" $k."," ",$hj["select"]))||!queries("DROP VIEW $k.$C"))return
false;}return
true;}return
false;}function
copy_tables($T,$ij,$fi){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$C=($fi==DB?table("copy_$R"):idf_escape($fi).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $C"))||!queries("CREATE TABLE $C LIKE ".table($R))||!queries("INSERT INTO $C SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){$Di=$K["Trigger"];if(!queries("CREATE TRIGGER ".($fi==DB?idf_escape("copy_$Di"):idf_escape($fi).".".idf_escape($Di))." $K[Timing] $K[Event] ON $C FOR EACH ROW\n$K[Statement];"))return
false;}}foreach($ij
as$R){$C=($fi==DB?table("copy_$R"):idf_escape($fi).".".table($R));$hj=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $C"))||!queries("CREATE VIEW $C AS $hj[select]"))return
false;}return
true;}function
trigger($C){if($C=="")return
array();$L=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($C));return
reset($L);}function
triggers($R){$J=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K)$J[$K["Trigger"]]=array($K["Timing"],$K["Event"]);return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
routine($C,$U){global$m;$ta=array("bool","boolean","integer","double precision","real","dec","numeric","fixed","national char","national varchar");$Ch="(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";$tc=$m->enumLength;$Ii="((".implode("|",array_merge(array_keys($m->types()),$ta)).")\\b(?:\\s*\\(((?:[^'\")]|$tc)++)\\))?"."\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?)(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s,]+)['\"]?)?";$fg="$Ch*(".($U=="FUNCTION"?"":$m->inout).")?\\s*(?:`((?:[^`]|``)*)`\\s*|\\b(\\S+)\\s+)$Ii";$i=get_val("SHOW CREATE $U ".idf_escape($C),2);preg_match("~\\(((?:$fg\\s*,?)*)\\)\\s*".($U=="FUNCTION"?"RETURNS\\s+$Ii\\s+":"")."(.*)~is",$i,$B);$p=array();preg_match_all("~$fg\\s*,?~is",$B[1],$Ge,PREG_SET_ORDER);foreach($Ge
as$Vf)$p[]=array("field"=>str_replace("``","`",$Vf[2]).$Vf[3],"type"=>strtolower($Vf[5]),"length"=>preg_replace_callback("~$tc~s",'Adminer\normalize_enum',$Vf[6]),"unsigned"=>strtolower(preg_replace('~\s+~',' ',trim("$Vf[8] $Vf[7]"))),"null"=>1,"full_type"=>$Vf[4],"inout"=>strtoupper($Vf[1]),"collation"=>strtolower($Vf[9]),);return
array("fields"=>$p,"comment"=>get_val("SELECT ROUTINE_COMMENT FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = ".q($C)),)+($U!="FUNCTION"?array("definition"=>$B[11]):array("returns"=>array("type"=>$B[12],"length"=>$B[13],"unsigned"=>$B[15],"collation"=>$B[16]),"definition"=>$B[17],"language"=>"SQL",));}function
routines(){return
get_rows("SELECT ROUTINE_NAME AS SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return
array();}function
routine_id($C,$K){return
idf_escape($C);}function
last_id($I){return
get_val("SELECT LAST_INSERT_ID()");}function
explain($g,$H){return$g->query("EXPLAIN ".(min_version(5.1)&&!min_version(5.7)?"PARTITIONS ":"").$H);}function
found_rows($S,$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$_a,$Ph){$J=get_val("SHOW CREATE TABLE ".table($R),1);if(!$_a)$J=preg_replace('~ AUTO_INCREMENT=\d+~','',$J);return$J;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($Jb){return"USE ".idf_escape($Jb);}function
trigger_sql($R){$J="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")),null,"-- ")as$K)$J.="\nCREATE TRIGGER ".idf_escape($K["Trigger"])." $K[Timing] $K[Event] ON ".table($K["Table"])." FOR EACH ROW\n$K[Statement];;\n";return$J;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field($o){if(preg_match("~binary~",$o["type"]))return"HEX(".idf_escape($o["field"]).")";if($o["type"]=="bit")return"BIN(".idf_escape($o["field"])." + 0)";if(preg_match("~geometry|point|linestring|polygon~",$o["type"]))return(min_version(8)?"ST_":"")."AsWKT(".idf_escape($o["field"]).")";}function
unconvert_field($o,$J){if(preg_match("~binary~",$o["type"]))$J="UNHEX($J)";if($o["type"]=="bit")$J="CONVERT(b$J, UNSIGNED)";if(preg_match("~geometry|point|linestring|polygon~",$o["type"])){$rg=(min_version(8)?"ST_":"");$J=$rg."GeomFromText($J, $rg"."SRID($o[field]))";}return$J;}function
support($Oc){return!preg_match("~scheme|sequence|type|view_trigger|materializedview".(min_version(8)?"":"|descidx".(min_version(5.1)?"":"|event|partitioning")).(min_version('8.0.16','10.2.1')?"":"|check")."~",$Oc);}function
kill_process($X){return
queries("KILL ".number($X));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}}define('Adminer\JUSH',Driver::$he);define('Adminer\SERVER',$_GET[DRIVER]);define('Adminer\DB',$_GET["db"]);define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').(SERVER!==null?DRIVER."=".urlencode(SERVER).'&':'').($_GET["ext"]?"ext=".urlencode($_GET["ext"]).'&':'').(isset($_GET["username"])?"username=".urlencode($_GET["username"]).'&':'').(DB!=""?'db='.urlencode(DB).'&'.(isset($_GET["ns"])?"ns=".urlencode($_GET["ns"])."&":""):''));function
page_header($pi,$n="",$Ma=array(),$qi=""){global$ca,$ia,$b,$ac;page_headers();if(is_ajax()&&$n){page_messages($n);exit;}if(!ob_get_level())ob_start(null,4096);$ri=$pi.($qi!=""?": $qi":"");$si=strip_tags($ri.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".$b->name());echo'<!DOCTYPE html>
<html lang="en" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$si,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=5.1.0"),'">
';$Db=$b->css();$yd=false;$wd=false;foreach($Db
as$q){if(strpos($q,"adminer.css")!==false)$yd=true;if(strpos($q,"adminer-dark.css")!==false)$wd=true;}$Gb=($yd?($wd?null:false):($wd?:null));$Oe=" media='(prefers-color-scheme: dark)'";if($Gb!==false)echo"<link rel='stylesheet'".($Gb?"":$Oe)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=5.1.0")."'>\n";echo"<meta name='color-scheme' content='".($Gb===null?"light dark":($Gb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=5.1.0");if($b->head($Gb))echo"<link rel='shortcut icon' type='image/x-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=favicon.ico&version=5.1.0")."'>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=favicon.ico&version=5.1.0")."'>\n";foreach($Db
as$X)echo"<link rel='stylesheet'".(preg_match('~-dark~',$X)&&!$Gb?$Oe:"")." href='".h($X)."'>\n";echo"\n<body class='".'ltr'." nojs'>\n";$q=get_temp_dir()."/adminer.version";if(!$_COOKIE["adminer_version"]&&function_exists('openssl_verify')&&file_exists($q)&&filemtime($q)+86400>time()){$gj=unserialize(file_get_contents($q));$_g="-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwqWOVuF5uw7/+Z70djoK
RlHIZFZPO0uYRezq90+7Amk+FDNd7KkL5eDve+vHRJBLAszF/7XKXe11xwliIsFs
DFWQlsABVZB3oisKCBEuI71J4kPH8dKGEWR9jDHFw3cWmoH3PmqImX6FISWbG3B8
h7FIx3jEaw5ckVPVTeo5JRm/1DZzJxjyDenXvBQ/6o9DgZKeNDgxwKzH+sw9/YCO
jHnq1cFpOIISzARlrHMa/43YfeNRAm/tsBXjSxembBPo7aQZLAWHmaj5+K19H10B
nCpz9Y++cipkVEiKRGih4ZEvjoFysEOdRLj6WiD/uUNky4xGeA6LaJqh5XpkFkcQ
fQIDAQAB
-----END PUBLIC KEY-----
";if(openssl_verify($gj["version"],base64_decode($gj["signature"]),$_g)==1)$_COOKIE["adminer_version"]=$gj["version"];}echo
script("mixin(document.body, {onkeydown: bodyKeydown, onclick: bodyClick".(isset($_COOKIE["adminer_version"])?"":", onload: partial(verifyVersion, '$ia', '".js_escape(ME)."', '".get_token()."')")."});
document.body.classList.replace('nojs', 'js');
const offlineMessage = '".js_escape('You are offline.')."';
const thousandsSeparator = '".js_escape(',')."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'></div>\n",script("mixin(qs('#help'), {onmouseover: () => { helpOpen = 1; }, onmouseout: helpMouseout});"),"<div id='content'>\n";if($Ma!==null){$A=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($A?:".").'">'.$ac[DRIVER].'</a> » ';$A=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$N=$b->serverName(SERVER);$N=($N!=""?$N:'Server');if($Ma===false)echo"$N\n";else{echo"<a href='".h($A)."' accesskey='1' title='Alt+Shift+1'>$N</a> » ";if($_GET["ns"]!=""||(DB!=""&&is_array($Ma)))echo'<a href="'.h($A."&db=".urlencode(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a> » ';if(is_array($Ma)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> » ';foreach($Ma
as$z=>$X){$Sb=(is_array($X)?$X[1]:h($X));if($Sb!="")echo"<a href='".h(ME."$z=").urlencode(is_array($X)?$X[0]:$X)."'>$Sb</a> » ";}}echo"$pi\n";}}echo"<h2>$ri</h2>\n","<div id='ajaxstatus' class='jsonly hidden'></div>\n";restart_session();page_messages($n);$j=&get_session("dbs");if(DB!=""&&$j&&!in_array(DB,$j,true))$j=null;stop_session();define('Adminer\PAGE_HEADER',1);}function
page_headers(){global$b;header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach($b->csp()as$Cb){$Ad=array();foreach($Cb
as$z=>$X)$Ad[]="$z $X";header("Content-Security-Policy: ".implode("; ",$Ad));}$b->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self'","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
get_nonce(){static$if;if(!$if)$if=base64_encode(rand_string());return$if;}function
page_messages($n){global$b;$Ti=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$Ue=$_SESSION["messages"][$Ti];if($Ue){echo"<div class='message'>".implode("</div>\n<div class='message'>",$Ue)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$Ti]);}if($n)echo"<div class='error'>$n</div>\n";if($b->error)echo"<div class='error'>$b->error</div>\n";}function
page_footer($We=""){global$b;echo"</div>\n\n<div id='menu'>\n";$b->navigation($We);echo"</div>\n\n";if($We!="auth")echo'<form action="" method="post">
<p class="logout">
<span>',h($_GET["username"])."\n",'</span>
<input type="submit" name="logout" value="Logout" id="logout">
',input_token(),'</p>
</form>
';echo
script("setupSubmitHighlight(document);");}function
int32($af){while($af>=2147483648)$af-=4294967296;while($af<=-2147483649)$af+=4294967296;return(int)$af;}function
long2str($W,$kj){$bh='';foreach($W
as$X)$bh.=pack('V',$X);if($kj)return
substr($bh,0,end($W));return$bh;}function
str2long($bh,$kj){$W=array_values(unpack('V*',str_pad($bh,4*ceil(strlen($bh)/4),"\0")));if($kj)$W[]=strlen($bh);return$W;}function
xxtea_mx($rj,$qj,$Sh,$ie){return
int32((($rj>>5&0x7FFFFFF)^$qj<<2)+(($qj>>3&0x1FFFFFFF)^$rj<<4))^int32(($Sh^$qj)+($ie^$rj));}function
encrypt_string($Nh,$z){if($Nh=="")return"";$z=array_values(unpack("V*",pack("H*",md5($z))));$W=str2long($Nh,true);$af=count($W)-1;$rj=$W[$af];$qj=$W[0];$Ag=floor(6+52/($af+1));$Sh=0;while($Ag-->0){$Sh=int32($Sh+0x9E3779B9);$ic=$Sh>>2&3;for($Tf=0;$Tf<$af;$Tf++){$qj=$W[$Tf+1];$Ze=xxtea_mx($rj,$qj,$Sh,$z[$Tf&3^$ic]);$rj=int32($W[$Tf]+$Ze);$W[$Tf]=$rj;}$qj=$W[0];$Ze=xxtea_mx($rj,$qj,$Sh,$z[$Tf&3^$ic]);$rj=int32($W[$af]+$Ze);$W[$af]=$rj;}return
long2str($W,false);}function
decrypt_string($Nh,$z){if($Nh=="")return"";if(!$z)return
false;$z=array_values(unpack("V*",pack("H*",md5($z))));$W=str2long($Nh,false);$af=count($W)-1;$rj=$W[$af];$qj=$W[0];$Ag=floor(6+52/($af+1));$Sh=int32($Ag*0x9E3779B9);while($Sh){$ic=$Sh>>2&3;for($Tf=$af;$Tf>0;$Tf--){$rj=$W[$Tf-1];$Ze=xxtea_mx($rj,$qj,$Sh,$z[$Tf&3^$ic]);$qj=int32($W[$Tf]-$Ze);$W[$Tf]=$qj;}$rj=$W[$af];$Ze=xxtea_mx($rj,$qj,$Sh,$z[$Tf&3^$ic]);$qj=int32($W[0]-$Ze);$W[0]=$qj;$Sh=int32($Sh-0x9E3779B9);}return
long2str($W,true);}$g='';$_d=$_SESSION["token"];if(!$_d)$_SESSION["token"]=rand(1,1e6);$vi=get_token();$hg=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($z)=explode(":",$X);$hg[$z]=$X;}}function
add_invalid_login(){global$b;$Fa=get_temp_dir()."/adminer.invalid";foreach(glob("$Fa*")?:array($Fa)as$q){$s=file_open_lock($q);if($s)break;}if(!$s)$s=file_open_lock("$Fa-".rand_string());if(!$s)return;$ae=unserialize(stream_get_contents($s));$mi=time();if($ae){foreach($ae
as$be=>$X){if($X[0]<$mi)unset($ae[$be]);}}$Zd=&$ae[$b->bruteForceKey()];if(!$Zd)$Zd=array($mi+30*60,0);$Zd[1]++;file_write_unlock($s,serialize($ae));}function
check_invalid_login(){global$b;$ae=array();foreach(glob(get_temp_dir()."/adminer.invalid*")as$q){$s=file_open_lock($q);if($s){$ae=unserialize(stream_get_contents($s));file_unlock($s);break;}}$Zd=($ae?$ae[$b->bruteForceKey()]:array());$hf=($Zd[1]>29?$Zd[0]-time():0);if($hf>0)auth_error(lang(array('Too many unsuccessful logins, try again in %d minute.','Too many unsuccessful logins, try again in %d minutes.'),ceil($hf/60)));}$za=$_POST["auth"];if($za){session_regenerate_id();$fj=$za["driver"];$N=$za["server"];$V=$za["username"];$F=(string)$za["password"];$k=$za["db"];set_password($fj,$N,$V,$F);$_SESSION["db"][$fj][$N][$V][$k]=true;if($za["permanent"]){$z=implode("-",array_map('base64_encode',array($fj,$N,$V,$k)));$vg=$b->permanentLogin(true);$hg[$z]="$z:".base64_encode($vg?encrypt_string($F,$vg):"");cookie("adminer_permanent",implode(" ",$hg));}if(count($_POST)==1||DRIVER!=$fj||SERVER!=$N||$_GET["username"]!==$V||DB!=$k)redirect(auth_url($fj,$N,$V,$k));}elseif($_POST["logout"]&&(!$_d||verify_token())){foreach(array("pwds","db","dbs","queries")as$z)set_session($z,null);unset_permanent();redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),'Logout successful.'.' '.'Thanks for using Adminer, consider <a href="https://www.adminer.org/en/donation/">donating</a>.');}elseif($hg&&!$_SESSION["pwds"]){session_regenerate_id();$vg=$b->permanentLogin();foreach($hg
as$z=>$X){list(,$ab)=explode(":",$X);list($fj,$N,$V,$k)=array_map('base64_decode',explode("-",$z));set_password($fj,$N,$V,decrypt_string(base64_decode($ab),$vg));$_SESSION["db"][$fj][$N][$V][$k]=true;}}function
unset_permanent(){global$hg;foreach($hg
as$z=>$X){list($fj,$N,$V,$k)=array_map('base64_decode',explode("-",$z));if($fj==DRIVER&&$N==SERVER&&$V==$_GET["username"]&&$k==DB)unset($hg[$z]);}cookie("adminer_permanent",implode(" ",$hg));}function
auth_error($n){global$b,$_d;$th=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$th]||$_GET[$th])&&!$_d)$n='Session expired, please login again.';else{restart_session();add_invalid_login();$F=get_password();if($F!==null){if($F===false)$n.=($n?'<br>':'').sprintf('Master password expired. <a href="https://www.adminer.org/en/extension/"%s>Implement</a> %s method to make it permanent.',target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);}unset_permanent();}}if(!$_COOKIE[$th]&&$_GET[$th]&&ini_bool("session.use_only_cookies"))$n='Session support must be enabled.';$Wf=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$Wf["lifetime"]);page_header('Login',$n,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth")))echo"<p class='message'>".'The action will be performed after successful login with the same credentials.'."\n";echo"</div>\n";$b->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent();page_header('No extension',sprintf('None of the supported PHP extensions (%s) are available.',implode(", ",Driver::$pg)),false);page_footer("auth");exit;}stop_session(true);if(isset($_GET["username"])&&is_string(get_password())){list($Fd,$lg)=explode(":",SERVER,2);if(preg_match('~^\s*([-+]?\d+)~',$lg,$B)&&($B[1]<1024||$B[1]>65535))auth_error('Connecting to privileged ports is not allowed.');check_invalid_login();$g=connect($b->credentials());if(is_object($g)){$m=new
Driver($g);if($b->operators===null)$b->operators=$m->operators;if(Driver::$he=='sql'||$g->flavor=='cockroach')save_settings(array("vendor-".DRIVER."-".SERVER=>$ac[DRIVER]));}}$Ae=null;if(!is_object($g)||($Ae=$b->login($_GET["username"],get_password()))!==true){$n=(is_string($g)?nl_br(h($g)):(is_string($Ae)?$Ae:'Invalid credentials.'));auth_error($n.(preg_match('~^ | $~',get_password())?'<br>'.'There is a space in the input password which might be the cause.':''));}if($_POST["logout"]&&$_d&&!verify_token()){page_header('Logout','Invalid CSRF token. Send the form again.');page_footer("db");exit;}if($za&&$_POST["token"])$_POST["token"]=$vi;$n='';if($_POST){if(!verify_token()){$Ud="max_input_vars";$Me=ini_get($Ud);if(extension_loaded("suhosin")){foreach(array("suhosin.request.max_vars","suhosin.post.max_vars")as$z){$X=ini_get($z);if($X&&(!$Me||$X<$Me)){$Ud=$z;$Me=$X;}}}$n=(!$_POST["token"]&&$Me?sprintf('Maximum number of allowed fields exceeded. Please increase %s.',"'$Ud'"):'Invalid CSRF token. Send the form again.'.' '.'If you did not send this request from Adminer then close this page.');}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){$n=sprintf('Too big POST data. Reduce the data or increase the %s configuration directive.',"'post_max_size'");if(isset($_GET["sql"]))$n.=' '.'You can upload a big SQL file via FTP and import it from server.';}function
select($I,$h=null,$If=array(),$_=0){$ze=array();$y=array();$f=array();$Ka=array();$Ji=array();$J=array();for($u=0;(!$_||$u<$_)&&($K=$I->fetch_row());$u++){if(!$u){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr>";for($ge=0;$ge<count($K);$ge++){$o=$I->fetch_field();$C=$o->name;$Hf=(isset($o->orgtable)?$o->orgtable:"");$Gf=(isset($o->orgname)?$o->orgname:$C);if($If&&JUSH=="sql")$ze[$ge]=($C=="table"?"table=":($C=="possible_keys"?"indexes=":null));elseif($Hf!=""){if(isset($o->table))$J[$o->table]=$Hf;if(!isset($y[$Hf])){$y[$Hf]=array();foreach(indexes($Hf,$h)as$x){if($x["type"]=="PRIMARY"){$y[$Hf]=array_flip($x["columns"]);break;}}$f[$Hf]=$y[$Hf];}if(isset($f[$Hf][$Gf])){unset($f[$Hf][$Gf]);$y[$Hf][$Gf]=$ge;$ze[$ge]=$Hf;}}if($o->charsetnr==63)$Ka[$ge]=true;$Ji[$ge]=$o->type;echo"<th".($Hf!=""||$o->name!=$Gf?" title='".h(($Hf!=""?"$Hf.":"").$Gf)."'":"").">".h($C).($If?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($C),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}echo"</thead>\n";}echo"<tr>";foreach($K
as$z=>$X){$A="";if(isset($ze[$z])&&!$f[$ze[$z]]){if($If&&JUSH=="sql"){$R=$K[array_search("table=",$ze)];$A=ME.$ze[$z].urlencode($If[$R]!=""?$If[$R]:$R);}else{$A=ME."edit=".urlencode($ze[$z]);foreach($y[$ze[$z]]as$eb=>$ge)$A.="&where".urlencode("[".bracket_escape($eb)."]")."=".urlencode($K[$ge]);}}elseif(is_url($X))$A=$X;if($X===null)$X="<i>NULL</i>";elseif($Ka[$z]&&!is_utf8($X))$X="<i>".lang(array('%d byte','%d bytes'),strlen($X))."</i>";else{$X=h($X);if($Ji[$z]==254)$X="<code>$X</code>";}if($A)$X="<a href='".h($A)."'".(is_url($A)?target_blank():'').">$X</a>";echo"<td".($Ji[$z]<=9||$Ji[$z]==246?" class='number'":"").">$X";}}echo($u?"</table>\n</div>":"<p class='message'>".'No rows.')."\n";return$J;}function
referencable_primary($lh){$J=array();foreach(table_status('',true)as$Xh=>$R){if($Xh!=$lh&&fk_support($R)){foreach(fields($Xh)as$o){if($o["primary"]){if($J[$Xh]){unset($J[$Xh]);break;}$J[$Xh]=$o;}}}}return$J;}function
textarea($C,$Y,$L=10,$ib=80){echo"<textarea name='".h($C)."' rows='$L' cols='$ib' class='sqlarea jush-".JUSH."' spellcheck='false' wrap='off'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($ya,$Cf,$Y="",$xf="",$ig=""){$ei=($Cf?"select":"input");return"<$ei$ya".($Cf?"><option value=''>$ig".optionlist($Cf,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$ig'>").($xf?script("qsl('$ei').onchange = $xf;",""):"");}function
json_row($z,$X=null){static$Uc=true;if($Uc)echo"{";if($z!=""){echo($Uc?"":",")."\n\t\"".addcslashes($z,"\r\n\t\"\\/").'": '.($X!==null?'"'.addcslashes($X,"\r\n\"\\/").'"':'null');$Uc=false;}else{echo"\n}\n";$Uc=true;}}function
edit_type($z,$o,$hb,$ed=array(),$Lc=array()){global$m;$U=$o["type"];echo"<td><select name='".h($z)."[type]' class='type' aria-labelledby='label-type'>";if($U&&!array_key_exists($U,$m->types())&&!isset($ed[$U])&&!in_array($U,$Lc))$Lc[]=$U;$Oh=$m->structuredTypes();if($ed)$Oh['Foreign keys']=$ed;echo
optionlist(array_merge($Lc,$Oh),$U),"</select><td>","<input name='".h($z)."[length]' value='".h($o["length"])."' size='3'".(!$o["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($hb?"<input list='collations' name='".h($z)."[collation]'".(preg_match('~(char|text|enum|set)$~',$U)?"":" class='hidden'")." value='".h($o["collation"])."' placeholder='(".'collation'.")'>":''),($m->unsigned?"<select name='".h($z)."[unsigned]'".(!$U||preg_match(number_type(),$U)?"":" class='hidden'").'><option>'.optionlist($m->unsigned,$o["unsigned"]).'</select>':''),(isset($o['on_update'])?"<select name='".h($z)."[on_update]'".(preg_match('~timestamp|datetime~',$U)?"":" class='hidden'").'>'.optionlist(array(""=>"(".'ON UPDATE'.")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$o["on_update"])?"CURRENT_TIMESTAMP":$o["on_update"])).'</select>':''),($ed?"<select name='".h($z)."[on_delete]'".(preg_match("~`~",$U)?"":" class='hidden'")."><option value=''>(".'ON DELETE'.")".optionlist(explode("|",$m->onActions),$o["on_delete"])."</select> ":" ");}function
get_partitions_info($R){global$g;$id="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$I=$g->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $id ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$J=array();list($J["partition_by"],$J["partition"],$J["partitions"])=$I->fetch_row();$cg=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $id AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$J["partition_names"]=array_keys($cg);$J["partition_values"]=array_values($cg);return$J;}function
process_length($we){global$m;$vc=$m->enumLength;return(preg_match("~^\\s*\\(?\\s*$vc(?:\\s*,\\s*$vc)*+\\s*\\)?\\s*\$~",$we)&&preg_match_all("~$vc~",$we,$Ge)?"(".implode(",",$Ge[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$we)));}function
process_type($o,$fb="COLLATE"){global$m;return" $o[type]".process_length($o["length"]).(preg_match(number_type(),$o["type"])&&in_array($o["unsigned"],$m->unsigned)?" $o[unsigned]":"").(preg_match('~char|text|enum|set~',$o["type"])&&$o["collation"]?" $fb ".(JUSH=="mssql"?$o["collation"]:q($o["collation"])):"");}function
process_field($o,$Hi){if($o["on_update"])$o["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$o["on_update"]);return
array(idf_escape(trim($o["field"])),process_type($Hi),($o["null"]?" NULL":" NOT NULL"),default_value($o),(preg_match('~timestamp|datetime~',$o["type"])&&$o["on_update"]?" ON UPDATE $o[on_update]":""),(support("comment")&&$o["comment"]!=""?" COMMENT ".q($o["comment"]):""),($o["auto_increment"]?auto_increment():null),);}function
default_value($o){global$m;$l=$o["default"];$ld=$o["generated"];return($l===null?"":(in_array($ld,$m->generated)?(JUSH=="mssql"?" AS ($l)".($ld=="VIRTUAL"?"":" $ld")."":" GENERATED ALWAYS AS ($l) $ld"):" DEFAULT ".(!preg_match('~^GENERATED ~i',$l)&&(preg_match('~char|binary|text|json|enum|set~',$o["type"])||preg_match('~^(?![a-z])~i',$l))?(JUSH=="sql"&&preg_match('~text|json~',$o["type"])?"(".q($l).")":q($l)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($l)":$l)))));}function
type_class($U){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$z=>$X){if(preg_match("~$z|$X~",$U))return" class='$z'";}}function
edit_fields($p,$hb,$U="TABLE",$ed=array()){global$m;$p=array_values($p);$Ob=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$nb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?'Column name':'Parameter name'),"<td id='label-type'>".'Type'."<textarea id='enum-edit' rows='4' cols='12' wrap='off' style='display: none;'></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<td id='label-length'>".'Length',"<td>".'Options';if($U=="TABLE")echo"<td id='label-null'>NULL\n","<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".'Auto Increment'."'>AI</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",'sqlite'=>"autoinc.html",'pgsql'=>"datatype-numeric.html#DATATYPE-SERIAL",'mssql'=>"t-sql/statements/create-table-transact-sql-identity-property",)),"<td id='label-default'$Ob>".'Default value',(support("comment")?"<td id='label-comment'$nb>".'Comment':"");echo"<td><input type='image' class='icon' name='add[".(support("move_col")?0:count($p))."]' src='".h(preg_replace("~\\?.*~","",ME)."?file=plus.gif&version=5.1.0")."' alt='+' title='".'Add next'."'>".script("row_count = ".count($p).";"),"</thead>\n<tbody>\n",script("mixin(qsl('tbody'), {onclick: editingClick, onkeydown: editingKeydown, oninput: editingInput});");foreach($p
as$u=>$o){$u++;$Jf=$o[($_POST?"orig":"field")];$Xb=(isset($_POST["add"][$u-1])||(isset($o["field"])&&!$_POST["drop_col"][$u]))&&(support("drop_col")||$Jf=="");echo"<tr".($Xb?"":" style='display: none;'").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$u][inout]",explode("|",$m->inout),$o["inout"]):"")."<th>";if($Xb)echo"<input name='fields[$u][field]' value='".h($o["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'>";echo
input_hidden("fields[$u][orig]",$Jf);edit_type("fields[$u]",$o,$hb,$ed);if($U=="TABLE")echo"<td>".checkbox("fields[$u][null]",1,$o["null"],"","","block","label-null"),"<td><label class='block'><input type='radio' name='auto_increment_col' value='$u'".($o["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$Ob>".($m->generated?html_select("fields[$u][generated]",array_merge(array("","DEFAULT"),$m->generated),$o["generated"])." ":checkbox("fields[$u][generated]",1,$o["generated"],"","","","label-default")),"<input name='fields[$u][default]' value='".h($o["default"])."' aria-labelledby='label-default'>",(support("comment")?"<td$nb><input name='fields[$u][comment]' value='".h($o["comment"])."' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'>":"");echo"<td>",(support("move_col")?"<input type='image' class='icon' name='add[$u]' src='".h(preg_replace("~\\?.*~","",ME)."?file=plus.gif&version=5.1.0")."' alt='+' title='".'Add next'."'> "."<input type='image' class='icon' name='up[$u]' src='".h(preg_replace("~\\?.*~","",ME)."?file=up.gif&version=5.1.0")."' alt='↑' title='".'Move up'."'> "."<input type='image' class='icon' name='down[$u]' src='".h(preg_replace("~\\?.*~","",ME)."?file=down.gif&version=5.1.0")."' alt='↓' title='".'Move down'."'> ":""),($Jf==""||support("drop_col")?"<input type='image' class='icon' name='drop_col[$u]' src='".h(preg_replace("~\\?.*~","",ME)."?file=cross.gif&version=5.1.0")."' alt='x' title='".'Remove'."'>":"");}}function
process_fields(&$p){$D=0;if($_POST["up"]){$qe=0;foreach($p
as$z=>$o){if(key($_POST["up"])==$z){unset($p[$z]);array_splice($p,$qe,0,array($o));break;}if(isset($o["field"]))$qe=$D;$D++;}}elseif($_POST["down"]){$gd=false;foreach($p
as$z=>$o){if(isset($o["field"])&&$gd){unset($p[key($_POST["down"])]);array_splice($p,$D,0,array($gd));break;}if(key($_POST["down"])==$z)$gd=$o;$D++;}}elseif($_POST["add"]){$p=array_values($p);array_splice($p,key($_POST["add"]),0,array(array()));}elseif(!$_POST["drop_col"])return
false;return
true;}function
normalize_enum($B){return"'".str_replace("'","''",addcslashes(stripcslashes(str_replace($B[0][0].$B[0][0],$B[0][0],substr($B[0],1,-1))),'\\'))."'";}function
grant($nd,$xg,$f,$uf){if(!$xg)return
true;if($xg==array("ALL PRIVILEGES","GRANT OPTION"))return($nd=="GRANT"?queries("$nd ALL PRIVILEGES$uf WITH GRANT OPTION"):queries("$nd ALL PRIVILEGES$uf")&&queries("$nd GRANT OPTION$uf"));return
queries("$nd ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$f, ",$xg).$f).$uf);}function
drop_create($bc,$i,$dc,$ii,$fc,$_e,$Te,$Re,$Se,$rf,$ef){if($_POST["drop"])query_redirect($bc,$_e,$Te);elseif($rf=="")query_redirect($i,$_e,$Se);elseif($rf!=$ef){$Ab=queries($i);queries_redirect($_e,$Re,$Ab&&queries($bc));if($Ab)queries($dc);}else
queries_redirect($_e,$Re,queries($ii)&&queries($fc)&&queries($bc)&&queries($i));}function
create_trigger($uf,$K){$oi=" $K[Timing] $K[Event]".(preg_match('~ OF~',$K["Event"])?" $K[Of]":"");return"CREATE TRIGGER ".idf_escape($K["Trigger"]).(JUSH=="mssql"?$uf.$oi:$oi.$uf).rtrim(" $K[Type]\n$K[Statement]",";").";";}function
create_routine($Xg,$K){global$m;$O=array();$p=(array)$K["fields"];ksort($p);foreach($p
as$o){if($o["field"]!="")$O[]=(preg_match("~^($m->inout)\$~",$o["inout"])?"$o[inout] ":"").idf_escape($o["field"]).process_type($o,"CHARACTER SET");}$Pb=rtrim($K["definition"],";");return"CREATE $Xg ".idf_escape(trim($K["name"]))." (".implode(", ",$O).")".($Xg=="FUNCTION"?" RETURNS".process_type($K["returns"],"CHARACTER SET"):"").($K["language"]?" LANGUAGE $K[language]":"").(JUSH=="pgsql"?" AS ".q($Pb):"\n$Pb;");}function
remove_definer($H){return
preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~','`@`(%|\1)',logged_user()).'`~','\1',$H);}function
format_foreign_key($r){global$m;$k=$r["db"];$jf=$r["ns"];return" FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$r["source"])).") REFERENCES ".($k!=""&&$k!=$_GET["db"]?idf_escape($k).".":"").($jf!=""&&$jf!=$_GET["ns"]?idf_escape($jf).".":"").idf_escape($r["table"])." (".implode(", ",array_map('Adminer\idf_escape',$r["target"])).")".(preg_match("~^($m->onActions)\$~",$r["on_delete"])?" ON DELETE $r[on_delete]":"").(preg_match("~^($m->onActions)\$~",$r["on_update"])?" ON UPDATE $r[on_update]":"");}function
tar_file($q,$ti){$J=pack("a100a8a8a8a12a12",$q,644,0,0,decoct($ti->size),decoct(time()));$Za=8*32;for($u=0;$u<strlen($J);$u++)$Za+=ord($J[$u]);$J.=sprintf("%06o",$Za)."\0 ";echo$J,str_repeat("\0",512-strlen($J));$ti->send();echo
str_repeat("\0",511-($ti->size+511)%512);}function
ini_bytes($Ud){$X=ini_get($Ud);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
doc_link($eg,$ji="<sup>?</sup>"){global$g;$rh=$g->server_info;$gj=preg_replace('~^(\d\.?\d).*~s','\1',$rh);$Vi=array('sql'=>"https://dev.mysql.com/doc/refman/$gj/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".($g->flavor=='cockroach'?"current":$gj)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://www.oracle.com/pls/topic/lookup?ctx=db".preg_replace('~^.* (\d+)\.(\d+)\.\d+\.\d+\.\d+.*~s','\1\2',$rh)."&id=",);if($g->flavor=='maria'){$Vi['sql']="https://mariadb.com/kb/en/";$eg['sql']=(isset($eg['mariadb'])?$eg['mariadb']:str_replace(".html","/",$eg['sql']));}return($eg[JUSH]?"<a href='".h($Vi[JUSH].$eg[JUSH].(JUSH=='mssql'?"?view=sql-server-ver$gj":""))."'".target_blank().">$ji</a>":"");}function
db_size($k){global$g;if(!$g->select_db($k))return"?";$J=0;foreach(table_status()as$S)$J+=$S["Data_length"]+$S["Index_length"];return
format_number($J);}function
set_utf8mb4($i){global$g;static$O=false;if(!$O&&preg_match('~\butf8mb4~i',$i)){$O=true;echo"SET NAMES ".charset($g).";\n\n";}}if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];if(!(DB!=""?$g->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!=""){header("HTTP/1.1 404 Not Found");page_header('Database'.": ".h(DB),'Invalid database.',true);}else{if($_POST["db"]&&!$n)queries_redirect(substr(ME,0,-1),'Databases have been dropped.',drop_databases($_POST["db"]));page_header('Select database',$n,false);echo"<p class='links'>\n";foreach(array('database'=>'Create database','privileges'=>'Privileges','processlist'=>'Process list','variables'=>'Variables','status'=>'Status',)as$z=>$X){if(support($z))echo"<a href='".h(ME)."$z='>$X</a>\n";}echo"<p>".sprintf('%s version: %s through PHP extension %s',$ac[DRIVER],"<b>".h($g->server_info)."</b>","<b>$g->extension</b>")."\n","<p>".sprintf('Logged as: %s',"<b>".h(logged_user())."</b>")."\n";if(isset($b->plugins)&&is_array($b->plugins)){echo"<p>".'Loaded plugins'.":\n<ul>\n";foreach($b->plugins
as$jg){$Lg=new
\ReflectionObject($jg);echo"<li><b>".get_class($jg)."</b>".h(preg_match('~^/[\s*]+(.+)~',$Lg->getDocComment(),$B)?": $B[1]":"")."\n";}echo"</ul>\n";}$j=$b->databases();if($j){$fh=support("scheme");$hb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),"<thead><tr>".(support("database")?"<td>":"")."<th>".'Database'.(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".'Refresh'."</a>":"")."<td>".'Collation'."<td>".'Tables'."<td>".'Size'." - <a href='".h(ME)."dbsize=1'>".'Compute'."</a>".script("qsl('a').onclick = partial(ajaxSetHtml, '".js_escape(ME)."script=connect');","")."</thead>\n";$j=($_GET["dbsize"]?count_tables($j):array_flip($j));foreach($j
as$k=>$T){$Wg=h(ME)."db=".urlencode($k);$v=h("Db-".$k);echo"<tr>".(support("database")?"<td>".checkbox("db[]",$k,in_array($k,(array)$_POST["db"]),"","","",$v):""),"<th><a href='$Wg' id='$v'>".h($k)."</a>";$gb=h(db_collation($k,$hb));echo"<td>".(support("database")?"<a href='$Wg".($fh?"&amp;ns=":"")."&amp;database=' title='".'Alter database'."'>$gb</a>":$gb),"<td align='right'><a href='$Wg&amp;schema=' id='tables-".h($k)."' title='".'Database schema'."'>".($_GET["dbsize"]?$T:"?")."</a>","<td align='right' id='size-".h($k)."'>".($_GET["dbsize"]?db_size($k):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".'Selected'." <span id='selected'></span></legend><div>\n".input_hidden("all").script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^db/)); };")."<input type='submit' name='drop' value='".'Drop'."'>".confirm()."\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}}page_footer("db");exit;}if(support("scheme")){if(DB!=""&&$_GET["ns"]!==""){if(!isset($_GET["ns"]))redirect(preg_replace('~ns=[^&]*&~','',ME)."ns=".get_schema());if(!set_schema($_GET["ns"])){header("HTTP/1.1 404 Not Found");page_header('Schema'.": ".h($_GET["ns"]),'Invalid schema.',true);page_footer("ns");exit;}}}class
TmpFile{private$handler,$size;function
__construct(){$this->handler=tmpfile();}function
write($wb){$this->size+=strlen($wb);fwrite($this->handler,$wb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$p=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$M=array(idf_escape($_GET["field"]));$I=$m->select($a,$M,array(where($_GET,$p)),$M);$K=($I?$I->fetch_row():array());echo$m->value($K[0],$p[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$p=fields($a);if(!$p)$n=error();$S=table_status1($a);$C=$b->tableName($S);page_header(($p&&is_view($S)?$S['Engine']=='materialized view'?'Materialized view':'View':'Table').": ".($C!=""?$C:h($a)),$n);$Vg=array();foreach($p
as$z=>$o)$Vg+=$o["privileges"];$b->selectLinks($S,(isset($Vg["insert"])||!support("table")?"":null));$mb=$S["Comment"];if($mb!="")echo"<p class='nowrap'>".'Comment'.": ".h($mb)."\n";if($p)$b->tableStructurePrint($p,$S);if(support("indexes")&&$m->supportsIndex($S)){echo"<h3 id='indexes'>".'Indexes'."</h3>\n";$y=indexes($a);if($y)$b->tableIndexesPrint($y);echo'<p class="links"><a href="'.h(ME).'indexes='.urlencode($a).'">'.'Alter indexes'."</a>\n";}if(!is_view($S)){if(fk_support($S)){echo"<h3 id='foreign-keys'>".'Foreign keys'."</h3>\n";$ed=foreign_keys($a);if($ed){echo"<table>\n","<thead><tr><th>".'Source'."<td>".'Target'."<td>".'ON DELETE'."<td>".'ON UPDATE'."<td></thead>\n";foreach($ed
as$C=>$r){echo"<tr title='".h($C)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$r["source"]))."</i>";$A=($r["db"]!=""?preg_replace('~db=[^&]*~',"db=".urlencode($r["db"]),ME):($r["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".urlencode($r["ns"]),ME):ME));echo"<td><a href='".h($A."table=".urlencode($r["table"]))."'>".($r["db"]!=""&&$r["db"]!=DB?"<b>".h($r["db"])."</b>.":"").($r["ns"]!=""&&$r["ns"]!=$_GET["ns"]?"<b>".h($r["ns"])."</b>.":"").h($r["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$r["target"]))."</i>)","<td>".h($r["on_delete"]),"<td>".h($r["on_update"]),'<td><a href="'.h(ME.'foreign='.urlencode($a).'&name='.urlencode($C)).'">'.'Alter'.'</a>',"\n";}echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'foreign='.urlencode($a).'">'.'Add foreign key'."</a>\n";}if(support("check")){echo"<h3 id='checks'>".'Checks'."</h3>\n";$Va=$m->checkConstraints($a);if($Va){echo"<table>\n";foreach($Va
as$z=>$X)echo"<tr title='".h($z)."'>","<td><code class='jush-".JUSH."'>".h($X),"<td><a href='".h(ME.'check='.urlencode($a).'&name='.urlencode($z))."'>".'Alter'."</a>","\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'check='.urlencode($a).'">'.'Create check'."</a>\n";}}if(support(is_view($S)?"view_trigger":"trigger")){echo"<h3 id='triggers'>".'Triggers'."</h3>\n";$Gi=triggers($a);if($Gi){echo"<table>\n";foreach($Gi
as$z=>$X)echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($z)."<td><a href='".h(ME.'trigger='.urlencode($a).'&name='.urlencode($z))."'>".'Alter'."</a>\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'trigger='.urlencode($a).'">'.'Add trigger'."</a>\n";}}elseif(isset($_GET["schema"])){page_header('Database schema',"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));$Zh=array();$ai=array();$ea=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$ea,$Ge,PREG_SET_ORDER);foreach($Ge
as$u=>$B){$Zh[$B[1]]=array($B[2],$B[3]);$ai[]="\n\t'".js_escape($B[1])."': [ $B[2], $B[3] ]";}$wi=0;$Ga=-1;$dh=array();$Kg=array();$ue=array();foreach(table_status('',true)as$R=>$S){if(is_view($S))continue;$mg=0;$dh[$R]["fields"]=array();foreach(fields($R)as$C=>$o){$mg+=1.25;$o["pos"]=$mg;$dh[$R]["fields"][$C]=$o;}$dh[$R]["pos"]=($Zh[$R]?:array($wi,0));foreach($b->foreignKeys($R)as$X){if(!$X["db"]){$se=$Ga;if($Zh[$R][1]||$Zh[$X["table"]][1])$se=min(floatval($Zh[$R][1]),floatval($Zh[$X["table"]][1]))-1;else$Ga-=.1;while($ue[(string)$se])$se-=.0001;$dh[$R]["references"][$X["table"]][(string)$se]=array($X["source"],$X["target"]);$Kg[$X["table"]][$R][(string)$se]=$X["target"];$ue[(string)$se]=true;}}$wi=max($wi,$dh[$R]["pos"][0]+2.5+$mg);}echo'<div id="schema" style="height: ',$wi,'em;">
<script',nonce(),'>
qs(\'#schema\').onselectstart = () => false;
const tablePos = {',implode(",",$ai)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$wi,';
document.onmousemove = schemaMousemove;
document.onmouseup = partialArg(schemaMouseup, \'',js_escape(DB),'\');
</script>
';foreach($dh
as$C=>$R){echo"<div class='table' style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em;'>",'<a href="'.h(ME).'table='.urlencode($C).'"><b>'.h($C)."</b></a>",script("qsl('div').onmousedown = schemaMousedown;");foreach($R["fields"]as$o){$X='<span'.type_class($o["type"]).' title="'.h($o["full_type"].($o["null"]?" NULL":'')).'">'.h($o["field"]).'</span>';echo"<br>".($o["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$gi=>$Mg){foreach($Mg
as$se=>$Hg){$te=$se-$Zh[$C][1];$u=0;foreach($Hg[0]as$Bh)echo"\n<div class='references' title='".h($gi)."' id='refs$se-".($u++)."' style='left: $te"."em; top: ".$R["fields"][$Bh]["pos"]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: ".(-$te)."em;'></div></div>";}}foreach((array)$Kg[$C]as$gi=>$Mg){foreach($Mg
as$se=>$f){$te=$se-$Zh[$C][1];$u=0;foreach($f
as$fi)echo"\n<div class='references' title='".h($gi)."' id='refd$se-".($u++)."'"." style='left: $te"."em; top: ".$R["fields"][$fi]["pos"]."em; height: 1.25em; background: url(".h(preg_replace("~\\?.*~","",ME)."?file=arrow.gif) no-repeat right center;&version=5.1.0")."'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$te)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($dh
as$C=>$R){foreach((array)$R["references"]as$gi=>$Mg){foreach($Mg
as$se=>$Hg){$Ve=$wi;$Ke=-10;foreach($Hg[0]as$z=>$Bh){$ng=$R["pos"][0]+$R["fields"][$Bh]["pos"];$og=$dh[$gi]["pos"][0]+$dh[$gi]["fields"][$Hg[1][$z]]["pos"];$Ve=min($Ve,$ng,$og);$Ke=max($Ke,$ng,$og);}echo"<div class='references' id='refl$se' style='left: $se"."em; top: $Ve"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($Ke-$Ve)."em;'></div></div>\n";}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".urlencode($ea)),'" id="schema-link">Permanent link</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$n){save_settings(array_intersect_key($_POST,array_flip(array("output","format","db_style","types","routines","events","table_style","auto_increment","triggers","data_style"))),"adminer_export");$T=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$Hc=dump_headers((count($T)==1?key($T):DB),(DB==""||count($T)>1));$de=preg_match('~sql~',$_POST["format"]);if($de){echo"-- Adminer $ia ".$ac[DRIVER]." ".str_replace("\n"," ",$g->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";$g->query("SET time_zone = '+00:00'");$g->query("SET sql_mode = ''");}}$Ph=$_POST["db_style"];$j=array(DB);if(DB==""){$j=$_POST["databases"];if(is_string($j))$j=explode("\n",rtrim(str_replace("\r","",$j),"\n"));}foreach((array)$j
as$k){$b->dumpDatabase($k);if($g->select_db($k)){if($de&&preg_match('~CREATE~',$Ph)&&($i=get_val("SHOW CREATE DATABASE ".idf_escape($k),1))){set_utf8mb4($i);if($Ph=="DROP+CREATE")echo"DROP DATABASE IF EXISTS ".idf_escape($k).";\n";echo"$i;\n";}if($de){if($Ph)echo
use_sql($k).";\n\n";$Qf="";if($_POST["types"]){foreach(types()as$v=>$U){$wc=type_values($v);if($wc)$Qf.=($Ph!='DROP+CREATE'?"DROP TYPE IF EXISTS ".idf_escape($U).";;\n":"")."CREATE TYPE ".idf_escape($U)." AS ENUM ($wc);\n\n";else$Qf.="-- Could not export type $U\n\n";}}if($_POST["routines"]){foreach(routines()as$K){$C=$K["ROUTINE_NAME"];$Xg=$K["ROUTINE_TYPE"];$i=create_routine($Xg,array("name"=>$C)+routine($K["SPECIFIC_NAME"],$Xg));set_utf8mb4($i);$Qf.=($Ph!='DROP+CREATE'?"DROP $Xg IF EXISTS ".idf_escape($C).";;\n":"")."$i;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$K){$i=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($K["Name"]),3));set_utf8mb4($i);$Qf.=($Ph!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($K["Name"]).";;\n":"")."$i;;\n\n";}}echo($Qf&&JUSH=='sql'?"DELIMITER ;;\n\n$Qf"."DELIMITER ;\n\n":$Qf);}if($_POST["table_style"]||$_POST["data_style"]){$ij=array();foreach(table_status('',true)as$C=>$S){$R=(DB==""||in_array($C,(array)$_POST["tables"]));$Hb=(DB==""||in_array($C,(array)$_POST["data"]));if($R||$Hb){if($Hc=="tar"){$ti=new
TmpFile;ob_start(array($ti,'write'),1e5);}$b->dumpTable($C,($R?$_POST["table_style"]:""),(is_view($S)?2:0));if(is_view($S))$ij[]=$C;elseif($Hb){$p=fields($C);$b->dumpData($C,$_POST["data_style"],"SELECT *".convert_fields($p,$p)." FROM ".table($C));}if($de&&$_POST["triggers"]&&$R&&($Gi=trigger_sql($C)))echo"\nDELIMITER ;;\n$Gi\nDELIMITER ;\n";if($Hc=="tar"){ob_end_flush();tar_file((DB!=""?"":"$k/")."$C.csv",$ti);}elseif($de)echo"\n";}}if(function_exists('Adminer\foreign_keys_sql')){foreach(table_status('',true)as$C=>$S){$R=(DB==""||in_array($C,(array)$_POST["tables"]));if($R&&!is_view($S))echo
foreign_keys_sql($C);}}foreach($ij
as$hj)$b->dumpTable($hj,$_POST["table_style"],1);if($Hc=="tar")echo
pack("x512");}}}$b->dumpFooter();exit;}page_header('Export',$n,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$Lb=array('','USE','DROP+CREATE','CREATE');$bi=array('','DROP+CREATE','CREATE');$Ib=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Ib[]='INSERT+UPDATE';$K=get_settings("adminer_export");if(!$K)$K=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"table_style"=>"DROP+CREATE","data_style"=>"INSERT");if(!isset($K["events"])){$K["routines"]=$K["events"]=($_GET["dump"]=="");$K["triggers"]=$K["table_style"];}echo"<tr><th>".'Output'."<td>".html_radios("output",$b->dumpOutput(),$K["output"])."\n","<tr><th>".'Format'."<td>".html_radios("format",$b->dumpFormat(),$K["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".'Database'."<td>".html_select('db_style',$Lb,$K["db_style"]).(support("type")?checkbox("types",1,$K["types"],'User types'):"").(support("routine")?checkbox("routines",1,$K["routines"],'Routines'):"").(support("event")?checkbox("events",1,$K["events"],'Events'):"")),"<tr><th>".'Tables'."<td>".html_select('table_style',$bi,$K["table_style"]).checkbox("auto_increment",1,$K["auto_increment"],'Auto Increment').(support("trigger")?checkbox("triggers",1,$K["triggers"],'Triggers'):""),"<tr><th>".'Data'."<td>".html_select('data_style',$Ib,$K["data_style"]),'</table>
<p><input type="submit" value="Export">
',input_token(),'
<table>
',script("qsl('table').onclick = dumpClick;");$sg=array();if(DB!=""){$Xa=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$Xa>".'Tables'."</label>".script("qs('#check-tables').onclick = partial(formCheck, /^tables\\[/);",""),"<th style='text-align: right;'><label class='block'>".'Data'."<input type='checkbox' id='check-data'$Xa></label>".script("qs('#check-data').onclick = partial(formCheck, /^data\\[/);",""),"</thead>\n";$ij="";$ci=tables_list();foreach($ci
as$C=>$U){$rg=preg_replace('~_.*~','',$C);$Xa=($a==""||$a==(substr($a,-1)=="%"?"$rg%":$C));$ug="<tr><td>".checkbox("tables[]",$C,$Xa,$C,"","block");if($U!==null&&!preg_match('~table~i',$U))$ij.="$ug\n";else
echo"$ug<td align='right'><label class='block'><span id='Rows-".h($C)."'></span>".checkbox("data[]",$C,$Xa)."</label>\n";$sg[$rg]++;}echo$ij;if($ci)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-databases'".($a==""?" checked":"").">".'Database'."</label>",script("qs('#check-databases').onclick = partial(formCheck, /^databases\\[/);",""),"</thead>\n";$j=$b->databases();if($j){foreach($j
as$k){if(!information_schema($k)){$rg=preg_replace('~_.*~','',$k);echo"<tr><td>".checkbox("databases[]",$k,$a==""||$a=="$rg%",$k,"","block")."\n";$sg[$rg]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$Uc=true;foreach($sg
as$z=>$X){if($z!=""&&$X>1){echo($Uc?"<p>":" ")."<a href='".h(ME)."dump=".urlencode("$z%")."'>".h($z)."</a>";$Uc=false;}}}elseif(isset($_GET["privileges"])){page_header('Privileges');echo'<p class="links"><a href="'.h(ME).'user=">'.'Create user'."</a>";$I=$g->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$nd=$I;if(!$I)$I=$g->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($nd?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".'Username'."<th>".'Server'."<th></thead>\n";while($K=$I->fetch_assoc())echo'<tr><td>'.h($K["User"])."<td>".h($K["Host"]).'<td><a href="'.h(ME.'user='.urlencode($K["User"]).'&host='.urlencode($K["Host"])).'">'.'Edit'."</a>\n";if(!$nd||DB!="")echo"<tr><td><input name='user' autocapitalize='off'><td><input name='host' value='localhost' autocapitalize='off'><td><input type='submit' value='".'Edit'."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$n&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");$b->dumpTable("","");$b->dumpData("","table",$_POST["query"]);$b->dumpFooter();exit;}restart_session();$Ed=&get_session("queries");$Dd=&$Ed[DB];if(!$n&&$_POST["clear"]){$Dd=array();redirect(remove_from_uri("history"));}page_header((isset($_GET["import"])?'Import':'SQL command'),$n);if(!$n&&$_POST){$s=false;if(!isset($_GET["import"]))$H=$_POST["query"];elseif($_POST["webfile"]){$Gh=$b->importServerPath();$s=@fopen((file_exists($Gh)?$Gh:"compress.zlib://$Gh.gz"),"rb");$H=($s?fread($s,1e6):false);}else$H=get_file("sql_file",true,";");if(is_string($H)){if(function_exists('memory_get_usage')&&($Pe=ini_bytes("memory_limit"))!="-1")@ini_set("memory_limit",max($Pe,2*strlen($H)+memory_get_usage()+8e6));if($H!=""&&strlen($H)<1e6){$Ag=$H.(preg_match("~;[ \t\r\n]*\$~",$H)?"":";");if(!$Dd||first(end($Dd))!=$Ag){restart_session();$Dd[]=array($Ag,time());set_session("queries",$Ed);stop_session();}}$Ch="(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";$Rb=";";$D=0;$qc=true;$h=connect($b->credentials());if(is_object($h)&&DB!=""){$h->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$h);}$lb=0;$yc=array();$Xf='[\'"'.(JUSH=="sql"?'`#':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|-- |$'.(JUSH=="pgsql"?'|\$[^$]*\$':'');$xi=microtime(true);$oa=get_settings("adminer_import");$hc=$b->dumpFormat();unset($hc["sql"]);while($H!=""){if(!$D&&preg_match("~^$Ch*+DELIMITER\\s+(\\S+)~i",$H,$B)){$Rb=$B[1];$H=substr($H,strlen($B[0]));}else{preg_match('('.preg_quote($Rb)."\\s*|$Xf)",$H,$B,PREG_OFFSET_CAPTURE,$D);list($gd,$mg)=$B[0];if(!$gd&&$s&&!feof($s))$H.=fread($s,1e5);else{if(!$gd&&rtrim($H)=="")break;$D=$mg+strlen($gd);if($gd&&rtrim($gd)!=$Rb){$Qa=$m->hasCStyleEscapes()||(JUSH=="pgsql"&&($mg>0&&strtolower($H[$mg-1])=="e"));$fg=($gd=='/*'?'\*/':($gd=='['?']':(preg_match('~^-- |^#~',$gd)?"\n":preg_quote($gd).($Qa?"|\\\\.":""))));while(preg_match("($fg|\$)s",$H,$B,PREG_OFFSET_CAPTURE,$D)){$bh=$B[0][0];if(!$bh&&$s&&!feof($s))$H.=fread($s,1e5);else{$D=$B[0][1]+strlen($bh);if(!$bh||$bh[0]!="\\")break;}}}else{$qc=false;$Ag=substr($H,0,$mg);$lb++;$ug="<pre id='sql-$lb'><code class='jush-".JUSH."'>".$b->sqlCommandQuery($Ag)."</code></pre>\n";if(JUSH=="sqlite"&&preg_match("~^$Ch*+ATTACH\\b~i",$Ag,$B)){echo$ug,"<p class='error'>".'ATTACH queries are not supported.'."\n";$yc[]=" <a href='#sql-$lb'>$lb</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$ug;ob_flush();flush();}$Lh=microtime(true);if($g->multi_query($Ag)&&is_object($h)&&preg_match("~^$Ch*+USE\\b~i",$Ag))$h->query($Ag);do{$I=$g->store_result();if($g->error){echo($_POST["only_errors"]?$ug:""),"<p class='error'>".'Error in query'.($g->errno?" ($g->errno)":"").": ".error()."\n";$yc[]=" <a href='#sql-$lb'>$lb</a>";if($_POST["error_stops"])break
2;}else{$mi=" <span class='time'>(".format_time($Lh).")</span>".(strlen($Ag)<1000?" <a href='".h(ME)."sql=".urlencode(trim($Ag))."'>".'Edit'."</a>":"");$qa=$g->affected_rows;$lj=($_POST["only_errors"]?"":$m->warnings());$mj="warnings-$lb";if($lj)$mi.=", <a href='#$mj'>".'Warnings'."</a>".script("qsl('a').onclick = partial(toggle, '$mj');","");$Fc=null;$Gc="explain-$lb";if(is_object($I)){$_=$_POST["limit"];$If=select($I,$h,array(),$_);if(!$_POST["only_errors"]){echo"<form action='' method='post'>\n";$kf=$I->num_rows;echo"<p class='sql-footer'>".($kf?($_&&$kf>$_?sprintf('%d / ',$_):"").lang(array('%d row','%d rows'),$kf):""),$mi;if($h&&preg_match("~^($Ch|\\()*+SELECT\\b~i",$Ag)&&($Fc=explain($h,$Ag)))echo", <a href='#$Gc'>Explain</a>".script("qsl('a').onclick = partial(toggle, '$Gc');","");$v="export-$lb";echo", <a href='#$v'>".'Export'."</a>".script("qsl('a').onclick = partial(toggle, '$v');","")."<span id='$v' class='hidden'>: ".html_select("output",$b->dumpOutput(),$oa["output"])." ".html_select("format",$hc,$oa["format"]).input_hidden("query",$Ag)."<input type='submit' name='export' value='".'Export'."'>".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$Ch*+(CREATE|DROP|ALTER)$Ch++(DATABASE|SCHEMA)\\b~i",$Ag)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(isset($g->info)?$g->info:"")."'>".lang(array('Query executed OK, %d row affected.','Query executed OK, %d rows affected.'),$qa)."$mi\n";}echo($lj?"<div id='$mj' class='hidden'>\n$lj</div>\n":"");if($Fc){echo"<div id='$Gc' class='hidden explain'>\n";select($Fc,$h,$If);echo"</div>\n";}}$Lh=microtime(true);}while($g->next_result());}$H=substr($H,$D);$D=0;}}}}if($qc)echo"<p class='message'>".'No commands to execute.'."\n";elseif($_POST["only_errors"])echo"<p class='message'>".lang(array('%d query executed OK.','%d queries executed OK.'),$lb-count($yc))," <span class='time'>(".format_time($xi).")</span>\n";elseif($yc&&$lb>1)echo"<p class='error'>".'Error in query'.": ".implode("",$yc)."\n";}else
echo"<p class='error'>".upload_error($H)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form">
';$Dc="<input type='submit' value='".'Execute'."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$Ag=$_GET["sql"];if($_POST)$Ag=$_POST["query"];elseif($_GET["history"]=="all")$Ag=$Dd;elseif($_GET["history"]!="")$Ag=$Dd[$_GET["history"]][0];echo"<p>";textarea("query",$Ag,20);echo
script(($_POST?"":"qs('textarea').focus();\n")."qs('#form').onsubmit = partial(sqlSubmit, qs('#form'), '".js_escape(remove_from_uri("sql|limit|error_stops|only_errors|history"))."');"),"<p>";$b->sqlPrintAfter();echo"$Dc\n",'Limit rows'.": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{echo"<fieldset><legend>".'File upload'."</legend><div>";$td=(extension_loaded("zlib")?"[.gz]":"");echo(ini_bool("file_uploads")?"SQL$td (&lt; ".ini_get("upload_max_filesize")."B): <input type='file' name='sql_file[]' multiple>\n$Dc":'File uploads are disabled.'),"</div></fieldset>\n";$Ld=$b->importServerPath();if($Ld)echo"<fieldset><legend>".'From server'."</legend><div>",sprintf('Webserver file %s',"<code>".h($Ld)."$td</code>"),' <input type="submit" name="webfile" value="'.'Run file'.'">',"</div></fieldset>\n";echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),'Stop on error')."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),'Show only errors')."\n",input_token();if(!isset($_GET["import"])&&$Dd){print_fieldset("history",'History',$_GET["history"]!="");for($X=end($Dd);$X;$X=prev($Dd)){$z=key($Dd);list($Ag,$mi,$lc)=$X;echo'<a href="'.h(ME."sql=&history=$z").'">'.'Edit'."</a>"." <span class='time' title='".@date('Y-m-d',$mi)."'>".@date("H:i:s",$mi)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(ltrim(str_replace("\n"," ",str_replace("\r","",preg_replace('~^(#|-- ).*~m','',$Ag)))),80,"</code>").($lc?" <span class='time'>($lc)</span>":"")."<br>\n";}echo"<input type='submit' name='clear' value='".'Clear'."'>\n","<a href='".h(ME."sql=&history=all")."'>".'Edit all'."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$p=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$p):""):where($_GET,$p));$Si=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($p
as$C=>$o){if(!isset($o["privileges"][$Si?"update":"insert"])||$b->fieldName($o)==""||$o["generated"])unset($p[$C]);}if($_POST&&!$n&&!isset($_GET["select"])){$_e=$_POST["referer"];if($_POST["insert"])$_e=($Si?null:$_SERVER["REQUEST_URI"]);elseif(!preg_match('~^.+&select=.+$~',$_e))$_e=ME."select=".urlencode($a);$y=indexes($a);$Ni=unique_array($_GET["where"],$y);$Dg="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($_e,'Item has been deleted.',$m->delete($a,$Dg,!$Ni));else{$O=array();foreach($p
as$C=>$o){$X=process_input($o);if($X!==false&&$X!==null)$O[idf_escape($C)]=$X;}if($Si){if(!$O)redirect($_e);queries_redirect($_e,'Item has been updated.',$m->update($a,$O,$Dg,!$Ni));if(is_ajax()){page_headers();page_messages($n);exit;}}else{$I=$m->insert($a,$O);$re=($I?last_id($I):0);queries_redirect($_e,sprintf('Item%s has been inserted.',($re?" $re":"")),$I);}}}$K=null;if($_POST["save"])$K=(array)$_POST["fields"];elseif($Z){$M=array();foreach($p
as$C=>$o){if(isset($o["privileges"]["select"])){$wa=($_POST["clone"]&&$o["auto_increment"]?"''":convert_field($o));$M[]=($wa?"$wa AS ":"").idf_escape($C);}}$K=array();if(!support("table"))$M=array("*");if($M){$I=$m->select($a,$M,array($Z),$M,array(),(isset($_GET["select"])?2:1));if(!$I)$n=error();else{$K=$I->fetch_assoc();if(!$K)$K=false;}if(isset($_GET["select"])&&(!$K||$I->fetch_assoc()))$K=null;}}if(!support("table")&&!$p){if(!$Z){$I=$m->select($a,array("*"),$Z,array("*"));$K=($I?$I->fetch_assoc():false);if(!$K)$K=array($m->primary=>"");}if($K){foreach($K
as$z=>$X){if(!$Z)$K[$z]=null;$p[$z]=array("field"=>$z,"null"=>($z!=$m->primary),"auto_increment"=>($z==$m->primary));}}}edit_form($a,$p,$K,$Si);}elseif(isset($_GET["create"])){$a=$_GET["create"];$Zf=array();foreach(array('HASH','LINEAR HASH','KEY','LINEAR KEY','RANGE','LIST')as$z)$Zf[$z]=$z;$Jg=referencable_primary($a);$ed=array();foreach($Jg
as$Xh=>$o)$ed[str_replace("`","``",$Xh)."`".str_replace("`","``",$o["field"])]=$Xh;$Lf=array();$S=array();if($a!=""){$Lf=fields($a);$S=table_status($a);if(!$S)$n='No tables.';}$K=$_POST;$K["fields"]=(array)$K["fields"];if($K["auto_increment_col"])$K["fields"][$K["auto_increment_col"]]["auto_increment"]=true;if($_POST)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($K["fields"])&&!$n){if($_POST["drop"])queries_redirect(substr(ME,0,-1),'Table has been dropped.',drop_tables(array($a)));else{$p=array();$ua=array();$Wi=false;$cd=array();$Kf=reset($Lf);$sa=" FIRST";foreach($K["fields"]as$z=>$o){$r=$ed[$o["type"]];$Hi=($r!==null?$Jg[$r]:$o);if($o["field"]!=""){if(!$o["generated"])$o["default"]=null;$zg=process_field($o,$Hi);$ua[]=array($o["orig"],$zg,$sa);if(!$Kf||$zg!==process_field($Kf,$Kf)){$p[]=array($o["orig"],$zg,$sa);if($o["orig"]!=""||$sa)$Wi=true;}if($r!==null)$cd[idf_escape($o["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$ed[$o["type"]],'source'=>array($o["field"]),'target'=>array($Hi["field"]),'on_delete'=>$o["on_delete"],));$sa=" AFTER ".idf_escape($o["field"]);}elseif($o["orig"]!=""){$Wi=true;$p[]=array($o["orig"]);}if($o["orig"]!=""){$Kf=next($Lf);if(!$Kf)$sa="";}}$bg="";if(support("partitioning")){if(isset($Zf[$K["partition_by"]])){$Wf=array();foreach($K
as$z=>$X){if(preg_match('~^partition~',$z))$Wf[$z]=$X;}foreach($Wf["partition_names"]as$z=>$C){if($C==""){unset($Wf["partition_names"][$z]);unset($Wf["partition_values"][$z]);}}if($Wf!=get_partitions_info($a)){$cg=array();if($Wf["partition_by"]=='RANGE'||$Wf["partition_by"]=='LIST'){foreach($Wf["partition_names"]as$z=>$C){$Y=$Wf["partition_values"][$z];$cg[]="\n  PARTITION ".idf_escape($C)." VALUES ".($Wf["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$bg.="\nPARTITION BY $Wf[partition_by]($Wf[partition])";if($cg)$bg.=" (".implode(",",$cg)."\n)";elseif($Wf["partitions"])$bg.=" PARTITIONS ".(+$Wf["partitions"]);}}elseif(preg_match("~partitioned~",$S["Create_options"]))$bg.="\nREMOVE PARTITIONING";}$Qe='Table has been altered.';if($a==""){cookie("adminer_engine",$K["Engine"]);$Qe='Table has been created.';}$C=trim($K["name"]);queries_redirect(ME.(support("table")?"table=":"select=").urlencode($C),$Qe,alter_table($a,$C,(JUSH=="sqlite"&&($Wi||$cd)?$ua:$p),$cd,($K["Comment"]!=$S["Comment"]?$K["Comment"]:null),($K["Engine"]&&$K["Engine"]!=$S["Engine"]?$K["Engine"]:""),($K["Collation"]&&$K["Collation"]!=$S["Collation"]?$K["Collation"]:""),($K["Auto_increment"]!=""?number($K["Auto_increment"]):""),$bg));}}page_header(($a!=""?'Alter table':'Create table'),$n,array("table"=>$a),h($a));if(!$_POST){$Ji=$m->types();$K=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($Ji["int"])?"int":(isset($Ji["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$K=$S;$K["name"]=$a;$K["fields"]=array();if(!$_GET["auto_increment"])$K["Auto_increment"]="";foreach($Lf
as$o){$o["generated"]=$o["generated"]?:(isset($o["default"])?"DEFAULT":"");$K["fields"][]=$o;}if(support("partitioning")){$K+=get_partitions_info($a);$K["partition_names"][]="";$K["partition_values"][]="";}}}$hb=collations();$sc=$m->engines();foreach($sc
as$rc){if(!strcasecmp($rc,$K["Engine"])){$K["Engine"]=$rc;break;}}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo'Table name'.": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($K["name"])."' autocapitalize='off'>\n",($sc?html_select("Engine",array(""=>"(".'engine'.")")+$sc,$K["Engine"]).on_help("event.target.value",1).script("qsl('select').onchange = helpClose;")."\n":"");if($hb)echo"<datalist id='collations'>".optionlist($hb)."</datalist>",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($K["Collation"])."' placeholder='(".'collation'.")'>");echo"<input type='submit' value='".'Save'."'>\n";}if(support("columns")){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($K["fields"],$hb,"TABLE",$ed);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",'Auto Increment'.": <input type='number' name='Auto_increment' class='size' value='".h($K["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),'Default values',"columnShow(this.checked, 5)","jsonly");$ob=($_POST?$_POST["comments"]:get_setting("comments"));echo(support("comment")?checkbox("comments",1,$ob,'Comment',"editingCommentsClick(this, true);","jsonly").' '.(preg_match('~\n~',$K["Comment"])?"<textarea name='Comment' rows='2' cols='20'".($ob?"":" class='hidden'").">".h($K["Comment"])."</textarea>":'<input name="Comment" value="'.h($K["Comment"]).'" data-maxlength="'.(min_version(5.5)?2048:60).'"'.($ob?"":" class='hidden'").'>'):''),'<p>
<input type="submit" value="Save">
';}echo'
';if($a!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$a));if(support("partitioning")){$ag=preg_match('~RANGE|LIST~',$K["partition_by"]);print_fieldset("partition",'Partition by',$K["partition_by"]);echo"<p>".html_select("partition_by",array(""=>"")+$Zf,$K["partition_by"]).on_help("event.target.value.replace(/./, 'PARTITION BY \$&')",1).script("qsl('select').onchange = partitionByChange;"),"(<input name='partition' value='".h($K["partition"])."'>)\n",'Partitions'.": <input type='number' name='partitions' class='size".($ag||!$K["partition_by"]?" hidden":"")."' value='".h($K["partitions"])."'>\n","<table id='partition-table'".($ag?"":" class='hidden'").">\n","<thead><tr><th>".'Partition name'."<th>".'Values'."</thead>\n";foreach($K["partition_names"]as$z=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off">',($z==count($K["partition_names"])-1?script("qsl('input').oninput = partitionNameChange;"):''),'<td><input name="partition_values[]" value="'.h($K["partition_values"][$z]).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Qd=array("PRIMARY","UNIQUE","INDEX");$S=table_status($a,true);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$Qd[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$Qd[]="SPATIAL";$y=indexes($a);$G=array();if(JUSH=="mongo"){$G=$y["_id_"];unset($Qd[0]);unset($y["_id_"]);}$K=$_POST;if($K)save_settings(array("index_options"=>$K["options"]));if($_POST&&!$n&&!$_POST["add"]&&!$_POST["drop_col"]){$c=array();foreach($K["indexes"]as$x){$C=$x["name"];if(in_array($x["type"],$Qd)){$f=array();$xe=array();$Tb=array();$O=array();ksort($x["columns"]);foreach($x["columns"]as$z=>$e){if($e!=""){$we=$x["lengths"][$z];$Sb=$x["descs"][$z];$O[]=idf_escape($e).($we?"(".(+$we).")":"").($Sb?" DESC":"");$f[]=$e;$xe[]=($we?:null);$Tb[]=$Sb;}}$Ec=$y[$C];if($Ec){ksort($Ec["columns"]);ksort($Ec["lengths"]);ksort($Ec["descs"]);if($x["type"]==$Ec["type"]&&array_values($Ec["columns"])===$f&&(!$Ec["lengths"]||array_values($Ec["lengths"])===$xe)&&array_values($Ec["descs"])===$Tb){unset($y[$C]);continue;}}if($f)$c[]=array($x["type"],$C,$O);}}foreach($y
as$C=>$Ec)$c[]=array($Ec["type"],$C,"DROP");if(!$c)redirect(ME."table=".urlencode($a));queries_redirect(ME."table=".urlencode($a),'Indexes have been altered.',alter_indexes($a,$c));}page_header('Indexes',$n,array("table"=>$a),h($a));$p=array_keys(fields($a));if($_POST["add"]){foreach($K["indexes"]as$z=>$x){if($x["columns"][count($x["columns"])]!="")$K["indexes"][$z]["columns"][]="";}$x=end($K["indexes"]);if($x["type"]||array_filter($x["columns"],'strlen'))$K["indexes"][]=array("columns"=>array(1=>""));}if(!$K){foreach($y
as$z=>$x){$y[$z]["name"]=$z;$y[$z]["columns"][]="";}$y[]=array("columns"=>array(1=>""));$K["indexes"]=$y;}$xe=(JUSH=="sql"||JUSH=="mssql");$wh=($_POST?$_POST["options"]:get_setting("index_options"));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap">
<thead><tr>
<th id="label-type">Index Type
<th><input type="submit" class="wayoff">','Column'.($xe?"<span class='idxopts".($wh?"":" hidden")."'> (".'length'.")</span>":"");if($xe||support("descidx"))echo
checkbox("options",1,$wh,'Options',"indexOptionsShow(this.checked)","jsonly")."\n";echo'<th id="label-name">Name
<th><noscript>',"<input type='image' class='icon' name='add[0]' src='".h(preg_replace("~\\?.*~","",ME)."?file=plus.gif&version=5.1.0")."' alt='+' title='".'Add next'."'>",'</noscript>
</thead>
';if($G){echo"<tr><td>PRIMARY<td>";foreach($G["columns"]as$z=>$e)echo
select_input(" disabled",$p,$e),"<label><input disabled type='checkbox'>".'descending'."</label> ";echo"<td><td>\n";}$ge=1;foreach($K["indexes"]as$x){if(!$_POST["drop_col"]||$ge!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$ge][type]",array(-1=>"")+$Qd,$x["type"],($ge==count($K["indexes"])?"indexesAddRow.call(this);":""),"label-type"),"<td>";ksort($x["columns"]);$u=1;foreach($x["columns"]as$z=>$e){echo"<span>".select_input(" name='indexes[$ge][columns][$u]' title='".'Column'."'",($p?array_combine($p,$p):$p),$e,"partial(".($u==count($x["columns"])?"indexesAddColumn":"indexesChangeColumn").", '".js_escape(JUSH=="sql"?"":$_GET["indexes"]."_")."')"),"<span class='idxopts".($wh?"":" hidden")."'>",($xe?"<input type='number' name='indexes[$ge][lengths][$u]' class='size' value='".h($x["lengths"][$z])."' title='".'Length'."'>":""),(support("descidx")?checkbox("indexes[$ge][descs][$u]",1,$x["descs"][$z],'descending'):""),"</span> </span>";$u++;}echo"<td><input name='indexes[$ge][name]' value='".h($x["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n","<td><input type='image' class='icon' name='drop_col[$ge]' src='".h(preg_replace("~\\?.*~","",ME)."?file=cross.gif&version=5.1.0")."' alt='x' title='".'Remove'."'>".script("qsl('input').onclick = partial(editingRemoveRow, 'indexes\$1[type]');");}$ge++;}echo'</table>
</div>
<p>
<input type="submit" value="Save">
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$K=$_POST;if($_POST&&!$n&&!isset($_POST["add_x"])){$C=trim($K["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),'Database has been dropped.',drop_databases(array(DB)));}elseif(DB!==$C){if(DB!=""){$_GET["db"]=$C;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".urlencode($C),'Database has been renamed.',rename_database($C,$K["collation"]));}else{$j=explode("\n",str_replace("\r","",$C));$Qh=true;$qe="";foreach($j
as$k){if(count($j)==1||$k!=""){if(!create_database($k,$K["collation"]))$Qh=false;$qe=$k;}}restart_session();set_session("dbs",null);queries_redirect(ME."db=".urlencode($qe),'Database has been created.',$Qh);}}else{if(!$K["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($C).(preg_match('~^[a-z0-9_]+$~i',$K["collation"])?" COLLATE $K[collation]":""),substr(ME,0,-1),'Database has been altered.');}}page_header(DB!=""?'Alter database':'Create database',$n,array(),h(DB));$hb=collations();$C=DB;if($_POST)$C=$K["name"];elseif(DB!="")$K["collation"]=db_collation(DB,$hb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$nd){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$nd,$B)&&$B[1]){$C=stripcslashes(idf_unescape("`$B[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add_x"]||strpos($C,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($C).'</textarea><br>':'<input name="name" autofocus value="'.h($C).'" data-maxlength="64" autocapitalize="off">')."\n".($hb?html_select("collation",array(""=>"(".'collation'.")")+$hb,$K["collation"]).doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",'mssql'=>"relational-databases/system-functions/sys-fn-helpcollations-transact-sql",)):""),'<input type="submit" value="Save">
';if(DB!="")echo"<input type='submit' name='drop' value='".'Drop'."'>".confirm(sprintf('Drop %s?',DB))."\n";elseif(!$_POST["add_x"]&&$_GET["db"]=="")echo"<input type='image' class='icon' name='add' src='".h(preg_replace("~\\?.*~","",ME)."?file=plus.gif&version=5.1.0")."' alt='+' title='".'Add next'."'>\n";echo
input_token(),'</form>
';}elseif(isset($_GET["scheme"])){$K=$_POST;if($_POST&&!$n){$A=preg_replace('~ns=[^&]*&~','',ME)."ns=";if($_POST["drop"])query_redirect("DROP SCHEMA ".idf_escape($_GET["ns"]),$A,'Schema has been dropped.');else{$C=trim($K["name"]);$A.=urlencode($C);if($_GET["ns"]=="")query_redirect("CREATE SCHEMA ".idf_escape($C),$A,'Schema has been created.');elseif($_GET["ns"]!=$C)query_redirect("ALTER SCHEMA ".idf_escape($_GET["ns"])." RENAME TO ".idf_escape($C),$A,'Schema has been altered.');else
redirect($A);}}page_header($_GET["ns"]!=""?'Alter schema':'Create schema',$n);if(!$K)$K["name"]=$_GET["ns"];echo'
<form action="" method="post">
<p><input name="name" autofocus value="',h($K["name"]),'" autocapitalize="off">
<input type="submit" value="Save">
';if($_GET["ns"]!="")echo"<input type='submit' name='drop' value='".'Drop'."'>".confirm(sprintf('Drop %s?',$_GET["ns"]))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$da=($_GET["name"]?:$_GET["call"]);page_header('Call'.": ".h($da),$n);$Xg=routine($_GET["call"],(isset($_GET["callf"])?"FUNCTION":"PROCEDURE"));$Md=array();$Qf=array();foreach($Xg["fields"]as$u=>$o){if(substr($o["inout"],-3)=="OUT")$Qf[$u]="@".idf_escape($o["field"])." AS ".idf_escape($o["field"]);if(!$o["inout"]||substr($o["inout"],0,2)=="IN")$Md[]=$u;}if(!$n&&$_POST){$Ra=array();foreach($Xg["fields"]as$z=>$o){if(in_array($z,$Md)){$X=process_input($o);if($X===false)$X="''";if(isset($Qf[$z]))$g->query("SET @".idf_escape($o["field"])." = $X");}$Ra[]=(isset($Qf[$z])?"@".idf_escape($o["field"]):$X);}$H=(isset($_GET["callf"])?"SELECT":"CALL")." ".table($da)."(".implode(", ",$Ra).")";$Lh=microtime(true);$I=$g->multi_query($H);$qa=$g->affected_rows;echo$b->selectQuery($H,$Lh,!$I);if(!$I)echo"<p class='error'>".error()."\n";else{$h=connect($b->credentials());if(is_object($h))$h->select_db(DB);do{$I=$g->store_result();if(is_object($I))select($I,$h);else
echo"<p class='message'>".lang(array('Routine has been called, %d row affected.','Routine has been called, %d rows affected.'),$qa)." <span class='time'>".@date("H:i:s")."</span>\n";}while($g->next_result());if($Qf)select($g->query("SELECT ".implode(", ",$Qf)));}}echo'
<form action="" method="post">
';if($Md){echo"<table class='layout'>\n";foreach($Md
as$z){$o=$Xg["fields"][$z];$C=$o["field"];echo"<tr><th>".$b->fieldName($o);$Y=$_POST["fields"][$C];if($Y!=""){if($o["type"]=="set")$Y=implode(",",$Y);}input($o,$Y,(string)$_POST["function"][$C]);echo"\n";}echo"</table>\n";}echo'<p>
<input type="submit" value="Call">
',input_token(),'</form>

<pre>
';function
pre_tr($bh){return
preg_replace('~^~m','<tr>',preg_replace('~\|~','<td>',preg_replace('~\|$~m',"",rtrim($bh))));}$R='(\+--[-+]+\+\n)';$K='(\| .* \|\n)';echo
preg_replace_callback("~^$R?$K$R?($K*)$R?~m",function($B){$Vc=pre_tr($B[2]);return"<table>\n".($B[1]?"<thead>$Vc</thead>\n":$Vc).pre_tr($B[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($Xg['comment']))));echo'</pre>
';}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$C=$_GET["name"];$K=$_POST;if($_POST&&!$n&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$K["source"]=array_filter($K["source"],'strlen');ksort($K["source"]);$fi=array();foreach($K["source"]as$z=>$X)$fi[$z]=$K["target"][$z];$K["target"]=$fi;}if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(" $C"=>($K["drop"]?"":" ".format_foreign_key($K))));else{$c="ALTER TABLE ".table($a);$I=($C==""||queries("$c DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($C)));if(!$K["drop"])$I=queries("$c ADD".format_foreign_key($K));}queries_redirect(ME."table=".urlencode($a),($K["drop"]?'Foreign key has been dropped.':($C!=""?'Foreign key has been altered.':'Foreign key has been created.')),$I);if(!$K["drop"])$n="$n<br>".'Source and target columns must have the same data type, there must be an index on the target columns and referenced data must exist.';}page_header('Foreign key',$n,array("table"=>$a),h($a));if($_POST){ksort($K["source"]);if($_POST["add"])$K["source"][]="";elseif($_POST["change"]||$_POST["change-js"])$K["target"]=array();}elseif($C!=""){$ed=foreign_keys($a);$K=$ed[$C];$K["source"][]="";}else{$K["table"]=$a;$K["source"]=array("");}echo'
<form action="" method="post">
';$Bh=array_keys(fields($a));if($K["db"]!="")$g->select_db($K["db"]);if($K["ns"]!=""){$Mf=get_schema();set_schema($K["ns"]);}$Ig=array_keys(array_filter(table_status('',true),'Adminer\fk_support'));$fi=array_keys(fields(in_array($K["table"],$Ig)?$K["table"]:reset($Ig)));$xf="this.form['change-js'].value = '1'; this.form.submit();";echo"<p>".'Target table'.": ".html_select("table",$Ig,$K["table"],$xf)."\n";if(support("scheme")){$eh=array_filter($b->schemas(),function($dh){return!preg_match('~^information_schema$~i',$dh);});echo'Schema'.": ".html_select("ns",$eh,$K["ns"]!=""?$K["ns"]:$_GET["ns"],$xf);if($K["ns"]!="")set_schema($Mf);}elseif(JUSH!="sqlite"){$Mb=array();foreach($b->databases()as$k){if(!information_schema($k))$Mb[]=$k;}echo'DB'.": ".html_select("db",$Mb,$K["db"]!=""?$K["db"]:$_GET["db"],$xf);}echo
input_hidden("change-js"),'<noscript><p><input type="submit" name="change" value="Change"></noscript>
<table>
<thead><tr><th id="label-source">Source<th id="label-target">Target</thead>
';$ge=0;foreach($K["source"]as$z=>$X){echo"<tr>","<td>".html_select("source[".(+$z)."]",array(-1=>"")+$Bh,$X,($ge==count($K["source"])-1?"foreignAddRow.call(this);":""),"label-source"),"<td>".html_select("target[".(+$z)."]",$fi,$K["target"][$z],"","label-target");$ge++;}echo'</table>
<p>
ON DELETE: ',html_select("on_delete",array(-1=>"")+explode("|",$m->onActions),$K["on_delete"]),' ON UPDATE: ',html_select("on_update",array(-1=>"")+explode("|",$m->onActions),$K["on_update"]),doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",'pgsql'=>"sql-createtable.html#SQL-CREATETABLE-REFERENCES",'mssql'=>"t-sql/statements/create-table-transact-sql",'oracle'=>"SQLRF01111",)),'<p>
<input type="submit" value="Save">
<noscript><p><input type="submit" name="add" value="Add column"></noscript>
';if($C!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$C));echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$K=$_POST;$Nf="VIEW";if(JUSH=="pgsql"&&$a!=""){$P=table_status($a);$Nf=strtoupper($P["Engine"]);}if($_POST&&!$n){$C=trim($K["name"]);$wa=" AS\n$K[select]";$_e=ME."table=".urlencode($C);$Qe='View has been altered.';$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$C&&JUSH!="sqlite"&&$U=="VIEW"&&$Nf=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($C).$wa,$_e,$Qe);else{$hi=$C."_adminer_".uniqid();drop_create("DROP $Nf ".table($a),"CREATE $U ".table($C).$wa,"DROP $U ".table($C),"CREATE $U ".table($hi).$wa,"DROP $U ".table($hi),($_POST["drop"]?substr(ME,0,-1):$_e),'View has been dropped.',$Qe,'View has been created.',$a,$C);}}if(!$_POST&&$a!=""){$K=view($a);$K["name"]=$a;$K["materialized"]=($Nf!="VIEW");if(!$n)$n=error();}page_header(($a!=""?'Alter view':'Create view'),$n,array("table"=>$a),h($a));echo'
<form action="" method="post">
<p>Name: <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$K["materialized"],'Materialized view'):""),'<p>';textarea("select",$K["select"]);echo'<p>
<input type="submit" value="Save">
';if($a!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$a));echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$Yd=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$Mh=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$K=$_POST;if($_POST&&!$n){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),'Event has been dropped.');elseif(in_array($K["INTERVAL_FIELD"],$Yd)&&isset($Mh[$K["STATUS"]])){$ch="\nON SCHEDULE ".($K["INTERVAL_VALUE"]?"EVERY ".q($K["INTERVAL_VALUE"])." $K[INTERVAL_FIELD]".($K["STARTS"]?" STARTS ".q($K["STARTS"]):"").($K["ENDS"]?" ENDS ".q($K["ENDS"]):""):"AT ".q($K["STARTS"]))." ON COMPLETION".($K["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?'Event has been altered.':'Event has been created.'),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$ch.($aa!=$K["EVENT_NAME"]?"\nRENAME TO ".idf_escape($K["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($K["EVENT_NAME"]).$ch)."\n".$Mh[$K["STATUS"]]." COMMENT ".q($K["EVENT_COMMENT"]).rtrim(" DO\n$K[EVENT_DEFINITION]",";").";"));}}page_header(($aa!=""?'Alter event'.": ".h($aa):'Create event'),$n);if(!$K&&$aa!=""){$L=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$K=reset($L);}echo'
<form action="" method="post">
<table class="layout">
<tr><th>Name<td><input name="EVENT_NAME" value="',h($K["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">Start<td><input name="STARTS" value="',h("$K[EXECUTE_AT]$K[STARTS]"),'">
<tr><th title="datetime">End<td><input name="ENDS" value="',h($K["ENDS"]),'">
<tr><th>Every<td><input type="number" name="INTERVAL_VALUE" value="',h($K["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$Yd,$K["INTERVAL_FIELD"]),'<tr><th>Status<td>',html_select("STATUS",$Mh,$K["STATUS"]),'<tr><th>Comment<td><input name="EVENT_COMMENT" value="',h($K["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$K["ON_COMPLETION"]=="PRESERVE",'On completion preserve'),'</table>
<p>';textarea("EVENT_DEFINITION",$K["EVENT_DEFINITION"]);echo'<p>
<input type="submit" value="Save">
';if($aa!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$aa));echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$da=($_GET["name"]?:$_GET["procedure"]);$Xg=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$K=$_POST;$K["fields"]=(array)$K["fields"];if($_POST&&!process_fields($K["fields"])&&!$n){$Jf=routine($_GET["procedure"],$Xg);$hi="$K[name]_adminer_".uniqid();foreach($K["fields"]as$z=>$o){if($o["field"]=="")unset($K["fields"][$z]);}drop_create("DROP $Xg ".routine_id($da,$Jf),create_routine($Xg,$K),"DROP $Xg ".routine_id($K["name"],$K),create_routine($Xg,array("name"=>$hi)+$K),"DROP $Xg ".routine_id($hi,$K),substr(ME,0,-1),'Routine has been dropped.','Routine has been altered.','Routine has been created.',$da,$K["name"]);}page_header(($da!=""?(isset($_GET["function"])?'Alter function':'Alter procedure').": ".h($da):(isset($_GET["function"])?'Create function':'Create procedure')),$n);if(!$_POST){if($da=="")$K["language"]="sql";else{$K=routine($_GET["procedure"],$Xg);$K["name"]=$da;}}$hb=get_vals("SHOW CHARACTER SET");sort($hb);$Yg=routine_languages();echo($hb?"<datalist id='collations'>".optionlist($hb)."</datalist>":""),'
<form action="" method="post" id="form">
<p>Name: <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',($Yg?'Language'.": ".html_select("language",$Yg,$K["language"])."\n":""),'<input type="submit" value="Save">
<div class="scrollable">
<table class="nowrap">
';edit_fields($K["fields"],$hb,$Xg);if(isset($_GET["function"])){echo"<tr><td>".'Return type';edit_type("returns",$K["returns"],$hb,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$K["definition"]);echo'<p>
<input type="submit" value="Save">
';if($da!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$da));echo
input_token(),'</form>
';}elseif(isset($_GET["sequence"])){$fa=$_GET["sequence"];$K=$_POST;if($_POST&&!$n){$A=substr(ME,0,-1);$C=trim($K["name"]);if($_POST["drop"])query_redirect("DROP SEQUENCE ".idf_escape($fa),$A,'Sequence has been dropped.');elseif($fa=="")query_redirect("CREATE SEQUENCE ".idf_escape($C),$A,'Sequence has been created.');elseif($fa!=$C)query_redirect("ALTER SEQUENCE ".idf_escape($fa)." RENAME TO ".idf_escape($C),$A,'Sequence has been altered.');else
redirect($A);}page_header($fa!=""?'Alter sequence'.": ".h($fa):'Create sequence',$n);if(!$K)$K["name"]=$fa;echo'
<form action="" method="post">
<p><input name="name" value="',h($K["name"]),'" autocapitalize="off">
<input type="submit" value="Save">
';if($fa!="")echo"<input type='submit' name='drop' value='".'Drop'."'>".confirm(sprintf('Drop %s?',$fa))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["type"])){$ga=$_GET["type"];$K=$_POST;if($_POST&&!$n){$A=substr(ME,0,-1);if($_POST["drop"])query_redirect("DROP TYPE ".idf_escape($ga),$A,'Type has been dropped.');else
query_redirect("CREATE TYPE ".idf_escape(trim($K["name"]))." $K[as]",$A,'Type has been created.');}page_header($ga!=""?'Alter type'.": ".h($ga):'Create type',$n);if(!$K)$K["as"]="AS ";echo'
<form action="" method="post">
<p>
';if($ga!=""){$Ji=$m->types();$wc=type_values($Ji[$ga]);if($wc)echo"<code class='jush-".JUSH."'>ENUM (".h($wc).")</code>\n<p>";echo"<input type='submit' name='drop' value='".'Drop'."'>".confirm(sprintf('Drop %s?',$ga))."\n";}else{echo'Name'.": <input name='name' value='".h($K['name'])."' autocapitalize='off'>\n",doc_link(array('pgsql'=>"datatype-enum.html",),"?");textarea("as",$K["as"]);echo"<p><input type='submit' value='".'Save'."'>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$C=$_GET["name"];$K=$_POST;if($K&&!$n){if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(),0,array(),$C,($K["drop"]?"":$K["clause"]));else{$I=($C==""||queries("ALTER TABLE ".table($a)." DROP CONSTRAINT ".idf_escape($C)));if(!$K["drop"])$I=queries("ALTER TABLE ".table($a)." ADD".($K["name"]!=""?" CONSTRAINT ".idf_escape($K["name"]):"")." CHECK ($K[clause])");}queries_redirect(ME."table=".urlencode($a),($K["drop"]?'Check has been dropped.':($C!=""?'Check has been altered.':'Check has been created.')),$I);}page_header(($C!=""?'Alter check'.": ".h($C):'Create check'),$n,array("table"=>$a));if(!$K){$Ya=$m->checkConstraints($a);$K=array("name"=>$C,"clause"=>$Ya[$C]);}echo'
<form action="" method="post">
<p>';if(JUSH!="sqlite")echo'Name'.': <input name="name" value="'.h($K["name"]).'" data-maxlength="64" autocapitalize="off"> ';echo
doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",'pgsql'=>"ddl-constraints.html#DDL-CONSTRAINTS-CHECK-CONSTRAINTS",'mssql'=>"relational-databases/tables/create-check-constraints",'sqlite'=>"lang_createtable.html#check_constraints",),"?"),'<p>';textarea("clause",$K["clause"]);echo'<p><input type="submit" value="Save">
';if($C!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$C));echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$C=$_GET["name"];$Fi=trigger_options();$K=(array)trigger($C,$a)+array("Trigger"=>$a."_bi");if($_POST){if(!$n&&in_array($_POST["Timing"],$Fi["Timing"])&&in_array($_POST["Event"],$Fi["Event"])&&in_array($_POST["Type"],$Fi["Type"])){$uf=" ON ".table($a);$bc="DROP TRIGGER ".idf_escape($C).(JUSH=="pgsql"?$uf:"");$_e=ME."table=".urlencode($a);if($_POST["drop"])query_redirect($bc,$_e,'Trigger has been dropped.');else{if($C!="")queries($bc);queries_redirect($_e,($C!=""?'Trigger has been altered.':'Trigger has been created.'),queries(create_trigger($uf,$_POST)));if($C!="")queries(create_trigger($uf,$K+array("Type"=>reset($Fi["Type"]))));}}$K=$_POST;}page_header(($C!=""?'Alter trigger'.": ".h($C):'Create trigger'),$n,array("table"=>$a));echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>Time<td>',html_select("Timing",$Fi["Timing"],$K["Timing"],"triggerChange(/^".preg_quote($a,"/")."_[ba][iud]$/, '".js_escape($a)."', this.form);"),'<tr><th>Event<td>',html_select("Event",$Fi["Event"],$K["Event"],"this.form['Timing'].onchange();"),(in_array("UPDATE OF",$Fi["Event"])?" <input name='Of' value='".h($K["Of"])."' class='hidden'>":""),'<tr><th>Type<td>',html_select("Type",$Fi["Type"],$K["Type"]),'</table>
<p>Name: <input name="Trigger" value="',h($K["Trigger"]),'" data-maxlength="64" autocapitalize="off">
',script("qs('#form')['Timing'].onchange();"),'<p>';textarea("Statement",$K["Statement"]);echo'<p>
<input type="submit" value="Save">
';if($C!="")echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',$C));echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){$ha=$_GET["user"];$xg=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$K){foreach(explode(",",($K["Privilege"]=="Grant option"?"":$K["Context"]))as$xb)$xg[$xb][$K["Privilege"]]=$K["Comment"];}$xg["Server Admin"]+=$xg["File access on server"];$xg["Databases"]["Create routine"]=$xg["Procedures"]["Create routine"];unset($xg["Procedures"]["Create routine"]);$xg["Columns"]=array();foreach(array("Select","Insert","Update","References")as$X)$xg["Columns"][$X]=$xg["Tables"][$X];unset($xg["Server Admin"]["Usage"]);foreach($xg["Tables"]as$z=>$X)unset($xg["Databases"][$z]);$df=array();if($_POST){foreach($_POST["objects"]as$z=>$X)$df[$X]=(array)$df[$X]+(array)$_POST["grants"][$z];}$od=array();$sf="";if(isset($_GET["host"])&&($I=$g->query("SHOW GRANTS FOR ".q($ha)."@".q($_GET["host"])))){while($K=$I->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$K[0],$B)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$B[1],$Ge,PREG_SET_ORDER)){foreach($Ge
as$X){if($X[1]!="USAGE")$od["$B[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$K[0]))$od["$B[2]$X[2]"]["GRANT OPTION"]=true;}}if(preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~",$K[0],$B))$sf=$B[1];}}if($_POST&&!$n){$tf=(isset($_GET["host"])?q($ha)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $tf",ME."privileges=",'User has been dropped.');else{$ff=q($_POST["user"])."@".q($_POST["host"]);$dg=$_POST["pass"];if($dg!=''&&!$_POST["hashed"]&&!min_version(8)){$dg=get_val("SELECT PASSWORD(".q($dg).")");$n=!$dg;}$Ab=false;if(!$n){if($tf!=$ff){$Ab=queries((min_version(5)?"CREATE USER":"GRANT USAGE ON *.* TO")." $ff IDENTIFIED BY ".(min_version(8)?"":"PASSWORD ").q($dg));$n=!$Ab;}elseif($dg!=$sf)queries("SET PASSWORD FOR $ff = ".q($dg));}if(!$n){$Ug=array();foreach($df
as$mf=>$nd){if(isset($_GET["grant"]))$nd=array_filter($nd);$nd=array_keys($nd);if(isset($_GET["grant"]))$Ug=array_diff(array_keys(array_filter($df[$mf],'strlen')),$nd);elseif($tf==$ff){$qf=array_keys((array)$od[$mf]);$Ug=array_diff($qf,$nd);$nd=array_diff($nd,$qf);unset($od[$mf]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$mf,$B)&&(!grant("REVOKE",$Ug,$B[2]," ON $B[1] FROM $ff")||!grant("GRANT",$nd,$B[2]," ON $B[1] TO $ff"))){$n=true;break;}}}if(!$n&&isset($_GET["host"])){if($tf!=$ff)queries("DROP USER $tf");elseif(!isset($_GET["grant"])){foreach($od
as$mf=>$Ug){if(preg_match('~^(.+)(\(.*\))?$~U',$mf,$B))grant("REVOKE",array_keys($Ug),$B[2]," ON $B[1] FROM $ff");}}}queries_redirect(ME."privileges=",(isset($_GET["host"])?'User has been altered.':'User has been created.'),!$n);if($Ab)$g->query("DROP USER $ff");}}page_header((isset($_GET["host"])?'Username'.": ".h("$ha@$_GET[host]"):'Create user'),$n,array("privileges"=>array('','Privileges')));$K=$_POST;if($K)$od=$df;else{$K=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$K["pass"]=$sf;if($sf!="")$K["hashed"]=true;$od[(DB==""||$od?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>Server<td><input name="host" data-maxlength="60" value="',h($K["host"]),'" autocapitalize="off">
<tr><th>Username<td><input name="user" data-maxlength="80" value="',h($K["user"]),'" autocapitalize="off">
<tr><th>Password<td><input name="pass" id="pass" value="',h($K["pass"]),'" autocomplete="new-password">
',($K["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8)?"":checkbox("hashed",1,$K["hashed"],'Hashed',"typePassword(this.form['pass'], this.checked);")),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".'Privileges'.doc_link(array('sql'=>"grant.html#priv_level"));$u=0;foreach($od
as$mf=>$nd){echo'<th>'.($mf!="*.*"?"<input name='objects[$u]' value='".h($mf)."' size='10' autocapitalize='off'>":input_hidden("objects[$u]","*.*")."*.*");$u++;}echo"</thead>\n";foreach(array(""=>"","Server Admin"=>'Server',"Databases"=>'Database',"Tables"=>'Table',"Columns"=>'Column',"Procedures"=>'Routine',)as$xb=>$Sb){foreach((array)$xg[$xb]as$wg=>$mb){echo"<tr><td".($Sb?">$Sb<td":" colspan='2'").' lang="en" title="'.h($mb).'">'.h($wg);$u=0;foreach($od
as$mf=>$nd){$C="'grants[$u][".h(strtoupper($wg))."]'";$Y=$nd[strtoupper($wg)];if($xb=="Server Admin"&&$mf!=(isset($od["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$C><option><option value='1'".($Y?" selected":"").">".'Grant'."<option value='0'".($Y=="0"?" selected":"").">".'Revoke'."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$C value='1'".($Y?" checked":"").($wg=="All privileges"?" id='grants-$u-all'>":">".($wg=="Grant option"?"":script("qsl('input').onclick = function () { if (this.checked) formUncheck('grants-$u-all'); };"))),"</label>";$u++;}}}echo"</table>\n",'<p>
<input type="submit" value="Save">
';if(isset($_GET["host"]))echo'<input type="submit" name="drop" value="Drop">',confirm(sprintf('Drop %s?',"$ha@$_GET[host]"));echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$n){$me=0;foreach((array)$_POST["kill"]as$X){if(kill_process($X))$me++;}queries_redirect(ME."processlist=",lang(array('%d process has been killed.','%d processes have been killed.'),$me),$me||!$_POST["kill"]);}}page_header('Process list',$n);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds">
',script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});");$u=-1;foreach(process_list()as$u=>$K){if(!$u){echo"<thead><tr lang='en'>".(support("kill")?"<th>":"");foreach($K
as$z=>$X)echo"<th>$z".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($z),'pgsql'=>"monitoring-stats.html#PG-STAT-ACTIVITY-VIEW",'oracle'=>"REFRN30223",));echo"</thead>\n";}echo"<tr>".(support("kill")?"<td>".checkbox("kill[]",$K[JUSH=="sql"?"Id":"pid"],0):"");foreach($K
as$z=>$X)echo"<td>".((JUSH=="sql"&&$z=="Info"&&preg_match("~Query|Killed~",$K["Command"])&&$X!="")||(JUSH=="pgsql"&&$z=="current_query"&&$X!="<IDLE>")||(JUSH=="oracle"&&$z=="sql_text"&&$X!="")?"<code class='jush-".JUSH."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(ME.($K["db"]!=""?"db=".urlencode($K["db"])."&":"")."sql=".urlencode($X)).'">'.'Clone'.'</a>':h($X));echo"\n";}echo'</table>
</div>
<p>
';if(support("kill"))echo($u+1)."/".sprintf('%d in total',max_connections()),"<p><input type='submit' value='".'Kill'."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif(isset($_GET["select"])){$a=$_GET["select"];$S=table_status1($a);$y=indexes($a);$p=fields($a);$ed=column_foreign_keys($a);$of=$S["Oid"];$pa=get_settings("adminer_import");$Vg=array();$f=array();$hh=array();$Ff=array();$li=null;foreach($p
as$z=>$o){$C=$b->fieldName($o);$bf=html_entity_decode(strip_tags($C),ENT_QUOTES);if(isset($o["privileges"]["select"])&&$C!=""){$f[$z]=$bf;if(is_shortable($o))$li=$b->selectLengthProcess();}if(isset($o["privileges"]["where"])&&$C!="")$hh[$z]=$bf;if(isset($o["privileges"]["order"])&&$C!="")$Ff[$z]=$bf;$Vg+=$o["privileges"];}list($M,$pd)=$b->selectColumnsProcess($f,$y);$M=array_unique($M);$pd=array_unique($pd);$ce=count($pd)<count($M);$Z=$b->selectSearchProcess($p,$y);$Ef=$b->selectOrderProcess($p,$y);$_=$b->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$Oi=>$K){$wa=convert_field($p[key($K)]);$M=array($wa?:idf_escape(key($K)));$Z[]=where_check($Oi,$p);$J=$m->select($a,$M,$Z,$M);if($J)echo
reset($J->fetch_row());}exit;}$G=$Qi=null;foreach($y
as$x){if($x["type"]=="PRIMARY"){$G=array_flip($x["columns"]);$Qi=($M?$G:array());foreach($Qi
as$z=>$X){if(in_array(idf_escape($z),$M))unset($Qi[$z]);}break;}}if($of&&!$G){$G=$Qi=array($of=>0);$y[]=array("type"=>"PRIMARY","columns"=>array($of));}if($_POST&&!$n){$oj=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$Ya=array();foreach($_POST["check"]as$Ua)$Ya[]=where_check($Ua,$p);$oj[]="((".implode(") OR (",$Ya)."))";}$oj=($oj?"\nWHERE ".implode(" AND ",$oj):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);$b->dumpTable($a,"");$id=($M?implode(", ",$M):"*").convert_fields($f,$p,$M)."\nFROM ".table($a);$rd=($pd&&$ce?"\nGROUP BY ".implode(", ",$pd):"").($Ef?"\nORDER BY ".implode(", ",$Ef):"");$H="SELECT $id$oj$rd";if(is_array($_POST["check"])&&!$G){$Mi=array();foreach($_POST["check"]as$X)$Mi[]="(SELECT".limit($id,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$p).$rd,1).")";$H=implode(" UNION ALL ",$Mi);}$b->dumpData($a,"table",$H);$b->dumpFooter();exit;}if(!$b->selectEmailProcess($Z,$ed)){if($_POST["save"]||$_POST["delete"]){$I=true;$qa=0;$O=array();if(!$_POST["delete"]){foreach($_POST["fields"]as$C=>$X){$X=process_input($p[$C]);if($X!==null&&($_POST["clone"]||$X!==false))$O[idf_escape($C)]=($X!==false?$X:idf_escape($C));}}if($_POST["delete"]||$O){if($_POST["clone"])$H="INTO ".table($a)." (".implode(", ",array_keys($O)).")\nSELECT ".implode(", ",$O)."\nFROM ".table($a);if($_POST["all"]||($G&&is_array($_POST["check"]))||$ce){$I=($_POST["delete"]?$m->delete($a,$oj):($_POST["clone"]?queries("INSERT $H$oj".$m->insertReturning($a)):$m->update($a,$O,$oj)));$qa=$g->affected_rows+(is_object($I)?$I->num_rows:0);}else{foreach((array)$_POST["check"]as$X){$nj="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$p);$I=($_POST["delete"]?$m->delete($a,$nj,1):($_POST["clone"]?queries("INSERT".limit1($a,$H,$nj)):$m->update($a,$O,$nj,1)));if(!$I)break;$qa+=$g->affected_rows;}}}$Qe=lang(array('%d item has been affected.','%d items have been affected.'),$qa);if($_POST["clone"]&&$I&&$qa==1){$re=last_id($I);if($re)$Qe=sprintf('Item%s has been inserted.'," $re");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page":""),$Qe,$I);if(!$_POST["delete"]){$qg=(array)$_POST["fields"];edit_form($a,array_intersect_key($p,$qg),$qg,!$_POST["clone"]);page_footer();exit;}}elseif(!$_POST["import"]){if(!$_POST["val"])$n='Ctrl+click on a value to modify it.';else{$I=true;$qa=0;foreach($_POST["val"]as$Oi=>$K){$O=array();foreach($K
as$z=>$X){$z=bracket_escape($z,1);$O[idf_escape($z)]=(preg_match('~char|text~',$p[$z]["type"])||$X!=""?$b->processInput($p[$z],$X):"NULL");}$I=$m->update($a,$O," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($Oi,$p),!$ce&&!$G," ");if(!$I)break;$qa+=$g->affected_rows;}queries_redirect(remove_from_uri(),lang(array('%d item has been affected.','%d items have been affected.'),$qa),$I);}}elseif(!is_string($Sc=get_file("csv_file",true)))$n=upload_error($Sc);elseif(!preg_match('~~u',$Sc))$n='File must be in UTF-8 encoding.';else{save_settings(array("output"=>$pa["output"],"format"=>$_POST["separator"]),"adminer_import");$I=true;$ib=array_keys($p);preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Sc,$Ge);$qa=count($Ge[0]);$m->begin();$nh=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$L=array();foreach($Ge[0]as$z=>$X){preg_match_all("~((?>\"[^\"]*\")+|[^$nh]*)$nh~",$X.$nh,$He);if(!$z&&!array_diff($He[1],$ib)){$ib=$He[1];$qa--;}else{$O=array();foreach($He[1]as$u=>$eb)$O[idf_escape($ib[$u])]=($eb==""&&$p[$ib[$u]]["null"]?"NULL":q(preg_match('~^".*"$~s',$eb)?str_replace('""','"',substr($eb,1,-1)):$eb));$L[]=$O;}}$I=(!$L||$m->insertUpdate($a,$L,$G));if($I)$m->commit();queries_redirect(remove_from_uri("page"),lang(array('%d row has been imported.','%d rows have been imported.'),$qa),$I);$m->rollback();}}}$Xh=$b->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header('Select'.": $Xh",$n);$O=null;if(isset($Vg["insert"])||!support("table")){$Wf=array();foreach((array)$_GET["where"]as$X){if(isset($ed[$X["col"]])&&count($ed[$X["col"]])==1&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$X["val"])))))$Wf["set"."[".bracket_escape($X["col"])."]"]=$X["val"];}$O=$Wf?"&".http_build_query($Wf):"";}$b->selectLinks($S,$O);if(!$f&&support("table"))echo"<p class='error'>".'Unable to select the table'.($p?".":": ".error())."\n";else{echo"<form action='' id='form'>\n","<div style='display: none;'>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";$b->selectColumnsPrint($M,$f);$b->selectSearchPrint($Z,$hh,$y);$b->selectOrderPrint($Ef,$Ff,$y);$b->selectLimitPrint($_);$b->selectLengthPrint($li);$b->selectActionPrint($y);echo"</form>\n";$E=$_GET["page"];if($E=="last"){$hd=get_val(count_rows($a,$Z,$ce,$pd));$E=floor(max(0,$hd-1)/$_);}$ih=$M;$qd=$pd;if(!$ih){$ih[]="*";$yb=convert_fields($f,$p,$M);if($yb)$ih[]=substr($yb,2);}foreach($M
as$z=>$X){$o=$p[idf_unescape($X)];if($o&&($wa=convert_field($o)))$ih[$z]="$wa AS $X";}if(!$ce&&$Qi){foreach($Qi
as$z=>$X){$ih[]=idf_escape($z);if($qd)$qd[]=idf_escape($z);}}$I=$m->select($a,$ih,$Z,$qd,$Ef,$_,$E,true);if(!$I)echo"<p class='error'>".error()."\n";else{if(JUSH=="mssql"&&$E)$I->seek($_*$E);$pc=array();echo"<form action='' method='post' enctype='multipart/form-data'>\n";$L=array();while($K=$I->fetch_assoc()){if($E&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}if($_GET["page"]!="last"&&$_!=""&&$pd&&$ce&&JUSH=="sql")$hd=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".'No rows.'."\n";else{$Ea=$b->backwardKeys($a,$Xh);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'>",script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"),"<thead><tr>".(!$pd&&$M?"":"<td><input type='checkbox' id='all-page' class='jsonly'>".script("qs('#all-page').onclick = partial(formCheck, /check/);","")." <a href='".h($_GET["modify"]?remove_from_uri("modify"):$_SERVER["REQUEST_URI"]."&modify=1")."'>".'Modify'."</a>");$cf=array();$kd=array();reset($M);$Fg=1;foreach($L[0]as$z=>$X){if(!isset($Qi[$z])){$X=$_GET["columns"][key($M)];$o=$p[$M?($X?$X["col"]:current($M)):$z];$C=($o?$b->fieldName($o,$Fg):($X["fun"]?"*":h($z)));if($C!=""){$Fg++;$cf[$z]=$C;$e=idf_escape($z);$Gd=remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($z);$Sb="&desc%5B0%5D=1";$Ah=isset($o["privileges"]["order"]);echo"<th id='th[".h(bracket_escape($z))."]'>".script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});","");$jd=apply_sql_function($X["fun"],$C);echo($Ah?'<a href="'.h($Gd.($Ef[0]==$e||$Ef[0]==$z||(!$Ef&&$ce&&$pd[0]==$e)?$Sb:'')).'">'."$jd</a>":$jd),"<span class='column hidden'>";if($Ah)echo"<a href='".h($Gd.$Sb)."' title='".'descending'."' class='text'> ↓</a>";if(!$X["fun"]&&isset($o["privileges"]["where"]))echo'<a href="#fieldset-search" title="'.'Search'.'" class="text jsonly"> =</a>',script("qsl('a').onclick = partial(selectSearch, '".js_escape($z)."');");echo"</span>";}$kd[$z]=$X["fun"];next($M);}}$xe=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$z=>$X)$xe[$z]=max($xe[$z],min(40,strlen(utf8_decode($X))));}}echo($Ea?"<th>".'Relations':"")."</thead>\n";if(is_ajax())ob_end_clean();foreach($b->rowDescriptions($L,$ed)as$af=>$K){$Ni=unique_array($L[$af],$y);if(!$Ni){$Ni=array();foreach($L[$af]as$z=>$X){if(!preg_match('~^(COUNT\((\*|(DISTINCT )?`(?:[^`]|``)+`)\)|(AVG|GROUP_CONCAT|MAX|MIN|SUM)\(`(?:[^`]|``)+`\))$~',$z))$Ni[$z]=$X;}}$Oi="";foreach($Ni
as$z=>$X){if((JUSH=="sql"||JUSH=="pgsql")&&preg_match('~char|text|enum|set~',$p[$z]["type"])&&strlen($X)>64){$z=(strpos($z,'(')?$z:idf_escape($z));$z="MD5(".(JUSH!='sql'||preg_match("~^utf8~",$p[$z]["collation"])?$z:"CONVERT($z USING ".charset($g).")").")";$X=md5($X);}$Oi.="&".($X!==null?urlencode("where[".bracket_escape($z)."]")."=".urlencode($X===false?"f":$X):"null%5B%5D=".urlencode($z));}echo"<tr>".(!$pd&&$M?"":"<td>".checkbox("check[]",substr($Oi,1),in_array(substr($Oi,1),(array)$_POST["check"])).($ce||information_schema(DB)?"":" <a href='".h(ME."edit=".urlencode($a).$Oi)."' class='edit'>".'edit'."</a>"));foreach($K
as$z=>$X){if(isset($cf[$z])){$o=$p[$z];$X=$m->value($X,$o);if($X!=""&&(!isset($pc[$z])||$pc[$z]!=""))$pc[$z]=(is_mail($X)?$cf[$z]:"");$A="";if(preg_match('~blob|bytea|raw|file~',$o["type"])&&$X!="")$A=ME.'download='.urlencode($a).'&field='.urlencode($z).$Oi;if(!$A&&$X!==null){foreach((array)$ed[$z]as$r){if(count($ed[$z])==1||end($r["source"])==$z){$A="";foreach($r["source"]as$u=>$Bh)$A.=where_link($u,$r["target"][$u],$L[$af][$Bh]);$A=($r["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.urlencode($r["db"]),ME):ME).'select='.urlencode($r["table"]).$A;if($r["ns"])$A=preg_replace('~([?&]ns=)[^&]+~','\1'.urlencode($r["ns"]),$A);if(count($r["source"])==1)break;}}}if($z=="COUNT(*)"){$A=ME."select=".urlencode($a);$u=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$Ni))$A.=where_link($u++,$W["col"],$W["val"],$W["op"]);}foreach($Ni
as$ie=>$W)$A.=where_link($u++,$ie,$W);}$X=select_value($X,$A,$o,$li);$v=h("val[$Oi][".bracket_escape($z)."]");$Y=$_POST["val"][$Oi][bracket_escape($z)];$kc=!is_array($K[$z])&&is_utf8($X)&&$L[$af][$z]==$K[$z]&&!$kd[$z]&&!$o["generated"];$ji=preg_match('~text|json|lob~',$o["type"]);echo"<td id='$v'".(preg_match(number_type(),$o["type"])&&is_numeric(strip_tags($X))?" class='number'":"");if(($_GET["modify"]&&$kc)||$Y!==null){$ud=h($Y!==null?$Y:$K[$z]);echo">".($ji?"<textarea name='$v' cols='30' rows='".(substr_count($K[$z],"\n")+1)."'>$ud</textarea>":"<input name='$v' value='$ud' size='$xe[$z]'>");}else{$Be=strpos($X,"<i>…</i>");echo" data-text='".($Be?2:($ji?1:0))."'".($kc?"":" data-warning='".h('Use edit link to modify this value.')."'").">$X";}}}if($Ea)echo"<td>";$b->backwardKeysPrint($Ea,$L[$af]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){if($L||$E){$Cc=true;if($_GET["page"]!="last"){if($_==""||(count($L)<$_&&($L||!$E)))$hd=($E?$E*$_:0)+count($L);elseif(JUSH!="sql"||!$ce){$hd=($ce?false:found_rows($S,$Z));if($hd<max(1e4,2*($E+1)*$_))$hd=first(slow_query(count_rows($a,$Z,$ce,$pd)));else$Cc=false;}}$Uf=($_!=""&&($hd===false||$hd>$_||$E));if($Uf)echo(($hd===false?count($L)+1:$hd-$E*$_)>$_?'<p><a href="'.h(remove_from_uri("page")."&page=".($E+1)).'" class="loadmore">'.'Load more data'.'</a>'.script("qsl('a').onclick = partial(selectLoadMore, ".(+$_).", '".'Loading'."…');",""):''),"\n";}echo"<div class='footer'><div>\n";if($L||$E){if($Uf){$Je=($hd===false?$E+(count($L)>=$_?2:1):floor(($hd-1)/$_));echo"<fieldset>";if(JUSH!="simpledb"){echo"<legend><a href='".h(remove_from_uri("page"))."'>".'Page'."</a></legend>",script("qsl('a').onclick = function () { pageClick(this.href, +prompt('".'Page'."', '".($E+1)."')); return false; };"),pagination(0,$E).($E>5?" …":"");for($u=max(1,$E-4);$u<min($Je,$E+5);$u++)echo
pagination($u,$E);if($Je>0)echo($E+5<$Je?" …":""),($Cc&&$hd!==false?pagination($Je,$E):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Je'>".'last'."</a>");}else
echo"<legend>".'Page'."</legend>",pagination(0,$E).($E>1?" …":""),($E?pagination($E,$E):""),($Je>$E?pagination($E+1,$E).($Je>$E+1?" …":""):"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".'Whole result'."</legend>";$Yb=($Cc?"":"~ ").$hd;$yf="const checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$Yb' : checked); selectCount('selected2', this.checked || !checked ? '$Yb' : checked);";echo
checkbox("all",1,0,($hd!==false?($Cc?"":"~ ").lang(array('%d row','%d rows'),$hd):""),$yf)."\n","</fieldset>\n";if($b->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':' class="jsonly"'),'><legend>Modify</legend><div>
<input type="submit" value="Save"',($_GET["modify"]?'':' title="'.'Ctrl+click on a value to modify it.'.'"'),'>
</div></fieldset>
<fieldset><legend>Selected <span id="selected"></span></legend><div>
<input type="submit" name="edit" value="Edit">
<input type="submit" name="clone" value="Clone">
<input type="submit" name="delete" value="Delete">',confirm(),'</div></fieldset>
';$fd=$b->dumpFormat();foreach((array)$_GET["columns"]as$e){if($e["fun"]){unset($fd['sql']);break;}}if($fd){print_fieldset("export",'Export'." <span id='selected2'></span>");$Rf=$b->dumpOutput();echo($Rf?html_select("output",$Rf,$pa["output"])." ":""),html_select("format",$fd,$pa["format"])," <input type='submit' name='export' value='".'Export'."'>\n","</div></fieldset>\n";}$b->selectEmailPrint(array_filter($pc,'strlen'),$f);}echo"</div></div>\n";if($b->selectImportPrint())echo"<div>","<a href='#import'>".'Import'."</a>",script("qsl('a').onclick = partial(toggle, 'import');",""),"<span id='import'".($_POST["import"]?"":" class='hidden'").">: ","<input type='file' name='csv_file'> ",html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$pa["format"])," <input type='submit' name='import' value='".'Import'."'>","</span>","</div>";echo
input_token(),"</form>\n",(!$pd&&$M?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$P=isset($_GET["status"]);page_header($P?'Status':'Variables');$ej=($P?show_status():show_variables());if(!$ej)echo"<p class='message'>".'No rows.'."\n";else{echo"<table>\n";foreach($ej
as$K){echo"<tr>";$z=array_shift($K);echo"<th><code class='jush-".JUSH.($P?"status":"set")."'>".h($z)."</code>";foreach($K
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: text/javascript; charset=utf-8");if($_GET["script"]=="db"){$Th=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$C=>$S){json_row("Comment-$C",h($S["Comment"]));if(!is_view($S)){foreach(array("Engine","Collation")as$z)json_row("$z-$C",h($S[$z]));foreach($Th+array("Auto_increment"=>0,"Rows"=>0)as$z=>$X){if($S[$z]!=""){$X=format_number($S[$z]);if($X>=0)json_row("$z-$C",($z=="Rows"&&$X&&$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")?"~ $X":$X));if(isset($Th[$z]))$Th[$z]+=($S["Engine"]!="InnoDB"||$z!="Data_free"?$S[$z]:0);}elseif(array_key_exists($z,$S))json_row("$z-$C","?");}}}foreach($Th
as$z=>$X)json_row("sum-$z",format_number($X));json_row("");}elseif($_GET["script"]=="kill")$g->query("KILL ".number($_POST["kill"]));else{foreach(count_tables($b->databases())as$k=>$X){json_row("tables-$k",$X);json_row("size-$k",db_size($k));}json_row("");}exit;}else{$di=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($di&&!$n&&!$_POST["search"]){$I=true;$Qe="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$I=truncate_tables($_POST["tables"]);$Qe='Tables have been truncated.';}elseif($_POST["move"]){$I=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$Qe='Tables have been moved.';}elseif($_POST["copy"]){$I=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$Qe='Tables have been copied.';}elseif($_POST["drop"]){if($_POST["views"])$I=drop_views($_POST["views"]);if($I&&$_POST["tables"])$I=drop_tables($_POST["tables"]);$Qe='Tables have been dropped.';}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$K)$Qe.="<b>".h($R)."</b>: ".h($K["integrity_check"])."<br>";}}elseif(JUSH!="sql"){$I=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?"":" ANALYZE"),$_POST["tables"]));$Qe='Tables have been optimized.';}elseif(!$_POST["tables"])$Qe='No tables.';elseif($I=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($K=$I->fetch_assoc())$Qe.="<b>".h($K["Table"])."</b>: ".h($K["Msg_text"])."<br>";}queries_redirect(substr(ME,0,-1),$Qe,$I);}page_header(($_GET["ns"]==""?'Database'.": ".h(DB):'Schema'.": ".h($_GET["ns"])),$n,true);if($b->homepage()){if($_GET["ns"]!==""){echo"<h3 id='tables-views'>".'Tables and views'."</h3>\n";$ci=tables_list();if(!$ci)echo"<p class='message'>".'No tables.'."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".'Search data in tables'." <span id='selected2'></span></legend><div>","<input type='search' name='query' value='".h($_POST["query"])."'>",script("qsl('input').onkeydown = partialArg(bodyKeydown, 'search');","")," <input type='submit' name='search' value='".'Search'."'>\n","</div></fieldset>\n";if($_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$m->convertOperator("LIKE %%");search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr class="wrap">','<td><input id="check-all" type="checkbox" class="jsonly">'.script("qs('#check-all').onclick = partial(formCheck, /^(tables|views)\[/);",""),'<th>'.'Table','<td>'.'Engine'.doc_link(array('sql'=>'storage-engines.html')),'<td>'.'Collation'.doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')),'<td>'.'Data Length'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT','oracle'=>'REFRN20286')),'<td>'.'Index Length'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT')),'<td>'.'Data Free'.doc_link(array('sql'=>'show-table-status.html')),'<td>'.'Auto Increment'.doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),'<td>'.'Rows'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'catalog-pg-class.html#CATALOG-PG-CLASS','oracle'=>'REFRN20286')),(support("comment")?'<td>'.'Comment'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-info.html#FUNCTIONS-INFO-COMMENT-TABLE')):''),"</thead>\n";$T=0;foreach($ci
as$C=>$U){$hj=($U!==null&&!preg_match('~table|sequence~i',$U));$v=h("Table-".$C);echo'<tr><td>'.checkbox(($hj?"views[]":"tables[]"),$C,in_array($C,$di,true),"","","",$v),'<th>'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".urlencode($C)."' title='".'Show structure'."' id='$v'>".h($C).'</a>':h($C));if($hj)echo'<td colspan="6"><a href="'.h(ME)."view=".urlencode($C).'" title="'.'Alter view'.'">'.(preg_match('~materialized~i',$U)?'Materialized view':'View').'</a>','<td align="right"><a href="'.h(ME)."select=".urlencode($C).'" title="'.'Select data'.'">?</a>';else{foreach(array("Engine"=>array(),"Collation"=>array(),"Data_length"=>array("create",'Alter table'),"Index_length"=>array("indexes",'Alter indexes'),"Data_free"=>array("edit",'New item'),"Auto_increment"=>array("auto_increment=1&create",'Alter table'),"Rows"=>array("select",'Select data'),)as$z=>$A){$v=" id='$z-".h($C)."'";echo($A?"<td align='right'>".(support("table")||$z=="Rows"||(support("indexes")&&$z!="Data_length")?"<a href='".h(ME."$A[0]=").urlencode($C)."'$v title='$A[1]'>?</a>":"<span$v>?</span>"):"<td id='$z-".h($C)."'>");}$T++;}echo(support("comment")?"<td id='Comment-".h($C)."'>":""),"\n";}echo"<tr><td><th>".sprintf('%d in total',count($ci)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),"<td>".h(db_collation(DB,collations()));foreach(array("Data_length","Index_length","Data_free")as$z)echo"<td align='right' id='sum-$z'>";echo"\n","</table>\n","</div>\n";if(!information_schema(DB)){echo"<div class='footer'><div>\n";$bj="<input type='submit' value='".'Vacuum'."'> ".on_help("'VACUUM'");$Af="<input type='submit' name='optimize' value='".'Optimize'."'> ".on_help(JUSH=="sql"?"'OPTIMIZE TABLE'":"'VACUUM OPTIMIZE'");echo"<fieldset><legend>".'Selected'." <span id='selected'></span></legend><div>".(JUSH=="sqlite"?$bj."<input type='submit' name='check' value='".'Check'."'> ".on_help("'PRAGMA integrity_check'"):(JUSH=="pgsql"?$bj.$Af:(JUSH=="sql"?"<input type='submit' value='".'Analyze'."'> ".on_help("'ANALYZE TABLE'").$Af."<input type='submit' name='check' value='".'Check'."'> ".on_help("'CHECK TABLE'")."<input type='submit' name='repair' value='".'Repair'."'> ".on_help("'REPAIR TABLE'"):"")))."<input type='submit' name='truncate' value='".'Truncate'."'> ".on_help(JUSH=="sqlite"?"'DELETE'":"'TRUNCATE".(JUSH=="pgsql"?"'":" TABLE'")).confirm()."<input type='submit' name='drop' value='".'Drop'."'>".on_help("'DROP TABLE'").confirm()."\n";$j=(support("scheme")?$b->schemas():$b->databases());if(count($j)!=1&&JUSH!="sqlite"){$k=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo"<p>".'Move to other database'.": ",($j?html_select("target",$j,$k):'<input name="target" value="'.h($k).'" autocapitalize="off">')," <input type='submit' name='move' value='".'Move'."'>",(support("copy")?" <input type='submit' name='copy' value='".'Copy'."'> ".checkbox("overwrite",1,$_POST["overwrite"],'overwrite'):""),"\n";}echo"<input type='hidden' name='all' value=''>",script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^(tables|views)\[/));".(support("table")?" selectCount('selected2', formChecked(this, /^tables\[/) || $T);":"")." }"),input_token(),"</div></fieldset>\n","</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo"<p class='links'><a href='".h(ME)."create='>".'Create table'."</a>\n",(support("view")?"<a href='".h(ME)."view='>".'Create view'."</a>\n":"");if(support("routine")){echo"<h3 id='routines'>".'Routines'."</h3>\n";$Zg=routines();if($Zg){echo"<table class='odds'>\n",'<thead><tr><th>'.'Name'.'<td>'.'Type'.'<td>'.'Return type'."<td></thead>\n";foreach($Zg
as$K){$C=($K["SPECIFIC_NAME"]==$K["ROUTINE_NAME"]?"":"&name=".urlencode($K["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').urlencode($K["SPECIFIC_NAME"]).$C).'">'.h($K["ROUTINE_NAME"]).'</a>','<td>'.h($K["ROUTINE_TYPE"]),'<td>'.h($K["DTD_IDENTIFIER"]),'<td><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').urlencode($K["SPECIFIC_NAME"]).$C).'">'.'Alter'."</a>";}echo"</table>\n";}echo'<p class="links">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.'Create procedure'.'</a>':'').'<a href="'.h(ME).'function=">'.'Create function'."</a>\n";}if(support("sequence")){echo"<h3 id='sequences'>".'Sequences'."</h3>\n";$qh=get_vals("SELECT sequence_name FROM information_schema.sequences WHERE sequence_schema = current_schema() ORDER BY sequence_name");if($qh){echo"<table class='odds'>\n","<thead><tr><th>".'Name'."</thead>\n";foreach($qh
as$X)echo"<tr><th><a href='".h(ME)."sequence=".urlencode($X)."'>".h($X)."</a>\n";echo"</table>\n";}echo"<p class='links'><a href='".h(ME)."sequence='>".'Create sequence'."</a>\n";}if(support("type")){echo"<h3 id='user-types'>".'User types'."</h3>\n";$Zi=types();if($Zi){echo"<table class='odds'>\n","<thead><tr><th>".'Name'."</thead>\n";foreach($Zi
as$X)echo"<tr><th><a href='".h(ME)."type=".urlencode($X)."'>".h($X)."</a>\n";echo"</table>\n";}echo"<p class='links'><a href='".h(ME)."type='>".'Create type'."</a>\n";}if(support("event")){echo"<h3 id='events'>".'Events'."</h3>\n";$L=get_rows("SHOW EVENTS");if($L){echo"<table>\n","<thead><tr><th>".'Name'."<td>".'Schedule'."<td>".'Start'."<td>".'End'."<td></thead>\n";foreach($L
as$K)echo"<tr>","<th>".h($K["Name"]),"<td>".($K["Execute at"]?'At given time'."<td>".$K["Execute at"]:'Every'." ".$K["Interval value"]." ".$K["Interval field"]."<td>$K[Starts]"),"<td>$K[Ends]",'<td><a href="'.h(ME).'event='.urlencode($K["Name"]).'">'.'Alter'.'</a>';echo"</table>\n";$Ac=get_val("SELECT @@event_scheduler");if($Ac&&$Ac!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($Ac)."\n";}echo'<p class="links"><a href="'.h(ME).'event=">'.'Create event'."</a>\n";}if($ci)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}}}page_footer();