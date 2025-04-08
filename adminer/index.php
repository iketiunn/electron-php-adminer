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
h($B[1]).$Rh.(isset($B[2])?"":"<i>â€¦</i>");}function
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
as$C=>$o){echo"<tr><th>".$b->fieldName($o);$l=$_GET["set"][bracket_escape($C)];if($l===null){$l=$o["default"];if($o["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$l,$Ng))$l=$Ng[1];if(JUSH=="sql"&&preg_match('~binary~',$o["type"]))$l=bin2hex($l);}$Y=($K!==null?($K[$C]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$o["type"])&&is_array($K[$C])?implode(",",$K[$C]):(is_bool($K[$C])?+$K[$C]:$K[$C])):(!$Si&&$o["auto_increment"]?"":(isset($_GET["select"])?false:$l)));if(!$_POST["save"]&&is_string($Y))$Y=$b->editVal($Y,$o);$t=($_POST["save"]?(string)$_POST["function"][$C]:($Si&&preg_match('~^CURRENT_TIMESTAMP~i',$o["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$Si&&$Y==$o["default"]&&preg_match('~^[\w.]+\(~',$Y))$t="SQL";if(preg_match("~time~",$o["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$t="now";}if($o["type"]=="uuid"&&$Y=="uuid()"){$Y="";$t="uuid";}if($Ba!==false)$Ba=($o["auto_increment"]||$t=="now"||$t=="uuid"?null:true);input($o,$Y,$t,$Ba);if($Ba)$Ba=false;echo"\n";}if(!support("table")&&!fields($R))echo"<tr>"."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;")."<td class='function'>".html_select("field_funs[]",$b->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>"."\n";echo"</table>\n";}echo"<p>\n";if($p){echo"<input type='submit' value='".'Save'."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($Si?'Save and continue edit':'Save and insert next')."' title='Ctrl+Shift+Enter'>\n",($Si?script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".'Saving'."â€¦', this); };"):"");}echo($Si?"<input type='submit' name='delete' value='".'Delete'."'>".confirm()."\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}if(isset($_GET["file"])){if(substr($ia,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}if($_GET["file"]=="favicon.ico"){header("Content-Type: image/x-icon");echo
lzw_decompress("\0\0\0` \0„\0\n @\0´C„è\"\0`EãQ¸àÿ‡?ÀtvM'”JdÁd\\Œb0\0Ä\"™ÀfÓˆ¤îs5›ÏçÑAXPaJ“0„¥‘8„#RŠT©‘z`ˆ#.©ÇcíXÃşÈ€?À-\0¡Im? .«M¶€\0È¯(Ì‰ıÀ/(%Œ\0");}elseif($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("b7™'³¼Øo9„c`ìÄa1šÌç#yÔÜd…£C³1¼ÜtFQxÄ\\2ˆ\nÆS‘Ân0‹'#I„Ø,\$M‡c)ĞÒc˜œåç1iÎXi3Í¦‘œÒn)TñiÜÒd:FcIĞ[™cã³é†	„ŒFÃ©”vt2+ÆC,äaŸG‡FèõºÊ:;NuÓ)’Ï„ÌÇ›!„tl§šÇFƒ|ğä,Ç`pwÆS-‡´œ°¶ûÎë¼oQk¡Ë n¿E×–O+,=í4×mMêù°Æ‹GS™ Zh6Ùø. uOäM–C@Ä÷ÑM'£(èb5‘Ò©”ê…Hàa2)æqĞ¸pe6ˆ?t#Z-€†óoxà<š«„s£ˆò¼;Œ£HÎ4\$àä¥Ûš´„a¼4‡\"’(Ö!C,DêN¼í;óÀ¼Jj¨„€@ø@†!Ãş¼ïKÖö½ï‹æ6¾©jXü\rïÓøÿ@ 2@¨b¥¡(ZìApl¡¤8ˆ¢hª.…=*Hú4q3³AĞ‚÷.¦ĞK²ÁÅ!ˆfğ©qr¡!Æ1ŠÈÂcÜòò*+ é(ƒ\nÖ2j²°­(dYAÅêûDÑtÇÏ‘¤m*RÄPî¤Qb#J+Î1Çê•NûÑÊÌ™>ÆAŒwKÑC`ÊPõàĞ=Õò<¨…@áÌŠûc ó]Rr¯C•õ€ûHõŠµZÆÕ´\0ó=PÜö\$ï€@o¥(òĞÕú0CóG-ÍĞZ›AÊ…t3:t»ôc€ŞÍµJ…’ŸlcöåRÊÈÊô\nËrÒŞl€Î¨kà9G–ìp¾!pjú_X]háXfuÃÓ0Ì³øÒŠ0è=½C˜à6#Ë75ës,Ÿ(f#z‡X>3[ïn?È1m÷\\XõÅPXéeggJ4>Ôõ \\Tõ·?£HÚ3yú~›hx˜t6ªã%uª¡8L¡³m\nXğªªõ”iöäM©ÿ[ˆ†¡,òY†äœPóÆ9|~OÌ1ß¨*é5+Ó£ºÒ1d{o=–ÔgåÍİx,ƒƒE¯P“æ©KÊ•sĞºãE^4ªfĞ;İ*J“¥#j=Õk÷tá8—W1våhÔÍİõšæùÊÔƒ¢3—Bíôt’È§¨éà.^Ì€os¤`Õ?‡Î7}?Z]yÔĞ[iPn¥¹¶Wğú‚ÚÊ”‚\"îzƒx\"­¤–4Ø½rò?'í\"@gò‹ f!Ô637àQCAè\ráİYº§T†PÜ}@¸ÑêMÑ}Í½+¥¾ÉÃ{ñ…!’ÂÕ²¡Ë÷ƒáİœ†ò4×¹ G	I¯æ\0^”RN}Eø°ŞDY¡‚{¬ê/>úŒ±ƒ#Æ*¡ nø•:ÀÉFpÔ×hn%ˆâù×w#Á°½ÆpÎü\\\0¸æ‡PÚ—êÿ+¬E’\rÒû)sLµÓ±°d\r×ó@‘©´â¨ÁÁĞ-\$ò‚ÇèV]Ãƒ¥ğ;Ëàd•ÒÔ0‡\0X%Ü°Q±è¸*¢ w]ÑdÒòX¨y†a&3MèM8ÆrXE”lQ%Ö½B2çt×!\0€1,¦ÊˆÉTI±-Í4½Ãhee3¨Å†DÑn“.¹•†PÍ'Œó3Õ†«™ìZªRŸsx½i•8Œ¸. 3ÑFIÖ’ÔšbæSií°¸i2ƒ)›+\r+pŒCc9..iÒpaI¨xn‘Ô°†RjaÃO)„\raÄ˜Î@Y‹\rrNPœjBC`d\0È7™Ì»'@#ÀÖ©Õ9•\"é	u\rtqÕ’\"ae‘Â0ä¹Y*Õ`rõBM†DÌlÛ59Æ’ÈàèĞ7<1œ3gúÀeœD²>Ò¢{\\Xyr€3mÂÌ¨£²*.3(7‡ò£Ãû.¯¬Y[/!Û3²(ÒÉÆsC» («)fÕèæQ'ÄòÕNÄÜ@*LÊaÅœ\"ÍdAÈ8 ºİ´\":’MÂçI	(şƒ*1CÃÃİ)ôL‡yˆfŒÈ4S(ŸÆ»\"QÛ„·ÀÍÖTWèA{&.î+6o1¬½:Ÿ\\GÈ£[éZ”3œ€Ø×CuH	Æœ™•qØ¼‘Šì	 0Sës-Ê&“´¶&èeCb@(\r(ÒRÁ¨5„£bHú\rÁNlÔî³Ò˜H™èeä|aàÃˆ1.!Äxè“‚YCHĞ)>JI²ìãÁğ =AÚ§É¥İC¨±ìT¥àpßr +ŒÁ™;IOe]è.5á®<[û¯vS|Ì”Á¸á4İ…£9äU™Ü©Qsâ´“‹Š-ß˜\n–n\$\n[1óĞòÊŠ'zÒ‚9ãL°Vz*X¶gçx¤Tú~Ã'ÓE•+¾Y,AÔKàGœŠxsÇêa]5úÆ¨ºA>Ti¯ªå¨ıRLjøq†=|Y58o<‘àÈjº\r±LP0N†ÖÙ°&|fÜ9b#Ñé,[TYŒ¢õuÖšÚcæ™`ö¦Öˆ»dºO û°v & VO-å·õj<òBøäò7»Ëp#ùx¨&¦ŸA’Chî ïQì5†”¶§%í£<Ùy+Ÿ\$¾~•Tm9?´ck•!jÅoƒ cV\ngÌ]É4¥¦çâ²SJI‰PT´_	>›ö~¢;Å›ôáO»üf›”Q‡\nw™M#„uÕ¥‘¸ÒX¢èo8¶ÏTK%eÛ\\d-†ï,mSàFêÉKà€·Eƒg\0\$—4LâÙ–ãˆ‘ìÓıMç\0ª†PÄÊè-îê§¼¥Ã»ûAÈ)kz­5Q®Òz©[‘4”d‘ŞÀe¥åÎá‚ø¾}/İŸ´†Æh§ù¢\rÌWWs<÷è¶şk¾»Iñ*[Ÿ§@icsÄBˆú@5GjŠ7	ÛŞ_/-Áè”òèôÊAĞ6\0Ğ‰zZ7¼ˆÎhO§õeô4ú—Q]Gß¾¸è‡æütJyQN	£SÎ‹êè\\æú¢‡ Pº\nJ¾ ŞúOÆnbÏŞàÃâçOâOÎÓÆáîŞŒïÎnNdNTå„cöoùÖFœÓ/¼ıcÎ­]ïıG\nP9Æ´)–‰¨ŸĞ2ÿˆ¦!…nş„\ng¾ıOªÔM!OşÿBü´Œ#D¯âô„¦0 €#ÔôÂlú¢^J\0PoFøQàt\0pp\nœĞFOò_û,âØLèÒ.,Y#R¶Ô-°Ô’Øh\0v²00ÿî\0APÑOõ¸âpæ/Pê'ícÑ Úœ°\0ë-\\ÿóÍ{ÂPSY±å.W`÷PN°\njÔ¢hàn%p}I{ÍŸbÃê6íşäÎQQéZæ)z¬1|™©pPé‚—®Z»”ÉªÇ¢`ZÃ ¤ŠàÄëæC\nàà\r\0áê\0fqY1SÉî4M·`é ZºLÙ@`*]@Z\r`Ê î>Ñ¼½KÙâ®bÂeåR§£Gàì&ÂÛgÒFn-Ñ¦QXKÒ¤ Œ¦/Ö\r Ä‹qö.ãèî?Í\"`ÌJ#qiç\$QöÈ4¢¥Q¹%ŠHzÒM&1Û&k‹&’HÀÅ'í8cŠ›R@QåäJŒSƒŠ8àè\r ¾  Ú²„¨Ãù*‡¢½ {R±)¢û+£`àèˆrÁ'2Ç*RÏ,@¿+ç¡)¨ï-£a,òã,©´Æ#)’Ã’×€¾6\0ï/²Æ¯© Xr‹(òÑÌRç‘÷0òe±«1J¯+2Ø†Àß,ó-3\0ê\ròäWrß/rÆ¤“,é{2ó>(«xóK,ëŠ0“T_ÓY5334ëŠóDfs)“ƒ×2Rõ-^\rò‰7QÕ‘İ7ÓO2³6ò‹0S˜)“b\r“›.¶Ù£Q.Á2r›-“J3)/Ó-:s;³“/ÒŠµN¸Y„\$óË,hR„¤K1“s(Ñ©\$cÅ3ç1’¡—4åò¢sH?T¬rª^t	6t#;Ò›A“W4ô%T\r+4 àÙ8sé)J¼óó%2W24=7óşèï@`éE+Aô3tUE”8Ô!6%@‰At	CÏB”YCSõ3ùCëåD [\$4IHtL rÎ\0Û:SÿHŸÑñTKFë9@ô:R1F4SKF\$ Œ\0½3A3#i‹JôuEtBÔ	B4Û9ô3HÔ1!4Ù+0	NÒ»ó.\0æô©.•J”f´ßPõ4´¤´ïOÔ•4òâ4FÑHÔ©\"[9-‹‹Pó•4Õ;/Õ?=³ƒ9ÓZ.A<aTÕRµW,tå;òÆ\0ÏTµG,u4\0¿VÕqV•OTÀÚkyV ¿WsXu;rıVu™,cPeuXÕ‘-•£ZuMQÓåLÓ‰12wI3ï;CŒ]s\\“Näâc4õ•55Ñ:3]2Õâ? 2%\\3\"z\rŠG[”7\" u'*­åQõ3JÖz-å4ïd ŒÀƒ&ÀÓSõÓ@ój{Mï6‹x0–/SµoX¶'+5×\n’ÏZõacòš0`Û!­£`iÖKKn ÀÉ\\SYuy2Ö7Xi8º,Ö[3Öš³ü voEÀéhth_ÖQ7i3¹iMf¶—ió6àe`e†:_#e@ìY\$–Ìc14öº×C40ƒ\rg©Ûgä+jàÜ:\"»\\MJıå,");}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("b7™'³¼Øo9„cäÄbÌF¬Îr7MÆHPÀ`2\r\"'Ó\r…\rF#s1„p;’Æ“™¤èe2I ğ‘ªY.˜GFÃI¸Ö:4ÎÆS²…3šÍã”šYÊu(ÃŒc(Æ`hà#%0[ÊL£‘¡½h¬ŒÆC!ˆÍŠE£¨àŒb5†ÃšøÊÅ²œ¬òyá„fbºÇw	äz#ŠÅã1¸PÌÆ6“¡„Äl2‹†MQ£d³e#¦Q Äm£>5Ù‹Önú1ŒÇSĞe0™½o#•º«ãG­ßyš¾GxA˜×Îåš]]Ô»ŸLÆ“)°Ès25øœ]¦ü6À8§Ø-–Ä¼»âüà¸oØè€ NZª)[Y #Ì¤)Pk ë¯p‚î†¬ú8C(\\5£›w\0C-üâ&\n(à:rÊ–£cä€lLäã¸tÀA HR @…‡	xûFtB8£LBÖI‘ˆÚ0\r@Ü3Âã|sŒ‘ì Ès\$)¦rªšãs¢…I2TF9„rĞ:³t0ß;A‹¸ï¡r˜Œ*Í”Ñ=»!Ê¹¹.*ÆÄÌræƒ\r.U†Ã534Ğh@@1?ìÌR…QnC‘4£(æ9Œ#8Ë7„a€ÄÕ’ñQO”PÌ2¢LB™H„š‰K%Œ‚YTUUe\\å´ÍE`†!È…nŞÑ(ò@‘M#Ğ0Ğ¬b0­ÍÁ4Œƒ\noX¡ÊİtÓƒtñX¨j1ŞsHÄ¢ÛˆÊ­+Šõ€½î\\	ˆHÜİ,ã@Ò÷ÜÔ6¤0,ı9Î¶ĞÊŠÙ£.2ã¢©Äc¶@5ã¸ş65äP¼Q\\†!€fŞ¯+@0†ƒ\r’ãXãOdÙZq\n*¹y“å:B©Â£“ÃˆÀ-øgCÚ>W¤ç¹c”ò[ÁoœçcJ=eëãê¬³ÀÍ;Û¢)š„S³§ƒ#‚¶W”Ì7éº­G¬Ã Ş8nĞä3¢a@`a XqÁpn×­ƒ4ÊõttŒCÊ¡¨®C‹¹/fpüOÆñüp`AHYX£éaÊo[æüÁ\0È4Û•skvÉ€GTİÁ\0áÙ ã:Š9öm.~9¾mº°CÕãy¾{Çèã³ Ãğzvà0ì•Å«ÚÛèF1D.HÆ9O\r0	÷,‚ä]³ÆMa¥6¯\0d™˜7xÁÍ%têô–s€1G”—»áíV	µĞmÖ›Q!AŒ›EwàhawÏ\0A<×±ä=<pÊòAqWVf¢xÄà›ÓÖ{X\nA,¸4ã˜¢øtˆÇ°÷'H”Á[MiïÜ¾˜õ2MÂ#D¦¸‚Òn\"‘åq¬ÆPÄÜ*+Ñ”8‡	ƒ„Œ¡Í‚”XàyC(6W‘î>¥°Z`a(±Æ@È0ZËÈw1ÄŠxÊ¥a¼;ˆğtûd“Ahv!°:†Y ¥î\r¡‰ë``Våæ¡‰Jh}#(j`¶7Çd\r¥\0cUrò?µÒDHˆô ®^PjˆŒtÈè8†É‰f9–&\0");}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("f:›ŒgCI¼Ü\n8œÅ3)°Ë7œ…†81ĞÊx:\nOg#)Ğêr7\n\"†è´`ø|2ÌgSi–H)N¦S‘ä§\r‡\"0¹Ä@ä)Ÿ`(\$s6O!ÓèœV/=ÈÎg@T4æ=„˜iS˜6IO G#ÒX·VCÆs¡ Z1.Ğhp8,³[¦Häµ~Cz§Éå2¹l¾c3šÍés£‘ÙI†bâ4\néF8NPC\r\$ÌnTª™£=\\‚r9O\"ã	Ààl<Š\rÇ\\x³I,—s\nA¤Æel1Ûğ‚çEÆ¯¶Lş³]°[M'=±ËK”¹e¹\0¬ÎlÓÓ™Åı¦\nÒiµ®v¿c³Úíğ»®ŒİÆí]EH×¤¶¡ëH¹o»šÖ½ÎˆĞéº®º‚ì¨‹¨6#ÀÒ\":cz>ß£C2vÑCXÊ<P¸Ãã*5ºÈè·0X97ñœN¸¬¬\"£3¨°ä!ƒæ…!¨œ‹#‰Ã\nZ%ÃÄ‡#CHÌ!¨ÒrXæ&:c¢Î7JHp~Œ# È‡A:86ã°Ê‹©½&¥¬¤|Ë éĞÎ3¡Á@Ò2<£sÎ†«PN„á] !¡0M-Ë²4¿0…Óõ\0àğ\\Ô—ï«Š33¨d!>H Ş5.,4ŒãpX25MÚ¢ó*•¢,Ã(îƒ\n,(ĞÊ!ƒ¥ƒaØÁpÎÙn\0RÖƒËv°mÂI.\rõ][Hõ€Ü„áÚÊ<J8«Ñ¶5NíÎC”¬<ŠÉ æı%mCr#ã`XDÍooS“L)^Bıã{ Áè`…wJÍJZÛ/œ¸XC(Š‡>a@OŠÉÓš’%¶DpÓ£ è8aĞ^ù«X2`é \\ˆŒá~º…áıò\$™`W¡·!ŞQcŒØ°Ê!\"#\"i‡iZnJ\r­\$.\$µ€Ğ:jš]1C{Œù`Ü7Ü»¦<¡ÁpïA­¹`n‹íº¶S·î#Fº3ëù`bA¨Ê6ï:u:n8ÈéµíVÔƒNaÜÒ2³’I0*iri‘%²8Ãgâ/5°ÃÜ­,tùİä3ÂáîY¯åùgšù¾r9uÃ=C[ĞÊ¦²:c@{Ÿ^Ë¨zÁXR…ÈºÖ7!ut0äò¿‡â¼¸&EƒBé¦äá´o¶4k†\" #PÂ<íÈ9ìƒHá°gè6!b½Hú”k×\\€™€æMAG ±RÂPK\"oä7*ÚKƒ CAa°2(&ô‹—z2\$ä+Òf^\nƒ«0Œ¬‚zEƒ I\rÍH<£ˆY!`tÁÚR‚‰!Èe€ö³·î]C˜[ˆ±Ã2.\\P((…Ö2åäƒ©pî’pöMàp!éYV†@\"àl&`ê4³(qĞ‘Ó\n,/ä2Ô)ç#Šñ_\0 ¤C8E]ˆ/2 W× )ŒA‘ª	\rÏº‘ÄqL†R§TË£O=³ºnc™âI1Üx6p\$!QÒQº0R–ƒ`=Kr|’àŞÔŠ\n,¡²”`èÛğc\rqÖ<•I~e t”1Ôy#’	4HŠXè‘ NñHz&Mi@6%Éƒ6Ì˜DAÈ6©\$·8Ãh[M=:Ni»:'Q\r‰Ñ Øoƒ\\°ÓÂuÏ0»=RìøX*8hrĞñ\$PA¦ ²B@t\r¥%A‚ÂEB[²à3¦ÀO!¨½\\X9~\nx\\@ ¼°¸Ø3Œ\0¬<ğÎ¿ÃBa†‡2nwV“‘*„ \"ETZQíVb¹ÇEŞ\n)\rÁÂ,€ ŞŠ9ZduN,ºY»2e³RÕ‚[¢äª•Ğ½T‡N21Q æCş´Å—¬tÓ,BydR‡Q¦‘í ó¢\rM“dÉQ\n#WUUj°t¢Üèz,€Ü«gV™LZ‘ÕÃ‚ë1YƒÙ8\r³zl×«!i&BrşÂÌ+8l‚He)†[[0ìHrªº•J¯VY´n3baW@ :Ë¨©‚×Š`à ·µ=ÚÉ¾ ªá ‡ä£N€ª@®ºx\nXÔ…£” ;R“~½@ÕMu¬2Y—¶ˆ†Ûo®Åj–|â©q&ÓT©õ6Xê¥Z§İõ²nÊÖ{fdµ›KwòwZ‰ÀTo`d`®K¬j¤ê¸VÚ{ÚçL¾èÂ˜¶Î!Ì™—‰ÖÚ0YáÒfÜ‰­™O©—!InBÎ#¡+˜°°Á;×¥î¢`ê‰CZ\\GF†ci!Êzÿ™:dI‘¢ÊœÉAÜÍæÑ“HÜX¤5×¼ IÁs†a½Üã3NK©'ÎĞ¼ı§By­!—§(Œ­&Qˆtršè	Tñzş™ÁÊê:¹W(´–#„>[Ğø^ÌĞe:U+¥ºØA,Àˆ7‡‡~aüßÕá\"OŒ¤cæfV¨¹@Şè|t5_Ô2Ö²ÔÀB–sIö[}ÜÀé®†[¹aÈ©í9S3dúÃñcÍİ·z¦éÍ˜Wqe\n]^d´k\0°ºïç=õ3²í½z©aõŞ¼~‚Ó2é£«ò‡0ø•hw7ap-…Àº	…š“òÜ†Û¿€\0³ÊGÛá‰ƒa”Ç	/gáwlµ^­Ö¸™ÓààCiïæÉ/ÅtºĞæ‚Ã0t	hŸ‚I}Ã!'çøfÏn¼€‚âß¼wÿ¼¾‡ic7Oµ9omƒ©~LÊ8À\r óäÉz‡'±UFŞàbïVëSÅÑÒÈcZóš4µÙè¬]Ùsê’¿[-iW¹om¿Çæ½à5¢ì».tÉ=½ğİKº¾õØbc¯XÍ¹Õõú=Ø82‡¿iºEÛGäˆQ*)CıÅuEµAJËt•\"Òp_ÃBà\" ¾›‚p¸ &T+“İHíÓÈ{À(õ2Å^O‘,öt¬+°€hLe‹—ÍËC7~©4úÿdü¸ˆJ3kjA@7¯s‘SqÀpı­SF(ë,ªùáè)äiîïb4ş@æÃir’\0A\0Kæ;‡FÄŠ\r8ÀXßä\\ù-I œ\$%%/<%E„. ]ÀÊÏËàWÕ bLàMF-àRM®:ı0 ê	åâz‚^écĞ–GZÆB:©.?išÓ€æ3(+@L&vÀÌY ™`èpœªğ¢# ®n€ĞĞœ¬ŠÅ“`È-¢Ş¤^éq¦v^ Ì\rŒèe^ëjûlBb](²X‹«\rFøXäâ&ß%:Pf¤‚°‡ ‚M@¤ÎøD‹–!¨†²&±fÊàzK§(úcÆU¨8¬o¢úb1>Ü@Ğ§…ˆˆ^Ñ<ƒ1N-móªå l±hrìÊvdHrb.•àöˆ«?Cîeö€ÂöÀ¶ Z@ºáŠ^`^™¯‚)‰h^ˆHd††¨(NÆ·‹³J´º`YîøÁ†·±ŒÑ‘Q™Ñ ÃQ¦ñªøÎúNíÖ£pˆîMNİdÕªèï®š×Â‚*Ï;Hx@Êûj	PÀXò‚èfÚÈ;ñ…ân3GŠç`ò% î\$-tù-FŠjGd f\$òFDòK!`z a\r#¦¢#GŒ‡üôEP‡22G\0Ñ'§‹ˆT´òëû&r\"Úmªƒl¶‰‘¶\"ñ»â(@Ú\rÀš§C€R¤˜îñnª0’§‘Å”b!*bX\rÎ\"Â\"	D¹ò#R¾\rÒŞRÉ6ÕFÍ-%NÒìd¢N£kb»I#\rE'Ìm\n¼¥qH…’²k'ÉR’á!qôD‡Â®ìó2«Î²#¤RV`ÒÎÄ·@Òl y3(¶-BØ\r”Ãb53&14h’\r\"âõ†Dk>`r¦¯‹4@ìœ)\$‡(K!IˆvNbòMDò„zÒÇe5BÒ%Ó[63FÙ­¨ä­¢\"fW†J[¯)Sµ«Á¤(*‘7FK7€Dr8\rß8B`DBV!ED9ñ,ş—â“\$(¢Ğâ@Ù#’Tu¢ZC29	u¤ƒ#Õ€È29#Ò@tàXÈât`L¬\nbtx»LqFÀĞôDÇd4‡@Ô®n\n‚Ô'°º´NÈNÔSEtZch­\0æsùCğ™Cà]\$ŠË\$òR’V2[\$òbRP‹DIš‡-?FíDÓ«²º”DÄ¦#êut0 Ä_FiáK ÜŞ­Ì2’ù4®²CÌ®\nåKíÁ)É&”Ø\r+62”ÒÁJŞ®4èÜôï>ª—R©tó–ôÜÒBD”E«D§VPTwG­u@§ªæä¬çDO	”¯¦YO*äô˜ÒÙDéšºqNôãQ`Nä¦§€Ö\\¥’\n€Ò%¤<³rqFŞ²UTû€Y&2t;h…N†ÑBléB£rÁÔHRL h([u•%Àf™?tGÌWQtIDñ++Ïüë•“%”‡Y€`Låz#úJlÎM àÿBEUÒÿç}3â¨Q¯\"2‰U¾-bâøì{¤’ÒeB\$ïî!µõ]Uø•õBÒ,jÒ”~Ù‹ƒ¦‰/Ô¯ØıÃõ^ñB¬ R¶–+ÖÚ6'7JËbõ„ Ã£Dö;\rãOÃ)Pòİ\rJæÎqS\0òR@CB\$Dô)CõJPÂh®qf9\0£ì¸àä&p°H5<Ñ±o´éàîÆS£€àkseC_¯­Lœ…ó„òÊÁÑÔ\"%`\n€ò­§d &¤¹ezP‡TîVĞöÔ­±ÔÔæ•n‡^	§ß\nbÖn±ÔÆl¼ŠìÂdL”k ñ5‚Úã±ÔMñp¬ÀÉ}÷\rìÁú™Âû’E6¾®Öënğ'o6×!vÎu÷Iln¶ü5 |aÑÎ®Å“rLÌE…—ræ!u%au÷b-¤ô ËzpÆ`t·¨À(&ü\r€àHÛ<ğîeX\0uä.3±;;,V3ìî eˆ_,Õ]§M]ê}_hğ©—À…è\\1q´‰Àğ“ÄD İ|(u)×è×­Wä%1¶›ª1/0àW±8ícÄí¶•óJ·b¨ì«~ìñÉÖæÁ‹2Ï¹„A‰œíâ`A1\"€Q\"ËOxÏVàû'Ú}çã@B&@Å ÖVwv‡B§eÅb8&L\"…~²¢&^ø€’éÃ‡âf*lW…àñC……EWÆQx *x§k‰Ã‡p?§İŠ\$2#È¶ü äüo´óS4`A+B®X¸YàDÇÌ’Øx¥È€_38ä()¢Hd¼?x¿ŠXÄ™¥=æ 8Ş„˜Â(Œ%È¤zäÎ\n\0\n`¨£À¹*%ù@IÂzèm‰7ö#¸‘‰ èk„ÒsàN¡¾\$€[t‰AKâàğ§p©Ğ¥ú%Â(jD×eàË”\\™FÂ9LjKtà°¹Šs È°·q©›ˆ€™ˆÀá™¶„‘ù„ Æ9B8°\"Ç‹<Ç–'úù9*qØÔT#R\\Œà`e-i/œ÷÷Œ4U\0Ç†˜m„ùƒ””Ìó—@Õ/š1ø¼}ùE@]Œ˜Ìyyèqx²ú]oåhX#Û íd¼öhl å%ş ›øó4xä}€ØHI¦Hù¡ì:‘\nSğ–üùµ”ˆ\\†`Q“ÖŒ8¹é…Ù#ª\"¢xbù\n8¡©1ËŸ¸‚u31w`z	@¦	à\r¨8&’Ú*!š.»Rº<ähª@“Ñƒ¨ÏLi‡2GDjONE4‚ä³ÌO0CÑ‹àŒ¾£Æ¾¸u¥ŸBè³C+û4®­İ‚,ÀË\$´L\$Iº³öpKg§kknNË–!Î \r)*’íMtğ˜Kx6†ÖP(TàbEàğƒRqàŞ•=N€ù³¤]³ûBÂIºÔàS%M¼Ã;€vTÅ67v¬tT\r° JÊ\n ¤	*\rº*7.&±ÈhÙy¹»Ÿº;¦¡V\0ÒQz(ÏlHz˜€u´È{‚5±'¯F€ŞBæDøUèEú×¬#ØÀ_\0@èû@âK%@êrP‚5¾¥û*Mjà¸à\\Qæøj8]·v[©U\0Úš_ºŸ”ÊD˜¯¦¸ª|?‹1¯­ZM¿›ôUÚ¡(<ÀË¬)Â©±°Dæ6ÜS¦¶Xqúí†>	yf@PÎR=F›’#Tj\rBkÉîp\0èèîÏ—¼oÄxÅ“U|¹` ^JÊÙî(:Ü;tX{\"ãåi†ÊÅşË,È\$Ñ[1&ô5F5¶Ñ-ZÓúå¯TXÚM\"–~­T¢#]`m;ÏÖRÑ0ºaÈ\\ŞÌÎöc;h<†Ì¼â‘öP€úD–ë{ibÈ:û\0˜µl~/bN¥2Õ·`QÓäÈä5»\nÔcT[HtlD\"Æ>ÁŠ/dÎÔ8£ÕMowZ\rÄiB•h§Ræ§«Z'd@o²¶iRò9ÚG«A®xÍG•®×Wj€„Gy–bğKVË}Fü\\3¨Ö\"©\0@zÏcÏ£X¼\"	ğ Y vÇ6ylƒ<R(R]ÿĞ2˜5š_·í>Ëb÷•B,\$ÜÅ”8q {ÈŒèË›¸Ì Éâ%¹öùÆNîPşnP¸¶ü-¦±†‚ #sÌ;¢ĞàXL©ãR ræÑ\nnr-ü3^_æ+6¹iÔm\0¤kÂ2¢İ|@`\0uéI^\\€àÕÌv,=4.*àbx7ÁkÁRÔ¦õoêğ	y·Î‚¯êôsÊQv‘¬´&.#]ûÂİD\rÏ­Ô’È8I\"r˜¿(²(’ à\ntç”¼™èÌ©ãì>”UKd®/}Ÿµ‹©°áŸ#‘0MT»&¥¹Û \rû¤r{İ	ºüYÃ–Jú»	<aÆX2®İûõ:¿Æ,uQ‡ÔZğG®î]ûƒ}ˆ®ßkô_{”=æ Ø¤3iı¿ƒ~,ğ ÀŠ†}48½8¶Hr	€ŞM2®#€Pêäbqı'ÃÏúN‚@ZîÅÔPI	ßÜrßÓî¯ÉûæĞ©ŸÜ?¹ÒNåw)Ô’9©mMs†ÙHáÒÅÑgN´ õ]p1×]Œ„™Ë:\$t•ÿ7õŸ‘ö«1ÑyŒFAg¼¢¸Ú!ÑYÑ8`,¡oDh‰Ó\\\0xıÅ:?é\"+“	‡°SÎ:QòÈ#A*Ş¸Œ—hâ@Xÿ@V ŒI	Îé>ĞA\0èâ	è¶JPê‹Bse¹t\ræÿerR.çÀ¾	…rr\nˆ±…Q¬¢}h\0t¾\rîmt0Ç ŞF§#}‚há`ª.ÚÚL”`=ş*z| àš5ò©ï…Ú<Óõ‰2¹AÏF„JÏ@¶Ô†È³ÛnhÖymtÄbF\0åÇ2İBŞ“5ñK\\aoZúŸQö	ªíŞ.É3T²iHV—eÜ!‚“^¡\\ëTû úKş!˜FÚÑğP„`øµÇW	 ş´¼šL„Ñ3Vß†—¥‘i\$œC¹oav[£Ã\rn\0d†0©!“²¹C4œ0Ğt¤4˜üMHj™&ğÉA©l¡ÇXlC)ğeM>;‘”mSQÀÿşCXT\\ö“n©À¶2#å£•V¨ãR©7\"–eĞ•­tån|¨´§é)ä5ˆ`}Ëz~rÙB˜täÌoÀ]@XŠğ28'çß¸æN9Ç†~%¡‰yO\"bBàízDÓŠÊTÓ\0Ê qE*'0TT\\¼\"#,…HzğõºŒ}3h»!ÈWKä\"H¦EhQ!ZŠèD‚\$Q„ìÇ	§€Ò€y’@±5©¯r¢àØ¸&Ô.0i(‰ÚÍwy–n¬¢\"à×E# që€»ˆ¼¼cñˆ?< ¡Ğ§HI¢í¼‹CôBÉp °©ÖfV€ATU´„n‘qKÃ2•×dUÜ®!w`-]êuÒæ˜1ıIÀ'¬RÈ£6\rŠJ5q¶Aª\\€ğ(àö.AmôpàZ‡êä\$ƒïŒÌfÆĞ‚\n\0°sA)‰˜êŞ(;—\$¹!¹#uÈ@¾ğ5‹°¸ê1«Šì`\0€#„±ÚĞÑğš\0€mp[óHFóò©æ8ƒXAÜ’á~ŠØÇ\0o€{ y˜ítå¿`£j…MËÊóÜ…bñ ØÊĞl\"WÜTÆX5‘yŒ·/ÛMiŞ²˜·È69i®Jl\$;\"ÛH~BÉ¶ùIÀN‚Ó0å\$+‚FDdµÓ¦B:B5q3@Zd£S±jG«W\r˜KEä0´ş6rQz5\0bÓàÉHÀ/= o ºØ-‡ rYÈç@äÀ1/l& -C\0¸32fÉ•òu8ÙÀ['2IÎN²Jè@ZCÉ–ÔrC	‚Õ—È!xÉ ˆáæ’“Âi)9‹X\"Ò†”@wå(É)Œˆú¤·ÎÆœ\$)E’2T`d•,¦e(pÍJx!Ò©”l‘^ºR€„-Œ2ˆm µ¥y&º|\r¢Ö‰\0Rñæ\0ªK¢‹ ¤Å+p€UÒˆ–Cƒe–0#X°Œ„€æ\"H ;å–à	ÿ(èyØkÄ6óç˜ò ËrÅ\$d¸„Ò!n´àk¨@u.QÊÖHÀ'—xåã.É!Lˆ#ÊZ…ABpÈ”R¡0†ÊŒ•Ìj`5‘Ôb%C0@ÁÊ,i\$˜HJ¥R¹ˆ;Âr+’DÀìa[H>¡:lyÄ/™·&eñé\nÄ	7'9@„×+(Ä}Â;¥…s‘\0‘S;ÀPà>p7nIl(VRtº¥m.(>_DÎNnIY¦M\\œ+èK”#ã/XÍBP»(á`×¢t\"±M'!~Ò¹©¦ ²K—‹¡ƒ‰á#Äa§xO1%1¼“m†½œ\n–¸ÔI˜Ùr¼ˆ¼&±4GëOÊğ[W_®u]“Œ‰Ç_(”	Í¼ôhxs1%œº!rÍÂ%ºllDÆD)7‰ü!Çj>ŠË\rÂOb| 3Aœ\nH¦‚h×™~60\0A”ó! à·©òu¸äƒ°2O<&ŠÙ6¤î ÎL¹è¦(!C¬£^ÌğùÈ6dxòó'¸E¦¬œfòÏd^,cëLBKò\$©¦ì†Mf^™ø¤Ÿæ3€‰e@„’P’nTJUHÉÿ>û¦Î~Æ 1Ä«¸KÄÌ‰Ë«é)ĞH¾×™ÑÊ±*ê*(‹gfÅFdwK¹ì\0\0*%«\0\"|€Hƒ@äş\0õÏJ>*d«(&µmj€eVÜ‚1ğ­Ê†Êî)ÕÔYñ\0¥‡¬\0\n5‘h-#äĞÁ“‰G,‰‚•UåWqjJx‡ØP(¶ª•£èutR`¤¨‡AJĞì¦©¡úÜ„“\"-»öŒô8\"M…±A*\"Q¦ˆã%\rhƒDÄÁ†.nD`-W¾_WñÄ*+m\"“9SÆ\na‚Ñ'DF!À2ŠIkÎÜÕŸhXŠ‡İÓ€}Y›fö«5:Raßi 8Ù_\0ou¢Å/)U\n½âø‹™×He|¤/«>•4b‘¼¤9o˜ùÇHRSĞXãÎMøô¤2#‹bÂä]€áL­Œ8ğ<ş…É³w¬yáé	o±9\0·MĞ¸‘=äô}áã†ÌÖi–\\Ğ	i™Ï@^À\$, |Y.€(MZoum5ÆYOŠ}Dy!\$ŞÑ‘\0P“\0”Í\nƒ†ÜĞ©„!3¥2Ì\\„Ğ*jµ‰h¥ ó@R3İDÓF(#°;œ…|	%LÊ0Ë„(€|¥h‡€àğ‘¾P‹Šª…ÛhÂEMY*hÀ©í+İ8\"JrO¡9Ü©\r7¦ˆÊpÌD3¡åJ ¡“|6aI%Nb…pˆ›Ş’òSºŒ &8ÔBi>uø(…E0(Š©âkJYŠ«¥€©€úœÀ0§9ÈUM„ŠéeWPƒÔ’¯H«¥<éO:ZUî£«E<Ñ.…V”ô®']]Çö*²‰´%D\n	.W©¢øÎ…(uP5(	¡2^âBCl¡zPÇ`3£²¹\0¾(Ğ1Tj·b]pN\\:ŞËáœ!,ÚLg`P×HHÂÒ®(²\ná<€áEtbƒ0GèĞà\nØ—D[”pÀÀ00€ã°Sqå Ó‡È¾6iIê	j{\0¶˜ÔøPKq¢\"É¯S‡Æº “t…;>Mp–ScğÉ¦Ğ–œx6„E‘£¹°4ïN'¨‡Î°±uò¥Í}\0Ë_eË×å²Hı!Ğ7B­¼ö\\k!^¢ Wò\rÖ±lq,¼ÔoXhª6¤“Úì=_cSØˆ#V\$o€’lQb¢Á!ğä‹¤¦À)µ§1\"™<Ö+VZ×I`K¸ND‹•_ô¯<e—Moaa9±Îæ3¤Àúì£e¤¬*ÔVb³ Ukc„;ØĞîj‚²b]’Vµn©»îƒ*ìí§¬JVHH bjdÖAV²¤#¾NB6@‰†n –•0àc—8dÕ•g«ØNÌäÌ‘÷@ÊĞ¾Ó®¬´ı‹\0Î•q9ZŠÂ¶¤gıEÛ6¡KUœJÕ¶Uµz mde;I®~Öãe©úTír–Ë]Ôèäàm~k…€­n[5÷Ei»íjq+ÎMYxÄ.ë€ùÕ\0lÃ¿D‚+bOÅ­“¨mèHP¹#_:1´˜\n,â(1rÛÎÆFçp¹²­ú*:W²×•0‘ËP|hhÔ9ààG°[KÎ5kL€–tyœÿBH¯´X´Õ¸.*YK‹¯±ùA_š7Jä¥Åa}t¸´2®D¿ÂYºtB¨³Õ€èÀPó¾Öc{®¹/êS„™–~F`-¼T#M¹¸¢¶±\n›°KEÉC¥ÓÛZ+÷\n¹©È&ªU—ş'Âƒí8ëwD¤…0F-qxº¤ŒÆ”Ğ€ìÅtÆxóœ·>¥CÓ“L^­èlaó˜9Ÿ\$3ºàE”˜;+ºİB’§%Ùt›ë­‹Ÿï[¯(ókÎLÑPŠJÛVé«ÌÕWóÉü±l5`iĞÃ¥¦óÕU”^â b2Çnó€eP½tåC	˜!¦õ¡½y%¯bµÓ^±UÂ>a¦‚PÚTVÊ¶I­±ˆÂ›U×„VÂø!4­˜kjÛã¥¤_ó‘»ÕâF;JßXW`iİ`+ä+½<XÂ/]=ÈÈa4Lßh‡×V²“!{:;P\"1Ğ«»]HG„hÖd€.¿ÈéRºhPîèÁçÅèÇˆ>\0 ›·¥ıiÒ›sÒİ`,ŸïoÖ…¦tk<=€ì`q3ğDKÅ*)ˆœH²ˆGG²­\"õ¦to™Àç£³à«1i-ÕØâÛ#]šF±uÁp£Œe—÷B¯(ÀZu@,!©jğOƒé\\Á*.Áş€ğE€giÁXè½„ü- «¸]ŒèÅƒÚ]Šo87æfùƒ³aRpF¶ØN­¼!7†æÜDóRøip¡x²€÷\nøh‘á½•ˆB¾×øC½y@Q„l\$a*K¸M¸XİZ:øO¢J¦ÀmïáÌğy«aË4 Q†°~÷îbTÏ@ÇE	ŠEáÎkxˆ¸ÑÒ	‹L+%G\n‚\0'©â®j2[â©¸C6\$„E«†t–K<ûŒí}ĞÀvş/O‹ğ–ãP«7>€V2Á‡ãõ¬	\"‰Wë[*Ù–¾DqÌmÊ‰*r¶½×K'EĞ“ˆÙ[A\0¥\0[ñ»ORdiœPäéXÙX h[g½¦4(:BÃgŒ8Á¸¶\\r y˜¯EËs_ã¤m°¶r#Ÿ\0Ãä8ä1„60È•ÎK–ãDwÂ*`ÅE”¨,aäú‹@Æ>2_´‰q~Á¢XÃ¸¦JWØ¤Kã<#>X@òv;œ“å^YBÉEÈ.]”\\ dÆçÓNÂRÍ¢Ÿq,Ääg_²b>²5eeÓØçÆˆÚ%-,n»«]\nx÷>€¿ïËt%Ë€´Ø1ÈF,›döß62Ãf-ıÕ›”âË•²gF,ıÓìBWA+€„ŠWm®\\œ\$äš¤º²Ém¤¤ÓkC5˜é@æ@â¬#uA.Ú‡6¶Ğ#¹‘8–dáI™\\Ğfˆ#9›8–ggmI„†oÆw!1Óšd—9Vfp\"=qå–éø¬	¦+ßI ‚„C-Òoó«.“7<¿60 'İÃ@émkzi iùŞ.bÛiLÁÓ:u_4r-­ØÜ“Q`,…\0\0C˜ÄSÉ½Õ©NiÙ™×´çpLvÎ @Eî+!g}¬ÏZ!³Ûù§ó€oXôê\nXlpŸòJ¡4\ról\rDÆÛ¼ø%-­ïÀÆ«0ÎÕr€yÅXİ\\ñg:ûà/;ıç¨ÒYì ®{€_œœŞ7H%ÑÆ‰B0Ş¨~Î<z,­€Ghy£wO¸™“óĞ=y´|‘=Y‰zA7>WÀÏÌ±CG=-2`sÔD&Cİ€&r3¤Ô¢3-&ù@k3Up^^Ï“\"±ÜS%YUO+µsKWXºc1e\"Ri¦å„³5Yö\$½T–Èe»•¯«No\0Ja+ÏVãÇæ€­uL›3aš\nmO½\rM§w:D DAÁ,8ò‹È~È€ã‡ \$\\Š\0œmy/V¥C‘-TEP\rÍ%+]L²ê@VJº™[’vÙ¨\0°3RQGñãß‰Òj7ûâ`%”{à9¸8ø\$´2[»ä#£T1ÀùÍJr)S’”ÏÕí%ÄÓTı\$4Õ÷\nVö—· æõ À8ïKzò¬÷ãÌ)´—¥›ZMs¡W\0ÃÅ 5Ù­şVëh£)«#Dµ«¯57¼\$'¾BY4î¬\r<Çú|LnŸ´á¨ÃhİyÔ:›¢]¼İúR€/gSY\nc&kÙQ°¤\$I	‘ì@E2€ÎŞ¼‚ã½¼À¥¡0B›Äô,ÙÔÏz{ıdìûNª=àbLÚ\0¯fÈ£E–«õN9\"­êĞµz¤Cf¬6±ª±Ê¥J¯ S)ÈÛZuè¥V°Ğ	”Ò‰Âó5Õ—­bÔ)t’Çó³˜Ø_á?Ï\"Z@ºÈMhÔ&Û¿xª*îùIŠlˆÜ\$êØ\$UmÄÍ)¡`dÅİK©£™K'šLÜá5rØ'ÖúYšäîÖNâ9–ÔxàR™.Ê|2SM”ÂNBÙG-·rjQ| 'İéÖ¹·ıÎNĞ;ÁÈİHCŒàZ|+FâdÔ/6ïCd¶n§‡Şp]wŠB\r)!ÃJ›–õB\rse§d#Š>Hšmà°¼WdŸßY–ñP½óº{ºa3\nÕc&q³•…¶yµstCæ£µDúşˆ>¦'snsy2¥óÁó·b·¶bõí£.{·ÒØB¸\n=éOÒ!;\\*Ì+¸ŞW›…ØX½D\\ÏÌ³X†ÀOh»F§ùéŠÂŞâ¾X»¶]Äá—»4.#M<†…µÎjµ’MtšS‹ìµÄ×&øGÍUP¸&tdãø­Bˆíñ0!{å>‰2ğ:°íQ¤Îı¨©af^’¼Bù¥`Ë“eF*0êZµ\\|Ù'»&hdÂ÷åøgx(;ñäìØG]½@\"Ğİ_¡%¸¯¶n,ïjPf´CrÀş0‘oŒiã--øÑK5a{9ÂÑÇ«ÕÜ’ÈñÂZ=\0M- £a~DMFæwé!ËÄSŒ¤øÅùkÉù1òˆ|¤Gš=_úì@¸[ãù¹ªA–Ë—V¬€HWĞ\\¸’eœt:×ã0yæ“Ê],MpÃ\\àï¶²¨Ö),\$w‚\$õÎÙÈój8ù%)Ûí-*Rq*ÅSHÅnıÔ2VjóU¤…h³ä[*¦¼õ8y£!©jw;åÄ´	şàâfÆ>‰]Í‡3Ä*¾}·¹¾\n«¯\\/¹\n;ó6m”@æ‰bö;–h¨†-È’ˆUµ:I„NP£ èóŒ}VºlnUæä¡%.¸9êB€š¤@Ip\0Š€\nK¦7\$å`EÀxD]Í\nF|›!nÇÔ„òßºó\\á«Ï¢È»y¤?›¥Ç,WÉÍ­´†ƒ©„:ãu\\qñ&4gœ5HÔå;‰Q‹Ç ¡\"Ë’sÖávhInfUå(¯VğÌV©¥#’±If;’:{Ö~ƒró­œ¨;Šæ—O1F×\$ƒ\\ÁC³“cV_ïÑï}ƒÈI9'¥V½O’lótn\nÀŞB“R·1ómÛ|ówK6OÆ Öé‘˜\0jô³Š†°O€=/iÈ>Fìv¨¯œnãè;_ÀØç¶|ùA9?+cÜ°™ ²¶#F\0P\nû|è•‚Óí!:š>/…N{¿Åª;¾0ñÁ%å>w@N`Ø¼ıä6Ú;àù \"/Xïg	D:ñg“iÆ##ıôËkKÂrRœ³MÉùàÁ:@˜¯\$Îw@©à\0šweüğ¸¬<İ•Ö·É{ğw…©P¿Ô2¹•™:<âª‡\$Ä&:~°ë¥½¶>ná¦DüwÀ83¤Ğ<ÓVñqËZæLdS˜Ğ\0,Íkƒ¶?A¿\rfÎ«=I\n£ÍhÁâ«<‡MÅI:E„ş0à@÷\rË2ØS°§œ+\"chI\0²d½ûì(\n[ÕÏ”:Û6„òKA(ü·„8ç#Ëóg™@ZCPáêô ë^UæàæCÑ mx{IBn6\n[DA33JLéN…IdÌÍdRìèĞqŠ2(û7ûuAòfp`zÓ×Åç@|¢İYêG‰âko­O‡ëŒŠyî>%Ô„H€÷åbùÕN¼‚Ğ“û0À2jĞnr©°9ÃÒj½§ÈĞ¨…½áç¯öëÀ¼€ğ]Ä£Íåïc¯ê‡š§ÕŞÕ6 Ô›sŸp\\œk\rğËjçõ'D*<éë>¡y †Áğ`‹pâåõrWô³\$¦!èlšGğ||(q•B¤R½ô0{Ÿü¾şâ¸½çğT*\0÷àÀ2ø×ÃGíO‡EI™)\ræ©^Eñopü¯å±ô°Œ…‘4•Š§®\"¬½Ë^P/\0»é^é©7ºÊh4²[.>.];î‹Q:şØ‚ß¤î²[8-Å+ë>óúç¶½F4ëûÅ6~Ó÷¡ô–8]_°ãßÂ\0002_JpšÛæKˆø—ÍPØ7ç£<XNJ”ûà·%õÅ£çt¤\nÆãğc§!PÀ¿Âİ÷ø×ú3ãíğú¸¿şÃù_üôL_?2EO4Ña~8Xÿø7ÎÿPÏT6z3ë 'üµÌüM)%ağ>•f|ú¶Ò'Q¹nF#ö_ëôè‘Oã¼}£Ö]Ø€q-è_HzHpåş€—èÃ…Çõô>ş\rÌp.Ë|ÄB@¥³öQûRåşŠ\0¬F‚äh\nıõJæŸı±ˆ 0\n’`ô´˜€¿şïK!«¿Üü‚„ˆ:ÿ‰)s'ôÿ³ÿü¿öÎø*çÍ;†íaóá^Š\"Û-bİ+²ôªfç¼ìÊN‰›Œn™ºd#Ñ\nõì~\0zÂSg ŸÈèU‚†p½Ü˜hGÒ@T`g,©HMÌMÄ\\‰x! 8\0’‹O'\$³¢Î\$œØØCA&s¨Ñ+?1šÁ'ª7²^˜Üëa®ú ƒ¬Å>À2‚N,€Õ%ë˜Ê´H³Ä+1	³2ìØiŸ Èúh@ñ\0¶³œMÌ³Çæœ½	ÉFE7ŒfmÁ´\rj\rÈàŠ\"aŒ‡«\$‹–€wƒó(-‡–YĞla²ƒöÈ`ZPêK!{†¤øùP BÉ”¡¿†¾ŞMí€†¸z0yà,çTPW4 zêPL>lü€Néğ\r4ÁBäãä@…Àœü\r€X@PT—¡Yd.A`³dÄ.Ajz{w‡©?m<+:•j‚´\r\"Ûü‚=¡š5K…¢íbÍÜe[ˆU(`P+¼p YéG,‹e²Š?Ô ”\rÆM7‚ÛØë\0 €");}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("v0œF£©ÌĞ==˜ÎFS	ĞÊ_6MÆ³˜èèr:™E‡CI´Êo:C„”Xc‚\ræØ„J(:=ŸE†¦a28¡xğ¸?Ä'ƒi°SANN‘ùğxs…NBáÌVl0›ŒçS	œËUl(D|Ò„çÊP¦À>šE†ã©¶yHchäÂ-3Eb“å ¸b½ßpEÁpÿ9.Š˜Ì~\n?Kb±iw|È`Ç÷d.¼x8EN¦ã!”Í2™‡3©ˆá\r‡ÑYÌèy6GFmY8o7\n\r³0²<d4˜E'¸\n#™\ròˆñ¸è.…C!Ä^tè(õÍbqHïÔ.…›¢sÿƒ2™N‚qÙ¤Ì9î‹¦÷À#{‡cëŞåµÁì3nÓ¸2»Ár¼:<ƒ+Ì9ˆCÈ¨®‰Ã\n<ô\r`Èö/bè\\š È!HØ2SÚ™F#8ĞˆÇIˆ78ÃK‘«*Úº!ÃÀèé‘ˆæ+¨¾:+¯›ù&2|¢:ã¢9ÊÁÚ:­ĞN§¶ãpA/#œÀ ˆ0Dá\\±'Ç1ØÓ‹ïª2a@¶¬+Jâ¼.£c,”ø£‚°1Œ¡@^.BàÜÑŒá`OK=`B‹ÎPè6’ Î>(ƒeK%! ^!Ï¬‰BÈáHS…s8^9Í3¤O1àÑ.Xj+†â¸îM	#+ÖF£:ˆ7SÚ\$0¾V(ÙFQÃ\r!Iƒä*¡X¶/ÌŠ˜¸ë•67=ÛªX3İ†Ø‡³ˆĞ^±ígf#WÕùg‹ğ¢8ß‹íhÆ7µ¡E©k\rÖÅ¹GÒ)íÏt…We4öVØ½Š…ó&7\0RôÈN!0Ü1Wİãy¢CPÊã!íåi|Àgn´Û.\rã0Ì9¿Aî‡İ¸¶…Û¶ø^×8vÁl\"bì|…yHYÈ2ê9˜0Òß…š.ı:yê¬áÚ6:²Ø¿·nû\0Qµ7áøbkü<\0òéæ¹¸è-îBè{³Á;Öù¤òã W³Ê Ï&Á/nå¥wíî2A×µ„‡˜ö¥AÁ0yu)¦­¬kLÆ¹tkÛ\0ø;Éd…=%m.ö×Åc5¨fì’ï¸*×@4‡İ Ò…¼cÿÆ¸Ü†|\"ë§³òh¸\\Úf¸PƒNÁğqû—ÈÁsŸfÎ~PˆÊpHp\n~ˆ«>T_³ÒQOQÏ\$ĞVßŞSpn1¤Êšœ }=©‚LëüJeuc¤ˆ©ˆØaA|;†È“Nšó-ºôZÑ@R¦§Í³‘ Î	Áú.¬¤2†Ğêè…ª`REŠéí^iP1&œ¸Şˆ(Š²\$ĞCÍY­5á¸Øƒø·axh@ÑÃ=Æ²â +>`€ş×¢Ğœ¯\r!˜b´“ğr€ö2pø(=¡İœø!˜es¯X4GòHhc íM‘S.—Ğ|YjHƒğzBàSVÀ 0æjä\nf\rà‚åÍÁD‘o”ğ%ø˜\\1ÿ“ÒMI`(Ò:“! -ƒ3=0äÔÍ è¬Sø¼ÓgW…e5¥ğzœ(h©ÖdårœÓ«„KiÊ@Y.¥áŒÈ\$@šsÑ±EI&çÃDf…SR}±ÅrÚ½?x\"¢@ng¬÷À™PI\\U‚€<ô5X\"E0‰—t8†Yé=‚`=£”>“Qñ4B’k ¬¸+p`ş(8/N´qSKõr¯ƒëÿiîO*[JœùRJY±&uÄÊ¢7¡¤‚³úØ#Ô>‰ÂÓXÃ»ë?AP‘òCDÁD…ò\$‚Ù’ÁõY¬´<éÕãµX[½d«d„å:¥ìa\$‚‹ˆ†¸Î üŠWç¨/É‚è¶!+eYIw=9ŒÂÍiÙ;q\r\nÿØ1è³•xÚ0]Q©<÷zI9~Wåı9RDŠKI6ƒÛL…íŞCˆz\"0NWŒWzH4½ x›gû×ª¯x&ÚF¿aÓƒ†è\\éxƒà=Ó^Ô“´şKH‘x‡¨Ù“0èEÃÒ‚Éšã§Xµk,ñ¼R‰ ~	àñÌ›ó—Nyº›Szú¨”6\0D	æ¡ìğØ†hs|.õò=I‚x}/ÂuNçƒü'‚[ñ¸R¼´` Nİ95\0°ÊCºÔÎù‘XøÙ’¡6w1P¥©‰u†L\0V«¨Ê²OÄ9[¼–O>®PK’tÃˆu\rá|ê–Ì®R²ğpO¡ÅU¬ÆDrfœ9æLÃcSvnÌËQoŞÍÃ@oÍå(‰œŞ°Ã pÍòa*Õ^¬O>OÉ¹<ù” e”ÏşŒ‡™“\"ÓÙ“±ÎP>™“H^ô²”	psTO\rá0dò{æZ\$	2Ÿ,7«C¨Ó!u Ì}B­^ŸÔÚÉ?ëDÀ“ÚƒFºİ±¬ˆúñH¹Î™`ÄéÜ'¢@JÚ¹3ˆĞ|OëÜ¹›BÎMbùf1ênŠƒ@“1¡èĞ(Õ²ìƒÌà!İoowıç»fë¦õ)I‚L\\[÷İÊØ‡8[1)Š!)¶Ò u¸~Ácõ-–6-¸†îy*	•‚“>\"m„61ğÂÓ•ä.â»~Î*¦x»Ûè«qÀåÇšG |‹’rløƒO*%À÷ˆ¨İ…¿A‹bRAxÚgæDŒfèV\\ÆİR5lú¦Ş¤`ë¬ó5`øØwã¼|ïÀ¿Sgç€ï’´O˜—B;¨Ï®^LÃ–ÑæW?‰5 ¼»ac}ªïsŞİ˜IÛîA¯¢rÎûİºO0ï;w¯xş—àP(ÏbÂmL'~Ìwh\0c×Â¨pE¼ß²:Cá{g&Ü¾/Æ‘>[İúïìÛœ)	a}nÍ¡³ÚwNË¼xï]V^ye&@A	P\"… øÂE?P>@¤Â€|û!8 „ĞŠ˜H	á\\·`¦Â@E	¡Ã‚õ4Ë\0Dûa!¦£ØëîÂìnrì¯œ\\¬†í8ço`í«Høfîø¯èÎ&é”ÔÌ’<ïrù°(jNÎeNÒ)6EOå4í.¸òn0Ü÷ÈõïÍ6\rŒ– °\$öĞîå\$“ª ÈN¤<íô|Î±Nö—jìOY\0°Rùn´Ü`äoƒìÈmkHáî†øĞ*ù-Ï˜æw	Oz‘NZ*Ê›n×O‡\nĞ#çnêâ“p[P_bäĞÖÑĞÜñ´jP¸òPœëĞ“\0}\n/á¬ØÓşö ÖöğçĞŸ	o}¦ÂS'ø‡`b¾éÆÄ\nPdÍp ?Po0sq\nï:b°LîÒõUu\r.L`ÜÑSP»°‘1mqì¥ñò~ò‘]%&Êš§QˆÀÌ ê\r‚DåpqğåpV|ÄŠfÖ8\$Âpæ&€Ò×‚ÕF‘Î&±Ô ºmOèwÀæˆG	±™1/elÖ€ú»D\0Ù`~¦ì`KâÃÂ\\Øb&ùQúQµ`Ê¾àA¬ ÀàVEW†n: Ø“BÆŒ²\rò*ƒ‚l\0NÀñDïrë­¦©±[&Gª hšrªH4A'ÃbP>¢VÆ±âÕM~©R„%2ŠÂrmÜó«\$Ù\0ä˜Ç2²c„´©ÄMhÊ‡vcÑŠ}cjgàs%l½DÈºˆ2»DÎ+òA²9#Â‹\$\0Ç\$RHÄl°Ë@Q!’˜%’œÕ\$R©FVÀNy+F\n ¤	 †%fzŒƒ½*’Ö¿ÄØMÉ¾ÀR†%@Ú6\"ìTN’ kÖƒ~@êF@ÈãLQBvÆã’ßâ6OD^hhm|6£n¼êL7`zrÖœZ@Ö€@Ü‡3h˜Â\$åÄ@Ñ«€Êà³t7zIààœ P\rkf DÀ\"àb`ÖE@æ\$\0äRZ1ƒ&‚\"~0€¸`ø£\nbşGÌ)	c>€[>Î®e\"å6¨ÙN4“@d¹‡Ğn“—9«î¬ó€É´D4&2€Ò\"/ãĞ|ó§7ƒu:Ó±;T3 ´Ô“i<TO`ÜZ€Ê÷«™BòØƒ§9ñ0‘S>Qhõr\0A2á8\0W!ët‚ÙtwH€OAÈ¦\0eI”‡FæÁJTõ4xì…sAÌAGÓJ2ƒi%:â=ĞÅ#Ø^ úÃgÙ7cr7sÀÅç%Ms©D vÃsZ5\rbßç\$­@¤ Î£PÀÔ\râ\$=Ğ%4‡änX\\XdúÓ,lØÌpOàæxë9b”m\" &‰€g4íOÓ\\½(àµ”î5&rs† Mÿ8ÌÓâ.I‹Y5U5•IP3dÃb/Màó\0ú¨3 y§^u^\"UbIõgTí?U4óN h`’5…t•‚›\r2}5-2¶’ğªçW€å(ôf7@¶Ãeµ/ô\rJ‹Kd7Õ- Sli3qU•Š¸€zÛ\0Ò)õ\$Úcú¹oF?@]LJb›DÒ¿ó0œœs?[gÊœê£%¤¦\rj“UnÉäÁ^©±R5,ÖªŞt‰FE\"­àxzm¦­\n`×-W#S(él	p²Ô%CU¶¦è¾š¥Fê&T|jb Z¢¦ƒê8	€Ê/4Lš*nÉ¦yBé:(í8Œ^9Ó8Uî KŠ¦ü{`Zç¦\nFŞ\0Cl\r÷'(`mÿeRÌ6ÔçMÓ€ÖB‘ÔÕCôÚÖ6ŞßvßçÀûn%#nvÙDÜÖjGo,^:`Û`s±l\rÁ_ê¬®×X5CoV- İ8RZ€@yÈÃ13q GSBt¢vÒÑ¢tÒöš#–ŸbB¡æèßãÁ]€ä#Îp±îæfZC Ä²©”áOZ€ğ÷Níà¿]‘°Ñò™slÈÔ‚‰°EL,+Qê@Yw·~9¶I\"Ö8!Õ´V5¹&r½\\ª7úÏWØ&—Ü¼ì¸[\r\ri\r­×~L|—Ødİ—äÜ·ë,—Ã|i€Ş@,\0Ô\"gâ¤\$Bã~ÒÖ!)5v0ÃV Ãü£b|M\$·÷åÑò¾øDèf\rì—8;€Ñï}f¾ïfšŒ¤àŒicÔ„V0,Fx\rRõĞ`‡a&nÈ§‹QB.# Y·È>wŞg¨‡òîE²ò[öÆ—àX”Ê™~RO‰ëY]8¥]rK}¢-‹ò?Œ8©v’LË@¿~ÖA*„¨f˜–øJÊMáƒñt×’¼àà-v…[#çxL'Lû>ølÏ8óPg\nÌÏ\r‘Q‘±ìÑ±\r˜M’ğ\":xw‚Õûƒ\$bÍÓ-øÅÎãìï=¸kRXoQä¹‡9;‡«Ëˆé¡sÕƒìÍ‹¹)¬Í~ÙgeBÍBtÍï‡™ªÍ,¿’íÎ,ÊÊìèKÀÊÏyèÿÍ-,mÓ€­“˜+‡¸07yCƒ€ËƒÙIzÅÆ™Y‡¹^GGW‡¸uœv0#kX‡£RJ\$JP+Ó6x‘‹1ï˜8œŒËYÌgŸ…¦{Œ­?¡\0ç¡XÖ\rî	XF‘—WÌ×”œV/“ùÍƒdIg9ß†ÕÑ–é–yï‡Â1–ú-G X™‰Ù‹@O‹ R¢yÁ‘Ïì‚!ëGuYí¤5”ZF\r¥ã•µ-\$ôO™e¨u-–ºZFøã—Zd·úi9+¦ìµ˜`M§z¶ñ\rÊÒ«I£Øy‚ùA¤VpĞ:“OJÿ¥: V:¤#:©—:cªù{«ºk l¯˜Zs«øW—Œ®ÏP0ƒ°ŒÄ#ú9g@McÏzw¯Û“[9Uì\\k‹‚÷ Û6º¤9Ó…› €Ê°y×,÷®çŞ€f6n-Zu´Äÿf÷Ù‹»c´,Ÿ¶—å˜[o[g‹d¸ ˆ:w#¬É!W\\@Ìn›` ß±º\r¡àÉ¡\$ÛŸ¸±¹¹…º\$Ÿ¢%¡€ß¡Û·ºz#ºº\$øimYõ¼cñŸÉ‚ék›I_·ÿ…Øı€y¿¶L˜˜ÀÏ¹•\$—`VİØ[‹š»»FŠ2C8Ç\$û¯¿ª©À»Ø¼ÓøÁÀòG[½¼˜ÑÂ¼˜Ü=ºU¨™Ï…[q‘×ØÊKïûŒ’YØÜàºİ‹úQ©ú?ª8¡“‚aX‰Ÿùm*Gˆ‡¢µ\\¤Ô?ûUÈ\0Ï¢ºï¢ûKÄ¤ş¢|CRÚÍ“Õ-œº‹|Éœa¤Üe®RYëÆºé¥˜Ü’¬†øÏÂ˜«ˆ¼³•ÑèPJE Î=ˆ uŸç¡öÅ\$¹{å¾8›Xî•ú{§úÈšÅİ˜ÍÌÙ“ÑÙ—š¬Õ™ÌÌ\r¹ Íù¦ÎÌÍ°Ù¬&¹±£YµÒ¹¸(Ù¼ÉM2)œV u7\0S Z_Ìüo]\\Ä|Ù©Ec7æÿS¼åÎ„[ÎÜğ<ôŞ<øüı„;çĞ-Ñi§Ù ´}™­Ñl¿˜ı!š,Å}%™½Êı-Û¬ÅÓ=·²¬óÓ¬û›=“ÔY»8³ÕæPV|Ò×øzE.Ùİıé²\rÜà¶ÔßìbLfÆ¸²Çh*;ö	Ö·¨;ùØ‡ÀQ{9\n_b\$5·‹l„UzXn—z\0xb€kîM	¹2ìœ Z\rìÅcÃ|××’/åİ}%Š›`ÜNŒA‹\0Û*=`ŞF›Öœ^Q3ŞWÍXÕ×<ãåıtR>rà`uégÌ§>iæÒzNØÍÀØ§ÃiåÜçé\$\0r¼‰şsëô–š^Cæ–ä¶è>Uê¼5è•Ûë^aç)ì	¥æJ+>¥uB®•@?íJÔ-Hµ’OJ'ì-TÊ€¼TİÙoUh×F†„{÷ğÔJ[’ÜN—ñV‘oJ&SåB\"I^5½I‚2ÉğÂ™òT«´¥é¾½Ğ]\0·¾\rkæL%ç}ˆtÓÛ·~I0õH|PkûL5ğ_TÃ<ÃwÙ÷=<ÿx\"esaKø\"ºššJH³+‡UÕaïâ'Yõ~¹—7û)WÑç<6=_–N¿hŠ?6Ü˜ıäyó,›üÃçaŸÒàwö\rÄ°×#-V@Úk‡ğ?iüb*%¸Şºõõp?ü¹ÿyĞ€Î†ùp®¾-pïë|ènÀøúCaôf§8Aà8†+#\ré°RŠ@nÿƒ¡–pæÿmĞ~Ûˆ{`®H?ívò*%§Ç¼‘v%ó€ ñGş`¶`ÍZàà.”ª­,‚6øz”¶U8—Ì|ïyÓÙV§Ÿ¼å/¿p“š^®ò×¤½mèï]zcÓìõµ\$IB0é|ÓÔşÃ@¿¹ñpRü\nÂjğ9 ÑüG·7ùı ì¤#pß­Â?±ÒÍË'ÌÁĞ=ê6HÅlÏˆ.½YŒOYƒÜ_V÷G¯²°O]I«ÏÕƒ=ª‚x‚ô\$Å÷®=È|Ïª{›Ô\nôÒ<;{:f^L'S¬A1%é8*¿^·¥p75±†—WÀÛ\nÜğ\0¸æ¸SâŸ•\02\nX(âu[»…rp€şBñ0Ú­˜xªô:n	‰ZI3¼C³€ˆà{†[†Õ&C(@}‡r¡à w2²é—Œ—nt•˜¬¨{Cñ±É†Y!\0ÊHe>©ıP\"›9t5œo´ŸÚ!€\$@\\7SS\rõ–C† Pã„„@¶Iÿƒ‡nhGƒöøÇ	IäSƒ`x’7Û0b+v5œ^gè‰r%b¹pU”Œ%)<+ŠS/Z@ 4!¢ûj¡Û8•À\0­vN-6a[>¹X¢,æe\ned/PX¥`¬}kOR‘N¼ô®+€1O\$¤Ï€ÓF6B-‰:wÚ¨ÃN²ÓT«D>ªÓx¢¶‰¥ÈY)ŒÃnş1‘&ğ7¡Á}è«&xZö\nŞ–°öÅ¸ªúWã”Û:U@ÁÅaàâºƒ@Ø.åRÅhbcT\"¡ƒ’“Õx\nÅ Eæãñˆ|ßˆğ\rÀ-\0 À\"QAàIhÓ\0´	 F‘ùP\0MHŞFøSBØ@œ\0*Æê9½üs\0Ï0'	@Et€O˜êÆÈ Cx@\"G†81ä`Ï¾P(G=1Ë\0„ğ\"f>Qê¸@¨`'>;ÑèälÀ¨ˆıÇÒ82>ÔzI© IGô\n€RH	ÀÄc\"“\0¶;1Ûìnã¨)¤¼„8 B`À†°(V@QÇ8c\"2€´€E4r\0œ9¼\r‚Ô‘ÿ€‹ \0'GzHºÒ5E!#ÓÿÉ\rAòJĞ‰Jÿ(çÇFCÑÊ&¨dŸ IÈ\"I²Vì†£µÈïG€SAXÿÀZ~`'UAçà @èßÈì+A­\n„p£‰i%ÇüÑ¿ˆG€Z`\$ÈĞ‡ÿ’À¤Ù>~?ÀEÀ\0‚} ¨<QÑ¤›å'èáÉêE’w“Ø¦¤û#\rÉ‚7rQ’ }Ë'iMIæOš0dm%  ùHÊ°\"-h#œ¹XFüM’„t\$Ã!ğÊìœR¡ìtä©,(ÿH8ò8!Jë5IxÇÕr\nåThÚ“~Pe@&eg\"[hØ–ù¿4³¡ÏË|„2ÏzÃDËÄlw#9	v{lb•Û/~\0ˆ© &I8%Õ,èIKAÂê\0—Œµ‹©/GYKµ*„>—ÑÀO/°€Ì2ÿtÀeÚ¾ÙªP93=\$ÂXád“Ì-È&ü˜|¶æ#154LUÇË‹G.ùiÌ2`Ä–Áó€M.B¦€ñ\00036—ISJ£-™~€ì©¦˜jF\\3	o4€u	(@a3¸A\0˜cµ¨`ÅP( Ğü0\$´š»\\}/d˜ê™˜\0§-€3È%b0\ncÆz`˜))%*´Ê6\"À€ÙÉÙ–ì×E4˜…F·q¼’ÑJœÇºädóÍ(åÓ€üà›×1™iLmµ2òœAó€¶À.)&q@\$œ`L§ÏÖ2Lrseœ¬ §.Àvss\ròĞÔİiÑKQÈó¤™¬ §0()Ì|ÅMbštU9!ÁED	Ñ(	Ã`8*pa<œÁı€¾80ËêsÍ\r NÆ©·8O0“Î„ÃÂd0°»OVxÁ@'Ÿ<ÀOlóßJ)ê	ğ~}ÌØ\0U=ÉôO¨'Å‡dô~\0™Of¯ïŸXÛHÈ	óL”ĞÒ (]'Ã@’EP‘LWÜöE'=¹ó\0À'‰\nŸÁN¨\$iIáĞZy´	à¥õ>ièOH6f”­' ßxà.\"}@‚Ğ-‚wa2vÓ…áÙA¢L>… ùš<0/Ñ„Ô¡P…½Bà‚€©Í¢€çT¬ŒÖ\n«„è¹<sSQ~|ƒÓ‚€P fÌi¹O€Ï†Ölqÿ„œ9T\r€ÚÔÿÑÑ•gÃ„ÑÀÉFÓ§‹%O¡(1¤hâº¶nÌm£vÑ;Æ|¦Ë”gËğ‚SaF°ÖRáÈ¤™NrÉÚ9z‘%&¤XÛË\0007\"æ2tÊ-\rh%fÅ¦Ö½õËâ3!İ\"(ê7I\$s/ ë-„7*J\rÎ•CÓLxwˆˆÙÖ—²é“´µ£(Òª¬B,+àh\nû¥Üf\r›FÊ7Rfô*™:ã\"œÎ”4t¤PæiÈXÒõ¥¬¸*â\0P.(#Ú+H¦oJAG¤ëqñ’.57˜+N	:-m`‡£õ&©µHJO°Uviª¨\0 \nGN:gR”nôç2ié)}#¥Â	Fé§©Ê>dÚ`ÙqêÁ¤½€ŸHÀóÑÆ•e©5J);HQ¿°”ø\nHÏ“GRW‰Ô”Õ/¨Jjª)K*UR°´®iéb8za.˜¡ªô³RGÌÌ!4Í£¨©@9ššÓäc: E.F|ÒÛT*˜”s¦<Z]_OÊi€òŸÕ§\r@£2øqTlVUkCQ\rOe™\"ª\n¦.ÕTéEUZ«Ô @iªå^ÈÜª½”L¸µaMUBêëVÖÉŠ¯´¶'U˜+Q ıV«¯W˜m°GºœÔº›u0« *íPúT+€!u\\ÙkVy@Æ¤”j+ÉÜH©äÜ\"E˜”P·¿,Ú`<ˆHØÿÕ”’p•ÄŸ%	l\nÚK ÁŠ¾\0®\$T!8@¨@º2’ôÀ¿h²Ş4LµæÅ+ÄÔ&°Èò,ñ|Ï±\"ËTÄÑQéœ‹‰b#w)umÅµ[Ş’õô)E}…Ÿ[Š•’Exdó)pƒ…»	nÀ¶-AK¬¨1}W\\IUÙnF^®\n•«` \$ƒ©m)«oZ˜’	PDÆP VŒ D ór%ÀR)ï„ÅbÒ±Òlö^Èwë)JB·€¹-KøD.1ÿ8Øì¬ø£\0Úğ;” le‹,L(\"m¡N\nò ¬Z“êK›ü®áÉgHÁƒ§eĞï\0öÌ\0t7]ŠÀKk\$‡yN¥¬²ÑX\0İ6ã(YŒÆÿ³‚¸ÿf›\\\r€K1y¼,å`0Ùğqo›³¶\0éh\$åÌ\n¥_¥œ¬èdR€åzEÄšŸCµhÛ<YÌÅöp!Ø\0ro;‘Ñå™í¤Š'g'*Ú!½¢Y´Xvà%›K4R–V°\r¬’‚ZÁ}Z´\rô€oúÉmpN]NõÕ5®­xUay‘„\rëjµÉWÕÏkûb•~¾+m„îËedyÙ¯Ê°Z«ksOÛ4;T€Èáaÿl@4[«ë]¶M£7n 7Û>Ü6À¶Ï“äÀ=‹hÁ*˜0HÎ«j\$§Û[`«öÚ,»íè«y	>Ş¼7púìD\$¸Àu9ãH ;ÿîÁ¡ÒöÅR¯Ø~®0[D˜ÃH•ì‚•6öÜ>-LxjëZ›kNÈ¢²¥’—nŞÛÜdgÄ;éC\\\nİPb[¤h)3M“c’D4¶0uRê#bP¯Ø5•:éaÅæEqH: öº•:é.X›‘?õc÷9¹%nÈK¢ÜøŒa±5­ƒJ‘`À7X¥\nà»q=È¿vr—EÖ<¹(~¬€”CÈ·PQxH½bK˜Üªæ–-]°ûÃÚ\"ë£QşéCºUá.a»§QÖév&¼¹ ®¨7 ]Ä¨åª»©>œ.9\0ù=K=)˜×ÀT³Ò ¤Ã_OXğı5ñ!‹b¦U«­háëóAPÖ-ğô—¼\rƒÉ%zPŞ”ß€<§x©âàÄğc7·|Ô4q˜€¢õ€p€C<•Nö½ÃYè5ÑŒ’°)×æ¾ˆÆ¿}AN_ŒRCTxÌFÀ*à3´ğg¶­À.•`*±ÓB¨š`&œTÒ:**¿7Æ·E°W R«\\×cãWĞî°[­Ş×Kb°¼\r¦oàHr¶¸ííu 2~/Õ­Á	@ßÀaIÁ ,%b †\0ºÂ¡+{î[–,`_6º7²µ.Ö@Ì†°)?Ámºmêb«a\nëvŒ¸šÔù„Û]`Œ’WÁ8ôğ!…ºëW`»Ø:ÀFpo-`7	ğ\re˜ƒXXzK„I:áç™bDï_ª5Ü>´†³Å—ìñf+<Y•âvg³¸,ä%à©H\\  d\$@•Ïqë\nêà±A \n˜ğ6–8F€'|ñI€èRäÄáT“{sÖm3ˆ€8b)à	@éŠÀôLc†M³’øF@×#Y`Â–­İNºÙÎDXˆíCxzYcÏ0y´Ÿ­3hDZÓ6\"¹t\\7”SE;ÊÀ„U#ÒR^Œ…Ş©‰s\0Cfb°ÜšğÉrrI\"Y¡	–tÃ¥¹8ZB/.¬`ÀEÉKì|’ø£öbÛÉ\n|_}òÀKC¤.ğ“Ì pÀ1:¹À#Y\nTC	%,,‚ô\r#¹@Ğ+œŠdqÅö\$”“„{’D	\\J\0ñ’«‡-`m!ğ|Àg’œdzÇVI¤vv&……AÉ`££MH\\IµÀ©ˆš|E›¸ÒùÊjÈB0ÛŠ@Ñ¡nUØÿKüàŞŒ¹Ã>–«–Ü]İ¸³hìßi X9uprù€ÀäaÈ\$7Évó˜Q¡™CAÌ>1˜ì²é¬xifìRÜÔ7*¸;8%ØŞÛ\"¤‚É„š¹ãw…PéâTB¹ªÀyH‰š'\næ”bØ¸¢°²v°ïT5xcH\$ƒ\\¢êÛı½¹¬Xl“Kııa¯`Àğú#tŒEwÀghµ1›À …zğí pó’‡Ğå4:¼\nÀCæ¢Ò2ğô„‘HèK<X	(!JŸ¾Â•;ùã¨ÇÙó,õÂuŠ3šy€sŸM€C9p€ªwz\0Ø€Õ 9ôìşÇˆ™xÔÇƒŸŠ1¨ëB–Ÿ§’à©èŸÙŠôĞ`r„)=hLÆ‚`¤çÓ?z9ÛEö?µ³ëJ°ºè1ÙÁ½÷ÀQ£²Rœ<\rÁL\n8(# Ãrèôóp>¢ŸL²Q¬™ ø¤À|¹  \"4(†µ*¢äÒ8ÀfpiWaQ\n˜QïŞ*‹œÀ\\0@HÀ;…VŠôYÏÎ†šÓüşOZxê›<F€…' Ií§¯A\n<ã¡]dP»è_NšT!\rË§éó@*~Ğ† B…¨=º%Úz †÷Ÿ­;ê³:ÓŞôAB}Ññ&Ñlú´cªİhØ`TÑÓOç˜))ó\0Êy‡œÓIßôÛ¦ı8ÏNyğÔÑ˜‹G©\r\0³Tú\"hn‰5W@}€¼şÓàÕ†Bë£}ZkVóÎÕĞ¤õy=sé³	zÀÓ”úõ©ı;\rìŒšÈÕ,òhTÉói|jza&˜Ö€\$°iŸSÂ°×HiµÙ>IìB{Z*UÀÓ˜¡Iænƒ³ÛOÒ}§ïXMsëóQ»Ø8µI°ÿĞŠƒ	Öv&! „k¿@ºæÖ#€å<‹çÛTÆZ¡.³¿¬ãj³Z:Ó	^µBç­}Yèäñ–ÂvıO3BTC¢¶É6=ª½kÿeSÉÖ~‰ÂÁ?]ij¿OúÑ¦ƒ„Àm,\0}Ø!¿õ´mF!¸[JŸ.²Âg¬İUlÇZPÙ¦Œ§ë«œO[;&Ùè¤ö¢] ŠOht	`aILA­°k£bkiÙNÙvY¢³ì¿m:êÚvŠv¥¶ıkÛg7ú )€¶ >Ğïb&°Øçp\0¼5¶I‘ÙêÀ]dp=È+:;ÈÄ)º íÌàDx@^oãĞÑ¸Aü¢ÔLö'¨öµ†§t wŞ&U€gºÀ3€¾B`/Á=ÀÇí'd> /“dbFá\0·w\0yİäñ÷9Ÿ´ànƒZ[£ş6TuºÕb´Z›Éİ~öã~¦„\nzd'âŸ@ÌRa\n\n@àG‘İ™0ı;vS½Öü¿={€¿~Ûø\0@_c0‡ovà1ß~üxÀş»›úà€†e»\0p…oÜà>ı83¿|ÜppÓ<÷IloË„ÜáO¸;Á ‹ç…Ç%8Gx.>ğoÀO=^uLGÀ÷\rğN7û´İ¶q8~&n»5ÂâlåÅ]Ú€¼Šë‰âÆí±üI..€¿4à¤“_Û¼=âßxûëŞP·™¼íè—IûÓá÷ø5Ã]ıïô[\0_à\0Íƒ»  ìœ<:â¸eÀoçÛüÚøûÀ òB€yä/¸íÂEqç‘»ÿà÷f'şJğ—‘wäÏ#7ıÇÎN›xàF˜×(yÁDğ7“\\§ä'€œY2Ë•Ü?ß¯)9eÊnGr§vQ	†.ù/Ä.YóÜª›<ì§zkMŞ ÓcáÛMşğB+­\"Û¼\ràgÈlø\0^\0˜øB@-	T€Ö6¾1§œûÊ\nîûïÆP@ \"\"ø@ë“™Fÿ™û0çtğ°æ°ÁUÒ\04€Í!Š»_|ıÖ(B\0Oc<ˆœ'‚¡¾ôt \"m)éTWÈÒÇF “¸P?f9û¹šCàÆM¦mk´³÷DşÀ¤Ş |ˆÃ	¡&ôÀ3ç`€dÎ„“¨\0O8ºyÔ@’œ\n\0I?@@¤@Ã/©½Oéù\n›â0¶ø<d\r\nË\0³ÓñHÉÙC>âk€ù”nm_:Gb§\$À\0ùÑ’ôà|±(v…I6¢0­\"KB‰ …JÂrK`|6ƒƒÕÜF³T»' 9Y9>r°@yìü@†%şÊ„–¶dä7<±Š\$p>tà\r\0|å¸yrÎÍó³Àk9+ùˆƒô6¤ŠÓ#òû\"97ö NåÚ®€ÎÅÍªùÀEnp{s^ã_;–\"îIú\0óJ <w6¨€eîjc%ØËç8è5½Ö€»û¶˜ÜL&F{ù2/w;ÉİôÇ&CDÁŞë+p€ø%#ôÈBYo:d4€#Hì!°A¤,İƒ\nsÎ±­8#=gìjl:èšU¡“B¥YX\0øeÕ¿tmd•(v†´î²§@k\\9vQ2úÎ-{&/Â¶Aï˜É<%N„…ğ`–EKJÿÙPÕº,s&˜ùß8+-–1ÿT@Wˆş8ìl…ÀÉÙD½‹x76@³\$øvÓ\"ø©€tîŸXÀßÎvj‘‹@t¶Húç'Ey@5°Ùƒ<ÉÖğ{½v¤OY{LW›Èr:ğ(µ,Ì—½ä˜\nñ+Â:(ï5ä¤¢˜ƒı’02ˆ%ŞDëQ¨BØÎ{¾x-(¨*à~.ùÙ˜‰CôJô\n—°Áô·”SÏ‡«Ñ#K²»|ä†®ØÂÉ¨2C@‰™aƒBœøïbCqèì¬yñL’7öK»‰4†ÈİO©ãfQ=Š'ûšû<!Ù™òfP+‹`Å×ÎgNDÿÖU˜šÒ¡½„!Ğ\$ú\$·Â-Ù/üö3ÒAz_¿@d~Q3ÊÍ'È>ã\nÈ\0Š11Ñ>ù÷ØJñ5Àı˜TŸ÷àk8;ï€Œûëá d¶Y«Ğ^ƒéÆ¥ç¯­\0ìªÓ‡ÕÕÛ(ÿ«²Fì™•ÿäÃ`k¼šƒQå+öI}Zçg0>¢0MW{ız_BkĞŸ;`©(ƒ¶-wJÅe&Ø¤;¸FA%L\r?!÷ŠÌ‹ô“\"ùV¨_ôƒ5G3ò‘ğs?-eØªQÏ,ÜYs?24æ~l\$ß±eØ¤Ş·óG\rérH†¿­ËáA~¥õO¡,şG@lû¿dÏ²Y“ğàlûbĞ‚ø?·ö#İÂ:ÜSß’àÊkünŸ±Ã¼Á,Õ3Jy’\rgÏfÏ€‰—äØÅõv‰½/4İ’kóÖdÎğA}ÊOY|t¤¿·ıÏÔÊKìAş÷ãŞ—şÇ?|ê†ÿÜŞ-Ù½İî¿æ&ŞÄW`ÿ…û¿êÿ_ù\0SóÀ›µñÿêşš´ß\"Äîos~¾÷ğGêÀr\$ÀDr¡à{#Ù'ûÿğ²EÍ½gûÿî/ş?öı×ò<¢ÿÓş¯ñ?èÎ:ÏĞ0ö'›ÀãºÀüZnÀ7¼ºˆ9h@ı?æ»ûïb@(ş3øo(À.ÿ»ÿÀÖÀ,ô«Şo>Ä{ÒçI\"ÄĞ³ä‘‚\"ı`9Ú‰^ËØÅë-ÀF7ø’%ÿÀhËÒ°¬*Ö¬»@|	\0iıÀ‡Ï@ä@~Cş°\0İX°ƒ­X\r,¨´´3¤Ô\0ÄøãïZT ä®€Ø6°.<;…C;2bğš\0èÉÙK=1š¤#í!±º³ 5ª:T³\nê™ªMtáµ€¨i¡l@ì»½à9¦ØëSb“@ÚÏ(¤Ğ81 ³èiAÒ ¯@Ú\r¸+Ğ8âøK¿B6–~È\rĞ8-R³ëL\n´*Æ`6ó1wĞBğ[¾OÙ»Ş:ĞãÁtï“Ê Aà\n©@±J\"û‰µA8kĞl[¼ÅÁªØ´²Co¥‚<_ã#AF€éXnğl’ë(„ÊWÁÀ,Ãê®ˆZ6¨¦¬È­Xn\0ÂœãûáJ3›PuŠÀõ° è>>°d!=VÈ{KGeşcFé¾ª‚ÉŒ°¼úªm/’‚0¼L‚ì¢XOi*†ÒË»¢\0B/“3zŸËá(ÿÁë°ãÀ©½}á0’Á¾ÿ+I¯BPp\nB°´ÇÓ×©†ôIuiµ,˜)0•à…%f	S»°h‘°àƒöÏœ{ãìÖ:ÎP #Ï_Âğ•á'T÷Ôk2h³ µÈ¾ŒáãĞiÂ¸B‰Ëçº\r¨ 0kªÎOn#>Äl–	é\n»ìB’€å\nÔ€2€°¼úÌº€³ÃîÎöVOiĞ°‚ØYÀ€büsÚî­‚\0“ñ­ø„düIÅ¿	„1À6B´[Ç,\\¦íÏ+2‚û(&¥˜\0ÙÓî\0•\r«ñp°‰^üZ)@<ALüzÉÖÁU\r€\r×ÂtdHºô\rl0DÃV1È °Á9Êd0Ltø¯Ìê€Äı@[À5¿P	P/‚¦+º‹<BzõznÂ“;Üf  \"½\nÌùxgÆj–šÎ`T€2ê4åÏƒXğ @;±ıá7åı»¼\"’€È›9hÛ®ş²>c<Á®ÿØC®·¿êù”-a\nD\np„‘9¶bZ¼¦Õ”k ı˜•ğ*2¡BÊ¡Œôü\\1ÁÄüXC¯Ğ'İêÉ¹äóìDÆD6Ô; 9;Ü+È®`ºôÊƒæJ¥ ‡C·©÷íè\0002şøœo€¨ûPH›>¼\rc×`2A•¤Îá@F÷œ`Û‚%\$‘\"D8èâäñ+AÒ\\`Õ½æÓy &74¢Àú†´xÂú\0Âºt©©Ñ¢p¼Ê i¡ÎZHe¡HRœªğ‡‡D#LZæ…Èp)»ı¸‘Ê.åbÉ€,‚¾pBà\$È%xBà&·TÉˆ`ÖE(ÔRÀºbı»Ê\0ï;F¹1i£¿oôTâ²€ôÑ4/ºk<UÀ*\0‡KöîšÅ\rÄQñZÅe’“Ñ]\0‡²É‘LEKØšÅ:),X‘c(ç?Nš¬,W½¾¼VñGBÊ¯¤RqhÅ€ˆihï<SñoÅ—ŒYàäEM„á»Å_œY±YE«Ì]Q]Å³ŒWñKÅ»45qvÅëóãñzEBû„^‘rÅ4õ±.“¾„9¨ä àÆ\ní”al*†+,`ÁS‚Uôb/QEœœ˜kQ5„XcÉİmTP€ÌT†{½`°õ¾%˜=	P\n\0…’òx{HqŒÆBËÜ!RŠ5üP`¤ö]³ä	¶Œ‹ái±>¢ÈÂ¤ï¡€èü´h°¯Fï\nN·¼<<| €¼hŒOj³ÉátÚ“ÔCÆ)ã†Fºú88(‘1Ñ8ŸNR¸iµ…ÈÔ\0ß¯ÀçàÖîi€Æè“€-€@'Ø2!‘¶ŸK@¿%X\0¤¬õäDkšò(Z‰µ\0ï¸À\0„üõë£†#ƒŒìii¡³€êãü(/-»º\$‹‡€Ø»º`t\$Ğù¬Æ÷[£;^ûÑ ×ƒ‚÷¬;O/:Î˜Ó½èë”]\n«JaêÅLóÍà9FŠúRSï¦\$ç˜T‚¯döù„ãÕƒ~`6°¼2Ê	‹µÌjÀ‰Dò2\\OGõQ8úĞ¬ÇÀ XEÂôÄÔØ4Šnlø†CfAî\0@àbX	b Xd‹Û4bk#V\r¨t’~ÍW5¨Ñ›FEN`„mà”#H «FäOXüƒó\0¦8úà\$%\n;£êÈ(€ÿÀ)€®”0’\n:D£ş€˜@@ÿà)€¦“p	Ár‚˜˜)È0“jMò\n\0€8ù\0œ(\näô#Ë!ó`ÒÔãQQ÷\r(Ö8ÉÃJ5R?±‘M³(œXê)(Ñ<~QúGì¡€RÑ¹6Ûä€‘ÿ dmÇ´]\"b…€€§˜\rÈµ°ÑÊ ƒ&>‚AŞ\$h?û¨cğÄ(\ní\0¨>è	ëèâ×Ø}RÈ¼~\rhH«¨{’,Gò<ä€mÈ(VN¸\"È\0_Ãh’7:ØŒ2Aƒô_˜>R\$™1\"\\‘æ27\"zŠ#ûGâl~rDGî‘m¶¶lÛã[€I-#Srr@u ;d* I/\"1àÀ¡ø'’]¶<ŸŒ‰\nHŠŞówÒAI ÜûŒíñ™Òİ8#áƒÀ	[v\0001€^lš#27\\¯ƒ}ÍÍÉ’3#š­ñ7E&|›i9¼”šòl€Ù&Ûvã£Â\rÈ«9µ'zC./œ3' @Áj+hå†œË*r@ÒÉhYéŒ;'ŠÓ2~˜í(96{¤A(9„áHCÌT¡D†€[¡Ò…¢](’ŒÊ,0°à¯u(¬ ò}Ê3Qå‹—‰)<R2(RL¡¦Éâ\rd£'”\nµªF2{J›’|Êu((SA»¹È±(o%û(È Â°\0[À. İÊ3Èò™†š©ò¥J1(T©2¨Ê\"j€úÊ«*Œ7Ò¯Ê]*¤«¨Iô:0.!H\n+ŸCÓÊ`­Îˆ‰(P?Ò¸„ş±L§aFƒÅ+³‚2¹Ê€9ˆá ÿ+£Ïƒ×*AF£L6ô£0¯\0×+­câ\$@cP?R¥ƒ# ²RŒÂXy:6póDï£ â,˜˜­ÑG‰5(ÈQQÔ¤cP\rò•Ã+Ä¯ï'JÑB“8,ˆmó8Œû‰òÙ«-¤·P©ËpM„·x²Ì¥B·V‘ˆ}|²G,Š< 6\nÜ\r¬¹Ò²JşSô 9€Z÷è¥ÍÉâÄ»2é€Å.¬ºEºğ€ 1K²8:ÕŒG*Aµ À&5-Ä¸!jKŠùÈÒËAe-˜9ù'#/£ˆ°©ËîÑU'Ës0¾Î'Ì\nÔÀòÛLUJN.mÄğÄ¶¥\nK…04¾ 9Lc²p\0Ê<¿ÂÉL0tÁ2ã‚B\$Ñ<LBLÄsLJ‘xhsˆû1l·n'Ä|‘»WÌdèäÀÒâLm,Ç\"²Ìw*tÁ’âLo-Yºhß¤ø\"Z 1¹È¥x­Îç„¨Ä¤ó /ƒ1ÒU 9Ì¤Ê’¬Kã2ÌËs.Êÿ'(Ì‚·vI³‚¥|¼®‡¢–Ô‘„Ì‡.cS\r‚\$âÜ’ş“a3¢r3\rÊàJ#×i‡<\r„£ 1»+ˆÎ€¯J¥4\$¡N™#è‡À¯-4jÃjM€\nĞo/ĞÊ34tÒÓHÊ˜lÈ’¿Í8LÒ/ê…÷4 SNÍ0¼Qã›«4ÜÒ³RM0]”¢¤ÈKøœÃ3>%0Ü')L?*TÏsÌêâ|¿3`Ì‹6üË|ÌÏRùÍ…3ë‰âa„J&ÂréMŒxs9á2<»s+Ì…6¸(³lÍ‘1€>³9ÍŸ5Û‰áTÍÍ6<Úx\0Ä\\İslM´û/}GJœÜ\0006MÅ7j7ó;¼í3ÌßgMÙ7C¦‚¡+\"³Kµ7Ìßs‚#~<¼òôË‘8dŞi\"Ëæç\$µ¥¦²ˆÀ+ÒåŒ,½ ıÄ 0Ë8Y&6€ç7xb/}#3ÜãË\0İ8Ø³¢L¬ä	2€é9ß“Mu9K1*ßÎ-/üä²Ÿ\n54ˆ›q“K¥üÅ“ÎwDæ Îo1SheÎ~#ìÃs˜šl©rƒ‰:ŒãÓœN|»ïĞÍ\"Ñ4êàäL79°?O}\0[KÓ‰Î7”ÜeE„²Ë(\ra¼N)3¬Ü³J•.kâ2çËBF¹ËK”ÔóºLú)I2o9¥%Ã|2fÉÆ´šsI©'DÌ’u·Û'pSByíñâ>/|Á“-\0æ¬ÃsÌÊ–ôƒr|˜O8€DH-N›<Èu³Jm:Äö“ÔÉÕ=X%)ƒ0˜Y3™2Ào\nÕ¤t	¨óÛMî,lDÕÍ£=ØKÓŞÉó=ü+èÙ‚¡6›²…OU>Œõ³I>\0²»MR\nÔĞ³èOY'„ôÎÊÎAì­SOM=DÁSïÏ«=”Ærô…¯;s­sO—=Ìş2ÏÎ?££“ûN[.Dÿ3ÒÉ£?ƒêOÿ=½\0\"LO[?u\0à€Ì7@Tø4v+p+\$Ïõ9L÷.µĞ1,H¯JÌGÌÀ“ùP7¼»FñĞ5>U“æĞ'A5´P?A\\Û÷Ğ%?ìôÌY@Ü÷M‰ÒC4LAhødÕÍŞ<²üP'ÍTNÕ?äÙ4%Ì¢ÃØ\r³õÊÒ½ÓĞoBŒEÉÏÒÕ\nÒËqA´ÃL£˜L¨aá„PDTµ	T.ÉóBı\n êĞ¯.´Ü422áØˆÓû)Å\r¬¢P§?UT1P³@D¿ˆĞ5Ü4\0¾ÎÔ¶ÆL9öæˆIäI}'òMÑÏ*3\$š`6É«'Hørv9Ü¥\nPßPÃ?lô£ÏPÄÀà<QUCíÒ_QGBÅ †Ñæ‚ŒP‡Ö4¤—€J„2|¶¶Ş¡qºÇŞé,}ƒè¦>¨0À«\$f”‡`)ÀPY‚˜( +\0Š’0£é¨• ¤Ş•´‡bWQ¼0Ôp\0Š\ne \$€rPÔs¡´\n²QÉQÏFÍôn0(ú@#¥J@à&Ñ3\0*€FZ9À\"€ˆíú #Î> 	 (QâÀêônÒ	FmôhƒEFè\n`(ÈN?r;ë\0¨È\\ ¤R&>«¶`'\0¬x	cê®(\nÉ@ÙFÈü€&\0²Ğ´nÑØø\näÆ¨úR /€„rD #ÑÄ‘(céQ§G€ÿ”ˆ\n>ÄTšÑïFRGô‚ÑœĞ%	ôÑ¥GxtjÑ® kT¦·JpArÒGJ˜,-§Ò®(Ô#»!e+©H©H•*4ŠR©K04Ar€ >øtƒG˜¯RßJ}À'QÍG	ôrQÍGE0\0’‘Hüô´\0ªeéFÑÄœƒ‰6ÒJÂ9É€±Km)´nÒ‚PÉG€³J8t‡ÒùKõ,±Rã Õ.t‹SHT…\0¥Lå+ôn¥(û(ÛÈ1Gu´|Ñ÷Gí\"£ÌÒH5tƒÌ•!@>S?M5\"4ÀRÇN•4‹ÓHÍ#`ş#Ô­I5cë#I=%4ÙÓIIl‡­¼?6ÔÁRL%0Ô‚ÓILÊQ”ó¾…3ÃùS@(\nTÑÒ±N`0´k€ˆÒMˆŞ\0“I¥&À'ÒqIĞ´T\rIı0N¢R†‘52árÓøE7  €“Gµ, «RoIÍ•Ò{Pe(5ÒŠe5ô¤Òø”%²#²>•2`\"ÈUKe?hâÈeK\\† «\0’íÀ	ƒíÈX*7kTH(ı#õÑ»KM2Ò#Ø¨	©†µR\nôŒ%*-!TÒQ®= ¢UT€?T‡´íƒ1O„\rµ.T\\å% ,‰UR]K!ÓQ%+»¤MQp\ni[\0§JåJõ!SQT„ÕÔ^“}4•7¶­JÀT™S5H´ıÔMSíOõ9ÈKQ`\\²ÈWSÅ+\0+%MPa­QµM`àÌõGŠGÃôà?.ÀÑëQã¨‰@#p*=À'£àåRt©Ó¬>­ÃëUSP•PrRòì\$ƒ\0%£áU…CëÈ0?ˆ\\µ.UuL„‚²Ò(şu7Õ(”¬‚¨î\0—UÂ7d¤NIfÏME\$5Kö?ìƒ÷â?˜0•j­J\rT@\"ÕHxü5oUV•U£ëÒİW)yS)Mİ]T¦…ËSå\$‰£p>âFc÷“üíÚ»OZ U.?åS5mU8%<à(Q©Fµ„ÓuFŞV\nµMT¼€‰Kİ_ãğU@=\\5qÕL?\rbusÕÓY\r4ÕwÕgY!1 #ÓeXÍa@«Uè>µd4ò\0®î\0İ#œìp	•>\0â=ÒÈ÷ µ hÜ¬?ˆ	ƒğ£œ?û©ô’ÔõL….ÕœÔ¨íà	@'ÖnX	5`\$J„4e÷K@ûô­V-n¡Ö±Kÿu±V²]WÕ«ÖÏµDÆU‡ZøÿÔmÖ6şàühãVX[óÖ\rVäòÕÙM-DÕ¾ÖíYui;ÓuU˜û)BU‰[\$•Ä£sTMG4kHï!]uWR}oôÓHïOoI\$À?EqõŠH; ø\nTƒÔ™GÂ:#õ\0¤şåtà«TMncôT°-D¢VJİu•Ù‚¬?„‚òÕT”%vCõ…ÊeG2;y]hhò\$ƒWÚ:)CWs^wuuÖïV½`µMÖù^E\\ÕÍW±^*Õ™WƒRÍRµW©VÍzÕN×Ÿ_Jtõ×>¨ûõÕ×¿WgÕó×V5wéG\0©Så}à«ÁF½ZUùV)Zuhõü€WK¸	4–£qHUÔëU7X½hUD×í_Åy6€ƒF°\\£õTß`M‚V\nİ`}4ÓXSİƒµ”Óe`H\néG€œpõóÑGU&#ô%ê}r	Öô”­e´ëW\"?=1I¥Zeà*Öé¥„îÜ£áT½‡ÿ†´‘,ÈÜXdít¯€ø¸	ªÒø¸\0&şkTÜÈõbMµ€P-TÚÓN`õ%Ø^¥BU\0§!œ…Â\0‹aú<€&€›GàüHò€˜?êDõ%ØeM9Ò=¿L…eÑà}Q6=Ö¤’k@¤R\ne(–AWWuŒµ WB]oÕÖY']â8µßUÍ@Ñ”‚VÔ¢¨-L5y€¡b kHçWh\r‰VO\0Vj?óÙUPÕOhâÓ«QÈ	À#€ª\rmöWÙcb}€\$ËLe?4jVk!Q`'U%^hèçR˜“EN\0Tníœ‚u\rTÎÕ_€*\0®-îÒ\$]¢76mÙ»Y½–4TmfU&8;p?5RU\"ÓúÅFà*?¹g-˜ÖxÔú½˜4ÜXì…IuSRf¨i[RSb8	4”Ù½g5 6‚Òùg *ÃìÒY¬ö‡€¡bÍ V…¤UE nÔ­½6t¥¤}O5€¶l#M+¤ÆÆ ö\"¯i5+t#yV¥‰ Ú] ’QÔ†º‡QM£óZoFÕ¥õ=Zlé­¥6'ZiÍ‡YZgQu¦¶‰ƒcíU”£Q²/5ÕsZÅ õTÚ0>•&cóıU@ıö®Q¯!ZMµÉUø\0û.Â\$YØP8RŠ?}kiÖNM“ÒITÚDãë¤K#¥x–'TRHúé7€‰GåµåTŞ-¶¾¤‚p\nµi¤ßUltÔUÔ|…V•×V”0ûµñÕûl³ûƒı\0²øıDÆ[+lİcì[ å¯ÖÏ€¿cšM5|\0ƒlİ:öÒ¤fG6²Ñ–\r1ò=Õím] ÃîÔ\\·TmÛQg‰1ÃîÛX…¶Öá£º>ıfu„»eÛÕíáb¬”kÛam öİ£kmÊQ–:\0Œ>”€##sn} '¶¥gñ\0¨Ã±ÖÊZÉU¬€\"ØXìuk®ÇTÎ>¥2URÕO µ%¶\\€ùbˆà\$\0¿`%7ğ8[:•´äÇ¿mm£7ÜmHü÷\\H=…”vÒKLÕ\$µpÒKFm\$µSH÷Z=«öÀW%cì0ö>ºcèt’€©o%¶ôXú}L\0\"ÀáSû–%ZÿoÒ7\0#Hö•µwÒ\n”{¨*ÖÍi¸	n¡Üh?]¿äÆÜí\rq—HT`õV£‡meUƒê€¿KÍi#ùÅví	 \"\0ˆÅ°¶Ô#£PM´7ÛIhÍËÔÜí\n?á¥g–µT7PEATŸRPrM5`S\n5xÜ×öíå@69ÒhíE!Ö6Ô“xúT™Z4‘˜ú×\r;QrıÑ(èÜ-Kº;•ØÛ` ştü»UKÅ/V²£¡N@üõS’”… ÷PVèm@õÈïnğüv¨ÈïbT•ºÚt>ÊE5İ;jC¼?#rLc·•”‰ëTÕ[` İyTå’Õå\0‰p-´W3ş‘½ÃÕÎÈ8-IãôS+TƒÕÊ]\"”·”ê:šÀ¼İíÍ:•=¹N¥„ )XOoÕ:—9\0ıq6Úİ¯r˜ú@!€¾ WaÛ‘]e#@/€¦?2tT]wUÉv%“mÜ’QÔ'¨õ¨Öo\\Õ·Ö‘©H<4å\\Yx¡SaYU\$–0XqHÅ”ÅSb‘¶ W)!İ õ>Yyb-…\0>UYÍKõG\0¥kw×“SEy-ŠnŞck-Ÿ	ØŸP@–\0øÈûWY`’\rgtš¿UD‹èÖÒ1=ÃèMŞ³!u€<Ä¦ıC°×¨\$t`d€9¿ÚºÌ\0‚ëz}ëŠcJDı@bÚ;õÀ\$.Ë{¡’µiš¢ïTP#±“†ñ\\É‘ì¾õá¯oÌxT¢²„ı•ï°k€ó|&eÚ<<D,³–B'|8WÅB©zkà- ^úp!é¿PßìfÁ%:Ş\rˆ\r.\\_1zĞ\r·Ë\$ä=ò0åßG|°B€ºÅ¢Ôí{z|Õ‡#='àğù€Ú­€*RÅºß}Åö.Ù_nFÅ÷7ÚCç}kßPÌ1×ø0²öZJ•¤à/Ö_eJî 7€ˆ´ <Ôn?-!X],\n`+UQy]Š6±Tr‘8ıUfÓNM»×DR¿Oø0 &Ó‘m=úÖ5€šÕÙi6×]¥;@İ=Kåş¶ÀTj]­5Yã¥ß÷ÿY]€\rwhòÔ‘RP0·şßï]uÿ2Ó€#©ø_ñ€‡iGØ*?ğ	\n_íQŞnÑÌ”}4•0…m  0æ\0ßtàí*:¬ º,™Ø7.÷;€ˆ ıUXı¸*\0004Œı9eì.¡ëÖÚ Jæ	%\nM‚X’‘>;÷!¢Bz@Ï¬ÈMtHa>Á1[ôê?\0¾N\\³<,È+­Ğ–Av8”D	Dùv\rø(½æüuÆjÆ”2(ñÜƒnôIj…H\$ÀÅØ/^²!sì@ãa\nvØ&dôš/A¥û{l¯NıÆ `ì'—¶¾T–nº,!<kŸ:İ„ìS@–Œ]°cñ­`ØŒhTøT`Æ^ T¸?;{ƒp5x4Dx=XkA³€Ç”\ná˜A°ë M®½º¸êÛÅ°\$¬S¬ ë…NêÃ¬o&“Ê›§àÖÎ È•î³:Ğìk¡£Në[§îµº„	«Òn¹ºäÒ™B€è³«ß®º/ôHéèëºõz«¯·¦:»,t0+›§2;ó‚ŠÊğãa)€ôvPL¸z)	{æ“#ËÚ‚Èæ6›¦€¸»ø3b/}˜„;)ó¹ï’â *ÈñQb,äpçb&5ğp²ˆP¦Î•YˆƒÎ1€¾\rX\r!%aî¾““<ğO\$hÁ„»„\0006/oâi{â)¯ÇÅï[®İâ*û†'à4G€ğpõa!Vh@-‚“bŒH?È ²°ÛJxáĞ¸Jc-Şø>*¸™„föb¯&—¾A_ˆğ\"%»‹-ïø=ÆW{ğJYbÉ~%¯ö;÷‹€%X/ ‹®\$QbíÄG8”„§‹f,øŒºÈ\rxˆc(\ra¢â:¾v1`>cˆÌ&a¡áğÄôa%b@£qLHkW¥Œˆ¹t\næ…Íë	ù†…†7³É¤È+V|û»Ğ?„€òŠN‰—cQ`¡ cgh 6€È«ìF0ô86xßÿ‡A]‘9\0Ã88¸ÕJ¹ËÉØÕƒc‚€ Î·ó1@ 0ç«Àab“ó7xÍ\$?8À2ÌNS„\$ÍJ'Dâ\\ğ5„ÖAï‰Œ%˜1½v3îöO¯3„!7N±Órh¸#‰;7Şãûñ{ÏÂ„³&%’ÇAw\$Û:¯Èà;ØÁƒ£Úàˆ·pK8şcŸ5ÀÜ˜Lğ…†n,È”øÈ€İÄ#¸ÃÙ	Å\0ÈĞ@:“RôNEBú3Ë¯ÌÇÌ.hÁSã=‡.3Ï\"†×ELsÃcR¹v)€úÇ­\$¼ùÄúë„iØO€é‡FImÑ™n™´!®JbÎ\r T“Ôd¥|`O¸³ÑànÇ;(hà5ÇØñ«wÍdÉ;ÈkNÓÊª¢Ù73ÆT-éù78ä\nàUY7D§„¬s™7@š\nÀ5.·…Ä	Tsf~åkÂn½«)	êmA7B’ÎNödîÍ¦™>@E“ø&P@Í •ãƒ„bøÒà:†ŒÒœãAE\0Ñ<\"ùQ¸k”ª¢É„Î7X¦¸„Ğ:\0îÆatŒl½Ë;\rò°q\0æ«Ñ)ˆÃ|\\S;(ÈéğYìës²_^ïc›˜&(–|Yj^ª¸~ZDÆ¸ÈKĞğ½£+Ü\0Ü„„Ù;ê¹=ĞÑ— +A’(—6\\iÙBz2mXB_ˆî}î€6ß‰.}õ“©_‰òÓ›eø [ÑB2eÿ|Ğ( ØfzÎZ™ƒºŞìc™…f}òÙ†\0ó˜P@2Ad‘Öbyˆf˜®bY…Nm—ÜAù2Ã——öd93f\rvd°åæ˜e9…Ë‰dY—f™naà•æc˜îe¹Šæ/™fÙ“f9˜Æf“eç~4?’_{å÷àf-÷læ“~7Úºã}ÖbY©µvM¯º¤LLÒ§ÓŠöÁvÆèéòeÑˆ\n9E…˜ˆ¹u˜U©Y\\óÄâ	 #‹\$—’n®gŠB«<ş Ì~ˆúãÜw™\r¤uCÛıù¸ˆW-d|˜ôÇ¬ÿÒyÃÚÊTzÙ	1Š,kÊ9ÇQ•VpROÄ,hCBÆæá~nYË¸QœÎp†jçŸY#ÊáNXùĞWumü¦Z‚(ÅĞg3V…‘LŠ^oy¹gqğ!ïœgz!]íp.:œq¡)	ÙägtJa|óöuÙÜƒ‚a6	î/ç‡ƒ¤õ€4d\$Ø6\n ‹€ğ2#1.g‘îs‘Å¾«å\\Ê&u¹·„º+¹,g©Ÿ”±ÙäwyÏYÇK1¡ƒ 0·ï‹‰9€‰:×Û­f6“ËöxYÿ9· QbË\$è ~tX'²Ÿ6zºàë.‚mƒ`ê1¡9sˆ@4ÍƒhDô©y2åâ˜¾vqÎ¶èVD.£\0á„6…´<®Æè\"\0®ç¶Šk»è¡>P9Ç1²vzÏç\r¼‰çØNÕŸäFYÇ–ÛV}\$:¹Ø6ƒÕ`ºÄ::';O§Od\$yF~ù¾8¿œ\"™í„š.ñ5yØ6O–ŒÙé,Q¦!=ïœt%³¨e£’\0ñ\0yf6€Ù}¬ÄáR\nÒAø`™P¢r,úC\0» ¸k@Ö¤S­zB™QCX!ÚI\0º.v‘N‚éş\$ñÁ@×TcšFæå Hi˜ZĞ2Ö‘Kİ\nÁ¤¶‘‹‚)]™»i>à77İß€MbÅ¸øä?›”µ™Å½C;ƒC„û‡Ş“c’³IŸ›4¸¿Á¯¤Ş#à0ç¾hTúMçÚD=zMèêX§µ£öCYíi¬@`º,¥ÓÅy¹Cİ‘Ùi¸ñc;›zV%¤±ˆàúé,M†’Âø…¨%~’:ENYŒî€ÚéŒ.ƒšNY NŸà‡è/’N€ 7h¨<ËA jë\\\nì¥aW-x`Ú‰çïdµš‹i~KP0ºMèĞ*i–Ë\$ÖFz|™QAV•Ié=øj!é,:tB0é-z–‰©œNº›¤V?@K¤¶AzxDbüV”úK\0æç8KD›§»Âğ^û¥;®Ggİje«Ã©F|ÂoC9¹àí¤u©önÈç(ó«\0‹áÇ*4íA1ÎÄëá“¬j\n–—B“f³=nĞõêQ¤³ŞzxbÜ‚D47i,!v£JP!­XÎúxPÅ{¡ZvéˆUøÓ€jB^!djù\rãğŒ¹¤°ßúK:4ŒšzÅÍ4¶¦bpÀl³¨ÌÒCÍCÜ¢y…±«ÄAo\$ƒŒ)6²z™Q ş?A\r`›­™\\zEï¬\rÚİƒs­¨Ö:Ehæe‘>‚ĞŒnêfínÚ¥;±®‘B‡°ç®¡•òj€n~’À‚Ÿw¤ThoêM¤[(úKKÉ®À°÷t!öŸëË¤TxÔ4€ó®î¥oÇyÆúEKRë€6:KG«À#±.\$t&¹á7c‡œ-šäƒï°@¶]°QÒQ:ÊŠß¾¨Ò¨i-,lQnÃ©¤´qOÉ+G©H¸:êf®:·ê“¯ĞIDŞë_¤†BoªìMªäAj9Îü©\néW®3«˜óF­¯~»/ˆëç±f9	Á0>’ÒöªGº¾d•µ™ÔD¹ª\\ÃA…®]bKš\"\rÿ¦F~¯Ÿƒ[Ø÷cØ\rŞË¸BOsê»1„d!ñy/Ğ…¨™n îºà¼\rŒ0û7\r‹§ê	ê%®š¶h\nØ2älõ ¬ÃJ×‘±³Ö8\"¥ hÛBh¤¤jÃJ7ê„-b*€K£ìõ°˜!ûFCV4¦½SKŒÙ‹F-¹€Ò~Ë2í;³FÊKÃ›4¥ÚøŒ‚™nÓZÀ™1¡vR9Òê\"LäÎ:.ĞÎ½‘dQh“ûõkŸanĞk#9N9¢ºÆ²dà­UÉÂ\0N°ó6O´áVÍæ5+ÑiÇ¢d„â]{Ø¬¸¸‰‡ê¶c	·¿gĞAM^= ˆì·Uğ{vlÀ\$™Pïë5·‰/°(ê\r)Â:`F_:Æ—Àó=À	¨!yï´VäÎ9îºÏŸEï†Q˜ì5ê>ÌÂë:5´<c‡‚†â‚Æ“—¸¸z»Œ¾	§M1˜[°náĞdn/ƒ®µ´Fé9¹Fˆ#`«¡vãºX‚<BöFjîdN`Qà5Œó¾´›KªÎ5oîæí	ñh;›—ı‚Îæï#¤ûÆBZË>…¹¾¿o@ck*è@‡·îÒÖ“ûî«D\\êS¶»)¹¾pÛ­„²ësC½º˜6­è†pU[›ÍG4†éûŸî?¨.ëe\na	¹¾>W@£Ğ{¼.ÈãÂ£¹şí›­Ìµ™\\9Ú˜>•­–CA»„ºùƒ×¥Ú`0ÖñÛdå]¼f‚ÒMì1óÊI7´[Îá¸•\n]éÑ,¸qÙVJ›¶Û‘?•tzƒŒ]£ƒíum*‰p›+í‹½Åá.…½¨\0Hùè«W¹ÛÑ;+ê¹ÆBzo½şx;^nE·tK€¬hq®ƒ„‘íêŸ“éE!³+n=Ìï®T±Øç“—æÍxkjú6›{ŞùƒÎÊ#†h‹½#ı[öo}§Œqàê¥PìDÕ²Ã®¡Ë¹²€•úoı1¡xcŸ£8DÜ\0Ğñ²†œšJ	¦°™ëê¡v=¼WÀFzz„mkÀèhOŞ“5j\$‹¦Xçƒë}´<A>™n¡{~h]³˜\"š\rÿ¨GDà£ÁxÌQ—)=:À5°Íê²G:ûPñÆD8ñp	¡sH2pzt¨¸‘º¹Ş\\Ú€ğ˜ùñğk¿|)‚Yt	¡½ĞPëE\\D¥0×İğÃÂ¾÷|pÎ1ÈÆs=&Æì`–hëĞIOïô\n”,òMí‹‚>Ae\\}·©Ï\\>êÕ£ÎGÏÉ7ÂNõ¯l\\¦ì¨L4!˜5c,ºTöè¦ñ‚ôñ!p}Ä¬§Ê<íQÏHè‰’89ğÒÿˆ!=ÁFÕ1j»ÀËAš@ ío„6ËÛéU¢åò9Éê»ù›‘Ä¹Æ˜î¸q ˆ\nM£Ï<_ì}½ö˜ï3q‰¨\0ùü‚\$n…Ïoî>\$z/	£ô+Ûäq}·æµç1³o\0äF8ğ?àİP½†‚Ürşä“š‚¼ğ;<ºNG…ÆñEÚcŸ¿\$*€ƒqUœâÈï}™¿séFˆŠÁ¡È8¹ïb¯C6ãú\rk•ÃGÊmù 4K<~4H!û­j¢âm8Nkr	f.UˆÈü¦zûºhÆ#¾S¬rU(	Zs„¹½nŒz!ñ /%\0‡¼ÍÃ/&û}¿ßÉæÚº6rxW`5˜cGµÅÀOÏÖbáˆW\$­bÊM]öá\$¾?ÂŒzƒêÌ\rŞ­\"q»Åéö«J‰ÊÎ˜nò­ŸÙ€¬A¿¤§&}šğŸ#[%çÉ¸-Ï'gt\$Æ•ÆjòâL†wN²reÄ\0\$8Zš#§¬:;¶s\0MÛò\\¿éƒ¯ÜÁs\nD¯MóeAñ„õ„êûäÿfÆ¢4IúBÔ¾’Šp`Âó@%Zü\0004É0ˆ}òO.Ñ\"‡­³L4¡±‰æ]\"˜'îH¶Ÿ›fÜ×™1ÍĞníÑ‹Ret®FŞ®ˆ.MY6ä¬–È™lc>h5ºÓ‚}<ÙÉŒéüç(ÄÜ7FLÑr ›m2(%„üìób7ì”ÒC\0[Í¸£M›sŒŸ#V’6‡Î§5M	&vî79ÎÏ7¤¹¬ã@¬!¹\0é†|šN6\$İ”ƒvôšÒnÑ!ÎTœÈ ãÏâ˜<ÿ«WDÇ@MØ€_Ğ(;İÌã'hÅòüLÊd©Êİ+øøríÏQäË¤HiåÊ±3,¥)t]+üæp=<è“tq1o3	FÑÜé³eÃÒş¢˜}Ò%\0001RÍ,”¡S˜OÆ_IÍ¥Ò)lt›8¾LIÄt¯:&ÅÖ\0ÏÒ¤Í!?Á_Ó^}0dÓ\0i\r'¸ÓgAôÙ)4Á?ôĞÊ/Lt·ÑáÎ¸IïE¢|œû™4W§?mi7‰Ïègİ	Ğ£u½ô/C1ìI¬ÀyI?CÆÛ{SZM±eĞmíKŒÊP \0”ê‘~ò\0ı¤A5°#.\$s¶ĞY)“û|ÒŠM9yd]Ï«A =9	õhë^šßÀrE@SO‚#>0L¡HKğôHEÿ%tÄñª.ÑmÏÇOüùfŞÑ¸R{Ñ~éİFğ%¶8ÜsKµB£ìÒYıw]/#ÂQõÌØÕcc·)HT_GX\\½pÛr>ÍÕ•¿Ø×Fµ”lXÿc½V½nuÉÖõ¶@uód85á¤ßlBÉ Ù-hEõÎÚTV\0Ûh=`-Tuvå’rTg^5ßÖQá×=b4l£ïZMUà«Yxuö'vC^M±cêÙ“UESõ­U1#§dî&vÙen@«RÓn%½“Ûì?dõ_vOeÅ—W‘öiT¥wf[)Ù?a=¡×_/iVMÓX…«]–ÙVodõ’ÕeÚf´ÒØEI'j ,ÈóÚmp®RcjÍÖ8â‘?^¶ÆÚïVg5úZûc•+}·Üskè\nµW¹ØueV‡ZõÛ½­vÆöùØàTlUÍ^UU½•ÕÍ[ÅS=Ã·kÙ\\İ›ö;W7guxÒ¿Uí86ÕÍÚ—v¸vïÚ(ûvÊUÍÚOsôÇÕ§Û½ow_UÌ?ÿiõY×³\\utyQğşçu¨ÜVMİ^]ÓckŸnôö¿W5eıÉYG^í%Öİ]P…_î[cWísÕ|VÙo=’÷•XÕwu¸ÖYØ\$İ•XÛYq:w±Ü]fõ÷ØÅd=ÈÖCUõd=Ìv…×=£VaŞ]ÿHòÕûß`\n]Ñw¦?wi•÷QlOjùö§ßzı÷gßÕâu•ÖIÚ÷–÷{Yx4ÿViHèû‰FVlíå»×+Ö{FÅÃ•¤ÔÜ>£·•µ\\sErVrÜŸ×ÙwYÕ}\\uÖÕÎuû×Å®½yÈñd<cÿ£pítÚq]9]øÖ!j=Uc;yb‰ÕGS±REå×”TûÙ?s•'×‡QÌ…TÚwF÷}=ÎÕUm„ƒ´ùwûâ-6õİø‘SïC.aıÔg&x{ø³·®-;îß¼i^1âÍ|\0ûu	Z^(I7Òùâş§¡¤cí;V§¸üU%hÍœ¶Y±g\r›–t\0Qh­…v9ÛcP”¶Héy‹·—†Èæ?8axDóòg•-ğ!‘3Yßgµ\$¶ŒYÔİ¯j7¢àP>ƒªÇee¬Xb¾«†sÚøh»a†›­Y£D/fÁØnáÁ¬¶nº=ú	^Î¼†ï³:øëöV››[°L½…ã«N¯a€êû¬x+¿ëÛÅÿäw‚9/xè>á•+¯Ûöa\$ŞùL;(”ÒØSF…tÑàƒ‚o;³lyÊÁxs\"€	E„Î ºßØ-ƒ@×¿ş5àè>…„~=È!ú\0¢1BUSÃbƒÅ\0O¡8L}„ÖÑ«„¹4q8L:ÉÎ.¡6’ò3ö.¸Yr®oÉ€ÂâYz[öäæ_+·Q¬põé?à¼62Ù/x½bÂ2ÚÎÓâë~-0+ñï–îr~œmCˆX!şŠbæ™º\0æí¨ÈA8‚9ˆæ&Rh	H?É–Œóï©^¨áW¬dåºçEæ¢¾ŸbÏŸàØÃz?Ë«Ø\\<j.ô Jc;²Š\$Í)Ã;N[°›Á¢Åyj	_¡œHÏIÂÙå°:ÛB*»­Ä¼“Ê3±:Sóôéùªä.lf¿P›QÃ¶¿hF[¯õ‰á6Ã@p\r{äìçÓìeú£î;|¶™çVïsêÌFN¹¸P+¹™kô™oûg½òÌ6¦[•©¾>ÛùµíÖ˜¶{lä+7Ù{ç+ÓfÔâ³íÔ\nùàŠcl=y¯œ¾py;ÛÖB¬»\n£­®·º÷’Ã¬m“ºÇ’Óæy÷Ğ%ÂhĞ@ÓL4``î·{ÔcnF®Ê{ßÅkÀóˆz¯Ô^úíéÿ½ü…[ïĞO´U|\0˜ùı¼ .Êd©’wÁy(¾gğnJ×Àd¿Ï¼ƒAOQïF_:×bŠPPÕhÖöÙÛaùğù–,Ò	1ñÚØùĞ:']Pæû¡g¾}ä6ëğ6XĞ—ñÅ˜/Pİñœ/-ƒI°¤>üMˆÍx1¶bŞ·Ÿ üUò#`Üÿd3 ²áûz¼Å”?¡6üC¿txÅßƒÇ»ú¸:LëòÎ×»Ÿ#,¦ø?0|·óÈSìmwòÑTîŒi£Ìß6üºâÿ8ñïò/Ë°%çÖ*h‰©wÃ§ÀÍò,¸Ÿ@ô`ÓüŒ2çĞçM}ëÀÀEıúŸÑÜ ı%ôoÏa)©_ô¿ĞQÕNMô×¿ÿ\"âYÎ¬Ñ)ˆÅçÎë›ÿƒP¾wÔRMÆ‡õ?Õ¡.B\rˆ5ßTbXÍá\$X/tÕÏÖ!)Á	)ÖI7Ä½[1}„nËß`ğƒÇŞóoñ`“£~AÎªbtoÊ’ĞwÚŸhûâønı/{IÔŸ°Ş}<vÿ şbÃ×ò(>8èÓÕ	µ\r3şå\"Šß÷(\rpıå\r7ŞŸ{lùÄı:ü‹âøàïoŸ^.}û ~İ¯£Á¿ò/à.mè7‘\0s?T~?áï’ıÿ><Ô|‹öoãMƒN™:Æ ÿyJq¦\0¼şo¶\ró£,<Ÿ}2	PJ†L~?;Wä-ƒi¡_İ¼\\}ã³àÇ:\"ıPA÷Ğ;5èÉùØŸò\rŸÏ @…½ò+‡8ƒ~…ÁfDß¤r\rùäÙŸ¡ï“ú,t_\"ş¡ÁÖÆ¿Yú·è?¨ş³ú'ß£çÁúïë“ş´}„cÙ¯4\"Öl]efÉÏÈy³‹›Ôà[ÙI LµNşí¸×a2ãÁûÏî ºğª!fÆPùû§SÀ–#	4ÿÀ_ŸñüJ’ı?ñß½‚²Ä ü[ÿÄ~÷û×ğENç®’4*Ã‚Uı\0%ŸËÆ8Ê‡ÈQ‡`èòS±¿Êÿ›H??Öh\\íö@–P2 J[xL²Gğ?İƒıæÛ\0¬È÷>Ã¼µ·/åR¸\"3ù¡HB{Ÿ¡öâ<.~Ü„l}}«<×|²ãÁş_û^±ÍwÇ/_J¹:Æïš´Ş¦‚&—ûÖÿwı ¿ÖhÆíıïkÿlN[´TäÄ@(´z€~M—0Ü#òh+Ü“6GETh˜ckØÑ tS2á(Ïq[Å ZÍè_ç>ªßY\n‚TTE\r\";(ßX s¢Íõ˜€¹˜-ÄÃ@¶D kÁS·J{(Ñpˆœ× ÏaÁ¦¾^\0ôé³bZf{ÍôÏ#di€ÁÎˆ¡DìL<œ¨2ÈlĞÄˆ_ãÛv‡Pæ“¯Ú	ÿ\0%ÆSÀŞÂ0Ñ’*D“!Ö½gĞ…;³Üv4dP'1 ÚßqZXb.YçfÍ‘Õ´[<ıc¦‘S¤œ['ä+ñˆ™ÏĞ‚|^•pøúÿ­ê èVáb…×İnª1(p¬Ù\n\0’2ä*ge G} ¢-/;Øï1^‰Ÿ\n€Œtqz€áP ™[ì ×	ˆ‰½˜p\"%’Z\0dí¼\"‚9+û¢.FOÂL1¡o}ƒjOªåğüPÑhCDE\\d_jŒ™9LÈc&¼9ÔĞxVèˆ7À5¯|te‹16¹P5B²¿\0ì}*ã2JÀnÙ=fäáò—µBQá'ìrR	}ğéãRÉBÔ8>–KØÆ°MC>QÉª`P3inÕ¯×wP¬ãŞa¿¾	# c3²YHòõE…h1øÇ_÷˜k0\nÄpe´GÇŸç´1ehá=\n29t*¸\0h(œüè!sQVåù\0İ{j&­ƒ«+@DÌş[Ö·0ulÏaÃ#•ğ²M;\rƒtXÇÕÃjƒğhQÎµ4‚CM¿3SéM_w6Í;A0n{lÖ íXxßÔz	šzfƒHBØî²¯rl	K!dOˆ# n~øÎps].1 Ájhà0Ê!!r0Ú¸Æpõpïd±9iDà©%r‚òÓùş£×füÎ\0àP4	3Ç€´gïÈ7Ú·Š>Jü\rƒLïMù™ü¹2kÚüá+¹8*Z·æ€h™ŒëôFßŒÒ‘1Zô½ùƒhdFÙŒ.ÆAÄĞ¹. mNY\0ÖƒÊKÜóXíAx6Q|£ˆh8fòÄcğ/Û%µ}°å¸ qĞcnWA`½ù¤`PBL¥¹€æƒÉ‚j`+ ©àé \\fî”Œˆ;ÈĞàëÁİg®İ˜,<ÀCÉù’;>g­õÔSà‘:Áğ8Í\n,îÛ³XAÖòÜ	c}H?Ã²„ë‚S=*ä¥ì¸8@¨Ğá7R„(«…äÄ”^Ë®7îgjŒÖß€W€8†z„8ÅYüÛ|CÜ°‡A‚ƒFDë}#PxE\n#8„PÙõ5ÚnğM¯öFXºÄ Šù¼6ƒ„r­İŸŠO¸z¨B_`LÔ†²®bE©”NM­ZÈ©ƒ©…«öÇ\nP>AmÍî7üPG°Gx9“Ø1¯á\09B^ktà¼£97¬P<7µVÕqş’¸JN)_u-€dÿaıÜæG`á<Áo¨Ä³\$'ĞJMõƒ¯…ôM¾	ó„yp¡ÜB4€˜iıô(á§ê@Œ8Uhb~ <(ğ\"¥Y§¢w4§X²7fzPA \"´ÄûAœbîTáTm…T!¸û•ä9­.PB­LöÆh.úU°MÌ_Ä•#VpØù“B¸(©£´[e^	zG-è— ç9gÒtE™dÈ?€CÍ 2ƒ–äV°ÉˆSOï›'<Z‚uŸ(ËÒ{¥e©=©šC°¹ÛÖÂê\0×¶Œv‰pO&ÁKi´ô–‚ Cà²·4n†|‡,/ç'MPÂU¹•~ÚlxvĞ‚‡©(Ö›‡(NQPÛ°d”‰\\ò¤TsÎ‘ˆÚ¨È¢… ÍË€@\0HN©\$xÉÛNo_¹)wYx«qÎ<8¦Ü\\ö9ÈsNÍ–é‰'†HC\"ç‚‘°ˆb !‰¤RIN’¹ \"KG8æå	è\$³s“ŒKŸD˜F£!‘¨“†”çÙ&ûøi İ@œb7Š;hŞC¡Ã{‰ã”H‘Q(è=Ô5qÒ0ÖTO‚ËKŠ–4+{pOà‚%\n†Í	m>JWølÂCR«Ãr†Ò\$5)úVÂLpİÃ¥ JE\r¡¦ØÔ¤ÉB‰8Ãi\\”6ÁÄønb‹²&‡\r¥2<8ÔÁãÚmÁÛ‡%\$à£§À_fç!©Ã_7…\r„+Ä63À®„˜‡pÇ´:V™Ê#Òd'†d¢MĞt9øjĞèJ#CYrä”¾L:ˆu„ù~”=‡:t!”û)A]i¨ÁfÉ%û“µUp)V¬.¶J9nyGn«n•{»È‡ ¬íß¹W€\nàU”;·wÖïõ^óÀåG*ÍŞ\n­\$Ş£”Lrëg„iñ«xdtÑe:ÄçbËİ¼>\0ªñKë·u%§SÖæ*Òxö±Àİ«·7^ë ^%)©V\\°Lb÷r±Tú­6T\$§ÙM\nŸ•Dª<¢,cSì£‰LüA?KaïDT2¦– ¼@õ!±”ñ©.U\$ì}#Û®ƒ·‹UT.6v¸«jØå·ÃÖC¬Ïvâµp’Ö•WK[	ªÎ\\‚µìíâØ'p.ß–È;°Zb´¡iR…ù‹KV¢-’_šµiò²²Ën–í®êQ–¯²á#õ}‹nU|­¼Zş©½frGµ¹ÊÎŞŒ]µ‰vË¶Õ€¡ùÖí«U[ÒYojŞò8†íVù*Àw\"·áy*ÅEÂ+YHŞæZş±9Ròá„êe”È p#œ¸aZ8}Ek‰•+Ÿxh³Mx1ÛÅL'P	®:væ‘_ºËe›ËAÖƒ®u=Qxí@h”+Ü¨\\ø®•I\"á¥\$ŠnŞC&\0Ít¹Å4@b p[º¶\"Òê•Kî¼íD¸Vü®MMƒÃ×Kƒõ‡èY¶^A•?d)ŠX…!lIÓD…k~—“­‰?¸¼ÍKàg7É\n©Fœ ë(ô–,ª,‹›lòœé9áî»'‡Q8„ŒDoX ÇŒj`Õ´ø‡¬ìh¹¶èrØ¼¡yİÌMÆn\0›<°²ôÇµsFñ6˜;BugÊİæÓÉâs×¶Â\0yl|í2ùƒ¯\r]˜s‚ŠjØ2B+ÑƒÆä=¾ÄÓp €DO~ƒÎ2ˆ++ÜÖî!^î²H{Àü°_£ö¦li\\Ë†¢†`\nÄK÷&£/ê´Çj 9‚ğ²ìİ¢†cd€€¡D'­o@Şø²cD–/?P†\n.YòŒÇã\rû%¢\0Ù¶¢…(†LEDGµµ™å­àÓ™Ò¹|¶x™kA¶!Ic›4Aeo¨àqŸ '®9XäÂáXx¨CsW×ÀÒ‘\"{¶Ó€\rY!‡èÌŒu¢ù)Ïñ\"5fFN¢±À¥Eû¸›P¢ÓöÏóÅH£·‹Hš¾l	&ü­±Ó¬\"ÜmºQ¿tZæÊ‘WÛ+Å²á¹§\$ ÄßÈ.ÇŠ-`a	²›F8·o’ÜX†#„€åáº„&R©Ä>Âî> ‰à}„\\ã¾ìöX9v~äê.©àøºño/#ëŠxı¹ÌÄSö,À™ÎŠ4 ˆÿc>·ÒpC4åÙÎù¶hgğÆ\rEì1@O|4(e \\äÅö6*’ä	àØdÇ!¨Ò‹íxæºMp`\0007§DğÊ4)cdšP‰ÃZV\nğÉ¸)¡ÒÀ@\0001\0n”üa°à\0€4\0g„œaÀ\0À†Œ5¤´ğP@\r£F\0l\0à°XÆ±ˆÀÆ#Œw˜ÀxÆ¥¤€Æ,Œ†\0À¤dÆ±@FHŒ–\0Ş1dd(ÅñŠ€Æ8Œ–Zx˜Éà@F.:Â1XhËÑˆ€€6\0a2œaĞ@\rÓ‚Æ`\0gæ2\\aèÎšc(F7Œw˜´ôepñ’c€5ŒÏê3Lb¸ÉQ”À€7\0sV2\\b`1›cF8\0d\0â2<e˜Ğ—ãF\0aB4\$b`ÑM [\0l\0Î3üf8ÆˆÀÆZ:ˆ´hXÈ±¤ãOF š4 ˆÉ‘¨£F…\0ir5ŒeˆÊQ¬@\0001\0mÂ0ÙièÅq¤ã`Æ+¤Ìg¸Ó¬@\000520ükˆÄQ™£PF;\0oÊ4dk \0\rcbFna™Æ3|kHÖQ‘ciF0{Ê1ÜeÍ¥#(Fj­|¡\"ğq£ÀFepdj7¤d˜×qˆ£GFñ7ÜnhÅQ–ã9ÆìŒËB2\\kĞ1¬#OF…Œ²M>3Lj˜Úñ’ã5ÆŞ\0â5¤g¸Õq“ã=ÆİŒT\0Â2Ìg 1Ç£(FP!Æ5HhßÑ¯#^ÆåŒ<\0æ1\$pÀ@\r@’FbIÂ8ÄcøæÀcF‡ÀÈHÛ‘–ãCÆGŒı1HÄÑºã\r–’\0i.2;ÈÜQ˜clÆ‚I^9Td¸è \r£FFeŒ‘ò2\$bØÉq§ã7Æ[Åf8\\l¸ß‘§ãqGÕÀŒe˜çñ‡£§ÇŒÓ®3,exÅ‘ÛãGA¡¤oXê \rc—F²P¤aÈÏ #¬Æ„Œ«†5<q Q‰£­F·™¦6¼l˜åÑ¡cÇHÍ‚<,h`‘Æck€2/ÔœgÈÙ­ãÂÆaˆ´”døÈ±Õc¯Æ;Œq3ìl˜ô‘™£F8j44{ØÍqâc8ÇO³<œcøÄÑÉĞ-Æ‡~8äsöÑŒ£¾F1ŒºFş8lfÀÊãiÇŒ9áâ2lxåqöcÉÇ]\0g8ôaĞ±¢ãÊ€5×’3ÌlˆÆQç£ÔG\$AÎ?m¸ìqµãôÆLNZz6¼uØÈÛc=ÇÜG68ÌsÄ±–ãÆGŸ’@D~0QŒXfGsŒƒ=|g¸êqã\$G}oz?dˆõC£üFõSF6üoøØñ®cóÇ<9*9ÔhhøßãvGG]¦4üe¸ğ‘ö\0001GÎ\0cŞ3¸YÑÀH.!9¤ØÜqüIH=U¶;ähbÒQË£§GWúAäq(ÔÜãÇ\\× †B,s(ÓñÈ\$ÆŒ÷ôŒlèóqÒ¤ÈY]ö2¼xÿñ¢¤/È%Œ“Ğ´”pøÎèa£êÆMã&7¬m˜Õ1£ãGëŒãNBätø×ò£&ÆÖİÊ4<e1Î#ÊÇO÷²8Œ€ŞQ½OF°CR9Ô{â1²dF~­25ü…éàcÍÆ,‘E²=Ll˜ùQà£ÕÈ+ŒE\"Æ2ô|ØÈ±#ŞÇAG´”Iˆa£ÙHÄŒéD„dXĞ±õc‘ÈÆc=4…˜ÕQÙcLÆ3‘= –9TjÈÕ¨#*ÈCŒ‹\"æFÜfxÎÑ¡#3G#õ\"?¼‰ÈÍ‘ØäVG½#28Ä}X×ñ‘¤cÇBÇ‚;fy±ğ#1GZŒeÎ2\$ƒ˜Ôñó£^Çï{¦9„©‘úc(GîÓC¬oHğ’Dc&Æ73R=b9‘Ò£úHyí²=ä‘xñ‘ı#vÈ@O R:ä|ÇÑ²#&É\$\"ö3Ü…ÈöòL#èF„¯#Ú3L†˜Ã±¤,Gò/Ê3eè²Nc=È­ŒI v4,q(İ1ˆä%HĞ‘á*F<|˜Í1¯cÜIQÿ²?ül˜ÖQ•ã.I‘õ\$3<©Ò\ncvGu’‘\"*G”Y‘õ£ÀÇŸ<ÔŒ(×±³dG©’¹–Jà(ë±úäYFSŒ‰ÚA\$”ğ1õd†ÇS‘5#’6Ü’HëÒ£(Ix“\"Z8Œqˆôñ”#\$Ç¯Œ; Z6LtÇäÄ£’GJ\0e\$’34nˆâ1ğãàIÍ\"²Gô‹Hİ‘#^Æq“Y¢3|bY3ñÅ#nH-<’>œi¸İ1Æ#şÆ×’¯F‘Y\0Q›ãFFDáMd•èüòc?H‹LJBŒbIòTã3ÇæIî@T|(ñ’Uã5Çé\0bLJBs	4¥ä>ÇŒ“m:ìb@r ¤HA’W1Ì‡ˆàpcÄÆË‘u'¢BTaÉ.‘Ú#3GzŒWÚ4üÄ²£šGŒ’¥#>>Ôu©ò¤4Æï’Å&®?\\œÀô£dF†ÓÂK´‰™#ñ°c‡I2ŒK¦J}ğr`¤ÖÉˆ”#Ö=ìbi?qÊ#5ÇmŒ«(^:k³#R6dVIŠŒÍ'3<yÒ’_ä.G4’õ&Â:x˜ã2G\$ÅG{r:ìp¸×ÒZäÎHmŒév?üc9CqŠc\"H¿!v3ìwóqÖ\$‘H¯›(æKL¢Y	¬3#È4¦’?é1)\$’£ÖÇ£‘ó'®7kù*d\nH„Wr2¸X×ãëõ#šE¢xÖ23e!Æk(b98İ8ãšå<š•v44uØë’“¥AÈ§*6O„›‰€I%GƒşH’	<ÑØãGI›“ÿ'RKl“hıÑÆc£ÉW“)<d 	?²iäRÇŒù%şLé1)Kq³¥ZÊb•?fGtz9R¤cÿÈĞ“ƒF=”}¹RK£nI I!F?<œéGq™ãjÉ~¿%\"3ÄŒ(ù;£\$JÅ•>0ì9*²3ãçIØe\"&St(Å²#ÈÈÜ“M!6BÔ¡™01Ğ£YHàÇVAtp¹Z²´ä]Ê¤’w&\"GŒøÜ2¥jGéŒ×#Ò5ŒkéNÒ¥dÆ¹ X	,‰`RdêGC‘3\"Ú;„zÉO2¥#bÉ\r”'®>Œm˜ÊŸ¥kIë_'®1<9£¤1xcÃÉ\\´£t\"%jV,µÎ£bòCıô@')£\n²gä³õVª­Õİ‡Õ\$Ú»QJúÍ‰hk\rU°*”`M-‘<´EdBc•¿ËMUU-<B¯ÅiğÿÖY»(wª®­ØšÊå¨‹Ge¬¬o–»ıJµÅ•ÑÔØ“^¬œB§†©QœKZ–º\"[‹±×bÒİ^>(×Y`¨ÆLM?%Ë?% -fÂ˜õ‘ĞùT®­Z<Šî[ø©p Ä½©]vÎ-ÛJ•×mræ–Ñ«óv³-an˜õ` À,»p‰‚²‘qs²Å:²é%Óà—P©‰Â×úŠWb\0¨ü—h±ÉGä·cÆ%½Ë·%|¦œ“zğ±0GŞ¯yaÆ)4¼p#ÂåäÁ§\n¶T¡O0}¥2ñ¾/p?ÚËÖ½Šöeí;ÇW†&0íÄ¶E^ÄnT¾3–z´c[ºçvËÅ%²<‹´£]Q4AÉ}’ûÔËôVÎîÙT¶}ë¶R<.\$ƒ4ì¿·ùÉFÜ—#0NÃÀĞ×ËûYì\riàˆ\0kGZIŒk\$Ák¬¾NmŠs\n™—˜5¥!KB%üK``\0­ÙÜÂ' \n}•˜D¨Éf‹³µÿ\0Ö¢<,¹´Å-³@µéÇiKæ_í,ùfe•/¥Æ…¡Zõuò`âÀ¢S˜‡0¹jX5@ªW¬D´­Qgp‰‹\nubZ‚åx=-\"a:­\0J¾€\$®Ûxı1m`úÏ \\ªÔ@!-Z¿µHJ—À)Õ‘	4M\n™e‚Á©kÎáeı5zbœÍ|@•P0«9ZF½¨f\0½Ä\nò /—=Ë–œ¸ºdR¿ÀıÒéCóK§­-atÈÉl‰J-iT\0GD•ò U«ıÆ¬ˆ\n®]GjÅ•©\n;fGKW™!2eX}•“jº%ªL­—_2ª\$ËÇàû+c&U+œX²±Ùd\nÆ•™\n¦_ºş\$N•]\$¸€0¨ã›%¬z˜õ-å^2¤µsÓ\0VIKõY\$³D?Iv€Ş?Lt,˜èÙÎµ«RùÛUÅmJĞf\\(‘P#ÀÖ–Lìˆ\$cÃw‰j„âg<~bPi>Ô³\$s €<<—fgó¯%~ËpîZš·ÑfŠ¯@kKÊ­,%Qù0d,M‰¡«T¥\0(^jÀvhŠÏ*È˜VJ»WYî¨\"hB&†k½•û)§vîìÛÔÏø.í]‡¶YC-…gÔÑU\\ÉCù\$˜4Î]dÉYuÓ%æWÍ+w&®¸>ú[„›”M7vù-sRÊ)¤ÓKå\$0™©4ùâZ”É‡Ë´\"«ÃSò¤8ê!P\n@!ºÜ\0¤µtDÕWeÊÁ#)Kvîïe[úÛE¯îÂÒ<€Cš“1öj‚·ù­\n5ˆMW˜O.‚k9#õ¶ôæ±)YR.fk4ÒÅ+Df/šß3FlÓõ+*Ã…ªlRÙ6%âZ¤E23	ıËi› ­şl”Ğ‡rŠf¦¾Í™%ì-a£Áy³ªZ¦ÄMqQº¼j^™ˆseÕ‘Îšû-Ze¼ÚÅkê›‘ªx	5õ€s°×{ócæ¸©v`1…^´Ô¹±JÅWLÍÂx–¦2^Û%ÿAü‰Ì³RZ¥å]¢™•…_UÄª¦^V­ıMYºû¦í®_›¨îŠkëºY¼+”UUMj—m7)ZBªÕuZÕDÍm›6ñİ:ªÉj“xfÄ`š7¢d¡™§`\næ¼M,—H£²Yêÿ9¹J„Õì»[–È¯fm¼Ü¥ùór•™M}™’­©Xô´·³FÂ{ª‰WÊì	L&	·ŠÈf”;Î—Êï°©q1ÓL†ÍÜ8q!TÓU&O–‡ş›ó2ZoäÓÕd ¦ ª”\\5qb¼9©'»ıšš²efŠ²ªJVæªN'wØaÚñ)Ug³VÄÇM^šÀ¤ªr3¿Ù­f±«y›×.ä?tŞéŒ†İöMs¬·Jk³°Ùkö&½-µœ©5ø\$Ø\0\nÈfÁ­˜¿8m,æÉ±‹&Õ;¼~w¼·Îlú¼9²§Q'3Í%6mOŒå†³Ÿ¦Ğà›E:	Xz±Ìê—'3©A›]:![‚¿I¶3šİö\$XÖ²õİÔãÕ†³nWÿÍ»Tñ:A`Û÷aªŞçMM^V,¥îs<Ü‰Ô«Cfæ<K›Ó2Õ]tİ)†Ó¨&ê‰Œ› ¬Qedæwa3®¢UNº›Á:‚oì	¼“•ç »ì›ÒªRo4ÖÅÚsœ'.;ı^¥æs:ñùfÓ´fú’m›ï:Rvtâù¿s˜çc\0Yœ.–s:¡8{²üçPN™œÏ8.vÂÃYÁ³¨'­âVÏ0¦u&ÂË¯g<2›xìqÒù„ó®Ôz;Yšh6i²ÎÙßÓNfıNMšy1wÂº—b3\\“Î4RQ8Ùr4ã‰ßª¥*KGœà¥ñÙ<ä5°ëS”c;Yœ‘5br\\ÕÉÉË–g(OU)9Nxšª©ËÎÃ€*Íìœ³;-eïwjSÊç0NIœÅ4õÙ:ŸéÍSÎg9©œë<	T¤èIÏKç>O)S¦BlÒÎy³“Ñ›Nxœş°nt3\09é€\nçO<~v{6®y2ª©ÑÓÕ©M°=UTéi¶ª<'ÏY›|ÀVa<õYìÙØ¯ÿI=­X¢é…bò÷'©Îªá7-e„êé¾S­Ø\0N²\0=Şu¬İuT“WÃê¨ì–Èí!Qtİiï“veœ»Rù=šq,İéês°gÈ+\"‘<¶o<ùYØóygÌNÊšİ9ntüù©ïsÆ©NÏ–ª¹&t›²yà“Œ%¬CÊˆ)=MàòWdóº“ÎëŸ+;µ[iŞågy;ôèÀ&pÌè¤M,\nCşÜ¢G‘âÒâDÀÖÓ¬±U%?(‘:“Å©óÀ(\0_VüªH?x	3ö'G‡ÑœS<I’¦§}*GçìK¥TÂ³6YÜâà	ª˜gñKFJ:z°C§Ê¤€+O–~´OÊå9rê´\0&ƒ&y\ršZúÍ/*Õfé©K1<Œ´ß0õ+­—W,Ë–`H¥- ´ÊYéj_gıªH—,«*à÷dríé-»—€¦B[şÓ©óMÈò€_#p²ä<»EÉ“õÔ¥°\nT“@Š%Õg*’@)¯Î‡â¼.] ä‡jÄhP\$\$‚ìSìÔÚ%àÍŸYš8™—”h ’—i,Ê‚zÓ%'óı¡üÎI\0¾¤(rî‰gFíWå¯Í]aYiE\0Õ)qÖ£ÄQ º´¶wDD©|3I)æ[,¹ftÆPúSú(7;/Hğ§ÎuY¬”İ¢©9®lª¥:3‰Ô¯­T ê¤Ä*ãP‡_ëAºbı:“‰À!OÖ¡BQj„½©®ËZgõNc#¹BlÌñZS’V´Ğ>GØÊ¦†”'gPŸ¡IB¥qtCJS‰èW)ZÒq!TÕ²8³Yg5¬o²¨º^‰#uA*Œ¦eNk¡Un’—Éæ´-€,Ğ¬˜C.…E04(RÍ=¡¨íy í©qÔ4×æ† L§¹Oí\rÕçÓm(ĞáWÛ?YRB¯É}´8—\"ªkçC*‡`õÉÔhnPç]½C*lb°9kR€*á¡'Cõb”şŠô?¨Q\0X«’‡ú®gt%–vLX¤ìîìÁŠTDf,H–Èµ‘r´ö*´?h_Í!˜1Dvlú¡9Ğ‹(ƒéD¢)CözrÏ)ôtIæ®ïX¤¶>ĞÕ'’îÒ.S ŠJ6aš&k29Îu Ø¯İSìÂ5™b&,Ì#YPa*ùi4RrP˜YE5eÍöKJV<-QTûEMßHÚ«Zh†)|`\$!kH}J*ëd(0Ë;X,´¢ˆ,òE¦Q–“¬©˜}A>Åå¥¡ÿÔƒLCS5?‰:¥Ê/.åh›,^Tn¶i¬òZ/ä±y,,¢ë1Ew²®‡a!ù§€Q‚\0§Ef9ŠÓI4©Ç1]½êËªS›ŞÑ›ŸÏDqªÍëç¢¼:,õº\$ä‰VmËd¦&6p€şjf×öÏİ[Q@iKZeİúİÂÍ¼^BB±\n Ö’¬ø–wGß¼ä@úTfÖÃÍ‰<±`-E¥*\\X*\0\$¾Gv‡ı* ´tÒÑ]£¬Hp¥z0”h?®{Vvâu²—ÅÏq-fH€N]æ²rŒÈ´L*€”­#\\™BJİU&4	¢Oİ]–»„¸ƒ@“Jî0\rTu=bŠÊ£u™*ŸN¥¯ô×<”\"EFëV\\Ï›¢\n@¸E/WúR¡ \rh‘I\"º ¤qb?°T(¨YZB¾Õ‹2Î©€N¡Šº‚ˆò¡B;‹{”kLªW”JeÔ÷JFJáÔ¾)¹õHõJÒººG«ÀÖh‘ï™¬¨Qr%ZS/À+Ë1 ¤£©bÊ›\n64whÍÑİ¢\n½_İ%°ô='ÍÑ'vI½~r´‰šSi/.·¤Ä©ab¤±E€‡@ ·‡-©“Yï¶2”š?Qè°îÍRZïUåJ¤™R^:3`‘K®UŒÁöèÑTŞHÌ²ÆjQ?Á¬f\0ª£‘RXYŒjl'Yª~ ,©Y}õZ\nÒ(ïR˜¥8®Yİ)µÊTd©\0¤QøÁsº@ÅH\n˜–\"-DT—J±JúÔJU4|?Oæ\\]IyS ú”«©UªÆ¢e;¢„ÎÉ©\nh”œ-î[ú³Ê–(Õ!ÒÙ&„/'£6JV•jÒúVk4gØ®Hv©½Qé#I(Î™¥:±}à%u1Dy	Í n¥ğƒ¾™ Ô™æ~Òœ£7Jfˆ*òª€˜¥1>¤G\\\rˆ!ĞtœÀRô«KØQe/4›YXRo\0PÆp(*—Í)œ\"Ï#Â´¼ñ\$S§íiŒøâ†ÜÑ)raà\\¡†›/(OÍ\$jF3fæŒ€Ãˆ(t\0¬`ô‹àdÀU	>hàêeÀc°Hÿ\rpİ`gPÊìc–[=çLäf¢‰”š®\0002\0/\0…–5\0b‰!Ò`à&\0]*px)ògÀCZu‡d-Ñ<ğ\$¦ñÂk%A‚üzšÔdÀ´ÕÒ¾ÓY\0Ö›5êkØÁ\0006¦ÒÒšøc@°@\ré·Áx¦Ø”Å7Zn4Ø@Óz¦×MÊ5è\noÔÙi°STtF5\"›U8@Æ”ÕdÓ]¦áMü†]5ºpgó)ÈÓb§MÚœ\\±r`†éÊÓn§/Mîš¥7Jsôä)ªSy§GN*œ­7Útôå\$Ï\$§6ÊœŠjuôÖiÖÓsN&dZvtØ#PF½§vÒí;jqãéÔG§}N®œå<jxTÙ)ÊSÆ¦¹NŞ%<Ğ\$ \r)ß€_§M¢E;\nxÔç©èS˜§¬–=Úotñ©ÓSâ§!OU>jx´àiêÇG§O2œätzytî)øÓ’§³Mf:==J~ôØ#£ÓØ§ÕNR:==Êxt÷¤Sí¦ñPP@juÔşÃF~§eP0ı@ê~tôiÎFf§Oş ¨Z‚t÷ªSÏ¨Mî µ?ªƒì*Sş¨?NB µ@*~	)ğSÕ¨-O–¡=:Š‚ÔújÓ””oN\n „£z}ôı*Gq¨]Nê¡Ì•J‡òj!Ô*¨yMf0ıAz†´ç*\$Ô¦÷Q& õBº‰ôéêÉ-¨wPR„’Ú‰Ôä\$–ÔQ¨M‚ImB*‰uiÍT’[Q¢Å5™%´øj1Ôj¨QšBEZ}•*Ô#¨İPú£¥Eê5*!Tt¨MQJ£äkêQ‡ªAÔ0©\0ÒCõCJàäT8¨íQòKÕG(Ëµ\$ª=SŞ#RJ£ıEú‘è*ITl© \"¤5I ò0j+ÓŞ‘ƒQnDŒ’´Şä`Ôb©#¤ÍG°Æ’0j4T¦¨[PBF\rJ–U(ê8Ô¸¨çR>¥œkÚ”µ+\$¹ÓÕ-%'~¦5GÚ˜/*\"T¾©S:¤õJ*˜õ.ª[F¥©“&~¦½L:r`ª%T‹©¹R¢œ¥MÊ•u6À’Ó–©—Sr¥=ê›•-i½ÔÜ©¢\03İAªmõ<c3ÔóKçQRœuOJ–5=*4Tô©ßSÒ¥ÕOJ‘IĞÓT¿§‡SÆ£­Pzõ)èU¨õT\"¥uPDÅ‘¹ê{%ÿ[TV¨-HJŸ5Bê€ÕªSÂ§½MŠ£uEªHÕ#ªRN©5Q8ôÕF*•UªAT’§İRŸµHjÕ!ª;T’¡ 0\r1ÜcCF²9áSÆ2ÈàZ§QÜcÆ¿WSÆ5RÄÆruj¥”sU4\rÜ­Jª\0ä}USŒÿUZ3}UŠ¦µE£*ÕXª?SÆ<\rUhñ5Xª	Õ`ªSŞ:UV*¦5E£[Õbª·T2MV*«õ=ã?UVŒeUÚ«=QhÄµ]ªµÕ	UÚ«}Q8ÙÕ]ª¹ÕŒÍUZ:åXŠ¬µ=ãOÕˆª]SÆ1ÍXŠ¦uaªÀÕKŒoV\"¬-PXÃõU£6Uœ«TZ2…YÊ±õ=ãkÕœ«'Vj¬­TèÄug*ÌÕŒ›UZ0í[\n³õAcÕ°«GTZ1%[\n´õkªÔÕQeVÂ­mT²ÒUU£EÕÄ«gTN4=\\J¶õAc‘ÕÄ«wW\n­íUHæq*àUL	%EtÏr§*ìTõªR˜Ì	%\\´Å’š*îÕÏ«­Ö®íStÄ@I*ê»C\rR:¨R_ô0Õv’ù¡†«W‚¦E]úºÒêìTÍªWÖ¦}_ú¾UF«UëŒ9Wö¯lbš¤UjñÕ%¬W‚©E`úºÕJêıUî'Wb6maŠ¼Õ…ªèÕD¬1X&Nı_åõ}dõÕØa™Xš«Í^¸õ5‰ª¾ÕãŒYXš¯UbêÄXk\nÆ)ªÇXÖš]ˆÄÕªñUà…Xê°İcjÃµ\\kGO«±UÖ±ÄsJÈõ}êëGî¬Xö²mbÚ¼_ë\$Ôl\0FÔ|—©EuvãÕB¬°2²Ôk\nË••«/®’õ%’²åcÊuˆ*ÃÖ8«Yº²e^ê±µ›«(Vr¬¥WZ4İ]ˆÕ5«5Vx¬AVn±ÅYÚĞ5œcÌV)«Çî®ÅZZĞ5cV«WZ±qŠ»mkGÛ­'YŞ¯tcŠÒu‡jáÖ8«‹Zz´%4Š»ĞkQÖ˜”©Z2®iêÁ4Õ*©ÉŸ­	&~´2c93õCQFÛ­[2µ½]3õŸ@İÉŸ­WP.°•kÚ¡5ˆÒÿÖZ­[Wæ¶j*¿•d½V¦’õYKÕkªÀõ³*kVÌ¬WÅ1eÚ¾•±kÖØLYvµ‰ÑÈËµ­#.ÖÎŒgZ¶3]nŠ×¨ª „Ö–T b˜´<—jKl>+aŸ+<¢·ºÒõÙè]˜ª7\0—>íÙ<ö©ês£+»•Í=LRÁ5S³Ô×+Õ] ¨\rH2’È„ÕÃ¯‰£–¤Ñ[:³%csİŞ*O‡v¤¸iL<õ5V@×‘Ïw›‰C¾¹2ô5ŠSà'ÄÑév©¾|âiò±\0İª«‰^¥n{½—jÎfÇVüŸJ©‰Hd÷uBsëÙ;W®…8ºöYöt»'Ú×–y6xÂB¢DÊ¤'WNÕñ|ì¸Ù‹.¶«œP-—q@	Ûã¶Ùfsr¦¦ÍŸšû8ü*®¯µ¾Ã­–`1CÜ=o¥™5Ú×Yİ%\r5idEúßê¹&Ñ»8ŸB«š¸\$á5mÁ§²ªæ®ª:ké+iÆ‹I&åW\r®,JÚ¸Åq%<µãkŠ×¯\"ï!åq¹¹UÇf¾× ¡Á^\n¹rY¹UèV^‘÷#ó6â»Uz9îSª+Õ)…®W7*eDË)×fíÍ2vï\\Òf:‘Êæó)«ÁO”›âî¦n3¯ \nêkŞÎŞ\$ß;‚p¢i¯®Õ”İ×Ë®‘8¼+»—€‹*ëÓÎ\n¥Ø«š}´¾9¥QÒ×S›»zh\"§ÙÈUØ§’ÎB¢o;r’ÙiÌóÊÔpPÅw±]ÊvjÃU¯áök»LïÒ¥Š·ôû¹Îõß§;;ì¯eLÕpzşàU«Ã×°´&¼]ed5ã«ËW¯0¤¾À=€Êâö€×U¿F•ß@}W‰Ó™ëÏ\$;œÏ^’uZÀ:õt•ªj¯ˆ¬†½Œİ‡…“¯&ŠØ(™\\Ô?ı{ºı\rkNĞ_pîùS¹UX×ÍË_>À=€'€ikêP‘wÙ;¡àEy:jÍañ¬®‰Y0AâE\næµ¾r°™‚«¶h«Á5š3hÓW¹wö\ri:ÑiÙ+ı·R@¤F£Vx|ÌW…1^Njx\\I‚f”ÂGpaƒ,‘Tü²N|z¸§cs0çELğxm5*—5ªÈÎ¬@Dg(Š‰‘›ÛQ“´Âæ¼çÎÁèÛ\0\"HÏX”:¿ÉŒì¢£ï4š\"1u(.÷ë`Óå±‘y,0é†øRÃ`µ°5€AÜ-	š~v	ö+†X²qM¨¢sË¨;¬[0‚BfÀ®((&h€q¬_³FÚƒ¹8ÛéÌé~6ObüÙ8'Û	†„íXÈdfu`ğ£¦42\nØ¿|.KuíPæH3:^/¦G|<ÛYë(´Å<\n€bƒ>’	Z;'z¬c\r‹3b2\r«NªĞLÍÁ2¢xU:\r6XÀÀ-€b\0ÆtßëT»%P˜ÊØêşÆXòSX!k®ÌÄ‡Qu‘a°?Ğv”g².ÜÌàSçS:l‘¤œídŠtÏÈHà\0=/¬`_3¥¹m‹FˆÃ‘%çlÑbÑƒB0 Ú¦ƒkâÕ5¶Åˆ°(¶PO?ú?ÎŠñ<ÏÁ\nĞ‹Sµ=5jä\nÀä{*\0ĞÑ3b!eT•’F ‰‘3¼•™Ò<bÊƒ*¡	Ìæ‚5FÉc¦	ãNÁ	H™Ø=¢ga6Íe„\rñ  6¯œ;\0¿&ÄšüaŒ›Qe†4‰Ğ‚¡¤hÖYadLü	\nÛÕ¤l‹*íG_™±×…¼	y‚ƒHÛ1©e.X´jÒÌtYë2Mw4³6ÈJË]˜ÀMÈìÏ½È™\n,‰¤jxFíG@º*g\0_¥šÅô ÙXY°³	f\r¤m˜9y€Ã ¦¦´æÍûß‡Ä>ÀÂ‡«o¶Í(jG;8\"yAµ3×ƒfü9µ	LÓÁ¾ÙÅmgQé[{ëds(YÅ~ŒŸ~@â@:	®àÿY¿³¾šíÈê6Fa\$lñ§)Obå³=Û<Vx˜Yuvx»ÁÄ5ÌYæ€š¸›¥•€ã±Yjuh¸¦\r‚/Ú™–c^ĞxÊ \röƒ…A,m®*ÎĞyw¡\0ÇÙ«ÚÙhuï´Ç7UÌ«HA{èÈê«#S­Ï{>Lµh´]¡ĞĞ‚µ&~f®ÃÑ¸xèïm„]¶.ÒÑèBôö&eÙm™—e¼lH‹¦+6ZÄ¿ç(ç\0Ç…¤Ù,:YPZak‰…¬QÛ.ªË‰…~œ°	[ëÑ-a_±:üÉœbPcA–˜í/\rhàÆÓe¦–¬h š'…´uiÆÓ¬\0„=Ïm\0´Éi¸›ä\0JPh-6Ó`rfiÚ=‡–mC‚R\"È'^Ãµ§Tqï’lS¾´ÈÉUİçÂ—^³ÅQ3ñ’ÃTš.AÇ=&gvÀlMª3@«-Tš+P‚¥ª ÆQA.!\0ÇjÉD[\"»Wƒ,Z'¦QRİ«€U&v­YXæµ[i0\"Õ—{Y ¬lØé{Š\"éé{P”\"aâW¶ZÑdÅ\0BÆPV.²åmm=0²kv\r5­5ïZØÚàµ¾h2Û4÷¤ÒñlOZÜµĞOÉ–í¦‹Ç.,§Úï:´ËF×Z('¦ì`-NÚûB Ú…‡Õ­6¶¹,ØÂ§aÆåš×ĞaÃl­…Å<6èÜ½\0000¶®­‰‡@±lM¯­ˆË4ÖÄÌŞZcğRÏÕ•« aloÚ|&Š´GæIb3µ´\n¦Ö\r0( Ó5[/¶fHè\rÅ®Z`öÍÌL–^¶d\$ÆLÎU(5-Ÿ[;ŠÎ(ºÅ8*˜ºvÍƒƒò¶~|Úa6¡¹öÓ4dÁlıõÁÄ\nÖÏÎ/ÛL€êyÚ*>Ç2Èû?Ÿ«¹¹›Ød!|’'O†(kîå¶P6!i¡t­x\"°“I©¡\0A¶ö ú“, «ôÀÛ7¦bá•Èz°ìŠÎÛJ2E´Cà\nB5Á@!ûŒFü¹h´àæ+-Ë:¢\0NMCësˆ–HßÛ=nAğà;s¤oÀ*Ú÷·:q›«Bæ§×\0ŒÛ¨NÅn¬İnõVÜ„º×4}Áóük6ìŞZÊ—¨_àtv­ÀÂÄî3>wı9\nƒÑL(ÏYy-B{èÓûš¨Gà\$6yeÌ‹tÇd]ç2À");}else{header("Content-Type: image/gif");switch($_GET["file"]){case"plus.gif":echo"GIF89a\0\0\0001îîî\0\0€™™™\0\0\0!ù\0\0\0,\0\0\0\0\0\0!„©ËíMñÌ*)¾oú¯) q•¡eˆµî#ÄòLË\0;";break;case"cross.gif":echo"GIF89a\0\0\0001îîî\0\0€™™™\0\0\0!ù\0\0\0,\0\0\0\0\0\0#„©Ëí#\naÖFo~yÃ._wa”á1ç±JîGÂL×6]\0\0;";break;case"up.gif":echo"GIF89a\0\0\0001îîî\0\0€™™™\0\0\0!ù\0\0\0,\0\0\0\0\0\0 „©ËíMQN\nï}ôa8ŠyšaÅ¶®\0Çò\0;";break;case"down.gif":echo"GIF89a\0\0\0001îîî\0\0€™™™\0\0\0!ù\0\0\0,\0\0\0\0\0\0 „©ËíMñÌ*)¾[Wş\\¢ÇL&ÙœÆ¶•\0Çò\0;";break;case"arrow.gif":echo"GIF89a\0\n\0€\0\0€€€ÿÿÿ!ù\0\0\0,\0\0\0\0\0\n\0\0‚i–±‹”ªÓ²Ş»\0\0;";break;}}exit;}if($_GET["script"]=="version"){$q=get_temp_dir()."/adminer.version";@unlink($q);$s=file_open_lock($q);if($s)file_write_unlock($s,serialize(array("signature"=>$_POST["signature"],"version"=>$_POST["version"])));exit;}global$b,$g,$m,$ac,$n,$ba,$ca,$pe,$hg,$_d,$vi,$Ai,$ia;if(!$_SERVER["REQUEST_URI"])$_SERVER["REQUEST_URI"]=$_SERVER["ORIG_PATH_INFO"];if(!strpos($_SERVER["REQUEST_URI"],'?')&&$_SERVER["QUERY_STRING"]!="")$_SERVER["REQUEST_URI"].="?$_SERVER[QUERY_STRING]";if($_SERVER["HTTP_X_FORWARDED_PREFIX"])$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];$ba=($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure");@ini_set("session.use_trans_sid",false);if(!defined("SID")){session_cache_limiter("");session_name("adminer_sid");session_set_cookie_params(0,preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),"",$ba,true);session_start();}remove_slashes(array(&$_GET,&$_POST,&$_COOKIE),$Tc);if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);@set_time_limit(0);@ini_set("precision",15);function
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
messageQuery($H,$mi,$Mc=false){global$m;restart_session();$Dd=&get_session("queries");if(!$Dd[$_GET["db"]])$Dd[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\nâ€¦";$Dd[$_GET["db"]][]=array($H,time(),$mi);$Hh="sql-".count($Dd[$_GET["db"]]);$J="<a href='#$Hh' class='toggle'>".'SQL command'."</a>\n";if(!$Mc&&($lj=$m->warnings())){$v="warnings-".count($Dd[$_GET["db"]]);$J="<a href='#$v' class='toggle'>".'Warnings'."</a>, $J<div id='$v' class='hidden'>\n$lj</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$Hh' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1000)."</code></pre>".($mi?" <span class='time'>($mi)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".urlencode(DB),"db=".urlencode($_GET["db"]),ME).'sql=&history='.(count($Dd[$_GET["db"]])-1)).'">'.'Edit'.'</a>':'').'</div>';}function
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
const thousandsSeparator = '".js_escape(',')."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'></div>\n",script("mixin(qs('#help'), {onmouseover: () => { helpOpen = 1; }, onmouseout: helpMouseout});"),"<div id='content'>\n";if($Ma!==null){$A=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($A?:".").'">'.$ac[DRIVER].'</a> Â» ';$A=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$N=$b->serverName(SERVER);$N=($N!=""?$N:'Server');if($Ma===false)echo"$N\n";else{echo"<a href='".h($A)."' accesskey='1' title='Alt+Shift+1'>$N</a> Â» ";if($_GET["ns"]!=""||(DB!=""&&is_array($Ma)))echo'<a href="'.h($A."&db=".urlencode(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a> Â» ';if(is_array($Ma)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> Â» ';foreach($Ma
as$z=>$X){$Sb=(is_array($X)?$X[1]:h($X));if($Sb!="")echo"<a href='".h(ME."$z=").urlencode(is_array($X)?$X[0]:$X)."'>$Sb</a> Â» ";}}echo"$pi\n";}}echo"<h2>$ri</h2>\n","<div id='ajaxstatus' class='jsonly hidden'></div>\n";restart_session();page_messages($n);$j=&get_session("dbs");if(DB!=""&&$j&&!in_array(DB,$j,true))$j=null;stop_session();define('Adminer\PAGE_HEADER',1);}function
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
input_hidden("fields[$u][orig]",$Jf);edit_type("fields[$u]",$o,$hb,$ed);if($U=="TABLE")echo"<td>".checkbox("fields[$u][null]",1,$o["null"],"","","block","label-null"),"<td><label class='block'><input type='radio' name='auto_increment_col' value='$u'".($o["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$Ob>".($m->generated?html_select("fields[$u][generated]",array_merge(array("","DEFAULT"),$m->generated),$o["generated"])." ":checkbox("fields[$u][generated]",1,$o["generated"],"","","","label-default")),"<input name='fields[$u][default]' value='".h($o["default"])."' aria-labelledby='label-default'>",(support("comment")?"<td$nb><input name='fields[$u][comment]' value='".h($o["comment"])."' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'>":"");echo"<td>",(support("move_col")?"<input type='image' class='icon' name='add[$u]' src='".h(preg_replace("~\\?.*~","",ME)."?file=plus.gif&version=5.1.0")."' alt='+' title='".'Add next'."'> "."<input type='image' class='icon' name='up[$u]' src='".h(preg_replace("~\\?.*~","",ME)."?file=up.gif&version=5.1.0")."' alt='â†‘' title='".'Move up'."'> "."<input type='image' class='icon' name='down[$u]' src='".h(preg_replace("~\\?.*~","",ME)."?file=down.gif&version=5.1.0")."' alt='â†“' title='".'Move down'."'> ":""),($Jf==""||support("drop_col")?"<input type='image' class='icon' name='drop_col[$u]' src='".h(preg_replace("~\\?.*~","",ME)."?file=cross.gif&version=5.1.0")."' alt='x' title='".'Remove'."'>":"");}}function
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
as$z=>$X){$ih[]=idf_escape($z);if($qd)$qd[]=idf_escape($z);}}$I=$m->select($a,$ih,$Z,$qd,$Ef,$_,$E,true);if(!$I)echo"<p class='error'>".error()."\n";else{if(JUSH=="mssql"&&$E)$I->seek($_*$E);$pc=array();echo"<form action='' method='post' enctype='multipart/form-data'>\n";$L=array();while($K=$I->fetch_assoc()){if($E&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}if($_GET["page"]!="last"&&$_!=""&&$pd&&$ce&&JUSH=="sql")$hd=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".'No rows.'."\n";else{$Ea=$b->backwardKeys($a,$Xh);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'>",script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"),"<thead><tr>".(!$pd&&$M?"":"<td><input type='checkbox' id='all-page' class='jsonly'>".script("qs('#all-page').onclick = partial(formCheck, /check/);","")." <a href='".h($_GET["modify"]?remove_from_uri("modify"):$_SERVER["REQUEST_URI"]."&modify=1")."'>".'Modify'."</a>");$cf=array();$kd=array();reset($M);$Fg=1;foreach($L[0]as$z=>$X){if(!isset($Qi[$z])){$X=$_GET["columns"][key($M)];$o=$p[$M?($X?$X["col"]:current($M)):$z];$C=($o?$b->fieldName($o,$Fg):($X["fun"]?"*":h($z)));if($C!=""){$Fg++;$cf[$z]=$C;$e=idf_escape($z);$Gd=remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($z);$Sb="&desc%5B0%5D=1";$Ah=isset($o["privileges"]["order"]);echo"<th id='th[".h(bracket_escape($z))."]'>".script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});","");$jd=apply_sql_function($X["fun"],$C);echo($Ah?'<a href="'.h($Gd.($Ef[0]==$e||$Ef[0]==$z||(!$Ef&&$ce&&$pd[0]==$e)?$Sb:'')).'">'."$jd</a>":$jd),"<span class='column hidden'>";if($Ah)echo"<a href='".h($Gd.$Sb)."' title='".'descending'."' class='text'> â†“</a>";if(!$X["fun"]&&isset($o["privileges"]["where"]))echo'<a href="#fieldset-search" title="'.'Search'.'" class="text jsonly"> =</a>',script("qsl('a').onclick = partial(selectSearch, '".js_escape($z)."');");echo"</span>";}$kd[$z]=$X["fun"];next($M);}}$xe=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$z=>$X)$xe[$z]=max($xe[$z],min(40,strlen(utf8_decode($X))));}}echo($Ea?"<th>".'Relations':"")."</thead>\n";if(is_ajax())ob_end_clean();foreach($b->rowDescriptions($L,$ed)as$af=>$K){$Ni=unique_array($L[$af],$y);if(!$Ni){$Ni=array();foreach($L[$af]as$z=>$X){if(!preg_match('~^(COUNT\((\*|(DISTINCT )?`(?:[^`]|``)+`)\)|(AVG|GROUP_CONCAT|MAX|MIN|SUM)\(`(?:[^`]|``)+`\))$~',$z))$Ni[$z]=$X;}}$Oi="";foreach($Ni
as$z=>$X){if((JUSH=="sql"||JUSH=="pgsql")&&preg_match('~char|text|enum|set~',$p[$z]["type"])&&strlen($X)>64){$z=(strpos($z,'(')?$z:idf_escape($z));$z="MD5(".(JUSH!='sql'||preg_match("~^utf8~",$p[$z]["collation"])?$z:"CONVERT($z USING ".charset($g).")").")";$X=md5($X);}$Oi.="&".($X!==null?urlencode("where[".bracket_escape($z)."]")."=".urlencode($X===false?"f":$X):"null%5B%5D=".urlencode($z));}echo"<tr>".(!$pd&&$M?"":"<td>".checkbox("check[]",substr($Oi,1),in_array(substr($Oi,1),(array)$_POST["check"])).($ce||information_schema(DB)?"":" <a href='".h(ME."edit=".urlencode($a).$Oi)."' class='edit'>".'edit'."</a>"));foreach($K
as$z=>$X){if(isset($cf[$z])){$o=$p[$z];$X=$m->value($X,$o);if($X!=""&&(!isset($pc[$z])||$pc[$z]!=""))$pc[$z]=(is_mail($X)?$cf[$z]:"");$A="";if(preg_match('~blob|bytea|raw|file~',$o["type"])&&$X!="")$A=ME.'download='.urlencode($a).'&field='.urlencode($z).$Oi;if(!$A&&$X!==null){foreach((array)$ed[$z]as$r){if(count($ed[$z])==1||end($r["source"])==$z){$A="";foreach($r["source"]as$u=>$Bh)$A.=where_link($u,$r["target"][$u],$L[$af][$Bh]);$A=($r["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.urlencode($r["db"]),ME):ME).'select='.urlencode($r["table"]).$A;if($r["ns"])$A=preg_replace('~([?&]ns=)[^&]+~','\1'.urlencode($r["ns"]),$A);if(count($r["source"])==1)break;}}}if($z=="COUNT(*)"){$A=ME."select=".urlencode($a);$u=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$Ni))$A.=where_link($u++,$W["col"],$W["val"],$W["op"]);}foreach($Ni
as$ie=>$W)$A.=where_link($u++,$ie,$W);}$X=select_value($X,$A,$o,$li);$v=h("val[$Oi][".bracket_escape($z)."]");$Y=$_POST["val"][$Oi][bracket_escape($z)];$kc=!is_array($K[$z])&&is_utf8($X)&&$L[$af][$z]==$K[$z]&&!$kd[$z]&&!$o["generated"];$ji=preg_match('~text|json|lob~',$o["type"]);echo"<td id='$v'".(preg_match(number_type(),$o["type"])&&is_numeric(strip_tags($X))?" class='number'":"");if(($_GET["modify"]&&$kc)||$Y!==null){$ud=h($Y!==null?$Y:$K[$z]);echo">".($ji?"<textarea name='$v' cols='30' rows='".(substr_count($K[$z],"\n")+1)."'>$ud</textarea>":"<input name='$v' value='$ud' size='$xe[$z]'>");}else{$Be=strpos($X,"<i>â€¦</i>");echo" data-text='".($Be?2:($ji?1:0))."'".($kc?"":" data-warning='".h('Use edit link to modify this value.')."'").">$X";}}}if($Ea)echo"<td>";$b->backwardKeysPrint($Ea,$L[$af]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){if($L||$E){$Cc=true;if($_GET["page"]!="last"){if($_==""||(count($L)<$_&&($L||!$E)))$hd=($E?$E*$_:0)+count($L);elseif(JUSH!="sql"||!$ce){$hd=($ce?false:found_rows($S,$Z));if($hd<max(1e4,2*($E+1)*$_))$hd=first(slow_query(count_rows($a,$Z,$ce,$pd)));else$Cc=false;}}$Uf=($_!=""&&($hd===false||$hd>$_||$E));if($Uf)echo(($hd===false?count($L)+1:$hd-$E*$_)>$_?'<p><a href="'.h(remove_from_uri("page")."&page=".($E+1)).'" class="loadmore">'.'Load more data'.'</a>'.script("qsl('a').onclick = partial(selectLoadMore, ".(+$_).", '".'Loading'."â€¦');",""):''),"\n";}echo"<div class='footer'><div>\n";if($L||$E){if($Uf){$Je=($hd===false?$E+(count($L)>=$_?2:1):floor(($hd-1)/$_));echo"<fieldset>";if(JUSH!="simpledb"){echo"<legend><a href='".h(remove_from_uri("page"))."'>".'Page'."</a></legend>",script("qsl('a').onclick = function () { pageClick(this.href, +prompt('".'Page'."', '".($E+1)."')); return false; };"),pagination(0,$E).($E>5?" â€¦":"");for($u=max(1,$E-4);$u<min($Je,$E+5);$u++)echo
pagination($u,$E);if($Je>0)echo($E+5<$Je?" â€¦":""),($Cc&&$hd!==false?pagination($Je,$E):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Je'>".'last'."</a>");}else
echo"<legend>".'Page'."</legend>",pagination(0,$E).($E>1?" â€¦":""),($E?pagination($E,$E):""),($Je>$E?pagination($E+1,$E).($Je>$E+1?" â€¦":""):"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".'Whole result'."</legend>";$Yb=($Cc?"":"~ ").$hd;$yf="const checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$Yb' : checked); selectCount('selected2', this.checked || !checked ? '$Yb' : checked);";echo
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