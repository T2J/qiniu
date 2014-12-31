<?php
$html='<html><head><meta charset="UTF-8"/></head><body>';
//function
//新建文件夹
function createFolder($path) 
{ 
if (!file_exists($path)) 
{ 
createFolder(dirname($path)); 
mkdir($path, 0777); 
} 
} 
//生成随机字符串
function generate( $length) {  
$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';  
$code = '';  
for ( $i = 0; $i < $length; $i++ )  
{  
$code .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
}  
return $code; 
} 
//文件安全下载函数
function downloads($name,$newname,$html){
        if (!file_exists($name)){
            header("Content-type: text/html; charset=utf-8");
            echo "File not found!";
            exit; 
        } else {
            $file = fopen($name,"r"); 
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length: ".filesize($name));
            Header("Content-Disposition: attachment; filename=".$name);
            echo fread($file, filesize($file_dir.$name));
            fclose($file);
            if(!rename($name,$newname))
            {
            echo ("Could not remove!");
            }
        }
    }

//基本数据地址
$dataurl=$_GET["data"];
$html.="==>您的数据文件地址：".$dataurl."</br>";
//开始解析数据
if($dataurl!=""&&1)
{
//准备工作
$content=json_decode(file_get_contents($dataurl));//读取json数据
$count_=count($content->items);//统计文件个数
$html.="==>一共有".$count_."个文件，开始准备打包，打包过程可能比较长，请您耐心等待。</br>";//打印文件个数
//新建文件夹
$foldername="qiniu";
createFolder($foldername); 
//循环下载文件
$i=10;
for($i=0;$i<$count_;$i++)
{
    $url=explode("?",$content->items[$i]->signed_download_url,2);//文件下载地址
    $html.="-->文件".$url[$i]."打包已完成。</br>";
    file_put_contents($foldername."/".$content->items[$i]->key,file_get_contents($url[0]));
}
include("zip.php");//引用压缩类
$zip = new PclZip($foldername.".zip");//压缩文件
$zip->create($foldername); 
$html.="==>打包完成，开始准备下载！<b>请在下载完成后关闭本页面,否则会造成文件缺失。</b></br>";
//后续删除文件
//随机生成替代文件名
$path = $foldername;
$foldername_new=generate(9);
if(!rename($path,$foldername_new))
  {
  $html.="Could not remove!";
  }
$file = $foldername.".zip";
$html.="==>感谢您使用我们的服务！祝您生活愉快！</br></body></html>";
echo $html;

downloads($file,$foldername_new.".zip");
 }
 else 
{
$html.="错误！</body></html>";
echo $html;
}
?>
