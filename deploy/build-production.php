<?php
/*
 * Builds the production upload package:
 *   php deploy/build-production.php
 * Output: ../scrap_daddy_production.zip  (scrap_daddy_core/ + public_html/ + DEPLOY.md)
 * Needs: php (zip ext) and composer on PATH. Never includes .env, logs, tests or dev vendor packages.
 */
$src    = dirname(__DIR__);
$stage  = sys_get_temp_dir().'/scrap_daddy_stage';
$zipOut = dirname($src).'/scrap_daddy_production.zip';

function rrmdir($d){ if(!is_dir($d))return; foreach(scandir($d) as $f){ if($f==='.'||$f==='..')continue; $p="$d/$f"; (is_dir($p)&&!is_link($p))?rrmdir($p):@unlink($p);} @rmdir($d);}
function rcopy($s,$d){ @mkdir($d,0777,true); foreach(scandir($s) as $f){ if($f==='.'||$f==='..')continue; $sp="$s/$f"; if(is_link($sp)||$f==='storage'&&basename($s)==='public')continue; is_dir($sp)?rcopy($sp,"$d/$f"):copy($sp,"$d/$f"); } }

rrmdir($stage);
$core = "$stage/scrap_daddy_core"; $pub = "$stage/public_html";
mkdir($core,0777,true);

foreach (['app','bootstrap','config','database','resources','routes'] as $d) rcopy("$src/$d","$core/$d");
foreach (['artisan','composer.json','composer.lock'] as $f) copy("$src/$f","$core/$f");
foreach (['packages','services','config'] as $c) @unlink("$core/bootstrap/cache/$c.php");

foreach (['app/public','framework/cache/data','framework/sessions','framework/views','framework/testing','logs'] as $d) {
    mkdir("$core/storage/$d",0777,true);
    file_put_contents("$core/storage/$d/.gitignore","*\n!.gitignore\n");
}

echo "composer install (no-dev)...\n";
passthru('cd '.escapeshellarg($core).' && composer install --no-dev --optimize-autoloader --no-interaction --no-scripts 2>&1', $rc);
if ($rc !== 0) exit("composer failed\n");

rcopy("$src/public",$pub);
@unlink("$pub/hot");

file_put_contents("$core/.htaccess","# App code must never be web-accessible\n<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n    Order deny,allow\n    Deny from all\n</IfModule>\n");
$env = file_get_contents("$src/.env.production.example");
file_put_contents("$core/.env.production", preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=base64:'.base64_encode(random_bytes(32)), $env));
copy(__DIR__.'/DEPLOY.md', "$stage/DEPLOY.md");

@unlink($zipOut);
$z = new ZipArchive(); $z->open($zipOut, ZipArchive::CREATE);
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($stage, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
$n = 0;
foreach ($it as $f) {
    $rel = str_replace('\\','/',substr($f->getPathname(), strlen($stage)+1));
    if ($f->isDir()) $z->addEmptyDir($rel); else { $z->addFile($f->getPathname(), $rel); $n++; }
}
$z->close();
rrmdir($stage);
printf("files: %d, zip: %.1f MB -> %s\n", $n, filesize($zipOut)/1048576, $zipOut);
