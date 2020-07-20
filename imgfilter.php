<?php
$confdir= array("file","drop","ok");//file->all pics,drop->unwanted pics,ok->wanted pics.please set this before use ?init=true
global $basedir;
list($basedir,$dropdir,$okdir) = $confdir;
global $dataimg;
$dataimg=json_decode(file_get_contents("./".$basedir.".json"),TRUE);
session_start();
function GetFileList($f){
    $file = scandir("./".$f."/");
    array_shift($file);
    array_shift($file);
    $data=json_encode($file);
    file_put_contents($f.".json",$data);
    $dataout=json_decode(file_get_contents($f.".json"),TRUE);
    $allcount=count($dataout);
    return $dataout;
}
function R18($imgid)
{
    $filepath=$GLOBALS['basedir']."/".$GLOBALS['dataimg'][$imgid];
    if(Movefile($filepath,"drop",$GLOBALS['dataimg'][$imgid]))
    {
        global $r18del;
        $r18del="文件已移动到R-18目录!";
        $nowimg=$_SESSION['now_img'];
        if($_SESSION['now_img']==$GLOBALS['allimgcount'])
        {
        $_SESSION['now_img']=0;  
        }
        else{
        $_SESSION['now_img']=$nowimg+1;
        }
    }
}
function Safe($imgid)
{
    $filepath=$GLOBALS['basedir']."/".$GLOBALS['dataimg'][$imgid];
    if(Movefile($filepath,"ok",$GLOBALS['dataimg'][$imgid])){
        global $safe;
        $safe="文件已移动到安全目录!";
        $nowimg=$_SESSION['now_img'];
        if($_SESSION['now_img']==$GLOBALS['allimgcount'])
        {
        $_SESSION['now_img']=0;  
        }
        else{
        $_SESSION['now_img']=$nowimg+1;
        }
    }
}
function img($imgid)
{
    $nowid=$_SESSION['now_img'];
    if($imgid=="next")
    {
        if($_SESSION['now_img']==$GLOBALS['allimgcount'])
        {
        ;
        }
        else{
        $_SESSION['now_img']=$nowid+1;
        }
    }
    else if($imgid=="prev"){
        if($_SESSION['now_img']<=0)
        {
        ;
        }
        else{
    $_SESSION['now_img']=$nowid-1;
        }
    }
}
function MoveFile($sourcefilepath,$folder,$filename)
{
    if(file_exists($sourcefilepath))
    {
        if(rename($sourcefilepath,$folder."/".$filename)){
        return true;
        }
        else{
        return false;
        }  
    }
    else{
        return false;
    }
    
}
function imghandler($imgact,$imgid)
{
    if($imgact=="ok")
    {
        Safe($imgid);
    }
    else if($imgact=="18")
    {
        R18($imgid);
    }
}
$allimgcount=count($dataimg)-1;
if($_GET['init'])
{
    GetFileList($basedir);
    $_SESSION['now_img']=0;
    global $initstat;
    $initstat="数据库已被初始化";
}
if(!isset($_SESSION['now_img']))
{
    GetFileList($basedir);
    $_SESSION['now_img']=0;
    global $initstat;
    $initstat="数据库已被初始化";
}
if($_GET['img'])
{
img($_GET['img']);
}
if($_GET['act'])
{
    imghandler($_GET['act'],$_SESSION['now_img']);
}
echo 
<<<head
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>图片筛选器</title><style>body,html{margin:0;overflow:hidden;scrollbar-width:none}body::-webkit-scrollbar,html::-webkit-scrollbar{width:0}.main{width:100%;height:100vh;background:#333;display:flex;justify-content:center;align-items:center}.main img{height:100%;width:100%;object-fit:scale-down}.main .hint{font-size:48px;font-weight:700;color:#fff}.indicator{z-index:4;position:fixed;padding:10px;background:rgba(0,0,0,0.498);top:0;left:50%;transform:translateX(-50%);color:#fff;opacity:.5;transition-duration:.3s}@media(max-width:599px){.indicator{left:25%}}.indicator:hover{opacity:1}.previous,.next{z-index:1;display:block;color:rgba(204,204,204,0.2);font-size:64px;font-weight:bold;position:fixed;top:0;line-height:75vh;height:75vh;text-decoration:none;width:15vw;text-align:center;transition-duration:.3s}.previous:hover,.next:hover{color:#fff;background-color:rgba(255,255,255,0.247)}.previous{padding-right:35vw;left:0}.next{padding-left:35vw;right:0}@media(min-width:600px){.previous{padding-right:10vw}.next{padding-left:10vw}}@media(min-width:1023px){.previous{padding-right:0}.next{padding-left:0}}.decline,.accept{z-index:2;position:fixed;bottom:0;height:25vh;width:50vw;font-size:64px;line-height:25vh;text-decoration:none;text-align:center;transition-duration:.3s}.decline{color:rgba(255,104,104,0.247);left:0}.decline:hover{color:#f54949;background-color:rgba(246,130,130,0.25)}.accept{color:rgba(114,255,104,0.247);right:0}.accept:hover{color:#53f348;background-color:rgba(165,250,159,0.25)}.init{z-index:3;position:fixed;top:0;right:25vw;transform:translateX(50%);padding:10px;background:rgba(0,0,0,0.498);color:#fff;opacity:.1;transition-duration:.3s;text-decoration:none}@media(max-width:599px){.init{opacity:.5}}.init:hover{opacity:1}.result{position:fixed;top:0;left:25vw;transform:translateX(-50%);padding:10px;background:rgba(0,0,0,0.498);color:#fff;opacity:1;visibility:visible;transition-duration:.3s;text-decoration:none}.result.hidden{opacity:0;visibility:hidden}@media(max-width:599px){.result{top:unset;bottom:0;left:50vw}.result.hidden{bottom:-50px}}</style></head>
<body>
head;
if(file_exists($basedir."/".$dataimg[$_SESSION['now_img']]))
{
echo("<div class=\"main\"><span class=\"indicator\">
图片".($_SESSION['now_img']+1)."/".($allimgcount+1)."</span>
<img src=".$basedir."/".rawurlencode($dataimg[$_SESSION['now_img']])."></img>");
}
else{
    echo ("<div class=\"main\"><span class=\"indicator\">图片".($_SESSION['now_img']+1)."/".($allimgcount+1)."</span><p class=\"hint\">该图片已被处理完毕，现已不存在。</p>");
}
if($_SESSION['now_img']==0)
{
    echo ("<a href=\"?img=next\" class=\"next\" title=\"下一张\">&gt;</a>
    <a href=\"?act=18\" class=\"decline\" title=\"拒绝\">N</a>
    <a href=\"?act=ok\" class=\"accept\" title=\"接受\">Y</a>
    <a href=\"?init=true\" class=\"init\">初始化数据库</a>
    ");
}
else if($_SESSION['now_img']==$allimgcount)
{
    echo ("<a href=\"?img=prev\" class=\"previous\" title=\"上一张\">&lt;</a>
    <a href=\"?act=18\" class=\"decline\" title=\"拒绝\">N</a>
    <a href=\"?act=ok\" class=\"accept\" title=\"接受\">Y</a>
    <a href=\"?init=true\" class=\"init\">初始化数据库</a>");
}
else {
    echo ("<a href=\"?img=prev\" class=\"previous\" title=\"上一张\">&lt;</a>
    <a href=\"?img=next\" class=\"next\" title=\"下一张\">&gt;</a>
    <a href=\"?act=18\" class=\"decline\" title=\"拒绝\">N</a>
    <a href=\"?act=ok\" class=\"accept\" title=\"接受\">Y</a>
    <a href=\"?init=true\" class=\"init\">初始化数据库</a>");
}
//echo ("<a href=\"?init=true\">初始化数据库</a><br>");
echo "<span class=\"result\">".$GLOBALS['r18del'].$GLOBALS['safe'].$GLOBALS['initstat']."</span>";
echo
<<<script
<script>const prev=document.querySelector(".previous"),next=document.querySelector(".next"),init=document.querySelector(".init"),decline=document.querySelector(".decline"),accept=document.querySelector(".accept"),result=document.querySelector(".result");document.addEventListener("keyup",event=>{const keyName=event.key;switch("undefined"!=typeof console&&"function"==typeof console.log&&console.log(keyName),keyName){case"KeyZ":case"z":decline.click();break;case"KeyX":case"x":accept.click();break;case"KeyR":case"r":init.click();break;case"ArrowLeft":prev.click();break;case"ArrowRight":next.click()}});setTimeout(()=>{result.classList.add('hidden')},1000)</script></body></html>
</body></html>
script;
?>
